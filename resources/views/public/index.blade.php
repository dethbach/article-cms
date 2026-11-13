@extends('layouts.public')

@section('content')

{{-- ==========================================
    HERO
========================================== --}}
<section class="ui-hero" id="search">
    <div class="ui-hero__bg" aria-hidden="true">
        <span class="ui-hero__blob ui-hero__blob--one"></span>
        <span class="ui-hero__blob ui-hero__blob--two"></span>
    </div>

    <div class="container ui-hero__inner">
        <span class="ui-hero__eyebrow">
            <i class="bi bi-stars"></i> {{ config('site.hero.eyebrow') }}
        </span>

        <h1 class="ui-hero__title">
            {{ config('site.hero.title') }}
            <span class="ui-hero__highlight">{{ config('site.hero.highlight') }}</span>
        </h1>

        <p class="ui-hero__sub">{{ config('site.hero.subtitle') }}</p>

        {{-- search --}}
        <form method="GET" action="{{ config('site.home_url') }}#latest" class="ui-hero__search" role="search">
            <i class="bi bi-search ui-hero__search-icon"></i>
            <input type="search" name="q" id="heroSearch" value="{{ request('q') }}"
                   placeholder="{{ config('site.hero.placeholder') }}"
                   autocomplete="off" aria-label="Search articles">
            <button type="submit"><span>Search</span> <i class="bi bi-arrow-right"></i></button>
        </form>

        {{-- category chips --}}
        @if ($categories->isNotEmpty())
            <div class="ui-hero__chips">
                <span class="ui-hero__chips-label">Popular topics</span>

                @foreach ($categories->take(6) as $category)
                    <a href="{{ config('site.home_url') }}/category/{{ $category->slug }}" class="ui-hero__chip">
                        {{ $category->name }}
                        <small>{{ $category->articles_count }}</small>
                    </a>
                @endforeach

                @if ($categories->count() > 6)
                    <a href="#categories" class="ui-hero__chip ui-hero__chip--more">
                        All topics <i class="bi bi-arrow-down"></i>
                    </a>
                @endif
            </div>
        @endif

        {{-- quick numbers --}}
        <div class="ui-hero__stats">
            <span><strong>{{ $totalArticles }}</strong> {{ Str::plural('article', $totalArticles) }}</span>
            <span class="ui-hero__stats-dot"></span>
            <span><strong>{{ $categories->count() }}</strong> {{ Str::plural('topic', $categories->count()) }}</span>
        </div>
    </div>
</section>

{{-- ==========================================
    FEATURED
========================================== --}}
@if ($featured->isNotEmpty())
    @php
        $main = $featured->first();
        $side = $featured->slice(1);
        $home = config('site.home_url');
    @endphp

    <section class="ui-section-pub" aria-labelledby="featuredTitle">
        <div class="container">
            <div class="ui-sec-head">
                <div>
                    <span class="ui-sec-head__eyebrow">Don't miss</span>
                    <h2 class="ui-sec-head__title" id="featuredTitle">Featured articles</h2>
                </div>
            </div>

            <div class="ui-feat {{ $side->isEmpty() ? 'ui-feat--solo' : '' }}">

                {{-- main --}}
                <article class="ui-fcard ui-fcard--main">
                    @include('public.partials.thumb', ['article' => $main, 'lazy' => false])

                    <div class="ui-fcard__body">
                        <div class="ui-fcard__tags">
                            <span class="ui-ptag"><i class="bi bi-stars"></i> Latest</span>
                            <a href="{{ $home }}/category/{{ $main->category->slug }}" class="ui-ptag ui-ptag--soft">
                                {{ $main->category->name }}
                            </a>
                        </div>

                        <h3 class="ui-fcard__title">
                            <a href="{{ $home }}/{{ $main->slug }}">{{ $main->title }}</a>
                        </h3>

                        @if ($main->excerpt)
                            <p class="ui-fcard__excerpt">{{ $main->excerpt }}</p>
                        @endif

                        <div class="ui-fcard__meta">
                            <span><i class="bi bi-person"></i> {{ $main->user->name ?? 'Team' }}</span>
                            <span class="ui-fcard__dot"></span>
                            <span><i class="bi bi-calendar3"></i> {{ $main->published_at->format('d M Y') }}</span>
                            <span class="ui-fcard__dot"></span>
                            <span><i class="bi bi-clock"></i> {{ $main->reading_time }} min read</span>
                        </div>
                    </div>
                </article>

                {{-- side --}}
                @if ($side->isNotEmpty())
                    <div class="ui-feat__side">
                        @foreach ($side as $article)
                            <article class="ui-fcard ui-fcard--side">
                                @include('public.partials.thumb', ['article' => $article])

                                <div class="ui-fcard__body">
                                    @if ($article->category)
                                        <a href="{{ $home }}/category/{{ $article->category->slug }}" class="ui-ptag ui-ptag--soft">
                                            {{ $article->category->name }}
                                        </a>
                                    @endif

                                    <h3 class="ui-fcard__title">
                                        <a href="{{ $home }}/{{ $article->slug }}">{{ $article->title }}</a>
                                    </h3>

                                    <div class="ui-fcard__meta">
                                        <span><i class="bi bi-calendar3"></i> {{ $article->published_at->format('d M Y') }}</span>
                                        <span class="ui-fcard__dot"></span>
                                        <span>{{ $article->reading_time }} min read</span>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>
