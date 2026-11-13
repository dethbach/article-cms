{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ config('site.name') }}</title>
        <link>{{ url(config('site.home_url')) }}</link>
        <description>{{ config('site.description') }}</description>
        <language>{{ str_replace('_', '-', app()->getLocale()) }}</language>
        <atom:link href="{{ url('feed.xml') }}" rel="self" type="application/rss+xml"/>
        @if ($articles->isNotEmpty())
        <lastBuildDate>{{ $articles->max('updated_at')->toRssString() }}</lastBuildDate>
        @endif

        @foreach ($articles as $article)
        <item>
            <title>{{ $article->title }}</title>
            <link>{{ route('public.article.show', $article) }}</link>
            <guid isPermaLink="true">{{ route('public.article.show', $article) }}</guid>
            <pubDate>{{ $article->published_at->toRssString() }}</pubDate>
            <category>{{ $article->category->name }}</category>
            @if ($article->user)<dc:creator xmlns:dc="http://purl.org/dc/elements/1.1/">{{ $article->user->name }}</dc:creator>@endif
            <description>{{ \Illuminate\Support\Str::limit(strip_tags($article->excerpt ?: $article->meta_description), 300) }}</description>
        </item>
        @endforeach
    </channel>
</rss>
