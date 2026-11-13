@extends('errors.layout')

@section('tone', 'indigo')
@section('icon', 'bi-compass')
@section('code', '404')
@section('heading', 'Page not found')
@section('message', 'The page you are looking for may have been moved, renamed or never existed. Try searching instead.')

@section('extra')
    <form method="GET" action="{{ config('site.home_url') }}#latest" class="ui-hero__search ui-err__search" role="search">
        <i class="bi bi-search ui-hero__search-icon"></i>
        <input type="search" name="q" placeholder="{{ config('site.hero.placeholder', 'Search articles...') }}"
               autocomplete="off" aria-label="Search articles">
        <button type="submit"><span>Search</span> <i class="bi bi-arrow-right"></i></button>
    </form>

    <div class="ui-hero__chips">
        <a href="{{ config('site.home_url') }}#categories" class="ui-hero__chip"><i class="bi bi-grid"></i> Browse topics</a>
        <a href="{{ config('site.home_url') }}#latest" class="ui-hero__chip"><i class="bi bi-newspaper"></i> Latest articles</a>
    </div>
@endsection
