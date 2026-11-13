@extends('layouts.sidebar')
@php
    $sideTitle = 'Settings';
    $me = auth()->user();

    $roles = [
        'admin'  => ['label' => 'Admin',  'icon' => 'bi-shield-check', 'desc' => 'Full access, including settings and users'],
        'user'   => ['label' => 'User',   'icon' => 'bi-person',        'desc' => 'Basic account, no admin access'],
    ];
    $hasFilter = request()->filled('search') || request()->filled('role') || request()->filled('status');
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
                <span class="hero-eyebrow"><i class="bi bi-gear"></i> SETTINGS</span>
                <span class="hero-dot"></span>
                <span class="hero-status">Users and access</span>
            </div>
            <h2 class="content-hero-title">User Management</h2>
            <p class="content-hero-description">
                Control who can sign in, what they can do, and suspend accounts when needed.
            </p>
        </div>

        <div class="content-hero-decoration">
            <div class="hero-circle hero-circle-one"></div>
            <div class="hero-circle hero-circle-two"></div>
            <div class="hero-folder"><i class="bi bi-people"></i></div>
            <span class="hero-floating-icon hero-icon-one"><i class="bi bi-shield-lock"></i></span>
            <span class="hero-floating-icon hero-icon-two"><i class="bi bi-person-check"></i></span>
        </div>
    </div>
</div>

{{-- STATS --}}
<div class="container-fluid">
    <div class="row g-4">
        <div class="col-6 col-xl-3">
            <div class="ui-stat ui-stat--indigo">
                <div class="ui-stat__icon"><i class="bi bi-people"></i></div>
                <div class="ui-stat__content">
                    <span class="ui-stat__label">Total users</span>
                    <h3 class="ui-stat__value">{{ $counts['total'] ?? 0 }}</h3>
                    <span class="ui-stat__hint">All accounts</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="ui-stat ui-stat--sky">
                <div class="ui-stat__icon"><i class="bi bi-shield-check"></i></div>
                <div class="ui-stat__content">
                    <span class="ui-stat__label">Admins</span>
                    <h3 class="ui-stat__value">{{ $counts['admin'] ?? 0 }}</h3>
                    <span class="ui-stat__hint">Full access</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="ui-stat ui-stat--emerald">
                <div class="ui-stat__icon"><i class="bi bi-person-check"></i></div>
                <div class="ui-stat__content">
                    <span class="ui-stat__label">Active</span>
                    <h3 class="ui-stat__value">{{ $counts['active'] ?? 0 }}</h3>
                    <span class="ui-stat__hint">Can sign in</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="ui-stat ui-stat--rose">
                <div class="ui-stat__icon"><i class="bi bi-person-slash"></i></div>
                <div class="ui-stat__content">
                    <span class="ui-stat__label">Suspended</span>
                    <h3 class="ui-stat__value">{{ $counts['suspended'] ?? 0 }}</h3>
                    <span class="ui-stat__hint">Blocked accounts</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- TOOLBAR + LIST --}}
