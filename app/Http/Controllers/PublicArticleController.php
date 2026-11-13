<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PublicArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = Category::whereHas('articles', fn ($q) => $q->published())
            ->withCount(['articles' => fn ($q) => $q->published()])
            ->orderByDesc('articles_count')
            ->orderBy('name')
            ->get();

        $totalArticles = Article::published()->count();

        $q          = trim((string) $request->query('q', ''));
        $activeSlug = $request->query('category');
        $filtering  = $q !== '' || filled($activeSlug);

        // relations shared by featured and latest
        $with = [
            'category'        => fn ($r) => $r->select('id', 'name', 'slug'),
            'user:id,name',
            'templateArticle' => fn ($r) => $r->withTrashed(),
        ];

        // featured: newest 4, only when not searching or filtering
        $featured = collect();
        if (!$filtering) {
            $featured = Article::published()
                ->whereHas('category')
                ->with($with)
                ->latest('published_at')
                ->orderByDesc('id')
                ->take(4)
                ->get();
        }

        $minLatest       = 3;
        $excludeFeatured = $featured->isNotEmpty()
            && ($totalArticles - $featured->count()) >= $minLatest;

        $articles = Article::published()
            ->whereHas('category', fn ($c) => $c->when($activeSlug, fn ($c) => $c->where('slug', $activeSlug)))
            ->with($with)
            ->when($q !== '', fn ($query) => $query->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                ->orWhere('excerpt', 'like', "%{$q}%");
            }))
            ->when($excludeFeatured, fn ($query) => $query->whereNotIn('id', $featured->pluck('id')))
            ->latest('published_at')
            ->orderByDesc('id')
            ->paginate(9)
            ->withQueryString()
            ->fragment('latest');

        $activeCategory = $activeSlug ? $categories->firstWhere('slug', $activeSlug) : null;

        return view('public.index', compact(
            'categories', 'totalArticles', 'featured', 'articles', 'q', 'activeCategory', 'filtering'
        ));
    }

    public function category(Request $request, Category $category)
    {
        $q = trim((string) $request->query('q', ''));

        $base = Article::published()->where('category_id', $category->id);

        $totalInCategory = (clone $base)->count();

        $articles = (clone $base)
            ->with([
                'user:id,name',
                'templateArticle' => fn ($r) => $r->withTrashed(),
            ])
            ->when($q !== '', fn ($query) => $query->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                ->orWhere('excerpt', 'like', "%{$q}%");
            }))
            ->latest('published_at')
            ->orderByDesc('id')
            ->paginate(9)
            ->withQueryString();

        // other topics, for the "keep exploring" row
        $otherCategories = Category::where('id', '!=', $category->id)
            ->whereHas('articles', fn ($r) => $r->published())
            ->withCount(['articles' => fn ($r) => $r->published()])
            ->orderByDesc('articles_count')
            ->orderBy('name')
            ->limit(10)
            ->get();

        return view('public.category', compact(
            'category', 'articles', 'q', 'totalInCategory', 'otherCategories'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        $article->load([
            'category'        => fn ($q) => $q->select('id', 'name', 'slug'),
            'user:id,name',
            'templateArticle' => fn ($q) => $q->withTrashed(),
        ]);

        // deleted category: 404 (same rule as the rest of the public site)
        abort_if(!$article->category, 404);

        // drafts and scheduled articles: visitors get 404, logged-in users get a preview
        $isLive  = $article->status === 'published';
        abort_if(!$isLive && !Auth::check(), 404);

        [$html, $toc] = $this->prepareContent((string) $article->content);

        $with = [
            'category'        => fn ($r) => $r->select('id', 'name', 'slug'),
            'user:id,name',
            'templateArticle' => fn ($r) => $r->withTrashed(),
        ];

        // related: same category first, then fill up with the newest others
        $related = Article::published()->whereHas('category')->with($with)
            ->where('id', '!=', $article->id)
            ->where('category_id', $article->category_id)
            ->latest('published_at')->take(3)->get();

        if ($related->count() < 3) {
            $more = Article::published()->whereHas('category')->with($with)
                ->where('id', '!=', $article->id)
                ->whereNotIn('id', $related->pluck('id'))
                ->latest('published_at')->take(3 - $related->count())->get();
            $related = $related->concat($more);
        }

        // SEO values
        $image = $article->thumbnail_type === 'template'
            ? $article->templateArticle?->image_url
            : ($article->thumbnail_path ? asset('storage/' . $article->thumbnail_path) : null);

        $canonical = $article->canonical_url ?: route('public.article.show', $article);
        $metaTitle = $article->meta_title ?: $article->title;
        $metaDesc  = $article->meta_description ?: $article->excerpt
            ?: Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags((string) $article->content))), 155);

        $robots = ($article->allow_indexing ? 'index' : 'noindex') . ','
                . ($article->allow_following ? 'follow' : 'nofollow');
        if (!$isLive) $robots = 'noindex,nofollow';   // previews never get indexed

        $schemas = $this->schemas($article, $canonical, $metaDesc, $image);

        return view('public.show', compact(
            'article', 'html', 'toc', 'related', 'image', 'canonical',
            'metaTitle', 'metaDesc', 'robots', 'schemas', 'isLive'
        ));
    }

    /** adds ids to h2/h3 headings and returns [html, table of contents] */
    private function prepareContent(string $html): array
    {
        if (trim($html) === '') return ['', []];

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?><div id="wrap">' . $html . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);
        $toc = [];
        $used = [];

        foreach ($xpath->query('//h2|//h3') as $h) {

            if (!$h instanceof \DOMElement) {
                continue;
            }

            $text = trim($h->textContent);
            if ($text === '') continue;

            $base = Str::slug($text) ?: 'section';
            $id = $base;
            $i = 2;
            while (isset($used[$id])) $id = $base . '-' . $i++;
            $used[$id] = true;

            $h->setAttribute('id', $id);
            $toc[] = ['id' => $id, 'text' => $text, 'level' => $h->nodeName === 'h2' ? 2 : 3];
        }

        $wrap = $xpath->query('//div[@id="wrap"]')->item(0);
        $out = '';
        foreach ($wrap->childNodes as $child) $out .= $dom->saveHTML($child);

        return [$out, $toc];
    }

    /** JSON-LD blocks: article, FAQ and breadcrumbs */
    private function schemas(Article $a, string $canonical, string $desc, ?string $image): array
    {
        $keywords = array_values(array_filter(array_merge(
            [$a->focus_keyword], $a->secondary_keywords ?? []
        )));

        $article = array_filter([
            '@context'         => 'https://schema.org',
            '@type'            => $a->schema_type ?: 'Article',
            'headline'         => Str::limit($a->title, 110, ''),
            'name'             => $a->title,
            'description'      => $desc,
            'image'            => $image ? [$image] : null,
            'datePublished'    => $a->published_at?->toIso8601String(),
            'dateModified'     => ($a->last_reviewed_at && $a->last_reviewed_at->gt($a->updated_at)
                                    ? $a->last_reviewed_at : $a->updated_at)->toIso8601String(),
            'author'           => ['@type' => 'Person', 'name' => $a->user->name ?? config('site.name')],
            'publisher'        => ['@type' => 'Organization', 'name' => config('site.name')],
            'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $canonical],
            'articleSection'   => $a->category->name,
            'keywords'         => $keywords ? implode(', ', $keywords) : null,
            'wordCount'        => str_word_count(strip_tags((string) $a->content)) ?: null,
            'inLanguage'       => str_replace('_', '-', app()->getLocale()),
            'citation'         => collect($a->source_references ?? [])->pluck('url')->filter()->values()->all() ?: null,
        ]);

        // the writer's custom JSON wins over the generated values
        if (is_array($a->schema_data)) {
            $article = array_merge($article, $a->schema_data);
        }

        $schemas = [$article];

        if (!empty($a->faq)) {
            $schemas[] = [
                '@context'   => 'https://schema.org',
                '@type'      => 'FAQPage',
                'mainEntity' => collect($a->faq)->map(fn ($f) => [
                    '@type' => 'Question',
                    'name'  => $f['question'],
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['answer']],
                ])->all(),
            ];
        }

        $home = url(config('site.home_url'));
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type'    => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $home],
                ['@type' => 'ListItem', 'position' => 2, 'name' => $a->category->name, 'item' => $home . '/category/' . $a->category->slug],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $a->title],
            ],
        ];

        return $schemas;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
