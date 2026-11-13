<?php

namespace App\Http\Controllers;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class SeoController extends Controller
{
private const TTL = 3600;   // 1 hour

    /** published, indexable, and self-canonical articles only */
    private function indexable()
    {
        return Article::published()
            ->where('allow_indexing', true)
            ->where(fn ($q) => $q->whereNull('canonical_url')->orWhere('canonical_url', ''))
            ->whereHas('category');
    }

    /* ---------- sitemap.xml ---------- */
    public function sitemap()
    {
        $xml = Cache::remember('seo.sitemap', self::TTL, function () {
            $articles = $this->indexable()
                ->with(['templateArticle' => fn ($q) => $q->withTrashed()])
                ->orderByDesc('published_at')
                ->limit(50000)   // sitemap protocol limit
                ->get(['id', 'slug', 'title', 'thumbnail_type', 'thumbnail_path',
                       'template_article_id', 'updated_at', 'last_reviewed_at']);

            $categories = Category::whereHas('articles', fn ($q) => $q->published()->where('allow_indexing', true))
                ->withMax(['articles as last_mod' => fn ($q) => $q->published()], 'updated_at')
                ->orderBy('name')
                ->get(['id', 'slug']);

            $homeMod = $articles->map(fn ($a) => $this->modified($a))->max();

            return view('seo.sitemap', compact('articles', 'categories', 'homeMod'))->render();
        });

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /* ---------- robots.txt ---------- */
    public function robots()
    {
        $txt = Cache::remember('seo.robots', self::TTL, function () {
            $rules = collect(config('site.seo.disallow'))
                ->map(fn ($p) => "Disallow: {$p}")->implode("\n");

            // a bot with its own group ignores the "*" group, so the rules are repeated
            $out  = "User-agent: *\nAllow: /\n{$rules}\n";

            if (config('site.seo.allow_ai')) {
                $bots = collect(config('site.seo.ai_bots'))
                    ->map(fn ($b) => "User-agent: {$b}")->implode("\n");
                $out .= "\n{$bots}\nAllow: /\n{$rules}\n";
            }

            return $out . "\nSitemap: " . url('sitemap.xml') . "\n";
        });

        return response($txt, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    /* ---------- llms.txt ---------- */
    public function llms()
    {
        $txt = Cache::remember('seo.llms', self::TTL, function () {
            $home = url(config('site.home_url'));

            $categories = Category::whereHas('articles', fn ($q) => $q->published())
                ->withCount(['articles' => fn ($q) => $q->published()])
                ->orderBy('name')->get(['id', 'name', 'slug', 'description']);

            $articles = $this->indexable()
                ->with('category:id,name')
                ->latest('published_at')->limit(100)
                ->get(['id', 'category_id', 'slug', 'title', 'excerpt', 'meta_description']);

            $out  = '# ' . config('site.name') . "\n\n";
            $out .= '> ' . config('site.description') . "\n\n";

            $out .= "## Topics\n\n";
            foreach ($categories as $c) {
                $desc = $c->description ? ': ' . $this->oneLine($c->description) : '';
                $out .= "- [{$c->name}]({$home}/category/{$c->slug}){$desc}\n";
            }

            $out .= "\n## Articles\n\n";
            foreach ($articles as $a) {
                $desc = $this->oneLine($a->excerpt ?: $a->meta_description);
                $out .= "- [{$a->title}](" . route('public.article.show', $a) . ')'
                      . ($desc ? ": {$desc}" : '') . "\n";
            }

            $out .= "\n## Optional\n\n- [Sitemap](" . url('sitemap.xml') . ")\n- [RSS feed](" . url('feed.xml') . ")\n";

            return $out;
        });

        return response($txt, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    /* ---------- feed.xml (RSS) ---------- */
    public function feed()
    {
        $xml = Cache::remember('seo.feed', self::TTL, function () {
            $articles = $this->indexable()
                ->with(['category:id,name', 'user:id,name'])
                ->latest('published_at')->limit(20)
                ->get(['id', 'user_id', 'category_id', 'slug', 'title', 'excerpt',
                       'meta_description', 'published_at', 'updated_at']);

            return view('seo.feed', compact('articles'))->render();
        });

        return response($xml, 200, ['Content-Type' => 'application/rss+xml; charset=UTF-8']);
    }

    /* ---------- helpers ---------- */
    private function modified($a)
    {
        return ($a->last_reviewed_at && $a->last_reviewed_at->gt($a->updated_at))
            ? $a->last_reviewed_at : $a->updated_at;
    }

    private function oneLine(?string $text): string
    {
        return \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags((string) $text))), 160);
    }
}
