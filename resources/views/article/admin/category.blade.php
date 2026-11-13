@extends('layouts.sidebar')
@php
$sideTitle = 'Category';
@endphp

@section('css')
 <link rel="stylesheet" href="{{asset('storage/css/contents.css')}}">
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

            <h2 class="content-hero-title">Categories</h2>
            <p class="content-hero-description">
                Manage, organize, and structure your articles
                with a well-organized category system.
            </p>

            <div class="d-flex flex-wrap gap-2 mt-4">
                <button class="btn btn-hero-primary"
                        data-bs-toggle="dialog"
                        data-bs-target="#createCategoryModal">
                    <i class="bi bi-plus-lg me-2"></i>
                    Create Category
                </button>
            </div>
        </div>

        <div class="content-hero-decoration">
            <div class="hero-circle hero-circle-one"></div>
            <div class="hero-circle hero-circle-two"></div>
            <div class="hero-folder">
                <i class="bi bi-folder2-open"></i>
            </div>
            <span class="hero-floating-icon hero-icon-one">
                <i class="bi bi-bookmarks"></i>
                {{-- <i class="bi bi-file-earmark-text"></i> --}}
            </span>
            <span class="hero-floating-icon hero-icon-two">
                <i class="bi bi-tags"></i>
            </span>
        </div>
    </div>

</div>
{{-- END HERO --}}

{{-- STATS CATEGORY --}}
<div class="container-fluid">
    <div class="row g-4">
        <div class="sm:col-12 md:col-4 xl:col-4">
            <div class="ui-stat ui-stat--indigo">
                <div class="ui-stat__icon"><i class="bi bi-collection"></i></div>
                <div class="ui-stat__content">
                    <span class="ui-stat__label">Total Categories</span>
                    <h3 class="ui-stat__value">{{ $categoryStats->total ?? 0 }}</h3>
                    <span class="ui-stat__hint">All registered categories</span>
                </div>
            </div>
        </div>

        <div class="sm:col-12 md:col-4 xl:col-4">
            <div class="ui-stat ui-stat--emerald">
                <div class="ui-stat__icon"><i class="bi bi-newspaper"></i></div>
                <div class="ui-stat__content">
                    <span class="ui-stat__label">Published Articles</span>
                    <h3 class="ui-stat__value">{{ $categoryStats->active ?? 0 }}</h3>
                    <span class="ui-stat__hint">Across all categories</span>
                </div>
            </div>
        </div>

        <div class="sm:col-12 md:col-4 xl:col-4">
            <div class="ui-stat ui-stat--amber">
                <div class="ui-stat__icon"><i class="bi bi-archive"></i></div>
                <div class="ui-stat__content">
                    <span class="ui-stat__label">Archive Categories</span>
                    <h3 class="ui-stat__value">{{ $categoryStats->inactive ?? 0 }}</h3>
                    <span class="ui-stat__hint">Hidden from public view</span>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- END STATS CATEGORY --}}

{{-- SEARCH --}}
<div class="container-fluid ui-section">
    <form method="GET" action="{{ route('admin.article.category') }}" class="ui-search" role="search">
        <div class="ui-search__field">
            <i class="bi bi-search ui-search__icon"></i>
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   class="ui-search__input"
                   placeholder="Search by name or slug..."
                   autocomplete="off">

            @if (request()->filled('search'))
                <a href="{{ route('admin.article.category') }}" class="ui-search__clear" title="Clear search">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </div>

        <button type="submit" class="ui-search__btn">Search</button>
    </form>

    <div class="ui-search__status">
        @if (request()->filled('search'))
            <span>{{ $categories->total() }} result(s) for</span>
            <span class="ui-chip">
                "{{ request('search') }}"
                <a href="{{ route('admin.article.category') }}" title="Remove filter"><i class="bi bi-x"></i></a>
            </span>
            <a href="{{ route('admin.article.category') }}" class="ui-search__reset">
                <i class="bi bi-arrow-counterclockwise"></i> Reset filter
            </a>
        @endif
    </div>
</div>
{{-- END SEARCH --}}

@php $colors = ['indigo', 'emerald', 'amber', 'rose', 'sky']; @endphp

