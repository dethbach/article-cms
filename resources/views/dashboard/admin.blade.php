@extends('layouts.sidebar')
@php
    $sideTitle = 'Dashboard';
    $me   = auth()->user();
    $hour = now()->hour;
    $hello = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
    $statusLabel = ['published' => 'Published', 'scheduled' => 'Scheduled', 'draft' => 'Draft'];
    $needs = $attention['staleDrafts'] + $attention['unreviewed'] + $users['suspended'];
@endphp

@section('css')
 <link rel="stylesheet" href="{{ asset('storage/css/contents.css') }}">
 <link rel="stylesheet" href="{{asset('storage/css/admin-article.css')}}">
 <link rel="stylesheet" href="{{asset('storage/css/thumbnail.css')}}">
@endsection

@section('contents')

{{-- HERO --}}
<div class="category-page">
    <div class="content-hero mb-4">
        <div class="content-hero-content">
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="hero-eyebrow"><i class="bi bi-house"></i> DASHBOARD</span>
                <span class="hero-dot"></span>
                <span class="hero-status">{{ now()->format('l, d M Y') }}</span>
            </div>
            <h2 class="content-hero-title">{{ $hello }}, {{ \Illuminate\Support\Str::before($me->name, ' ') }}</h2>
            <p class="content-hero-description">
                Here is how your content and team are doing today.
            </p>

            <div class="d-flex flex-wrap gap-2 mt-4">
                <a class="btn btn-hero-primary" href="/{{ $me->role }}/article/write-post">
                    <i class="bi bi-feather me-2"></i> Write article
                </a>
                <a class="btn btn-hero-primary" href="/{{ $me->role }}/article/post">
                    <i class="bi bi-file-richtext me-2"></i> All articles
                </a>
                <a class="btn btn-hero-primary" href="/admin/settings">
                    <i class="bi bi-people me-2"></i> Manage users
                </a>
            </div>
        </div>

        <div class="content-hero-decoration">
            <div class="hero-circle hero-circle-one"></div>
            <div class="hero-circle hero-circle-two"></div>
            <div class="hero-folder"><i class="bi bi-house"></i></div>
            <span class="hero-floating-icon hero-icon-one"><i class="bi bi-file-earmark-text"></i></span>
            <span class="hero-floating-icon hero-icon-two"><i class="bi bi-people"></i></span>
        </div>
    </div>
</div>

{{-- STATS --}}
<div class="container-fluid">
    <div class="row g-4">
        <div class="col-6 col-xl-3">
            <div class="ui-stat ui-stat--emerald">
                <div class="ui-stat__icon"><i class="bi bi-rocket-takeoff"></i></div>
                <div class="ui-stat__content">
                    <span class="ui-stat__label">Published</span>
                    <h3 class="ui-stat__value">{{ $articles['published'] }}</h3>
                    <span class="ui-stat__hint">Live articles</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="ui-stat ui-stat--amber">
                <div class="ui-stat__icon"><i class="bi bi-pencil-square"></i></div>
                <div class="ui-stat__content">
                    <span class="ui-stat__label">Drafts</span>
                    <h3 class="ui-stat__value">{{ $articles['draft'] }}</h3>
                    <span class="ui-stat__hint">Work in progress</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="ui-stat ui-stat--sky">
                <div class="ui-stat__icon"><i class="bi bi-calendar-event"></i></div>
                <div class="ui-stat__content">
                    <span class="ui-stat__label">Scheduled</span>
                    <h3 class="ui-stat__value">{{ $articles['scheduled'] }}</h3>
                    <span class="ui-stat__hint">Going live soon</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="ui-stat ui-stat--indigo">
                <div class="ui-stat__icon"><i class="bi bi-people"></i></div>
                <div class="ui-stat__content">
                    <span class="ui-stat__label">Users</span>
                    <h3 class="ui-stat__value">{{ $users['total'] }}</h3>
                    <span class="ui-stat__hint">{{ $users['active'] }} active, {{ $users['admin'] }} admin</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ROW 1: chart + attention --}}
