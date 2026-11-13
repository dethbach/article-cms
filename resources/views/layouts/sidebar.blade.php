<!doctype html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ config('app.name') }} - {{$sideTitle}}</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@6.0.0-alpha.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-B/GM4XqrwHnWXNOWMbloTmrYXZg10cakYGmpfsR/bbzQ6JAJI4ihuyADKLnBgrCe" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.2/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="{{asset('storage/css/sidebar.css')}}">
  <script>
    /* Set theme + sidebar state before first paint (no flash) */
    (function () {
      var t; try { t = localStorage.getItem('sb-theme'); } catch (e) {}
      if (!t) t = matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
      document.documentElement.setAttribute('data-bs-theme', t);
    })();
  </script>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @yield('css')
</head>
<body>

  <aside class="app-sidebar" id="sidebar" aria-label="Main navigation">
    {{-- Option 1: image icon + brand name, icon only when collapsed --}}
    {{-- <a class="sb-brand ui-sb-brand" href="/{{auth()->user()->role}}/dashboard">
        <img class="ui-sb-brand__logo" src="{{ asset('storage/web-material/logo.png') }}" alt="">
        <span class="ui-sb-brand__name sb-label">{{ config('app.name') }}</span>
    </a> --}}

    {{-- Option 2: image only, a different image when collapsed --}}
    {{-- <a class="sb-brand ui-sb-brand" href="/{{auth()->user()->role}}/dashboard" aria-label="{{ config('app.name') }}">
        <img class="ui-sb-brand__full" src="{{ asset('storage/web-material/logo_wide.png') }}" alt="{{ config('app.name') }}">
        <img class="ui-sb-brand__logo ui-sb-brand__mini" src="{{ asset('storage/web-material/logo.png') }}" alt="">
    </a> --}}

    {{-- Option 3: brand name only, a letter you choose when collapsed --}}
    <a class="sb-brand ui-sb-brand ui-sb-brand--pro" href="/{{ auth()->user()->role }}/dashboard"
    aria-label="{{ config('app.name') }}">
        <span class="ui-sb-brand__letter" aria-hidden="true">{{ mb_strtoupper(mb_substr(config('app.name'), 0, 1)) }}</span>

        <span class="ui-sb-brand__text sb-label">
            <span class="ui-sb-brand__name">{{ config('app.name') }}</span>
            <small class="ui-sb-brand__tag">{{ ucfirst(auth()->user()->role) }} panel</small>
        </span>
    </a>

    <nav class="sb-nav">
        {{-- DROP DOWN IF NEEDED --}}
        {{-- php
        $role = auth()->user()->role;
        $inArticle = in_array($sideTitle, ['Category', 'Thumbnail', 'Article']);
        endphp

        <div class="ui-side-group">
            <span class="ui-side-title">Content</span>

            <a class="sb-link ui-side-link ui-side-parent sb-parent @if($inArticle) active @endif"
            href="#menuArticles" role="button" title="Articles"
            aria-expanded="{{ $inArticle ? 'true' : 'false' }}" aria-controls="menuArticles">
                <span class="ui-side-link__icon"><i class="bi bi-journal-bookmark"></i></span>
                <span class="sb-label">Articles</span>
                <i class="bi bi-chevron-down sb-chevron sb-label ms-auto"></i>
            </a>

            <div class="sb-sub @if($inArticle) show @endif" id="menuArticles">
                <a class="sb-link sb-sublink ui-side-sub @if($sideTitle == 'Article') active @endif"
                href="/{{ $role }}/article/post" data-page="Article">
                    <span class="sb-label"><i class="bi bi-file-earmark-richtext"></i> Article</span>
                </a>
                <a class="sb-link sb-sublink ui-side-sub @if($sideTitle == 'Category') active @endif"
                href="/{{ $role }}/article/category" data-page="Category">
                    <span class="sb-label"><i class="bi bi-bookmarks"></i> Category</span>
                </a>
                <a class="sb-link sb-sublink ui-side-sub @if($sideTitle == 'Thumbnail') active @endif"
                href="/{{ $role }}/article/thumbnail" data-page="Thumbnail">
                    <span class="sb-label"><i class="bi bi-file-image"></i> Thumbnail</span>
                </a>
            </div>
        </div> --}}

        <div class="ui-side-group">
            <span class="ui-side-title">Overview</span>
            <a class="sb-link ui-side-link @if($sideTitle == 'Dashboard') active @endif"
            href="/{{auth()->user()->role}}/dashboard" data-page="Dashboard" title="Dashboard">
                <span class="ui-side-link__icon"><i class="bi bi-house"></i></span>
                <span class="sb-label">Dashboard</span>
            </a>
        </div>

        <div class="ui-side-group">
            <span class="ui-side-title">Content</span>

            <a class="sb-link ui-side-link @if($sideTitle == 'Article') active @endif"
            href="/{{auth()->user()->role}}/article/post" data-page="Article" title="Article">
                <span class="ui-side-link__icon"><i class="bi bi-file-earmark-richtext"></i></span>
                <span class="sb-label">Article</span>
            </a>

            <a class="sb-link ui-side-link @if($sideTitle == 'Category') active @endif"
            href="/{{auth()->user()->role}}/article/category" data-page="Category" title="Category">
                <span class="ui-side-link__icon"><i class="bi bi-bookmarks"></i></span>
                <span class="sb-label">Category</span>
            </a>

            <a class="sb-link ui-side-link @if($sideTitle == 'Thumbnail') active @endif"
            href="/{{auth()->user()->role}}/article/thumbnail" data-page="Thumbnail" title="Thumbnail">
                <span class="ui-side-link__icon"><i class="bi bi-camera"></i></span>
                <span class="sb-label">Thumbnail</span>
            </a>
        </div>
        <div class="ui-side-group">
            <span class="ui-side-title">System</span>

            <a class="sb-link ui-side-link @if($sideTitle == 'Settings') active @endif"
            href="/{{auth()->user()->role}}/settings" data-page="Settings" title="Settings">
                <span class="ui-side-link__icon"><i class="bi bi-people"></i></span>
                <span class="sb-label">Users &amp; access</span>
            </a>
        </div>
    </nav>
    <div class="sb-footer">
        <div class="sb-user-menu" id="userMenu">

            <a class="sb-link ui-side-link @if($sideTitle == 'Profile') active @endif"
            href="/{{auth()->user()->role}}/profile" data-page="Profile" title="Profile">
                <span class="ui-side-link__icon"><i class="bi bi-person"></i></span>
                <span class="sb-label">Profile</span>
            </a>
            <a class="sb-link ui-side-link ui-side-danger sb-danger" href="{{ route('logout') }}"
            title="{{ __('Sign out') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <span class="ui-side-link__icon"><i class="bi bi-box-arrow-right"></i></span>
                <span class="sb-label">{{ __('Sign out') }}</span>
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>

        @php
            $user = auth()->user();

            // First letter of the first two words: "Jane Doe" = JD, "Jon Bon Jovi" = JB, "Aerosmith" = A
            $initials = collect(preg_split('/\s+/', trim($user->name), -1, PREG_SPLIT_NO_EMPTY))
                ->take(2)
                ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
                ->implode('');
        @endphp

        <button class="sb-link sb-user" type="button" aria-expanded="false" aria-controls="userMenu"
                data-tip="{{ $user->name }}{{ $user->username ? ' (@'.$user->username.')' : '' }}">
            @if ($user->photo)
                <img class="sb-avatar" src="{{ asset('storage/profile-pic/'.$user->photo) }}" alt="">
            @else
                <span class="sb-avatar">{{ $initials }}</span>
            @endif

            <span class="sb-label sb-user-text">
                <strong>{{ \Illuminate\Support\Str::limit($user->name, 18, '…') }}</strong>
                @if ($user->username)
                    <small>&#64;{{ \Illuminate\Support\Str::limit($user->username, 16, '…') }}</small>
                @endif
            </span>
            <i class="bi bi-chevron-expand sb-chevron sb-label ms-auto"></i>
        </button>
    </div>
  </aside>
  <div class="sb-backdrop"></div>

  <div class="app-main">
    <header class="app-topbar">
      <button class="btn btn-outline-secondary" id="sbToggle" type="button" aria-controls="sidebar" aria-label="Toggle sidebar">
        <i class="bi bi-list"></i>
      </button>


      <h1 class="h5 mb-0 ms-2" id="pageTitle">{{$sideTitle}}</h1>

      <button class="btn btn-outline-secondary ms-auto sb-theme-toggle" id="themeToggle" type="button" aria-label="Toggle light/dark mode">
        <i class="bi bi-moon-stars-fill"></i><i class="bi bi-sun-fill"></i>
      </button>
    </header>

    <!-- Page content goes here -->
    <main class="app-content">
      @include('components.alert')
      @yield('contents')
    </main>
  </div>

  <script type="module" src="https://cdn.jsdelivr.net/npm/bootstrap@6.0.0-alpha.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-1a/pXj49ZQ1aHEmrJ+gMw1otqoVsYwlEnlD8mIfY2TV03r20Y0CN7uqx1tQogjPL" crossorigin="anonymous"></script>
  <script src="{{asset('storage/js/sidebar.js')}}"></script>
  @yield('javascript')
</body>
</html>