<div class="container-fluid mt-5">
    <div class="ui-section-head">
        <div>
            <h5 class="ui-section-head__title">All Categories</h5>
            <span class="ui-section-head__sub">Manage and organize your article categories</span>
        </div>
        @if(request()->routeIs('admin.article.category'))
        <a href="/{{auth()->user()->role}}/article/archived-category" class="btn btn-archives">
            <i class="bi bi-archive"></i>
            <span>Archives</span>
            <i class="bi bi-arrow-up-right ms-1 arrow-icon"></i>
        </a>
        @else
        <a href="/{{auth()->user()->role}}/article/category" class="btn btn-archives">
            <i class="bi bi-list-task"></i>
            <span>Active Categories</span>
            <i class="bi bi-arrow-up-right ms-1 arrow-icon"></i>
        </a>
        @endif
    </div>

    <div class="row g-4">
        @forelse ($categories as $category)
        @php $color = $colors[$loop->index % count($colors)]; @endphp

        <div class="col-12 col-md-6 col-xl-4">
            <div class="ui-cat ui-cat--{{ $color }} {{ $category->is_active ? '' : 'is-archived' }}">
                <div class="ui-cat__head">
                    <div class="ui-cat__icon">
                        <i class="bi bi-folder2-open"></i>
                    </div>

                    <div class="ui-cat__title">
                        <h6 class="ui-cat__name">{{ $category->name }}</h6>
                        <span class="ui-cat__slug">/{{ $category->slug }}</span>
                    </div>

                    <span class="ui-cat__badge {{ $category->is_active ? '' : 'ui-cat__badge--muted' }}">
                        {{ $category->is_active ? 'Active' : 'Archived' }}
                    </span>
                </div>

                @if ($category->description)
                    <p class="ui-cat__desc">{{ $category->description }}</p>
                @endif

                <div class="ui-cat__foot">
                    <span class="ui-cat__meta">
                        <i class="bi bi-journal-text"></i>
                        {{ $category->articles_count }} articles
                    </span>

                    <div class="ui-cat__actions">

                        @if(request()->routeIs('admin.article.category'))
                        <a href="#" role="button"
                            data-bs-toggle="dialog"
                            data-bs-target="#editCategoryModal"
                            class="ui-cat__btn"
                            title="Edit"
                            data-bs-id="{{$category->id}}"
                            data-bs-name="{{$category->name}}"
                            data-bs-description="{{$category->description}}">
                            <i class="bi bi-pencil"></i>
                        </a>

                        <a href="#" role="button"
                            data-bs-toggle="dialog"
                            data-bs-target="#archiveCategoryModal"
                            class="ui-cat__btn"
                            title="Archive"
                            data-bs-archive-id="{{$category->id}}"
                            data-bs-archive-name="{{$category->name}}">
                            <i class="bi bi-archive"></i>
                        </a>
                        @else
                        <a href="#" role="button"
                            data-bs-toggle="dialog"
                            data-bs-target="#activateCategoryModal"
                            class="ui-cat__btn"
                            title="Restore Category"
                            data-bs-activate-id="{{$category->id}}"
                            data-bs-activate-name="{{$category->name}}">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                        @endif
                        <a href="#" role="button"
                            data-bs-toggle="dialog"
                            data-bs-target="#deleteModal"
                            class="ui-cat__btn ui-cat__btn--danger"
                            title="Delete"
                            data-bs-deletion-id="{{$category->id}}"
                            data-bs-deletion-name="{{$category->name}}">
                            <i class="bi bi-trash"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
            <div class="col-12">
                <div class="ui-empty">
                    @if($categoryStats->total >= 1)
                    <i class="bi bi-search"></i>
                    @else
                    <i class="bi bi-inbox"></i>
                    @endif
                    <h6>{{ request()->filled('search') ? 'No matching categories' : 'No categories yet' }}</h6>
                    <p>
                        {{ request()->filled('search')
                            ? 'Try a different keyword or reset the filter.'
                            : 'Create your first category to get started.' }}
                    </p>
                </div>
            </div>
        @endforelse
    </div>
    @if ($categories->hasPages())
    <div class="ui-pager">
        <span class="ui-pager__info">
            Showing {{ $categories->firstItem() }}-{{ $categories->lastItem() }}
            of {{ $categories->total() }}
        </span>
        {{ $categories->links() }}
    </div>
    @endif
</div>

