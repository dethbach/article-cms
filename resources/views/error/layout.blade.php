<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">

    <script>
        (function () {
            try {
                var t = localStorage.getItem('theme');
                if (!t) t = matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                document.documentElement.setAttribute('data-bs-theme', t);
            } catch (e) {}
        })();
    </script>

    <title>@yield('heading') | {{ config('site.name') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@6.0.0-alpha.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-B/GM4XqrwHnWXNOWMbloTmrYXZg10cakYGmpfsR/bbzQ6JAJI4ihuyADKLnBgrCe" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.2/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('storage/css/public.css') }}">
</head>
<body class="ui-public">

    <a href="#main" class="ui-skip">Skip to content</a>

    {{-- the navbar only uses config(), so it is safe on error pages --}}
    @include('public.partials.navbar')

    <main id="main" class="ui-public__main">
        <section class="ui-err ui-err--@yield('tone', 'indigo')">
            <div class="ui-hero__bg" aria-hidden="true">
                <span class="ui-hero__blob ui-hero__blob--one"></span>
                <span class="ui-hero__blob ui-hero__blob--two"></span>
            </div>

            <div class="container ui-err__inner">
                <span class="ui-err__icon" aria-hidden="true"><i class="bi @yield('icon', 'bi-exclamation-triangle')"></i></span>

                <p class="ui-err__code" aria-hidden="true">@yield('code')</p>

                <h1 class="ui-err__title">@yield('heading')</h1>
                <p class="ui-err__msg">@yield('message')</p>

                <div class="ui-err__actions">
                    @hasSection('actions')
                        @yield('actions')
                    @else
                        <button type="button" class="ui-err__btn ui-err__btn--ghost" data-back>
                            <i class="bi bi-arrow-left"></i> Go back
                        </button>
                    @endif
                    <a href="{{ config('site.home_url') }}" class="ui-err__btn ui-err__btn--primary">
                        <i class="bi bi-house-door"></i> Back to home
                    </a>
                </div>

                @yield('extra')
            </div>
        </section>
    </main>

    {{-- slim footer: no database queries --}}
    <footer class="ui-footer ui-footer--slim">
        <div class="container">
            <div class="ui-footer__bottom">
                <span>&copy; {{ date('Y') }} {{ config('site.name') }}. All rights reserved.</span>
                <a href="{{ config('site.home_url') }}" class="ui-footer__top"><i class="bi bi-house-door"></i> Home</a>
            </div>
        </div>
    </footer>

    <script type="module" src="https://cdn.jsdelivr.net/npm/bootstrap@6.0.0-alpha.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-1a/pXj49ZQ1aHEmrJ+gMw1otqoVsYwlEnlD8mIfY2TV03r20Y0CN7uqx1tQogjPL" crossorigin="anonymous"></script>
    <script src="{{ asset('storage/js/public.js') }}"></script>
    <script>
        // "Go back": use history when there is some, otherwise go home
        document.querySelectorAll('[data-back]').forEach(function (b) {
            b.addEventListener('click', function () {
                if (history.length > 1 && document.referrer) history.back();
                else location.href = @json(config('site.home_url'));
            });
        });
    </script>
</body>
</html>
