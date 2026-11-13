<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- set the theme before the page paints, so there is no light flash in dark mode --}}
    <script>
        (function () {
            try {
                var t = localStorage.getItem('theme');
                if (!t) t = matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                document.documentElement.setAttribute('data-bs-theme', t);
            } catch (e) {}
        })();
    </script>

    {{-- SEO: every page can override these with @section --}}
    <title>@yield('title', config('site.name') . ' | ' . config('site.tagline'))</title>
    <meta name="description" content="@yield('meta_description', config('site.description'))">
    <meta name="robots" content="@yield('robots', 'index,follow')">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    <meta property="og:site_name" content="{{ config('site.name') }}">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('og_title', config('site.name'))">
    <meta property="og:description" content="@yield('og_description', config('site.description'))">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    @hasSection('og_image')
        <meta property="og:image" content="@yield('og_image')">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@6.0.0-alpha.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-B/GM4XqrwHnWXNOWMbloTmrYXZg10cakYGmpfsR/bbzQ6JAJI4ihuyADKLnBgrCe" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.2/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('storage/css/public.css') }}">
    <link rel="sitemap" type="application/xml" href="{{ url('sitemap.xml') }}">
<link rel="alternate" type="application/rss+xml" title="{{ config('site.name') }} RSS" href="{{ url('feed.xml') }}">
    @stack('head')
    @stack('css')
</head>
<body class="ui-public">

    <a href="#main" class="ui-skip">Skip to content</a>

    @include('public.partials.navbar')

    <main id="main" class="ui-public__main">
        @yield('content')
    </main>

    @include('public.partials.footer')

    <script type="module" src="https://cdn.jsdelivr.net/npm/bootstrap@6.0.0-alpha.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-1a/pXj49ZQ1aHEmrJ+gMw1otqoVsYwlEnlD8mIfY2TV03r20Y0CN7uqx1tQogjPL" crossorigin="anonymous"></script>
    <script src="{{ asset('storage/js/public.js') }}"></script>
    @stack('scripts')
</body>
</html>
