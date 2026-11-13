@php
    $home = config('site.home_url');
@endphp

<header class="ui-nav" id="uiNav">
    <div class="container ui-nav__inner">

        <a href="{{ $home }}" class="ui-brand">
            <span class="ui-brand__icon"><i class="bi {{ config('site.icon') }}"></i></span>
            <span class="ui-brand__name">{{ config('site.name') }}</span>
        </a>

        <nav class="ui-nav__menu" id="uiNavMenu" aria-label="Main">
            <a href="{{ $home }}" class="ui-nav__link {{ request()->is(ltrim($home, '/')) ? 'is-active' : '' }}">
                <i class="bi bi-house-door"></i> Home
            </a>
            <a href="{{ $home }}#categories" class="ui-nav__link">
                <i class="bi bi-grid"></i> Categories
            </a>
            <a href="{{ $home }}#latest" class="ui-nav__link">
                <i class="bi bi-newspaper"></i> Latest
            </a>

            {{-- shown inside the mobile menu only --}}
            <a href="{{ config('site.login_url') }}" class="ui-nav__login ui-nav__login--menu">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </a>
        </nav>

        <div class="ui-nav__actions">
            <a href="{{ $home }}#search" class="ui-nav__icon" title="Search" aria-label="Search">
                <i class="bi bi-search"></i>
            </a>

            <button type="button" class="ui-nav__icon" id="themeToggle" title="Switch theme" aria-label="Switch theme">
                <i class="bi bi-moon-stars ui-theme-dark-icon"></i>
                <i class="bi bi-sun ui-theme-light-icon"></i>
            </button>

            <a href="{{ auth()->check()
                    ? url('/' . auth()->user()->role . '/dashboard')
                    : config('site.login_url') }}"
                class="ui-nav__login ui-nav__login--bar">
                <i class="bi bi-box-arrow-in-right"></i>
                {{ auth()->check() ? 'Dashboard' : 'Login' }}
            </a>

            <button type="button" class="ui-nav__icon ui-nav__burger" id="navToggle"
                    aria-label="Open menu" aria-expanded="false" aria-controls="uiNavMenu">
                <i class="bi bi-list"></i>
            </button>
        </div>
    </div>
</header>
