{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
    <url>
        <loc>{{ url(config('site.home_url')) }}</loc>
        @if ($homeMod)<lastmod>{{ $homeMod->toAtomString() }}</lastmod>@endif
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    @foreach ($categories as $category)
    <url>
        <loc>{{ url(config('site.home_url') . '/category/' . $category->slug) }}</loc>
        @if ($category->last_mod)<lastmod>{{ \Illuminate\Support\Carbon::parse($category->last_mod)->toAtomString() }}</lastmod>@endif
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach

    @foreach ($articles as $article)
    @php
        $img = $article->thumbnail_type === 'template'
            ? $article->templateArticle?->image_url
            : ($article->thumbnail_path ? asset('storage/' . $article->thumbnail_path) : null);
        $mod = ($article->last_reviewed_at && $article->last_reviewed_at->gt($article->updated_at))
            ? $article->last_reviewed_at : $article->updated_at;
    @endphp
    <url>
        <loc>{{ route('public.article.show', $article) }}</loc>
        <lastmod>{{ $mod->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
        @if ($img)
        <image:image>
            <image:loc>{{ $img }}</image:loc>
            <image:title>{{ $article->title }}</image:title>
        </image:image>
        @endif
    </url>
    @endforeach
</urlset>
