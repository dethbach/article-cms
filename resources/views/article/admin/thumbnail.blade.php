@extends('layouts.sidebar')
@php
$sideTitle = 'Thumbnail';
@endphp

@section('css')
 <link rel="stylesheet" href="{{asset('storage/css/contents.css')}}">
 <link rel="stylesheet" href="{{asset('storage/css/thumbnail.css')}}">
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

            <h2 class="content-hero-title">Article Thumbnails</h2>
            <p class="content-hero-description">
                Enhance your content layout by uploading post thumbnails to represent your articles.
            </p>

            <div class="d-flex flex-wrap gap-2 mt-4">
                <button class="btn btn-hero-primary"
                        data-bs-toggle="dialog"
                        data-bs-target="#createCategoryModal">
                    <i class="bi bi-upload me-2"></i>
                    Upload Thumbnail
                </button>
            </div>
        </div>

        <div class="content-hero-decoration">
            <div class="hero-circle hero-circle-one"></div>
            <div class="hero-circle hero-circle-two"></div>
            <div class="hero-folder">
                <i class="bi bi-camera"></i>
            </div>
            <span class="hero-floating-icon hero-icon-one">
                <i class="bi bi-file-earmark-image"></i>
            </span>
            <span class="hero-floating-icon hero-icon-two">
                <i class="bi bi-images"></i>
            </span>
        </div>
    </div>

</div>