{{-- MODAL ADD CATEGORY --}}
<dialog class="dialog dialog-slide-up ui-dialog" id="createCategoryModal" data-bs-backdrop="static">
  <div class="dialog-header ui-dialog__header">
    <div class="ui-dialog__icon"><i class="bi bi-folder-plus"></i></div>
    <div class="ui-dialog__heading">
      <h1 class="dialog-title ui-dialog__title">Add new category</h1>
      <span class="ui-dialog__sub">Organize your articles under a new topic</span>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="dialog" aria-label="Close"></button>
  </div>

  <form action="/{{ auth()->user()->role }}/article/category/store" method="POST">
    @csrf
    <div class="dialog-body ui-dialog__body">
      <div class="ui-field">
        <label for="name" class="ui-field__label">
          Category name <span class="ui-field__req">*</span>
        </label>
        <div class="ui-field__control">
          <i class="bi bi-tag ui-field__icon"></i>
          <input type="text"
                 class="ui-field__input @error('name') is-invalid @enderror"
                 id="name" name="name" value="{{ old('name') }}"
                 placeholder="e.g. Technology" autocomplete="off" required>
        </div>
        @error('name')
          <span class="ui-field__error">{{ $message }}</span>
        @else
          <span class="ui-field__hint">
            URL: <strong>/<span id="slugPreview">your-category</span></strong>
          </span>
        @enderror
      </div>

      <div class="ui-field">
        <label for="description" class="ui-field__label">
          Description <span class="ui-field__opt">Optional</span>
        </label>
        <textarea class="ui-field__input ui-field__textarea"
                  id="description" name="description" rows="4" maxlength="255"
                  placeholder="What kind of articles belong here?">{{ old('description') }}</textarea>
        <span class="ui-field__hint ui-field__count"><span id="descCount">0</span>/255</span>
      </div>
    </div>

    <div class="dialog-footer ui-dialog__footer">
      <button type="button" class="ui-dialog__btn ui-dialog__btn--ghost" data-bs-dismiss="dialog">Cancel</button>
      <button type="submit" class="ui-dialog__btn ui-dialog__btn--primary">
        <i class="bi bi-check2"></i> Save category
      </button>
    </div>
  </form>
</dialog>

{{-- MODAL EDIT CATEGORY --}}
<dialog class="dialog dialog-slide-up ui-dialog" id="editCategoryModal" data-bs-backdrop="static">
  <div class="dialog-header ui-dialog__header">
    <div class="ui-dialog__icon"><i class="bi bi-folder-plus"></i></div>
    <div class="ui-dialog__heading">
      <h1 class="dialog-title ui-dialog__title">Edit category</h1>
      <span class="ui-dialog__sub">Organize your articles under a new topic</span>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="dialog" aria-label="Close"></button>
  </div>

  <form action="/{{ auth()->user()->role }}/article/category/update" method="POST">
    @csrf
    <div class="dialog-body ui-dialog__body">
      <div class="ui-field">
        <label for="edit_name" class="ui-field__label">
          Category name <span class="ui-field__req">*</span>
        </label>
        <div class="ui-field__control">
          <i class="bi bi-tag ui-field__icon"></i>
          <input type="text"
                 class="ui-field__input"
                 id="edit_name" name="edit_name"
                 placeholder="e.g. Technology" autocomplete="off" required>
          <input type="text" name="edit_id" id="edit_id" hidden>
        </div>
        <span class="ui-field__hint">
            URL: <strong>/<span id="editSlugPreview"></span></strong>
        </span>
      </div>

      <div class="ui-field">
        <label for="edit_description" class="ui-field__label">
          Description <span class="ui-field__opt">Optional</span>
        </label>
        <textarea class="ui-field__input ui-field__textarea"
                  id="edit_description" name="edit_description" rows="4" maxlength="255"
                  placeholder="What kind of articles belong here?"></textarea>
        <span class="ui-field__hint ui-field__count"><span id="editDescCount">0</span>/255</span>
      </div>
    </div>

    <div class="dialog-footer ui-dialog__footer">
      <button type="button" class="ui-dialog__btn ui-dialog__btn--ghost" data-bs-dismiss="dialog">Cancel</button>
      <button type="submit" class="ui-dialog__btn ui-dialog__btn--primary">
        <i class="bi bi-check2"></i> Update category
      </button>
    </div>
  </form>
</dialog>

