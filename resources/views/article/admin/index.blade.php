@extends('layouts.sidebar')
@php
$sideTitle = 'Article';
@endphp

@section('css')
 <link rel="stylesheet" href="{{asset('storage/css/contents.css')}}">
 <link rel="stylesheet" href="{{asset('storage/css/admin-article.css')}}">
@endsection

@section('contents')


{{-- HERO --}}
<div class="category-page">

    <!-- Page Header -->
    <div class="content-hero mb-4">
        <div class="content-hero-content">
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="hero-eyebrow">
                    <i class="bi bi-grid"></i> ARTICLE MANAGEMENT
                </span>
                <span class="hero-dot"></span>
                <span class="hero-status">Organize your content</span>
            </div>

            <h2 class="content-hero-title">Article</h2>
            <p class="content-hero-description">
                Enhance your reader experience by writing compelling, structured content to tell your story.
            </p>

            <div class="d-flex flex-wrap gap-2 mt-4">
                <a class="btn btn-hero-primary"
                        href="/{{auth()->user()->role}}/article/write-post">
                    <i class="bi bi-feather me-2"></i>
                    Write Article
                </a>
            </div>
        </div>

        <div class="content-hero-decoration">
            <div class="hero-circle hero-circle-one"></div>
            <div class="hero-circle hero-circle-two"></div>
            <div class="hero-folder">
                <i class="bi bi-file-earmark-post"></i>
            </div>
            <span class="hero-floating-icon hero-icon-one">
                <i class="bi bi-file-richtext"></i>
            </span>
            <span class="hero-floating-icon hero-icon-two">
                <i class="bi bi-envelope-paper"></i>
            </span>
        </div>
    </div>

</div>

@php
    $role = auth()->user()->role;
    $hasFilter = request()->filled('search') || request()->filled('category') || $status !== 'all';
    $tabs = ['all' => 'All', 'published' => 'Published', 'scheduled' => 'Scheduled', 'draft' => 'Draft'];
    $statusLabel = ['published' => 'Published', 'scheduled' => 'Scheduled', 'draft' => 'Draft'];
@endphp

<div class="container-fluid ui-section">

    {{-- Status tabs --}}
    <nav class="ui-tabs" aria-label="Article status">
        @foreach ($tabs as $key => $label)
            <a href="{{ request()->fullUrlWithQuery(['status' => $key === 'all' ? null : $key, 'page' => null]) }}"
               class="ui-tabs__item {{ $status === $key ? 'is-active' : '' }}">
                {{ $label }} <span class="ui-tabs__count">{{ $counts[$key] }}</span>
            </a>
        @endforeach
    </nav>

    {{-- Search + filters --}}
    <form method="GET" action="{{ url()->current() }}" class="ui-search" role="search" id="articleFilter">
        @if ($status !== 'all') <input type="hidden" name="status" value="{{ $status }}"> @endif

        <div class="ui-search__field">
            <i class="bi bi-search ui-search__icon"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   class="ui-search__input" placeholder="Search by title or slug..." autocomplete="off">
            @if (request()->filled('search'))
                <a href="{{ request()->fullUrlWithQuery(['search' => null, 'page' => null]) }}"
                   class="ui-search__clear" title="Clear search"><i class="bi bi-x-lg"></i></a>
            @endif
        </div>

        <select name="category" class="ui-search__select" data-autosubmit aria-label="Category">
            <option value="">All categories</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>

        <select name="sort" class="ui-search__select" data-autosubmit aria-label="Sort">
            <option value="newest" @selected(request('sort', 'newest') === 'newest')>Newest first</option>
            <option value="oldest" @selected(request('sort') === 'oldest')>Oldest first</option>
            <option value="title"  @selected(request('sort') === 'title')>Title A-Z</option>
        </select>

        <button type="submit" class="ui-search__btn">Search</button>
    </form>

    {{-- Status line + chips --}}
    <div class="ui-search__status">
        <span>{{ $articles->total() }} {{ Str::plural('article', $articles->total()) }}</span>

        @if (request()->filled('search'))
            <span class="ui-chip">"{{ request('search') }}"
                <a href="{{ request()->fullUrlWithQuery(['search' => null, 'page' => null]) }}"><i class="bi bi-x"></i></a>
            </span>
        @endif
        @if (request()->filled('category'))
            <span class="ui-chip">{{ $categories->firstWhere('id', request('category'))?->name }}
                <a href="{{ request()->fullUrlWithQuery(['category' => null, 'page' => null]) }}"><i class="bi bi-x"></i></a>
            </span>
        @endif

        @if ($hasFilter)
            <a href="{{ url()->current() }}" class="ui-search__reset">
                <i class="bi bi-arrow-counterclockwise"></i> Reset filters
            </a>
        @endif
    </div>

    {{-- Article list --}}
    <div class="ui-art-list">
        @forelse ($articles as $article)
            @php $s = $article->status; @endphp

            <article class="ui-art ui-art--{{ $s }}">

                {{-- thumbnail --}}
                <div class="ui-art__thumb ui-thumb__media">
                    @if ($article->thumbnail_type === 'template' && $article->templateArticle)
                        <img src="{{ $article->templateArticle->image_url }}" alt="" loading="lazy">
                        @if ($article->templateArticle->overlay_color)
                            <span class="ui-thumb__tint"
                                  style="background: {{ $article->templateArticle->overlay_color }}; opacity: {{ $article->templateArticle->overlay_opacity / 100 }};"></span>
                        @endif
                        <div class="ui-thumb__text ui-thumb__text--{{ $article->templateArticle->text_position }}"
                             style="color: {{ $article->templateArticle->text_color }};">
                            <span>{{ $article->title }}</span>
                        </div>
                    @elseif ($article->thumbnail_path)
                        <img src="{{ asset('storage/' . $article->thumbnail_path) }}" alt="" loading="lazy">
                    @else
                        <div class="ui-art__nothumb"><i class="bi bi-image"></i></div>
                    @endif
                </div>

                {{-- main --}}
                <div class="ui-art__main">
                    <div class="ui-art__badges">
                        <span class="ui-badge ui-badge--{{ $s }}">{{ $statusLabel[$s] }}</span>
                        <span class="ui-art__cat"><i class="bi bi-folder2-open"></i> {{ $article->category->name ?? 'Uncategorized' }}</span>
                    </div>

                    <h5 class="ui-art__title">{{ $article->title }}</h5>

                    @if ($article->excerpt)
                        <p class="ui-art__excerpt">{{ $article->excerpt }}</p>
                    @endif

                    <div class="ui-art__meta">
                        <span><i class="bi bi-person"></i> {{ $article->user->name ?? 'Unknown' }}</span>
                        <span class="ui-thumb__dot"></span>
                        <span>
                            <i class="bi bi-clock"></i>
                            @if ($s === 'published') Published {{ $article->published_at->format('d M Y') }}
                            @elseif ($s === 'scheduled') Goes live {{ $article->published_at->format('d M Y, H:i') }}
                            @else Edited {{ $article->updated_at->diffForHumans() }}
                            @endif
                        </span>
                        <span class="ui-thumb__dot"></span>
                        <span class="ui-art__slug">/{{ $article->slug }}</span>
                    </div>
                </div>

                {{-- actions --}}
                <div class="ui-art__actions">
                    @if ($s === 'published')
                        <a href="{{ url('/article/' . $article->slug) }}" target="_blank" class="ui-cat__btn" title="View on site">
                            <i class="bi bi-box-arrow-up-right"></i>
                        </a>
                    @endif
                    <a href="/{{ $role }}/article/edit/{{ $article->slug }}" class="ui-cat__btn" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <button type="button" class="ui-cat__btn ui-cat__btn--danger" title="Delete"
                            data-bs-toggle="dialog" data-bs-target="#deleteArticleModal"
                            data-id="{{ $article->id }}" data-title="{{ $article->title }}">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </article>
        @empty
            <div class="ui-empty">
                <i class="bi {{ $hasFilter ? 'bi-search' : 'bi-file-earmark-text' }}"></i>
                <h6>{{ $hasFilter ? 'No matching articles' : 'No articles yet' }}</h6>
                <p>{{ $hasFilter ? 'Try different filters or reset them.' : 'Write your first article to get started.' }}</p>
            </div>
        @endforelse
    </div>

    @if ($articles->hasPages())
        <div class="ui-pager">
            <span class="ui-pager__info">
                Showing {{ $articles->firstItem() }}-{{ $articles->lastItem() }} of {{ $articles->total() }}
            </span>
            {{ $articles->links() }}
        </div>
    @endif