<div class="container-fluid ui-section">

    <form method="GET" action="{{ url()->current() }}" class="ui-search" role="search">
        <div class="ui-search__field">
            <i class="bi bi-search ui-search__icon"></i>
            <input type="text" name="search" value="{{ request('search') }}" class="ui-search__input"
                   placeholder="Search by name or email..." autocomplete="off">
            @if (request()->filled('search'))
                <a href="{{ request()->fullUrlWithQuery(['search' => null, 'page' => null]) }}"
                   class="ui-search__clear" title="Clear search"><i class="bi bi-x-lg"></i></a>
            @endif
        </div>

        <select name="role" class="ui-search__select" data-autosubmit aria-label="Role">
            <option value="">All roles</option>
            @foreach ($roles as $key => $r)
                <option value="{{ $key }}" @selected(request('role') === $key)>{{ $r['label'] }}</option>
            @endforeach
        </select>

        <select name="status" class="ui-search__select" data-autosubmit aria-label="Status">
            <option value="">All status</option>
            <option value="active"    @selected(request('status') === 'active')>Active</option>
            <option value="suspended" @selected(request('status') === 'suspended')>Suspended</option>
        </select>

        <button type="submit" class="ui-search__btn">Search</button>
    </form>

    <div class="ui-search__status">
        <span>{{ $users->total() }} {{ Str::plural('user', $users->total()) }}</span>
        @if ($hasFilter)
            <a href="{{ url()->current() }}" class="ui-search__reset">
                <i class="bi bi-arrow-counterclockwise"></i> Reset filters
            </a>
        @endif
    </div>

    <div class="ui-user-list">

        {{-- column titles (desktop only) --}}
        <div class="ui-user-head" aria-hidden="true">
            <span>User</span><span>Role</span><span>Status</span><span>Joined</span><span></span>
        </div>

        @forelse ($users as $user)
            @php
                $isMe = $user->id === $me->id;
                $active = (bool) $user->status;
                $r = $roles[$user->role] ?? ['label' => ucfirst($user->role), 'icon' => 'bi-person'];
                $initials = collect(explode(' ', trim($user->name)))->filter()->take(2)
                            ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
            @endphp

            <article class="ui-user {{ $active ? '' : 'is-suspended' }}">
                <div class="ui-user__who">
                    <span class="ui-user__avatar ui-user__avatar--{{ $user->role }}">{{ $initials ?: '?' }}</span>
                    <div class="ui-user__id">
                        <h6 class="ui-user__name">
                            {{ $user->name }}
                            @if ($isMe) <span class="ui-user__you">You</span> @endif
                        </h6>
                        <span class="ui-user__email">{{ $user->email }}</span>
                    </div>
                </div>

                <div class="ui-user__cell" data-label="Role">
                    <span class="ui-role ui-role--{{ $user->role }}">
                        <i class="bi {{ $r['icon'] }}"></i> {{ $r['label'] }}
                    </span>
                </div>

                <div class="ui-user__cell" data-label="Status">
                    <span class="ui-status {{ $active ? 'is-active' : 'is-off' }}">
                        <span class="ui-status__dot"></span> {{ $active ? 'Active' : 'Suspended' }}
                    </span>
                </div>

                <div class="ui-user__cell ui-user__date" data-label="Joined">
                    {{ $user->created_at->format('d M Y') }}
                </div>

                <div class="ui-user__actions">
                    <button type="button" class="ui-cat__btn" title="{{ $isMe ? 'You cannot edit your own access' : 'Manage user' }}"
                            data-bs-toggle="dialog" data-bs-target="#manageUserModal"
                            data-id="{{ $user->id }}"
                            data-name="{{ $user->name }}"
                            data-email="{{ $user->email }}"
                            data-initials="{{ $initials }}"
                            data-role="{{ $user->role }}"
                            data-active="{{ $active ? 1 : 0 }}"
                            data-me="{{ $isMe ? 1 : 0 }}">
                        <i class="bi bi-sliders"></i>
                    </button>
                </div>
            </article>
        @empty
            <div class="ui-empty">
                <i class="bi {{ $hasFilter ? 'bi-search' : 'bi-people' }}"></i>
                <h6>{{ $hasFilter ? 'No matching users' : 'No users yet' }}</h6>
                <p>{{ $hasFilter ? 'Try different filters or reset them.' : 'Accounts will appear here once people register.' }}</p>
            </div>
        @endforelse
    </div>

    @if ($users->hasPages())
        <div class="ui-pager">
            <span class="ui-pager__info">
                Showing {{ $users->firstItem() }}-{{ $users->lastItem() }} of {{ $users->total() }}
            </span>
            {{ $users->onEachSide(1)->links() }}
        </div>
    @endif
</div>

