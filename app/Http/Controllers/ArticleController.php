<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ThumbnailTemplate;
use Illuminate\Http\Request;
use App\Models\Article;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        $now = now();

        $articles = Article::with(['category:id,name', 'user:id,name', 'templateArticle'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $kw = $request->search;
                $q->where(fn ($w) => $w->where('title', 'like', "%{$kw}%")
                                    ->orWhere('slug', 'like', "%{$kw}%"));
            })
            ->when($request->filled('category'), fn ($q) => $q->where('category_id', $request->category))
            ->when($status === 'published', fn ($q) => $q->where('is_published', true)->where('published_at', '<=', $now))
            ->when($status === 'scheduled', fn ($q) => $q->where('is_published', true)->where('published_at', '>', $now))
            ->when($status === 'draft',     fn ($q) => $q->where('is_published', false))
            ->when(true, function ($q) use ($request) {
                match ($request->query('sort', 'newest')) {
                    'oldest' => $q->oldest(),
                    'title'  => $q->orderBy('title'),
                    default  => $q->latest(),
                };
            })
            ->paginate(10)
            ->withQueryString();

        // one query for all the tab counts (soft-deleted rows are excluded automatically)
        $row = Article::selectRaw(
            'COUNT(*) as total,
            SUM(is_published = 0) as draft,
            SUM(is_published = 1 AND published_at <= ?) as published,
            SUM(is_published = 1 AND published_at > ?) as scheduled',
            [$now, $now]
        )->first();

        $counts = [
            'all'       => (int) ($row->total ?? 0),
            'published' => (int) ($row->published ?? 0),
            'scheduled' => (int) ($row->scheduled ?? 0),
            'draft'     => (int) ($row->draft ?? 0),
        ];

        $categories = Category::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        return view('article.admin.index', compact('articles', 'counts', 'categories', 'status'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name', 'asc')->get();
        $thumbnails = ThumbnailTemplate::where('is_active', true)->latest()->get();
        return view('article.admin.write', compact('categories', 'thumbnails'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // "Save as draft" button or the Draft card both mean draft
        $isDraft = $request->input('action') === 'draft'
            || $request->input('publish_mode') === 'draft';

        /* ---------- 1. Validation ---------- */
        $validated = $request->validate([
            // Basic information
            'title'       => ['required', 'string', 'max:120'],
            'slug'        => ['nullable', 'string', 'max:255'],
            'category_id' => ['required', Rule::exists('categories', 'id')->whereNull('deleted_at')],
            'excerpt'     => ['nullable', 'string', 'max:200'],
            'content'     => [$isDraft ? 'nullable' : 'required', 'string'],

            // Thumbnail
            'thumbnail_type'      => ['required', 'in:upload,template'],
            'thumbnail'           => [
                Rule::requiredIf(!$isDraft && $request->thumbnail_type === 'upload'),
                'nullable', 'file', 'mimes:png,jpg,jpeg', 'max:2048',
            ],
            'template_article_id' => [
                Rule::requiredIf(!$isDraft && $request->thumbnail_type === 'template'),
                'nullable',
                Rule::exists('thumbnail_templates', 'id')
                    ->where('is_active', true)
                    ->whereNull('deleted_at'),
            ],

            // SEO
            'meta_title'         => ['nullable', 'string', 'max:70'],
            'meta_description'   => ['nullable', 'string', 'max:170'],
            'focus_keyword'      => ['nullable', 'string', 'max:100'],
            'secondary_keywords' => ['nullable', 'string', 'max:255'],
            'allow_indexing'     => ['required', 'boolean'],
            'allow_following'    => ['required', 'boolean'],

            // GEO
            'key_takeaways'             => ['nullable', 'array', 'max:7'],
            'key_takeaways.*'           => ['nullable', 'string', 'max:200'],
            'faq'                       => ['nullable', 'array', 'max:10'],
            'faq.*.question'            => ['nullable', 'string', 'max:200'],
            'faq.*.answer'              => ['nullable', 'string', 'max:1000'],
            'source_references'         => ['nullable', 'array', 'max:10'],
            'source_references.*.title' => ['nullable', 'string', 'max:150'],
            'source_references.*.url'   => ['nullable', 'url', 'max:500'],
            'schema_type'               => ['required', 'in:Article,BlogPosting,NewsArticle,HowTo'],
            'schema_data'               => ['nullable', 'json'],
            'last_reviewed_at'          => ['nullable', 'date'],

            // Publishing
            'publish_mode' => ['required', 'in:draft,now,schedule'],
            'published_at' => ['required_if:publish_mode,schedule', 'nullable', 'date', 'after:now'],
        ]);

        // Tiptap sends "<p></p>" for an empty editor, so check the real text
        if (!$isDraft && trim(strip_tags($validated['content'] ?? '')) === '') {
            throw ValidationException::withMessages([
                'content' => 'The article content is required.',
            ]);
        }

        /* ---------- 2. Basic information ---------- */
        $data = [
            'user_id'     => Auth::user()->id,
            'category_id' => $validated['category_id'],
            'title'       => $validated['title'],
            'slug'        => $this->uniqueSlug($validated['slug'] ?: $validated['title']),
            'excerpt'     => $validated['excerpt'] ?? null,
            'content'     => $validated['content'] ?? '',   // column is NOT NULL
        ];

        /* ---------- 3. Thumbnail (only one option is kept) ---------- */
        $data['thumbnail_type']      = $validated['thumbnail_type'];
        $data['thumbnail_path']      = null;
        $data['template_article_id'] = null;

        $uploadedPath = null;

        if ($validated['thumbnail_type'] === 'upload' && $request->hasFile('thumbnail')) {
            $uploadedPath = $request->file('thumbnail')->store('article-thumbnails', 'public');
            $data['thumbnail_path'] = $uploadedPath;
        }

        if ($validated['thumbnail_type'] === 'template') {
            $data['template_article_id'] = $validated['template_article_id'] ?? null;
        }

        /* ---------- 4. SEO ---------- */
        $metaTitle       = $validated['meta_title'] ?? null ?: $validated['title'];
        $metaDescription = $validated['meta_description'] ?? null ?: ($validated['excerpt'] ?? null);

        $data += [
            'meta_title'         => $metaTitle,
            'meta_description'   => $metaDescription,
            'focus_keyword'      => $validated['focus_keyword'] ?? null,
            'secondary_keywords' => $this->commaList($validated['secondary_keywords'] ?? null),
            'og_title'           => $metaTitle,
            'og_description'     => $metaDescription,
            'allow_indexing'     => $request->boolean('allow_indexing'),
            'allow_following'    => $request->boolean('allow_following'),
        ];

        /* ---------- 5. GEO ---------- */
        $data += [
            'key_takeaways' => collect($validated['key_takeaways'] ?? [])
                ->map(fn ($v) => trim((string) $v))
                ->filter()->values()->all() ?: null,

            'faq' => collect($validated['faq'] ?? [])
                ->filter(fn ($f) => filled($f['question'] ?? null) && filled($f['answer'] ?? null))
                ->map(fn ($f) => [
                    'question' => trim($f['question']),
                    'answer'   => trim($f['answer']),
                ])
                ->values()->all() ?: null,

            'source_references' => collect($validated['source_references'] ?? [])
                ->filter(fn ($s) => filled($s['url'] ?? null))
                ->map(fn ($s) => [
                    'title' => trim($s['title'] ?? ''),
                    'url'   => trim($s['url']),
                ])
                ->values()->all() ?: null,

            'schema_type'      => $validated['schema_type'],
            'schema_data'      => filled($validated['schema_data'] ?? null)
                ? json_decode($validated['schema_data'], true)
                : null,
            'last_reviewed_at' => $validated['last_reviewed_at'] ?? null,
        ];

        /* ---------- 6. Publishing ---------- */
        $mode = $isDraft ? 'draft' : $validated['publish_mode'];

        $data['is_published'] = $mode !== 'draft';
        $data['published_at'] = match ($mode) {
            'schedule' => $validated['published_at'],
            'now'      => now(),
            default    => null,
        };

        /* ---------- 7. Save ---------- */
        try {
            $article = Article::create($data);
        } catch (\Throwable $e) {
            // don't leave an orphan file if the insert fails
            if ($uploadedPath) {
                Storage::disk('public')->delete($uploadedPath);
            }
            throw $e;
        }

        $message = match ($mode) {
            'draft'    => 'Article saved as draft.',
            'schedule' => 'Article scheduled successfully.',
            default    => 'Article published successfully.',
        };

        return redirect()
            ->route('admin.article.index')   // change to your articles list route
            ->with('success', $message);
    }

    /**
     * Turn any text into a slug that is not used yet.
     * withTrashed() because your unique index also covers soft-deleted rows.
     */
    private function uniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source) ?: 'article';
        $slug = $base;
        $i = 2;

        while (Article::withTrashed()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    /** "php, laravel, php" becomes ['php', 'laravel'], or null when empty */
    private function commaList(?string $value): ?array
    {
        $list = collect(explode(',', (string) $value))
            ->map(fn ($k) => trim($k))
            ->filter()->unique()->values()->all();

        return $list ?: null;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article)
    {
        $article->load('templateArticle');

        $categories = Category::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        // active templates, plus the one this article already uses (even if it was deactivated)
        $thumbnails = ThumbnailTemplate::where('is_active', true)
            ->orWhere('id', $article->template_article_id)
            ->latest()->get();

        return view('article.admin.edit', compact('article', 'categories', 'thumbnails'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article)
    {
        $isDraft = $request->input('action') === 'draft'
            || $request->input('publish_mode') === 'draft';

        $hasUpload = !empty($article->thumbnail_path);

        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:120'],
            'slug'        => ['nullable', 'string', 'max:255'],
            'category_id' => ['required', Rule::exists('categories', 'id')->whereNull('deleted_at')],
            'excerpt'     => ['nullable', 'string', 'max:200'],
            'content'     => [$isDraft ? 'nullable' : 'required', 'string'],

            'thumbnail_type' => ['required', 'in:upload,template'],
            'thumbnail'      => [
                // not required when the article already has an uploaded image
                Rule::requiredIf(!$isDraft && $request->thumbnail_type === 'upload' && !$hasUpload),
                'nullable', 'file', 'mimes:png,jpg,jpeg', 'max:2048',
            ],
            'template_article_id' => [
                Rule::requiredIf(!$isDraft && $request->thumbnail_type === 'template'),
                'nullable',
                Rule::exists('thumbnail_templates', 'id')->whereNull('deleted_at')
                    ->where(fn ($q) => $q->where('is_active', true)
                        ->orWhere('id', $article->template_article_id)),
            ],

            'meta_title'         => ['nullable', 'string', 'max:70'],
            'meta_description'   => ['nullable', 'string', 'max:170'],
            'focus_keyword'      => ['nullable', 'string', 'max:100'],
            'secondary_keywords' => ['nullable', 'string', 'max:255'],
            'allow_indexing'     => ['required', 'boolean'],
            'allow_following'    => ['required', 'boolean'],

            'key_takeaways'             => ['nullable', 'array', 'max:7'],
            'key_takeaways.*'           => ['nullable', 'string', 'max:200'],
            'faq'                       => ['nullable', 'array', 'max:10'],
            'faq.*.question'            => ['nullable', 'string', 'max:200'],
            'faq.*.answer'              => ['nullable', 'string', 'max:1000'],
            'source_references'         => ['nullable', 'array', 'max:10'],
            'source_references.*.title' => ['nullable', 'string', 'max:150'],
            'source_references.*.url'   => ['nullable', 'url', 'max:500'],
            'schema_type'               => ['required', 'in:Article,BlogPosting,NewsArticle,HowTo'],
            'schema_data'               => ['nullable', 'json'],
            'last_reviewed_at'          => ['nullable', 'date'],

            'publish_mode' => ['required', 'in:draft,now,schedule'],
            'published_at' => ['required_if:publish_mode,schedule', 'nullable', 'date', 'after:now'],
        ]);

        if (!$isDraft && trim(strip_tags($validated['content'] ?? '')) === '') {
            throw ValidationException::withMessages(['content' => 'The article content is required.']);
        }

        /* basic information (user_id stays the original author) */
        $data = [
            'category_id' => $validated['category_id'],
            'title'       => $validated['title'],
            'slug'        => $this->uniqueSlug($validated['slug'] ?: $validated['title'], $article->id),
            'excerpt'     => $validated['excerpt'] ?? null,
            'content'     => $validated['content'] ?? '',
        ];

        /* thumbnail */
        $oldPath = $article->thumbnail_path;
        $newPath = null;

        $data['thumbnail_type'] = $validated['thumbnail_type'];

        if ($validated['thumbnail_type'] === 'upload') {
            if ($request->hasFile('thumbnail')) {
                $newPath = $request->file('thumbnail')->store('article-thumbnails', 'public');
                $data['thumbnail_path'] = $newPath;
            } else {
                $data['thumbnail_path'] = $oldPath;   // keep the current image
            }
            $data['template_article_id'] = null;
        } else {
            $data['thumbnail_path']      = null;
            $data['template_article_id'] = $validated['template_article_id'] ?? null;
        }

        /* SEO */
        $metaTitle       = ($validated['meta_title'] ?? null) ?: $validated['title'];
        $metaDescription = ($validated['meta_description'] ?? null) ?: ($validated['excerpt'] ?? null);

        $data += [
            'meta_title'         => $metaTitle,
            'meta_description'   => $metaDescription,
            'focus_keyword'      => $validated['focus_keyword'] ?? null,
            'secondary_keywords' => $this->commaList($validated['secondary_keywords'] ?? null),
            'og_title'           => $metaTitle,
            'og_description'     => $metaDescription,
            'allow_indexing'     => $request->boolean('allow_indexing'),
            'allow_following'    => $request->boolean('allow_following'),
        ];

        /* GEO */
        $data += [
            'key_takeaways' => collect($validated['key_takeaways'] ?? [])
                ->map(fn ($v) => trim((string) $v))->filter()->values()->all() ?: null,

            'faq' => collect($validated['faq'] ?? [])
                ->filter(fn ($f) => filled($f['question'] ?? null) && filled($f['answer'] ?? null))
                ->map(fn ($f) => ['question' => trim($f['question']), 'answer' => trim($f['answer'])])
                ->values()->all() ?: null,

            'source_references' => collect($validated['source_references'] ?? [])
                ->filter(fn ($s) => filled($s['url'] ?? null))
                ->map(fn ($s) => ['title' => trim($s['title'] ?? ''), 'url' => trim($s['url'])])
                ->values()->all() ?: null,

            'schema_type'      => $validated['schema_type'],
            'schema_data'      => filled($validated['schema_data'] ?? null)
                ? json_decode($validated['schema_data'], true) : null,
            'last_reviewed_at' => $validated['last_reviewed_at'] ?? null,
        ];

        /* publishing */
        $mode = $isDraft ? 'draft' : $validated['publish_mode'];

        $data['is_published'] = $mode !== 'draft';
        $data['published_at'] = match ($mode) {
            'schedule' => $validated['published_at'],
            // already live: keep the original publish date
            'now'      => ($article->is_published && $article->published_at?->isPast())
                            ? $article->published_at : now(),
            default    => null,
        };

        /* save, then clean up the old file */
        try {
            $article->update($data);
        } catch (\Throwable $e) {
            if ($newPath) Storage::disk('public')->delete($newPath);
            throw $e;
        }

        if ($oldPath && $oldPath !== ($data['thumbnail_path'] ?? null)) {
            Storage::disk('public')->delete($oldPath);
        }

        return redirect()->route('admin.article.index')->with('success', match ($mode) {
            'draft'    => 'Draft updated.',
            'schedule' => 'Article rescheduled.',
            default    => 'Article updated.',
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $request->validate(['article_id' => ['required', 'exists:articles,id']]);

        Article::findOrFail($request->article_id)->delete(); // soft delete

        return back()->with('success', 'Article deleted.');
    }
}