</div>

{{-- MODAL DELETE ARTICLE --}}
<dialog class="dialog dialog-slide-up ui-dialog" id="deleteArticleModal" data-bs-backdrop="static">
  <div class="dialog-header ui-dialog__header">
    <div class="ui-dialog__icon ui-dialog__icon--danger"><i class="bi bi-trash"></i></div>
    <div class="ui-dialog__heading">
      <h1 class="dialog-title ui-dialog__title">Delete article</h1>
      <span class="ui-dialog__sub">It will be removed from the site</span>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="dialog" aria-label="Close"></button>
  </div>

  <form action="/{{ $role }}/article/delete" method="POST">
    @csrf
    <input type="hidden" name="article_id" id="delete_article_id">

    <div class="dialog-body ui-dialog__body">
      <p class="ui-dialog__text">
        Are you sure you want to delete
        <strong class="ui-dialog__target ui-dialog__target--danger" id="delete_article_title">this article</strong>?
      </p>
      <div class="ui-dialog__notice ui-dialog__notice--danger">
        <i class="bi bi-info-circle"></i>
        <span>Readers will no longer be able to open it. Its URL stays reserved.</span>
      </div>
    </div>

    <div class="dialog-footer ui-dialog__footer">
      <button type="button" class="ui-dialog__btn ui-dialog__btn--ghost" data-bs-dismiss="dialog">Cancel</button>
      <button type="submit" class="ui-dialog__btn ui-dialog__btn--danger"><i class="bi bi-trash"></i> Yes, delete</button>
    </div>
  </form>
</dialog>

@endsection

@section('javascript')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // auto-apply dropdown filters
        document.querySelectorAll('[data-autosubmit]').forEach(el =>
            el.addEventListener('change', () => el.form.submit()));

        // fill the delete dialog
        document.addEventListener('click', e => {
            const btn = e.target.closest('[data-bs-target="#deleteArticleModal"]');
            if (!btn) return;
            document.getElementById('delete_article_id').value = btn.dataset.id;
            document.getElementById('delete_article_title').textContent = btn.dataset.title;
        });
    });
</script>
@endsection