<div class="container-fluid ui-section">
    <div class="row g-4">

        <div class="col-12 col-xl-8">
            <section class="ui-panel ui-dash-panel">
                <div class="ui-panel__head">
                    <div class="ui-panel__icon"><i class="bi bi-bar-chart"></i></div>
                    <div class="ui-panel__heading">
                        <h4 class="ui-panel__title">Publishing activity</h4>
                        <span class="ui-panel__sub">Articles published in the last 6 months</span>
                    </div>
                </div>
                <div class="ui-panel__body">
                    <div class="ui-bars" role="img"
                         aria-label="Articles per month: {{ $months->map(fn ($m) => $m['label'] . ' ' . $m['count'])->implode(', ') }}">
                        @foreach ($months as $m)
                            <div class="ui-bars__col">
                                <span class="ui-bars__num">{{ $m['count'] }}</span>
                                <span class="ui-bars__bar {{ $m['count'] === 0 ? 'is-zero' : '' }}"
                                      style="--h: {{ round($m['count'] / $maxMonth * 100) }}%"></span>
                                <span class="ui-bars__label">{{ $m['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        </div>

        <div class="col-12 col-xl-4">
            <section class="ui-panel ui-dash-panel">
                <div class="ui-panel__head">
                    <div class="ui-panel__icon ui-panel__icon--amber"><i class="bi bi-bell"></i></div>
                    <div class="ui-panel__heading">
                        <h4 class="ui-panel__title">Needs attention</h4>
                        <span class="ui-panel__sub">{{ $needs ? 'A few things to look at' : 'All clear' }}</span>
                    </div>
                </div>
                <div class="ui-panel__body">
                    @if ($needs)
                        <ul class="ui-todo">
                            @if ($attention['staleDrafts'])
                                <li>
                                    <a href="/{{ $me->role }}/article?status=draft">
                                        <span class="ui-todo__icon ui-todo__icon--amber"><i class="bi bi-hourglass-split"></i></span>
                                        <span class="ui-todo__text"><strong>{{ $attention['staleDrafts'] }}</strong> {{ Str::plural('draft', $attention['staleDrafts']) }} untouched for 14+ days</span>
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                </li>
                            @endif
                            @if ($attention['unreviewed'])
                                <li>
                                    <a href="/{{ $me->role }}/article?status=published">
                                        <span class="ui-todo__icon ui-todo__icon--sky"><i class="bi bi-patch-question"></i></span>
                                        <span class="ui-todo__text"><strong>{{ $attention['unreviewed'] }}</strong> published {{ Str::plural('article', $attention['unreviewed']) }} not reviewed in 6 months</span>
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                </li>
                            @endif
                            @if ($users['suspended'])
                                <li>
                                    <a href="/admin/settings?status=suspended">
                                        <span class="ui-todo__icon ui-todo__icon--rose"><i class="bi bi-person-slash"></i></span>
                                        <span class="ui-todo__text"><strong>{{ $users['suspended'] }}</strong> suspended {{ Str::plural('account', $users['suspended']) }}</span>
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    @else
                        <div class="ui-dash-ok"><i class="bi bi-check-circle"></i><span>Nothing needs your attention right now.</span></div>
                    @endif
                </div>
            </section>
        </div>
    </div>
</div>

{{-- ROW 2: GEO readiness + top categories --}}
<div class="container-fluid ui-section ui-section--tight">
    <div class="row g-4">

        <div class="col-12 col-lg-6">
            <section class="ui-panel ui-dash-panel">
                <div class="ui-panel__head">
                    <div class="ui-panel__icon"><i class="bi bi-stars"></i></div>
                    <div class="ui-panel__heading">
                        <h4 class="ui-panel__title">GEO readiness</h4>
                        <span class="ui-panel__sub">Share of published articles with each element</span>
                    </div>
                </div>
                <div class="ui-panel__body">
                    @foreach ($geo as $g)
                        <div class="ui-meter">
                            <div class="ui-meter__top">
                                <span><i class="bi {{ $g['icon'] }}"></i> {{ $g['label'] }}</span>
                                <strong>{{ $g['value'] }}%</strong>
                            </div>
                            <div class="ui-meter__track" role="progressbar" aria-valuenow="{{ $g['value'] }}" aria-valuemin="0" aria-valuemax="100">
                                <span class="ui-meter__fill {{ $g['value'] >= 70 ? 'is-good' : ($g['value'] >= 40 ? 'is-mid' : 'is-low') }}"
                                      style="width: {{ $g['value'] }}%"></span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        <div class="col-12 col-lg-6">
            <section class="ui-panel ui-dash-panel">
                <div class="ui-panel__head">
                    <div class="ui-panel__icon"><i class="bi bi-folder2-open"></i></div>
                    <div class="ui-panel__heading">
                        <h4 class="ui-panel__title">Top categories</h4>
                        <span class="ui-panel__sub">By published articles</span>
                    </div>
                </div>
                <div class="ui-panel__body">
                    @forelse ($topCategories as $cat)
                        <div class="ui-meter">
                            <div class="ui-meter__top">
                                <span>{{ $cat->name }}</span>
                                <strong>{{ $cat->articles_count }}</strong>
                            </div>
                            <div class="ui-meter__track">
                                <span class="ui-meter__fill" style="width: {{ round($cat->articles_count / $maxCat * 100) }}%"></span>
                            </div>
                        </div>
                    @empty
                        <div class="ui-dash-ok"><i class="bi bi-folder-x"></i><span>No categories yet.</span></div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</div>

{{-- ROW 3: recent articles + new users --}}
<div class="container-fluid ui-section ui-section--tight">
    <div class="row g-4">

        <div class="col-12 col-xl-8">
            <section class="ui-panel ui-dash-panel">
                <div class="ui-panel__head">
                    <div class="ui-panel__icon"><i class="bi bi-clock-history"></i></div>
                    <div class="ui-panel__heading">
                        <h4 class="ui-panel__title">Recent articles</h4>
                        <span class="ui-panel__sub">Last edited</span>
                    </div>
                    <a href="/{{ $me->role }}/article" class="ui-dash-link">View all <i class="bi bi-arrow-right"></i></a>
                </div>
                <div class="ui-panel__body ui-panel__body--flush">
                    @forelse ($recentArticles as $article)
                        @php $s = $article->status; @endphp
                        <a href="/{{ $me->role }}/article/edit/{{ $article->slug }}" class="ui-mini">
                            <span class="ui-badge ui-badge--{{ $s }}">{{ $statusLabel[$s] }}</span>
                            <span class="ui-mini__main">
                                <strong>{{ $article->title }}</strong>
                                <small>{{ $article->category->name ?? 'Uncategorized' }} · {{ $article->user->name ?? 'Unknown' }}</small>
                            </span>
                            <span class="ui-mini__time">{{ $article->updated_at->diffForHumans(null, true, true) }}</span>
                        </a>
                    @empty
                        <div class="ui-dash-ok"><i class="bi bi-file-earmark-text"></i><span>No articles yet. Write your first one.</span></div>
                    @endforelse
                </div>
            </section>
        </div>

        <div class="col-12 col-xl-4">
            <section class="ui-panel ui-dash-panel">
                <div class="ui-panel__head">
                    <div class="ui-panel__icon"><i class="bi bi-person-plus"></i></div>
                    <div class="ui-panel__heading">
                        <h4 class="ui-panel__title">Newest users</h4>
                        <span class="ui-panel__sub">Latest sign-ups</span>
                    </div>
                    <a href="/admin/settings" class="ui-dash-link">Manage <i class="bi bi-arrow-right"></i></a>
                </div>
                <div class="ui-panel__body ui-panel__body--flush">
                    @forelse ($recentUsers as $user)
                        @php
                            $initials = collect(explode(' ', trim($user->name)))->filter()->take(2)
                                ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
                        @endphp
                        <div class="ui-mini ui-mini--user">
                            <span class="ui-user__avatar ui-user__avatar--{{ $user->role }} ui-user__avatar--sm">{{ $initials ?: '?' }}</span>
                            <span class="ui-mini__main">
                                <strong>{{ $user->name }}</strong>
                                <small>{{ $user->email }}</small>
                            </span>
                            <span class="ui-role ui-role--{{ $user->role }}">{{ ucfirst($user->role) }}</span>
                        </div>
                    @empty
                        <div class="ui-dash-ok"><i class="bi bi-people"></i><span>No users yet.</span></div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</div>

@endsection
