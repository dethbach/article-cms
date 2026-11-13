@extends('layouts.public')

@php
    $home = config('site.home_url');
    $page = $articles->currentPage();

    $metaDesc = $category->description
        ? \Illuminate\Support\Str::limit(strip_tags($category->description), 155)
        : "Browse {$totalInCategory} " . Str::plural('article', $totalInCategory) . " about {$category->name} on " . config('site.name') . '.';
@endphp

@section('title', $category->name . ($page > 1 ? " (Page {$page})" : '') . ' | ' . config('site.name'))
@section('meta_description', $metaDesc)
@section('canonical', $page > 1 ? url()->current() . '?page=' . $page : url()->current())
@section('og_title', $category->name . ' | ' . config('site.name'))
@section('og_description', $metaDesc)
{{-- search results and empty categories should not be indexed --}}
@section('robots', ($q !== '' || $totalInCategory === 0) ? 'noindex,follow' : 'index,follow')

@section('content')

{{-- ==========================================
    CATEGORY HEADER
========================================== --}}
<section class="ui-chead">
    <div class="container">

        <nav class="ui-pcrumbs" aria-label="Breadcrumb">
            <ol>
                <li><a href="{{ $home }}"><i class="bi bi-house-door"></i> Home</a></li>
                <li><a href="{{ $home }}#categories">Categories</a></li>
                <li aria-current="page">{{ $category->name }}</li>
            </ol>
        </nav>

        <div class="ui-chead__card">
            <span class="ui-chead__icon"><i class="bi bi-folder2-open"></i></span>

            <div class="ui-chead__text">
                <span class="ui-sec-head__eyebrow">Category</span>
                <h1 class="ui-chead__title">{{ $category->name }}</h1>
                @if ($category->description)
                    <p class="ui-chead__desc">{{ $category->description }}</p>
                @endif
                <span class="ui-chead__count">
                    <i class="bi bi-journal-text"></i>
                    {{ $totalInCategory }} {{ Str::plural('article', $totalInCategory) }}
                </span>
            </div>

            {{-- search inside this category --}}
            @if ($totalInCategory > 0)
                <form method="GET" action="{{ url()->current() }}" class="ui-csearch" role="search">
                    <i class="bi bi-search"></i>
                    <input type="search" name="q" value="{{ $q }}" autocomplete="off"
                           placeholder="Search in {{ $category->name }}..." aria-label="Search in this category">
                    @if ($q !== '')
                        <a href="{{ url()->current() }}" class="ui-csearch__clear" title="Clear search" aria-label="Clear search">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </form>
            @endif
        </div>
    </div>
</section>

{{-- ==========================================
    ARTICLES
========================================== --}}
<section class="ui-section-pub" id="articles" aria-label="Articles in {{ $category->name }}">
    <div class="container">

        @if ($q !== '')
            <div class="ui-sec-head">
                <h2 class="ui-sec-head__title">Results for "{{ $q }}"</h2>
                <span class="ui-sec-head__note">
                    {{ $articles->total() }} {{ Str::plural('article', $articles->total()) }}
                    · <a href="{{ url()->current() }}" class="ui-link-reset"><i class="bi bi-x-circle"></i> Clear search</a>
                </span>
            </div>
        @endif

        @if ($articles->isNotEmpty())
            <div class="ui-lgrid">
                @foreach ($articles as $article)
                    @include('public.partials.article-card', ['article' => $article, 'showCategory' => false])
                @endforeach
            </div>

            @if ($articles->hasPages())
                <div class="ui-ppager">
                    <span class="ui-ppager__info">
                        Showing {{ $articles->firstItem() }}-{{ $articles->lastItem() }} of {{ $articles->total() }}
                    </span>
                    {{ $articles->onEachSide(1)->fragment('articles')->links() }}
                </div>
            @endif
        @else
            <div class="ui-pempty">
                <i class="bi {{ $q !== '' ? 'bi-search' : 'bi-journal-text' }}"></i>
                <h3>{{ $q !== '' ? 'No matching articles' : 'No articles in this category yet' }}</h3>
                <p>{{ $q !== '' ? 'Try another keyword.' : 'New articles are on the way. Please check back soon.' }}</p>
                <a href="{{ $q !== '' ? url()->current() : $home }}" class="ui-pcat-more__btn">
                    {{ $q !== '' ? 'Clear search' : 'Back to all articles' }}
                </a>
            </div>
        @endif
    </div>
</section>

{{-- ==========================================
    OTHER TOPICS
========================================== --}}
@if ($otherCategories->isNotEmpty())
    <section class="ui-section-pub" aria-labelledby="otherTitle">
        <div class="container">
            <div class="ui-sec-head">
                <div>
                    <span class="ui-sec-head__eyebrow">Keep exploring</span>
                    <h2 class="ui-sec-head__title" id="otherTitle">Other topics</h2>
                </div>
            </div>

            <nav class="ui-pills ui-pills--wrap" aria-label="Other categories">
                @foreach ($otherCategories as $cat)
                    <a href="{{ $home }}/category/{{ $cat->slug }}" class="ui-pill">
                        {{ $cat->name }} <small>{{ $cat->articles_count }}</small>
                    </a>
                @endforeach
            </nav>
        </div>
    </section>
@endif

@endsection