@endif

{{-- ==========================================
    BROWSE BY CATEGORY
========================================== --}}
@if ($categories->isNotEmpty())
    @php
        $colors = ['indigo', 'emerald', 'amber', 'rose', 'sky'];
        $limit  = 8;
        $home   = config('site.home_url');
    @endphp

    <section class="ui-section-pub" id="categories" aria-labelledby="catTitle">
        <div class="container">
            <div class="ui-sec-head">
                <div>
                    <span class="ui-sec-head__eyebrow">Explore</span>
                    <h2 class="ui-sec-head__title" id="catTitle">Browse by category</h2>
                </div>
                <span class="ui-sec-head__note">
                    {{ $categories->count() }} {{ Str::plural('topic', $categories->count()) }}
                </span>
            </div>

            <div class="ui-pcat-grid" id="catGrid">
                @foreach ($categories as $category)
                    <a href="{{ $home }}/category/{{ $category->slug }}"
                       class="ui-pcat ui-pcat--{{ $colors[$loop->index % count($colors)] }} {{ $loop->index >= $limit ? 'is-extra' : '' }}">

                        <span class="ui-pcat__icon"><i class="bi bi-folder2-open"></i></span>

                        <h3 class="ui-pcat__name">{{ $category->name }}</h3>

                        @if ($category->description)
                            <p class="ui-pcat__desc">{{ $category->description }}</p>
                        @endif

                        <span class="ui-pcat__foot">
                            <span class="ui-pcat__count">
                                {{ $category->articles_count }} {{ Str::plural('article', $category->articles_count) }}
                            </span>
                            <span class="ui-pcat__go"><i class="bi bi-arrow-right"></i></span>
                        </span>
                    </a>
                @endforeach
            </div>

            @if ($categories->count() > $limit)
                <div class="ui-pcat-more">
                    <button type="button" class="ui-pcat-more__btn" id="catToggle" aria-expanded="false" aria-controls="catGrid">
                        <span>Show all {{ $categories->count() }} topics</span>
                        <i class="bi bi-chevron-down"></i>
                    </button>
                </div>
            @endif
        </div>
    </section>
@endif

{{-- ==========================================
    LATEST ARTICLES
========================================== --}}
@php $home = config('site.home_url'); @endphp

