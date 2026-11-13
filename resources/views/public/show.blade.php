@extends('layouts.public')

@php
    $home   = config('site.home_url');
    $author = $article->user->name ?? 'Team';
    $shareUrl   = urlencode($canonical);
    $shareTitle = urlencode($article->title);
    $updated = $article->updated_at && $article->published_at && $article->updated_at->gt($article->published_at->copy()->addDay());
@endphp

@section('title', $metaTitle . ' | ' . config('site.name'))
@section('meta_description', $metaDesc)
@section('canonical', $canonical)
@section('robots', $robots)
@section('og_type', 'article')
@section('og_title', $article->og_title ?: $metaTitle)
@section('og_description', $article->og_description ?: $metaDesc)
@if ($image) @section('og_image', $image) @endif

@push('head')
    <meta name="twitter:card" content="{{ $image ? 'summary_large_image' : 'summary' }}">
    @if ($article->published_at)
        <meta property="article:published_time" content="{{ $article->published_at->toIso8601String() }}">
    @endif
    <meta property="article:modified_time" content="{{ $article->updated_at->toIso8601String() }}">
    <meta property="article:section" content="{{ $article->category->name }}">
    @foreach ($schemas as $schema)
        <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
    @endforeach
@endpush

@section('content')

<div class="ui-progress" id="readProgress" aria-hidden="true"></div>

@unless ($isLive)
    <div class="ui-preview-bar">
        <i class="bi bi-eye"></i>
        Preview: this article is <strong>{{ $article->status }}</strong> and not visible to visitors.
    </div>
@endunless