<div class="container-fluid ui-section">
    <div class="row g-4">
        @forelse ($thumbnails as $thumbnail)
            <div class="col-6 col-md-4 col-xl-3 col-xxl-2">
                <div class="ui-thumb {{ $thumbnail->is_active ? '' : 'is-inactive' }}">

                    {{-- live preview of the template --}}
                    <div class="ui-thumb__media">
                        <img src="{{ $thumbnail->image_url }}" alt="Thumbnail template #{{ $thumbnail->id }}" loading="lazy">

                        @if ($thumbnail->overlay_color)
                            <span class="ui-thumb__tint"
                                  style="background: {{ $thumbnail->overlay_color }}; opacity: {{ $thumbnail->overlay_opacity / 100 }};"></span>
                        @endif

                        <div class="ui-thumb__text ui-thumb__text--{{ $thumbnail->text_position }}"
                             style="color: {{ $thumbnail->text_color }};">
                            <span>Your article title goes here</span>
                        </div>

                        <span class="ui-thumb__badge {{ $thumbnail->is_active ? '' : 'ui-thumb__badge--muted' }}">
                            {{ $thumbnail->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <div class="ui-thumb__body">
                        <div class="ui-thumb__meta">
                            <span class="ui-thumb__swatch" style="background: {{ $thumbnail->text_color }};" title="Text {{ $thumbnail->text_color }}"></span>
                            <span class="ui-thumb__swatch" style="background: {{ $thumbnail->overlay_color ?? 'transparent' }};" title="Overlay {{ $thumbnail->overlay_color ?? 'none' }}"></span>
                            <span class="ui-thumb__pos">{{ $thumbnail->text_position }}</span>
                            <span class="ui-thumb__dot"></span>
                            <span>{{ $thumbnail->articles_count }} articles</span>
                        </div>

                        <div class="ui-thumb__actions">
                            <button type="button" class="ui-thumb__btn" title="Edit style"
                                    data-bs-toggle="dialog" data-bs-target="#editThumbnailModal"
                                    data-id="{{ $thumbnail->id }}"
                                    data-src="{{ $thumbnail->image_url }}"
                                    data-text-color="{{ $thumbnail->text_color }}"
                                    data-text-position="{{ $thumbnail->text_position }}"
                                    data-overlay-color="{{ $thumbnail->overlay_color }}"
                                    data-overlay-opacity="{{ $thumbnail->overlay_opacity }}"
                                    data-active="{{ $thumbnail->is_active ? 1 : 0 }}">
                                <i class="bi bi-sliders"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="ui-empty">
                    <i class="bi bi-images"></i>
                    <h6>No thumbnail templates yet</h6>
                    <p>Upload your first template so writers can pick it for their articles.</p>
                </div>
            </div>
        @endforelse
    </div>

    @if ($thumbnails->hasPages())
        <div class="ui-pager">
            <span class="ui-pager__info">
                Showing {{ $thumbnails->firstItem() }}-{{ $thumbnails->lastItem() }}
                of {{ $thumbnails->total() }}
            </span>
            {{ $thumbnails->links() }}
        </div>
    @endif
</div>

{{-- MODAL UPLOAD THUMBNAIL TEMPLATE --}}
<dialog class="dialog dialog-slide-up ui-dialog ui-dialog--lg" id="createCategoryModal" data-bs-backdrop="static">
  <div class="dialog-header ui-dialog__header">
    <div class="ui-dialog__icon"><i class="bi bi-cloud-arrow-up"></i></div>
    <div class="ui-dialog__heading">
      <h1 class="dialog-title ui-dialog__title">Upload thumbnail template</h1>
      <span class="ui-dialog__sub">Writers can pick it later without uploading</span>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="dialog" aria-label="Close"></button>
  </div>

  <form action="/{{ auth()->user()->role }}/article/thumbnail/store" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="dialog-body ui-dialog__body">

      {{-- Drop zone + live preview --}}
      <div class="ui-field">
        <span class="ui-field__label">Image <span class="ui-field__req">*</span></span>

        <label class="ui-drop" id="thumbDrop" for="thumbFile">
          <div class="ui-drop__idle">
            <i class="bi bi-images"></i>
            <strong>Drag &amp; drop your image here</strong>
            <span>or <u>click to browse</u></span>
            <small>PNG or JPG, max 2 MB, 16:9 recommended</small>
          </div>

          <div class="ui-drop__preview ui-thumb__media">
            <img id="thumbImg" alt="Preview">
            <span class="ui-thumb__tint" id="thumbTint"></span>
            <div class="ui-thumb__text ui-thumb__text--center" id="thumbText">
              <span>Your article title goes here</span>
            </div>
            <button type="button" class="ui-drop__remove" id="thumbRemove" title="Remove image">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>
        </label>

        <input type="file" id="thumbFile" name="image" accept="image/png,image/jpeg" hidden>
        <span class="ui-field__error" id="thumbError" hidden></span>
        @error('image') <span class="ui-field__error">{{ $message }}</span> @enderror
      </div>

      {{-- Text color + position --}}
      <div class="ui-dialog__grid">
        <div class="ui-field">
          <label class="ui-field__label" for="thumbTextColor">Text color</label>
          <label class="ui-color">
            <input type="color" id="thumbTextColor" name="text_color" value="{{ old('text_color', '#FFFFFF') }}">
            <span id="thumbTextColorHex">#FFFFFF</span>
          </label>
        </div>

        <div class="ui-field">
          <span class="ui-field__label">Text position</span>
          <div class="ui-seg">
            <label><input type="radio" name="text_position" value="top"><span>Top</span></label>
            <label><input type="radio" name="text_position" value="center" checked><span>Center</span></label>
            <label><input type="radio" name="text_position" value="bottom"><span>Bottom</span></label>
          </div>
        </div>
      </div>

      {{-- Overlay --}}
      <div class="ui-field">
        <div class="ui-switch-row">
          <span class="ui-field__label">Color overlay</span>
          <label class="ui-switch">
            <input type="checkbox" id="thumbOverlayOn" checked>
            <span class="ui-switch__track"></span>
          </label>
        </div>

        <div class="ui-dialog__grid ui-dialog__grid--overlay" id="thumbOverlayFields">
          <label class="ui-color">
            <input type="color" id="thumbOverlayColor" name="overlay_color" value="{{ old('overlay_color', '#000000') }}">
            <span id="thumbOverlayColorHex">#000000</span>
          </label>

          <div class="ui-range">
            <input type="range" id="thumbOpacity" name="overlay_opacity" min="0" max="100" value="{{ old('overlay_opacity', 40) }}">
            <output id="thumbOpacityOut">40%</output>
          </div>
        </div>
        <span class="ui-field__hint">A dark overlay keeps white text readable on bright images.</span>
      </div>

      {{-- Active --}}
      <div class="ui-switch-row">
        <div>
          <span class="ui-field__label">Active</span>
          <span class="ui-field__hint d-block">Inactive templates are hidden from writers.</span>
        </div>
        <label class="ui-switch">
          <input type="hidden" name="is_active" value="0">
          <input type="checkbox" name="is_active" value="1" checked>
          <span class="ui-switch__track"></span>
        </label>
      </div>
    </div>

    <div class="dialog-footer ui-dialog__footer">
      <button type="button" class="ui-dialog__btn ui-dialog__btn--ghost" data-bs-dismiss="dialog">Cancel</button>
      <button type="submit" class="ui-dialog__btn ui-dialog__btn--primary" id="thumbSubmit" disabled>
        <i class="bi bi-check2"></i> Save template
      </button>
    </div>
  </form>
</dialog>

{{-- MODAL EDIT THUMBNAIL TEMPLATE --}}
<dialog class="dialog dialog-slide-up ui-dialog ui-dialog--lg" id="editThumbnailModal" data-bs-backdrop="static">
  <div class="dialog-header ui-dialog__header">
    <div class="ui-dialog__icon"><i class="bi bi-sliders"></i></div>
    <div class="ui-dialog__heading">
      <h1 class="dialog-title ui-dialog__title">Edit thumbnail template</h1>
      <span class="ui-dialog__sub">Change the style or replace the image</span>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="dialog" aria-label="Close"></button>
  </div>

  <form action="/{{ auth()->user()->role }}/article/thumbnail/update" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="edit_id" id="editThumbId">

    <div class="dialog-body ui-dialog__body">

      {{-- Image + live preview --}}
      <div class="ui-field">
        <span class="ui-field__label">Image <span class="ui-field__opt">Optional</span></span>

        <label class="ui-drop has-file" id="editThumbDrop" for="editThumbFile">
          <div class="ui-drop__preview ui-thumb__media">
            <img id="editThumbImg" alt="Preview">
            <span class="ui-thumb__tint" id="editThumbTint"></span>
            <div class="ui-thumb__text ui-thumb__text--center" id="editThumbText">
              <span>Your article title goes here</span>
            </div>

            <span class="ui-drop__replace"><i class="bi bi-arrow-repeat"></i> Click or drop to replace</span>

            <button type="button" class="ui-drop__remove" id="editThumbRevert" title="Use original image" hidden>
              <i class="bi bi-arrow-counterclockwise"></i>
            </button>
          </div>
        </label>

        <input type="file" id="editThumbFile" name="image" accept="image/png,image/jpeg" hidden>
        <span class="ui-field__hint">PNG or JPG, max 2 MB, 16:9 recommended. Leave empty to keep the current image.</span>
        <span class="ui-field__error" id="editThumbError" hidden></span>
        @error('image') <span class="ui-field__error">{{ $message }}</span> @enderror
      </div>

      {{-- Text color + position --}}
      <div class="ui-dialog__grid">
        <div class="ui-field">
          <label class="ui-field__label" for="editThumbTextColor">Text color</label>
          <label class="ui-color">
            <input type="color" id="editThumbTextColor" name="text_color" value="#FFFFFF">
            <span id="editThumbTextColorHex">#FFFFFF</span>
          </label>
        </div>

        <div class="ui-field">
          <span class="ui-field__label">Text position</span>
          <div class="ui-seg">
            <label><input type="radio" name="text_position" value="top"><span>Top</span></label>
            <label><input type="radio" name="text_position" value="center" checked><span>Center</span></label>
            <label><input type="radio" name="text_position" value="bottom"><span>Bottom</span></label>
          </div>
        </div>
      </div>

      {{-- Overlay --}}
      <div class="ui-field">
        <div class="ui-switch-row">
          <span class="ui-field__label">Color overlay</span>
          <label class="ui-switch">
            <input type="checkbox" id="editThumbOverlayOn">
            <span class="ui-switch__track"></span>
          </label>
        </div>

        <div class="ui-dialog__grid ui-dialog__grid--overlay" id="editThumbOverlayFields">
          <label class="ui-color">
            <input type="color" id="editThumbOverlayColor" name="overlay_color" value="#000000">
            <span id="editThumbOverlayColorHex">#000000</span>
          </label>

          <div class="ui-range">
            <input type="range" id="editThumbOpacity" name="overlay_opacity" min="0" max="100" value="40">
            <output id="editThumbOpacityOut">40%</output>
          </div>
        </div>
      </div>

      {{-- Active --}}
      <div class="ui-switch-row">
        <div>
          <span class="ui-field__label">Active</span>
          <span class="ui-field__hint d-block">Inactive templates are hidden from writers.</span>
        </div>
        <label class="ui-switch">
          <input type="hidden" name="is_active" value="0">
          <input type="checkbox" name="is_active" id="editThumbActive" value="1">
          <span class="ui-switch__track"></span>
        </label>
      </div>
    </div>

    <div class="dialog-footer ui-dialog__footer">
      <button type="button" class="ui-dialog__btn ui-dialog__btn--ghost" data-bs-dismiss="dialog">Cancel</button>
      <button type="submit" class="ui-dialog__btn ui-dialog__btn--primary">
        <i class="bi bi-check2"></i> Save changes
      </button>
    </div>
  </form>
</dialog>

@endsection

@section('javascript')
<script src="{{asset('storage/js/admin-article.js')}}"></script>
@endsection
