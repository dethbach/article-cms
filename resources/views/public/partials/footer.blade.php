@php
    $home = config('site.home_url');
    $socials = [
        'facebook'  => 'bi-facebook',
        'instagram' => 'bi-instagram',
        'x'         => 'bi-twitter-x',
        'youtube'   => 'bi-youtube',
    ];
@endphp

<footer class="ui-footer">
    <div class="container">
        <div class="ui-footer__grid">

            <div class="ui-footer__brand">
                <a href="{{ $home }}" class="ui-brand">
                    <span class="ui-brand__icon"><i class="bi {{ config('site.icon') }}"></i></span>
                    <span class="ui-brand__name">{{ config('site.name') }}</span>
                </a>
                <p>{{ config('site.description') }}</p>

                <div class="ui-footer__social">
                    @foreach ($socials as $key => $icon)
                        @if (config("site.social.$key"))
                            <a href="{{ config("site.social.$key") }}" target="_blank" rel="noopener noreferrer"
                               aria-label="{{ ucfirst($key) }}"><i class="bi {{ $icon }}"></i></a>
                        @endif
                    @endforeach
                </div>
            </div>

            <div>
                <h6 class="ui-footer__title">Categories</h6>
                <ul class="ui-footer__list">
                    @forelse ($footerCategories as $cat)
                        <li><a href="{{ $home }}/category/{{ $cat->slug }}">{{ $cat->name }}</a></li>
                    @empty
                        <li class="ui-footer__muted">Coming soon</li>
                    @endforelse
                </ul>
            </div>

            <div>
                <h6 class="ui-footer__title">Quick links</h6>
                <ul class="ui-footer__list">
                    <li><a href="{{ $home }}">Home</a></li>
                    <li><a href="{{ $home }}#latest">Latest articles</a></li>
                    <li><a href="{{ $home }}#categories">Browse categories</a></li>
                    <li><a href="{{ config('site.login_url') }}">Login</a></li>
                </ul>
            </div>
        </div>

        <div class="ui-footer__bottom">
            <span>&copy; {{ date('Y') }} {{ config('site.name') }}. All rights reserved.</span>
            <a href="#main" class="ui-footer__top"><i class="bi bi-arrow-up"></i> Back to top</a>
        </div>
    </div>
</footer>