<section class="ui-section-pub" id="latest" aria-labelledby="latestTitle">
    <div class="container">

        <div class="ui-sec-head">
            <div>
                <span class="ui-sec-head__eyebrow">{{ $filtering ? 'Search results' : 'Fresh reads' }}</span>
                <h2 class="ui-sec-head__title" id="latestTitle">
                    @if ($q !== '')
                        Results for "{{ $q }}"
                    @elseif ($activeCategory)
                        {{ $activeCategory->name }}
                    @else
                        Latest articles
                    @endif
                </h2>
            </div>

            <span class="ui-sec-head__note">
                {{ $articles->total() }} {{ Str::plural('article', $articles->total()) }}
                @if ($filtering)
                    · <a href="{{ $home }}#latest" class="ui-link-reset"><i class="bi bi-x-circle"></i> Clear filters</a>
                @endif
            </span>
        </div>

        {{-- category pills --}}
        @if ($categories->isNotEmpty())
            <nav class="ui-pills" aria-label="Filter by category">
                <a href="{{ request()->fullUrlWithQuery(['category' => null, 'page' => null]) }}#latest"
                   class="ui-pill {{ !$activeCategory ? 'is-active' : '' }}">All</a>

                @foreach ($categories as $cat)
                    <a href="{{ request()->fullUrlWithQuery(['category' => $cat->slug, 'page' => null]) }}#latest"
                       class="ui-pill {{ $activeCategory?->id === $cat->id ? 'is-active' : '' }}">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </nav>
        @endif

        {{-- grid --}}
        @if ($articles->isNotEmpty())
            <div class="ui-lgrid">
                @foreach ($articles as $article)
                    <article class="ui-lcard">
                        @include('public.partials.thumb', ['article' => $article])

                        <div class="ui-lcard__body">
                            @if ($article->category)
                                <a href="{{ $home }}/category/{{ $article->category->slug }}" class="ui-ptag ui-ptag--soft">
                                    {{ $article->category->name }}
                                </a>
                            @endif

                            <h3 class="ui-lcard__title">
                                <a href="{{ $home }}/{{ $article->slug }}">{{ $article->title }}</a>
                            </h3>

                            @if ($article->excerpt)
                                <p class="ui-lcard__excerpt">{{ $article->excerpt }}</p>
                            @endif

                            <div class="ui-fcard__meta">
                                <span><i class="bi bi-person"></i> {{ $article->user->name ?? 'Team' }}</span>
                                <span class="ui-fcard__dot"></span>
                                <span>{{ $article->published_at->format('d M Y') }}</span>
                                <span class="ui-fcard__dot"></span>
                                <span>{{ $article->reading_time }} min</span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            @if ($articles->hasPages())
                <div class="ui-ppager">
                    <span class="ui-ppager__info">
                        Showing {{ $articles->firstItem() }}-{{ $articles->lastItem() }} of {{ $articles->total() }}
                    </span>
                    {{ $articles->onEachSide(1)->links() }}
                </div>
            @endif
        @else
            <div class="ui-pempty">
                <i class="bi {{ $filtering ? 'bi-search' : 'bi-journal-text' }}"></i>
                <h3>{{ $filtering ? 'No matching articles' : 'No articles yet' }}</h3>
                <p>{{ $filtering ? 'Try another keyword or pick a different category.' : 'New articles are on the way. Please check back soon.' }}</p>
                @if ($filtering)
                    <a href="{{ $home }}#latest" class="ui-pcat-more__btn">Clear filters</a>
                @endif
            </div>
        @endif
    </div>
</section>

@endsection

@push('scripts')
<script>
    // arriving from the navbar search icon (#search): focus the box
    document.addEventListener('DOMContentLoaded', () => {
        const box = document.getElementById('heroSearch');
        const focusIfHash = () => { if (location.hash === '#search') box?.focus({ preventScroll: true }); };
        focusIfHash();
        window.addEventListener('hashchange', focusIfHash);
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const btn  = document.getElementById('catToggle');
    const grid = document.getElementById('catGrid');
    if (!btn || !grid) return;

    const total = grid.querySelectorAll('.ui-pcat').length;

    btn.addEventListener('click', () => {
        const open = grid.classList.toggle('is-expanded');
        btn.setAttribute('aria-expanded', open);
        btn.classList.toggle('is-open', open);
        btn.firstElementChild.textContent = open ? 'Show fewer' : `Show all ${total} topics`;
    });
});
</script>
@endpush