{{-- ==========================================
    MODAL: MANAGE USER
========================================== --}}
<dialog class="dialog dialog-slide-up ui-dialog ui-dialog--lg" id="manageUserModal" data-bs-backdrop="static">
  <div class="dialog-header ui-dialog__header">
    <div class="ui-dialog__icon"><i class="bi bi-person-gear"></i></div>
    <div class="ui-dialog__heading">
      <h1 class="dialog-title ui-dialog__title">Manage user</h1>
      <span class="ui-dialog__sub">Change role and account status</span>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="dialog" aria-label="Close"></button>
  </div>

  <form action="/{{ $me->role }}/settings/users/update" method="POST">
    @csrf
    <input type="hidden" name="user_id" id="mu_id">

    <div class="dialog-body ui-dialog__body">

        {{-- who --}}
        <div class="ui-user-card">
            <span class="ui-user__avatar" id="mu_avatar">?</span>
            <div class="ui-user__id">
                <h6 class="ui-user__name" id="mu_name">Name</h6>
                <span class="ui-user__email" id="mu_email">email</span>
            </div>
        </div>

        <div class="ui-dialog__notice ui-dialog__notice--warning" id="mu_self" hidden>
            <i class="bi bi-info-circle"></i>
            <span>This is your own account. Role and status are locked so you cannot lock yourself out.</span>
        </div>

        {{-- role --}}
        <div class="ui-field">
            <span class="ui-field__label">Role</span>
            <div class="ui-choice-grid ui-choice-grid--stack">
                @foreach ($roles as $key => $r)
                    <label class="ui-choice">
                        <input type="radio" name="role" value="{{ $key }}">
                        <span class="ui-choice__card">
                            <span class="ui-choice__icon"><i class="bi {{ $r['icon'] }}"></i></span>
                            <span class="ui-choice__text">
                                <strong>{{ $r['label'] }}</strong>
                                <small>{{ $r['desc'] }}</small>
                            </span>
                            <span class="ui-choice__check"><i class="bi bi-check-lg"></i></span>
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- status --}}
        <div class="ui-field">
            <span class="ui-field__label">Account status</span>
            <div class="ui-seg ui-seg--status">
                <label><input type="radio" name="status" value="1"><span><i class="bi bi-check-circle"></i> Active</span></label>
                <label><input type="radio" name="status" value="0"><span><i class="bi bi-slash-circle"></i> Suspended</span></label>
            </div>
            <span class="ui-field__hint" id="mu_hint">Suspended users cannot sign in until you activate them again.</span>
        </div>

        {{-- summary of the pending change --}}
        <div class="ui-dialog__notice ui-dialog__notice--danger" id="mu_change" hidden>
            <i class="bi bi-exclamation-triangle"></i>
            <span id="mu_change_text"></span>
        </div>
    </div>

    <div class="dialog-footer ui-dialog__footer">
      <button type="button" class="ui-dialog__btn ui-dialog__btn--ghost" data-bs-dismiss="dialog">Cancel</button>
      <button type="submit" class="ui-dialog__btn ui-dialog__btn--primary" id="mu_submit" disabled>
        <i class="bi bi-check2"></i> Save changes
      </button>
    </div>
  </form>
</dialog>

@endsection

@section('javascript')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const dlg = document.getElementById('manageUserModal');
        if (!dlg) return;

        const $ = id => document.getElementById(id);
        const form   = dlg.querySelector('form');
        const submit = $('mu_submit');
        const roles  = { admin: 'Admin', user: 'User' };   // match your $roles
        let original = null;

        // dropdown filters apply immediately
        document.querySelectorAll('[data-autosubmit]').forEach(el =>
            el.addEventListener('change', () => el.form.submit()));

        // read the current choice
        const current = () => ({
            role:   form.elements['role'].value,
            active: form.elements['status'].value,
        });

        const refresh = () => {
            const c = current();
            const changed = c.role !== original.role || c.active !== original.active;
            const locked = dlg.classList.contains('is-locked');

            submit.disabled = !changed || locked;

            // warn about risky changes
            const notes = [];
            if (c.active === '0' && original.active === '1') notes.push('This user will be signed out and blocked from signing in.');
            if (original.role === 'admin' && c.role !== 'admin') notes.push('This user will lose admin access.');
            if (original.role !== 'admin' && c.role === 'admin') notes.push('This user will get full admin access.');

            $('mu_change').hidden = !notes.length;
            $('mu_change_text').textContent = notes.join(' ');
        };

        // fill the dialog when a Manage button is clicked
        document.addEventListener('click', e => {
            const btn = e.target.closest('[data-bs-target="#manageUserModal"]');
            if (!btn) return;
            const d = btn.dataset;

            $('mu_id').value = d.id;
            $('mu_name').textContent = d.name;      // textContent keeps names safe
            $('mu_email').textContent = d.email;
            $('mu_avatar').textContent = d.initials || '?';
            $('mu_avatar').className = 'ui-user__avatar ui-user__avatar--' + d.role;

            form.elements['role'].value = d.role;
            form.elements['status'].value = d.active;
            original = { role: d.role, active: d.active };

            const isMe = d.me === '1';
            dlg.classList.toggle('is-locked', isMe);
            $('mu_self').hidden = !isMe;
            form.querySelectorAll('input[name="role"], input[name="status"]')
                .forEach(i => i.disabled = isMe);

            refresh();
        });

        form.addEventListener('change', () => original && refresh());
    });
</script>
@endsection