<article class="ui-read container">

    {{-- breadcrumb --}}
    <nav class="ui-pcrumbs" aria-label="Breadcrumb">
        <ol>
            <li><a href="{{ $home }}"><i class="bi bi-house-door"></i> Home</a></li>
            <li><a href="{{ $home }}/category/{{ $article->category->slug }}">{{ $article->category->name }}</a></li>
            <li aria-current="page" class="ui-read__crumb-current">{{ Str::limit($article->title, 50) }}</li>
        </ol>
    </nav>

    {{-- header --}}
    <header class="ui-read__head">
        <a href="{{ $home }}/category/{{ $article->category->slug }}" class="ui-ptag">
            <i class="bi bi-folder2-open"></i> {{ $article->category->name }}
        </a>

        <h1 class="ui-read__title">{{ $article->title }}</h1>

        @if ($article->excerpt)
            <p class="ui-read__lead">{{ $article->excerpt }}</p>
        @endif

        <div class="ui-read__meta">
            <span><i class="bi bi-person-circle"></i> {{ $author }}</span>
            @if ($article->published_at)
                <span><i class="bi bi-calendar3"></i>
                    <time datetime="{{ $article->published_at->toDateString() }}">{{ $article->published_at->format('d M Y') }}</time>
                </span>
            @endif
            <span><i class="bi bi-clock"></i> {{ $article->reading_time }} min read</span>
            @if ($updated)
                <span><i class="bi bi-arrow-repeat"></i> Updated {{ $article->updated_at->format('d M Y') }}</span>
            @endif
        </div>
    </header>

    {{-- cover --}}
    <figure class="ui-read__cover">
        @include('public.partials.thumb', ['article' => $article, 'lazy' => false])
    </figure>

    {{-- body --}}
    <div class="ui-read__grid {{ count($toc) >= 2 ? 'has-toc' : '' }}">

        <div class="ui-read__main">

            {{-- key takeaways --}}
            @if (!empty($article->key_takeaways))
                <section class="ui-take" aria-labelledby="takeTitle">
                    <h2 class="ui-take__title" id="takeTitle"><i class="bi bi-lightbulb"></i> Key takeaways</h2>
                    <ul>
                        @foreach ($article->key_takeaways as $point)
                            <li><i class="bi bi-check2-circle"></i> <span>{{ $point }}</span></li>
                        @endforeach
                    </ul>
                </section>
            @endif

            {{-- content (stored HTML, sanitize on save, see notes) --}}
            <div class="ui-prose" id="articleBody">
                {!! $html !!}
            </div>

            {{-- FAQ --}}
            @if (!empty($article->faq))
                <section class="ui-faq" aria-labelledby="faqTitle">
                    <h2 class="ui-block-title" id="faqTitle"><i class="bi bi-question-circle"></i> Frequently asked questions</h2>

                    @foreach ($article->faq as $i => $item)
                        <details class="ui-faq__item" @if ($i === 0) open @endif>
                            <summary>
                                <span>{{ $item['question'] }}</span>
                                <i class="bi bi-chevron-down"></i>
                            </summary>
                            <p>{{ $item['answer'] }}</p>
                        </details>
                    @endforeach
                </section>
            @endif

            {{-- sources --}}
            @if (!empty($article->source_references))
                <section class="ui-src" aria-labelledby="srcTitle">
                    <h2 class="ui-block-title" id="srcTitle"><i class="bi bi-journal-bookmark"></i> Sources</h2>
                    <ol>
                        @foreach ($article->source_references as $src)
                            <li>
                                <a href="{{ $src['url'] }}" target="_blank" rel="noopener noreferrer nofollow">
                                    {{ $src['title'] ?: parse_url($src['url'], PHP_URL_HOST) }}
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                            </li>
                        @endforeach
                    </ol>
                </section>
            @endif

            {{-- footer of the article --}}
            <footer class="ui-read__foot">
                @if ($article->last_reviewed_at)
                    <span class="ui-reviewed">
                        <i class="bi bi-patch-check-fill"></i>
                        Reviewed on {{ $article->last_reviewed_at->format('d M Y') }}
                    </span>
                @endif

                <div class="ui-share" data-url="{{ $canonical }}" data-title="{{ $article->title }}">
                    <span class="ui-share__label">Share</span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Share on Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareTitle }}" target="_blank" rel="noopener noreferrer" aria-label="Share on X"><i class="bi bi-twitter-x"></i></a>
                    <a href="https://wa.me/?text={{ $shareTitle }}%20{{ $shareUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Share on WhatsApp"><i class="bi bi-whatsapp"></i></a>
                    <button type="button" data-copy aria-label="Copy link"><i class="bi bi-link-45deg"></i> <span>Copy link</span></button>
                </div>
            </footer>
        </div>

        {{-- table of contents --}}
        @if (count($toc) >= 2)
            <aside class="ui-read__aside">
                <details class="ui-toc" id="toc">
                    <summary><i class="bi bi-list-nested"></i> On this page <i class="bi bi-chevron-down ui-toc__chev"></i></summary>
                    <nav aria-label="Table of contents">
                        @foreach ($toc as $item)
                            <a href="#{{ $item['id'] }}" class="ui-toc__link ui-toc__link--{{ $item['level'] }}" data-toc="{{ $item['id'] }}">
                                {{ $item['text'] }}
                            </a>
                        @endforeach
                    </nav>
                </details>
            </aside>
        @endif
    </div>
</article>

{{-- related --}}
@if ($related->isNotEmpty())
    <section class="ui-section-pub" aria-labelledby="relTitle">
        <div class="container">
            <div class="ui-sec-head">
                <div>
                    <span class="ui-sec-head__eyebrow">Keep reading</span>
                    <h2 class="ui-sec-head__title" id="relTitle">Related articles</h2>
                </div>
                <a href="{{ $home }}#latest" class="ui-link-reset">All articles <i class="bi bi-arrow-right"></i></a>
            </div>

            <div class="ui-lgrid">
                @foreach ($related as $rel)
                    @include('public.partials.article-card', ['article' => $rel])
                @endforeach
            </div>
        </div>
    </section>
@endif

@endsection

@push('scripts')
<script src="{{asset('storage/js/public.js')}}"></script>
@endpush