{{-- MODAL ARCHIVE CATEGORY --}}
<dialog class="dialog dialog-slide-up ui-dialog" id="archiveCategoryModal" data-bs-backdrop="static">
  <div class="dialog-header ui-dialog__header">
    <div class="ui-dialog__icon ui-dialog__icon--warning"><i class="bi bi-archive"></i></div>
    <div class="ui-dialog__heading">
      <h1 class="dialog-title ui-dialog__title">Archive category</h1>
      <span class="ui-dialog__sub">You can restore it later</span>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="dialog" aria-label="Close"></button>
  </div>

  <form action="/{{ auth()->user()->role }}/article/category/archieve" method="POST">
    @csrf
    <input type="text" name="archive_id" id="archive_id" hidden>

    <div class="dialog-body ui-dialog__body">
      <p class="ui-dialog__text">
        Are you sure you want to archive
        <strong class="ui-dialog__target" id="archive_name">this category</strong>?
      </p>

      <div class="ui-dialog__notice ui-dialog__notice--warning">
        <i class="bi bi-info-circle"></i>
        <span>It will be marked as archived. Its articles are not deleted.</span>
      </div>
    </div>

    <div class="dialog-footer ui-dialog__footer">
      <button type="button" class="ui-dialog__btn ui-dialog__btn--ghost" data-bs-dismiss="dialog">Cancel</button>
      <button type="submit" class="ui-dialog__btn ui-dialog__btn--warning">
        <i class="bi bi-archive"></i> Yes, archive
      </button>
    </div>
  </form>
</dialog>

{{-- MODAL ACTIVATE CATEGORY --}}
<dialog class="dialog dialog-slide-up ui-dialog" id="activateCategoryModal" data-bs-backdrop="static">
  <div class="dialog-header ui-dialog__header">
    <div class="ui-dialog__icon ui-dialog__icon--warning"><i class="bi bi-folder-check"></i></div>
    <div class="ui-dialog__heading">
      <h1 class="dialog-title ui-dialog__title">Restore category</h1>
      <span class="ui-dialog__sub">Bring this category back to your active list</span>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="dialog" aria-label="Close"></button>
  </div>

  <form action="/{{ auth()->user()->role }}/article/category/activate" method="POST">
    @csrf
    <input type="text" name="activate_id" id="activate_id" hidden>

    <div class="dialog-body ui-dialog__body">
      <p class="ui-dialog__text">
        Are you sure you want to restore
        <strong class="ui-dialog__target" id="activate_name">this category</strong>?
      </p>

      <div class="ui-dialog__notice ui-dialog__notice--warning">
        <i class="bi bi-info-circle"></i>
        <span>This category will become active and available again. Its existing articles will remain untouched.</span>
      </div>
    </div>

    <div class="dialog-footer ui-dialog__footer">
      <button type="button" class="ui-dialog__btn ui-dialog__btn--ghost" data-bs-dismiss="dialog">Cancel</button>
      <button type="submit" class="ui-dialog__btn ui-dialog__btn--warning">
        <i class="bi bi-arrow-counterclockwise"></i>
                Yes, restore category
      </button>
    </div>
  </form>
</dialog>

{{-- MODAL DELETE CATEGORY --}}
<dialog class="dialog dialog-slide-up ui-dialog" id="deleteModal" data-bs-backdrop="static">
  <div class="dialog-header ui-dialog__header">
    <div class="ui-dialog__icon ui-dialog__icon--warning"><i class="bi bi-exclamation-triangle"></i></div>
    <div class="ui-dialog__heading">
        <h1 class="dialog-title ui-dialog__title">Delete Category</h1>
        <span class="ui-dialog__sub">Permanently delete this category</span>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="dialog" aria-label="Close"></button>
  </div>

  <form action="/{{ auth()->user()->role }}/article/category/destroy" method="POST">
    @csrf
    <input type="text" name="deletion_id" id="deletion_id" hidden>

    <div class="dialog-body ui-dialog__body">
      <p class="ui-dialog__text">
        Are you sure you want to delete
        <strong class="ui-dialog__target" id="deletion_name">this category</strong>?
      </p>

      <div class="ui-dialog__notice ui-dialog__notice--warning">
        <i class="bi bi-info-circle"></i>
        <span>This category will be permanently deleted along with all of its associated articles.</span>
      </div>
    </div>

    <div class="dialog-footer ui-dialog__footer">
      <button type="button" class="ui-dialog__btn ui-dialog__btn--ghost" data-bs-dismiss="dialog">Cancel</button>
      <button type="submit" class="ui-dialog__btn ui-dialog__btn--warning">
        <i class="bi bi-arrow-counterclockwise"></i>
                Yes, delete this category
      </button>
    </div>
  </form>
</dialog>

@endsection

@section('javascript')
<script src="{{asset('storage/js/admin-article.js')}}"></script>
@endsection
