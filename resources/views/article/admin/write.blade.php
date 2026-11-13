@extends('layouts.sidebar')
@php
$sideTitle = 'Article';
@endphp

@section('css')
<link rel="stylesheet" href="{{asset('storage/css/contents.css')}}">
<link rel="stylesheet" href="{{asset('storage/css/thumbnail.css')}}">
@endsection


@section('contents')

@php $role = auth()->user()->role; @endphp

@include('partials.breadcrumb', [
    'items' => [
        ['label' => 'Home',     'url' => "/{$role}/article/post", 'icon' => 'bi-house-door'],
        // ['label' => 'Articles', 'url' => "/{$role}/article"],
        ['label' => 'Write article'],
    ],
    'back' => ['url' => "/{$role}/article/post", 'label' => 'Back to articles'],
])

<form id="articleForm" method="POST" action="/{{auth()->user()->role}}/article/store-article" enctype="multipart/form-data">
    @csrf

    {{-- ==========================================
        1. BASIC INFORMATION
    ========================================== --}}

    <section class="ui-panel">
        <div class="ui-panel__head">
            <div class="ui-panel__icon"><i class="bi bi-card-heading"></i></div>
            <div class="ui-panel__heading">
                <h4 class="ui-panel__title">Basic information</h4>
                <span class="ui-panel__sub">Title, URL, category and a short summary</span>
            </div>
        </div>

        <div class="ui-panel__body">

            {{-- Title --}}
            <div class="ui-field">
                <label for="title" class="ui-field__label">
                    Article title <span class="ui-field__req">*</span>
                </label>
                <div class="ui-field__control">
                    <i class="bi bi-type ui-field__icon"></i>
                    <input type="text" id="title" name="title"
                        class="ui-field__input @error('title') is-invalid @enderror"
                        value="{{ old('title') }}" maxlength="120"
                        placeholder="Enter a clear, catchy title" autocomplete="off" required>
                </div>
                @error('title')
                    <span class="ui-field__error">{{ $message }}</span>
                @else
                    <span class="ui-field__hint ui-field__count"><span id="titleCount">0</span>/120</span>
                @enderror
            </div>

            {{-- Slug + Category --}}
            <div class="ui-form-grid">
                <div class="ui-field">
                    <label for="slug" class="ui-field__label">
                        Slug <span class="ui-field__opt">Auto</span>
                    </label>
                    <div class="ui-field__control">
                        <i class="bi bi-link-45deg ui-field__icon"></i>
                        <input type="text" id="slug" name="slug"
                            class="ui-field__input ui-field__input--action @error('slug') is-invalid @enderror"
                            value="{{ old('slug') }}" placeholder="article-url-slug" autocomplete="off">
                        <button type="button" class="ui-field__action" id="slugRegen" title="Regenerate from title">
                            <i class="bi bi-arrow-repeat"></i>
                        </button>
                    </div>
                    @error('slug')
                        <span class="ui-field__error">{{ $message }}</span>
                    @else
                        <span class="ui-field__hint">URL: <strong>/articles/<span id="slugPreview">your-article</span></strong></span>
                    @enderror
                </div>

                <div class="ui-field">
                    <label for="category_id" class="ui-field__label">
                        Category <span class="ui-field__req">*</span>
                    </label>
                    <div class="ui-field__control">
                        <i class="bi bi-folder2-open ui-field__icon"></i>
                        <select id="category_id" name="category_id"
                                class="ui-field__input ui-field__select @error('category_id') is-invalid @enderror" required>
                            <option value="">Select category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <i class="bi bi-chevron-down ui-field__chevron"></i>
                    </div>
                    @error('category_id')
                        <span class="ui-field__error">{{ $message }}</span>
                    @else
                        <span class="ui-field__hint">Pick the closest topic.</span>
                    @enderror
                </div>
            </div>

            {{-- Excerpt --}}
            <div class="ui-field">
                <label for="excerpt" class="ui-field__label">
                    Article excerpt <span class="ui-field__opt">Optional</span>
                </label>
                <textarea id="excerpt" name="excerpt" rows="3" maxlength="200"
                        class="ui-field__input ui-field__textarea @error('excerpt') is-invalid @enderror"
                        placeholder="A short summary shown in article lists and search results">{{ old('excerpt') }}</textarea>
                @error('excerpt')
                    <span class="ui-field__error">{{ $message }}</span>
                @else
                    <span class="ui-field__hint ui-field__count"><span id="excerptCount">0</span>/200</span>
                @enderror
            </div>

                {{-- Content --}}
                <div class="ui-field">
                    <span class="ui-field__label">Article content <span class="ui-field__req">*</span></span>

                    <div class="ui-editor">
                        <div id="editorToolbar" class="ui-editor__toolbar">

                            <div class="ui-editor__group">
                                <div class="ui-editor__select">
                                    <select id="headingSelect" aria-label="Text style">
                                        <option value="paragraph">Paragraph</option>
                                        <option value="h1">Heading 1</option>
                                        <option value="h2">Heading 2</option>
                                        <option value="h3">Heading 3</option>
                                        <option value="h4">Heading 4</option>
                                    </select>
                                    <i class="bi bi-chevron-down"></i>
                                </div>
                            </div>

                            <span class="ui-editor__divider"></span>

                            <div class="ui-editor__group">
                                <button type="button" data-action="bold" title="Bold"><i class="bi bi-type-bold"></i></button>
                                <button type="button" data-action="italic" title="Italic"><i class="bi bi-type-italic"></i></button>
                                <button type="button" data-action="strike" title="Strikethrough"><i class="bi bi-type-strikethrough"></i></button>
                            </div>

                            <span class="ui-editor__divider"></span>

                            <div class="ui-editor__group">
                                <button type="button" data-action="alignLeft" title="Align left"><i class="bi bi-text-left"></i></button>
                                <button type="button" data-action="alignCenter" title="Align center"><i class="bi bi-text-center"></i></button>
                                <button type="button" data-action="alignRight" title="Align right"><i class="bi bi-text-right"></i></button>
                                <button type="button" data-action="alignJustify" title="Justify"><i class="bi bi-justify"></i></button>
                            </div>

                            <span class="ui-editor__divider"></span>

                            <div class="ui-editor__group">
                                <button type="button" data-action="bulletList" title="Bullet list"><i class="bi bi-list-ul"></i></button>
                                <button type="button" data-action="orderedList" title="Numbered list"><i class="bi bi-list-ol"></i></button>
                                <button type="button" data-action="blockquote" title="Blockquote"><i class="bi bi-quote"></i></button>
                            </div>

                            <span class="ui-editor__divider"></span>

                            <div class="ui-editor__group">
                                <button type="button" data-action="link" title="Insert link"><i class="bi bi-link-45deg"></i></button>
                                <button type="button" data-action="unlink" title="Remove link"><i class="bi bi-link"></i><i class="bi bi-slash"></i></button>
                            </div>

                            <span class="ui-editor__divider ui-editor__divider--push"></span>

                            <div class="ui-editor__group">
                                <button type="button" data-action="undo" title="Undo"><i class="bi bi-arrow-counterclockwise"></i></button>
                                <button type="button" data-action="redo" title="Redo"><i class="bi bi-arrow-clockwise"></i></button>
                            </div>
                        </div>
                    <div id="articleEditor" data-content="{{ $article->content ?? '' }}"></div>
                </div>
                <input type="hidden" name="content" id="article-content">
            </div>
        </div>
    </section>


    {{-- ==========================================
        2. THUMBNAIL & MEDIA
    ========================================== --}}
    <section class="ui-panel" id="thumbPanel">
        <div class="ui-panel__head">
            <div class="ui-panel__icon"><i class="bi bi-image"></i></div>
            <div class="ui-panel__heading">
                <h4 class="ui-panel__title">Thumbnail &amp; media</h4>
                <span class="ui-panel__sub">Upload your own image or pick a ready-made template</span>
            </div>
        </div>

        <div class="ui-panel__body">

            {{-- Type switch --}}
            <div class="ui-field">
                <span class="ui-field__label">Thumbnail type</span>
                <div class="ui-seg ui-seg--icon ui-seg--auto">
                    <label>
                        <input type="radio" name="thumbnail_type" value="upload"
                            @checked(old('thumbnail_type', 'upload') === 'upload')>
                        <span><i class="bi bi-cloud-arrow-up"></i> Upload image</span>
                    </label>
                    <label>
                        <input type="radio" name="thumbnail_type" value="template"
                            @checked(old('thumbnail_type') === 'template')>
                        <span><i class="bi bi-grid-1x2"></i> Use template</span>
                    </label>
                </div>
            </div>

            {{-- IF UPLOAD --}}
            <div class="ui-field" id="thumbUploadPane">
                <span class="ui-field__label">Upload thumbnail <span class="ui-field__req">*</span></span>

                <label class="ui-drop" id="articleThumbDrop" for="articleThumbFile">
                    <div class="ui-drop__idle">
                        <i class="bi bi-images"></i>
                        <strong>Drag &amp; drop your image here</strong>
                        <span>or <u>click to browse</u></span>
                        <small>PNG or JPG, max 2 MB, 16:9 recommended</small>
                    </div>

                    <div class="ui-drop__preview ui-thumb__media">
                        <img id="articleThumbImg" alt="Thumbnail preview">
                        <span class="ui-drop__replace"><i class="bi bi-arrow-repeat"></i> Click or drop to replace</span>
                        <button type="button" class="ui-drop__remove" id="articleThumbRemove" title="Remove image">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </label>

                <input type="file" id="articleThumbFile" name="thumbnail" accept="image/png,image/jpeg" hidden>
                <span class="ui-field__error" id="articleThumbError" hidden></span>
                @error('thumbnail') <span class="ui-field__error">{{ $message }}</span> @enderror
            </div>

            {{-- IF TEMPLATE --}}
            <div class="ui-field" id="thumbTemplatePane" hidden>
                <span class="ui-field__label">
                    Thumbnail template <span class="ui-field__req">*</span>
                </span>

                @if ($thumbnails->isEmpty())
                    <div class="ui-empty">
                        <i class="bi bi-images"></i>
                        <h6>No templates available</h6>
                        <p>Ask an admin to upload thumbnail templates, or upload your own image.</p>
                    </div>
                @else
                    <div class="ui-pick-grid">
                        @foreach ($thumbnails as $thumbnail)
                            <label class="ui-pick">
                                <input type="radio" name="template_article_id" value="{{ $thumbnail->id }}"
                                    @checked(old('template_article_id') == $thumbnail->id)>

                                <div class="ui-thumb__media ui-pick__frame">
                                    <img src="{{ $thumbnail->image_url }}" alt="Template #{{ $thumbnail->id }}" loading="lazy">

                                    @if ($thumbnail->overlay_color)
                                        <span class="ui-thumb__tint"
                                            style="background: {{ $thumbnail->overlay_color }}; opacity: {{ $thumbnail->overlay_opacity / 100 }};"></span>
                                    @endif

                                    <div class="ui-thumb__text ui-thumb__text--{{ $thumbnail->text_position }}"
                                        style="color: {{ $thumbnail->text_color }};">
                                        <span data-title-preview>Your article title goes here</span>
                                    </div>
                                </div>

                                <span class="ui-pick__check"><i class="bi bi-check-lg"></i></span>
                            </label>
                        @endforeach
                    </div>
                    <span class="ui-field__hint">The text on each card updates as you type the article title.</span>
                @endif

                @error('template_article_id') <span class="ui-field__error">{{ $message }}</span> @enderror
            </div>
        </div>
    </section>

    {{-- ==========================================
        3. SEO SETTINGS
    ========================================== --}}
    <section class="ui-panel" id="seoPanel">
        <div class="ui-panel__head">
            <div class="ui-panel__icon"><i class="bi bi-search"></i></div>
            <div class="ui-panel__heading">
                <h4 class="ui-panel__title">SEO settings</h4>
                <span class="ui-panel__sub">Control how this article appears in search results</span>
            </div>
        </div>

        <div class="ui-panel__body">

            {{-- Live search preview --}}
            <div class="ui-serp" aria-label="Search result preview">
                <span class="ui-serp__label"><i class="bi bi-google"></i> Search preview</span>
                <div class="ui-serp__url">
                    {{ parse_url(config('app.url'), PHP_URL_HOST) }}
                    <span>› articles › <b id="serpSlug">your-article</b></span>
                </div>
                <div class="ui-serp__title" id="serpTitle">Your article title</div>
                <div class="ui-serp__desc" id="serpDesc">Your meta description will appear here.</div>
            </div>

            {{-- Meta title --}}
            <div class="ui-field">
                <label for="meta_title" class="ui-field__label">
                    Meta title <span class="ui-field__opt">Falls back to article title</span>
                </label>
                <div class="ui-field__control">
                    <i class="bi bi-type-h1 ui-field__icon"></i>
                    <input type="text" id="meta_title" name="meta_title"
                        class="ui-field__input @error('meta_title') is-invalid @enderror"
                        value="{{ old('meta_title') }}" maxlength="70" autocomplete="off"
                        placeholder="SEO title for search engines">
                </div>
                @error('meta_title')
                    <span class="ui-field__error">{{ $message }}</span>
                @else
                    <span class="ui-field__hint ui-field__count" id="metaTitleHint">
                        <span id="metaTitleCount">0</span>/70 · best between 30 and 60
                    </span>
                @enderror
            </div>

            {{-- Meta description --}}
            <div class="ui-field">
                <label for="meta_description" class="ui-field__label">
                    Meta description <span class="ui-field__opt">Falls back to excerpt</span>
                </label>
                <textarea id="meta_description" name="meta_description" rows="3" maxlength="170"
                        class="ui-field__input ui-field__textarea @error('meta_description') is-invalid @enderror"
                        placeholder="Write a short description for search engines">{{ old('meta_description') }}</textarea>
                @error('meta_description')
                    <span class="ui-field__error">{{ $message }}</span>
                @else
                    <span class="ui-field__hint ui-field__count" id="metaDescHint">
                        <span id="metaDescCount">0</span>/170 · best between 70 and 160
                    </span>
                @enderror
            </div>

            {{-- Keywords --}}
            <div class="ui-form-grid">
                <div class="ui-field">
                    <label for="focus_keyword" class="ui-field__label">Focus keyword</label>
                    <div class="ui-field__control">
                        <i class="bi bi-bullseye ui-field__icon"></i>
                        <input type="text" id="focus_keyword" name="focus_keyword"
                            class="ui-field__input @error('focus_keyword') is-invalid @enderror"
                            value="{{ old('focus_keyword') }}" autocomplete="off"
                            placeholder="Main keyword">
                    </div>
                    @error('focus_keyword')
                        <span class="ui-field__error">{{ $message }}</span>
                    @else
                        <span class="ui-field__hint">The one phrase people should find this article with.</span>
                    @enderror
                </div>

                <div class="ui-field">
                    <label for="secondary_keywords" class="ui-field__label">
                        Secondary keywords <span class="ui-field__opt">Optional</span>
                    </label>
                    <div class="ui-field__control">
                        <i class="bi bi-tags ui-field__icon"></i>
                        <input type="text" id="secondary_keywords" name="secondary_keywords"
                            class="ui-field__input @error('secondary_keywords') is-invalid @enderror"
                            value="{{ old('secondary_keywords') }}" autocomplete="off"
                            placeholder="keyword 1, keyword 2, keyword 3">
                    </div>
                    @error('secondary_keywords')
                        <span class="ui-field__error">{{ $message }}</span>
                    @else
                        <span class="ui-field__hint">Separate with commas.</span>
                    @enderror
                </div>
            </div>

            {{-- Keyword check --}}
            <div class="ui-checks" id="kwChecks" hidden>
                <span class="ui-check" data-check="title"><i class="bi bi-circle"></i> In title</span>
                <span class="ui-check" data-check="desc"><i class="bi bi-circle"></i> In description</span>
                <span class="ui-check" data-check="slug"><i class="bi bi-circle"></i> In URL</span>
            </div>

            {{-- Advanced --}}
            <details class="ui-advanced" @if(old('allow_indexing') === '0' || old('allow_following') === '0') open @endif>
                <summary>
                    <span><i class="bi bi-gear"></i> Advanced</span>
                    <i class="bi bi-chevron-down ui-advanced__chevron"></i>
                </summary>

                <div class="ui-advanced__body">
                    <div class="ui-switch-row">
                        <div>
                            <span class="ui-field__label">Allow search engine indexing</span>
                            <span class="ui-field__hint d-block">Turn off to keep this article out of search results.</span>
                        </div>
                        <label class="ui-switch">
                            <input type="hidden" name="allow_indexing" value="0">
                            <input type="checkbox" name="allow_indexing" value="1"
                                @checked(old('allow_indexing', 1) == 1)>
                            <span class="ui-switch__track"></span>
                        </label>
                    </div>

                    <div class="ui-switch-row">
                        <div>
                            <span class="ui-field__label">Allow search engine following</span>
                            <span class="ui-field__hint d-block">Lets search engines follow the links inside this article.</span>
                        </div>
                        <label class="ui-switch">
                            <input type="hidden" name="allow_following" value="0">
                            <input type="checkbox" name="allow_following" value="1"
                                @checked(old('allow_following', 1) == 1)>
                            <span class="ui-switch__track"></span>
                        </label>
                    </div>
                </div>
            </details>
        </div>
    </section>

    {{-- ==========================================
        4. GEO SETTINGS
    ========================================== --}}
    @php
        $takeaways = old('key_takeaways', $article->key_takeaways ?? []);
        $faqs      = old('faq', $article->faq ?? []);
        $sources   = old('source_references', $article->source_references ?? []);
        $schemaRaw = old('schema_data', isset($article) && $article->schema_data
            ? json_encode($article->schema_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            : '');
        $reviewed  = old('last_reviewed_at', isset($article) ? optional($article->last_reviewed_at)->format('Y-m-d') : '');
    @endphp

    <section class="ui-panel" id="geoPanel">
        <div class="ui-panel__head">
            <div class="ui-panel__icon"><i class="bi bi-stars"></i></div>
            <div class="ui-panel__heading">
                <h4 class="ui-panel__title">GEO settings</h4>
                <span class="ui-panel__sub">Help AI search engines understand and cite this article</span>
            </div>
        </div>

        <div class="ui-panel__body">

            {{-- Key takeaways --}}
            <div class="ui-field" data-repeater data-max="7">
                <span class="ui-field__label">Key takeaways <span class="ui-field__opt">Up to 7</span></span>

                <div class="ui-repeater__list" data-list>
                    @foreach ($takeaways as $text)
                        <div class="ui-repeater__row">
                            <span class="ui-repeater__num"></span>
                            <input type="text" name="key_takeaways[]" value="{{ $text }}" maxlength="200"
                                class="ui-field__input ui-field__input--plain"
                                placeholder="A main point readers should remember">
                            <button type="button" class="ui-repeater__remove" data-remove title="Remove"><i class="bi bi-x-lg"></i></button>
                        </div>
                    @endforeach
                </div>

                <button type="button" class="ui-repeater__add" data-add><i class="bi bi-plus-lg"></i> Add takeaway</button>
                <span class="ui-field__hint">Short, standalone sentences work best. AI answers often quote these.</span>
                @error('key_takeaways.*') <span class="ui-field__error">{{ $message }}</span> @enderror

                <template data-template>
                    <div class="ui-repeater__row">
                        <span class="ui-repeater__num"></span>
                        <input type="text" name="key_takeaways[]" maxlength="200"
                            class="ui-field__input ui-field__input--plain"
                            placeholder="A main point readers should remember">
                        <button type="button" class="ui-repeater__remove" data-remove title="Remove"><i class="bi bi-x-lg"></i></button>
                    </div>
                </template>
            </div>

            {{-- FAQ --}}
            <div class="ui-field" data-repeater data-max="10">
                <span class="ui-field__label">Frequently asked questions <span class="ui-field__opt">Up to 10</span></span>

                <div class="ui-repeater__list" data-list>
                    @foreach ($faqs as $i => $item)
                        <div class="ui-repeater__row ui-repeater__row--card">
                            <span class="ui-repeater__num"></span>
                            <div class="ui-repeater__fields">
                                <input type="text" name="faq[{{ $i }}][question]" value="{{ $item['question'] ?? '' }}" maxlength="200"
                                    class="ui-field__input ui-field__input--plain" placeholder="Question">
                                <textarea name="faq[{{ $i }}][answer]" rows="2" maxlength="1000"
                                        class="ui-field__input ui-field__textarea" placeholder="Answer">{{ $item['answer'] ?? '' }}</textarea>
                            </div>
                            <button type="button" class="ui-repeater__remove" data-remove title="Remove"><i class="bi bi-x-lg"></i></button>
                        </div>
                    @endforeach
                </div>

                <button type="button" class="ui-repeater__add" data-add><i class="bi bi-plus-lg"></i> Add question</button>
                <span class="ui-field__hint">Incomplete pairs (question without answer) are ignored when saving.</span>
                @error('faq.*') <span class="ui-field__error">{{ $message }}</span> @enderror

                <template data-template>
                    <div class="ui-repeater__row ui-repeater__row--card">
                        <span class="ui-repeater__num"></span>
                        <div class="ui-repeater__fields">
                            <input type="text" name="faq[__i__][question]" maxlength="200"
                                class="ui-field__input ui-field__input--plain" placeholder="Question">
                            <textarea name="faq[__i__][answer]" rows="2" maxlength="1000"
                                    class="ui-field__input ui-field__textarea" placeholder="Answer"></textarea>
                        </div>
                        <button type="button" class="ui-repeater__remove" data-remove title="Remove"><i class="bi bi-x-lg"></i></button>
                    </div>
                </template>
            </div>

            {{-- Sources --}}
            <div class="ui-field" data-repeater data-max="10">
                <span class="ui-field__label">Source references <span class="ui-field__opt">Up to 10</span></span>

                <div class="ui-repeater__list" data-list>
                    @foreach ($sources as $i => $src)
                        <div class="ui-repeater__row">
                            <span class="ui-repeater__num"></span>
                            <div class="ui-repeater__fields ui-repeater__fields--2">
                                <input type="text" name="source_references[{{ $i }}][title]" value="{{ $src['title'] ?? '' }}" maxlength="150"
                                    class="ui-field__input ui-field__input--plain" placeholder="Source name">
                                <input type="url" name="source_references[{{ $i }}][url]" value="{{ $src['url'] ?? '' }}" maxlength="500"
                                    class="ui-field__input ui-field__input--plain" placeholder="https://...">
                            </div>
                            <button type="button" class="ui-repeater__remove" data-remove title="Remove"><i class="bi bi-x-lg"></i></button>
                        </div>
                    @endforeach
                </div>

                <button type="button" class="ui-repeater__add" data-add><i class="bi bi-plus-lg"></i> Add source</button>
                <span class="ui-field__hint">Citing trustworthy sources makes the article more credible to AI engines.</span>
                @error('source_references.*') <span class="ui-field__error">{{ $message }}</span> @enderror

                <template data-template>
                    <div class="ui-repeater__row">
                        <span class="ui-repeater__num"></span>
                        <div class="ui-repeater__fields ui-repeater__fields--2">
                            <input type="text" name="source_references[__i__][title]" maxlength="150"
                                class="ui-field__input ui-field__input--plain" placeholder="Source name">
                            <input type="url" name="source_references[__i__][url]" maxlength="500"
                                class="ui-field__input ui-field__input--plain" placeholder="https://...">
                        </div>
                        <button type="button" class="ui-repeater__remove" data-remove title="Remove"><i class="bi bi-x-lg"></i></button>
                    </div>
                </template>
            </div>

            @php
                $schemaTypes = [
                    'Article'     => ['label' => 'Article',      'icon' => 'bi-file-earmark-text', 'desc' => 'General purpose, works for most content'],
                    'BlogPosting' => ['label' => 'Blog Posting', 'icon' => 'bi-pencil-square',     'desc' => 'Personal or opinion style blog posts'],
                    'NewsArticle' => ['label' => 'News Article', 'icon' => 'bi-newspaper',         'desc' => 'Timely news and reporting'],
                    'HowTo'       => ['label' => 'How To',       'icon' => 'bi-list-check',        'desc' => 'Step by step guides and tutorials'],
                ];
                $currentSchema = old('schema_type', $article->schema_type ?? 'Article');
            @endphp

            <div class="ui-field">
                <span class="ui-field__label">Structured data type</span>

                <div class="ui-choice-grid">
                    @foreach ($schemaTypes as $value => $type)
                        <label class="ui-choice">
                            <input type="radio" name="schema_type" value="{{ $value }}" @checked($currentSchema === $value)>
                            <span class="ui-choice__card">
                                <span class="ui-choice__icon"><i class="bi {{ $type['icon'] }}"></i></span>
                                <span class="ui-choice__text">
                                    <strong>{{ $type['label'] }}</strong>
                                    <small>{{ $type['desc'] }}</small>
                                </span>
                                <span class="ui-choice__check"><i class="bi bi-check-lg"></i></span>
                            </span>
                        </label>
                    @endforeach
                </div>

                @error('schema_type') <span class="ui-field__error">{{ $message }}</span> @enderror
            </div>
            {{-- Schema type + last reviewed --}}
            <div class="ui-form-grid">

                <div class="ui-field">
                    <label for="last_reviewed_at" class="ui-field__label">
                        Last reviewed <span class="ui-field__opt">Optional</span>
                    </label>
                    <div class="ui-field__control">
                        <i class="bi bi-calendar-check ui-field__icon"></i>
                        <input type="date" id="last_reviewed_at" name="last_reviewed_at" value="{{ $reviewed }}"
                            class="ui-field__input ui-field__input--action @error('last_reviewed_at') is-invalid @enderror">
                        <button type="button" class="ui-field__action ui-field__action--text" id="reviewedToday">Today</button>
                    </div>
                    @error('last_reviewed_at')
                        <span class="ui-field__error">{{ $message }}</span>
                    @else
                        <span class="ui-field__hint">Shows readers and AI that the content is up to date.</span>
                    @enderror
                </div>
            </div>

            {{-- Custom schema --}}
            <details class="ui-advanced" @if($schemaRaw !== '' || $errors->has('schema_data')) open @endif>
                <summary>
                    <span><i class="bi bi-braces"></i> Custom schema data (JSON)</span>
                    <i class="bi bi-chevron-down ui-advanced__chevron"></i>
                </summary>

                <div class="ui-advanced__body">
                    <div class="ui-field">
                        <textarea id="schema_data" name="schema_data" rows="8" spellcheck="false"
                                class="ui-field__input ui-field__textarea ui-field__code @error('schema_data') is-invalid @enderror"
                                placeholder='{ "author": { "@type": "Person", "name": "..." } }'>{{ $schemaRaw }}</textarea>

                        <div class="ui-json-bar">
                            <span class="ui-json-status" id="schemaStatus"></span>
                            <button type="button" class="ui-repeater__add ui-repeater__add--sm" id="schemaFormat">
                                <i class="bi bi-magic"></i> Format
                            </button>
                        </div>

                        @error('schema_data')
                            <span class="ui-field__error">{{ $message }}</span>
                        @else
                            <span class="ui-field__hint">Extra fields merged into the generated schema. Leave empty if unsure.</span>
                        @enderror
                    </div>
                </div>
            </details>
        </div>
    </section>


    {{-- ==========================================
        5. PUBLISHING SETTINGS
    ========================================== --}}
    @php
        $mode = old('publish_mode');
        if (!$mode) {
            if (!isset($article))                          $mode = 'now';
            elseif (!$article->is_published)               $mode = 'draft';
            elseif ($article->published_at?->isFuture())   $mode = 'schedule';
            else                                           $mode = 'now';
        }
        $scheduledAt = old('published_at', isset($article) ? $article->published_at?->format('Y-m-d\TH:i') : '');

        $publishModes = [
            'draft'    => ['label' => 'Draft',       'icon' => 'bi-pencil-square', 'desc' => 'Only you can see it, keep working on it'],
            'now'      => ['label' => 'Publish now', 'icon' => 'bi-rocket-takeoff', 'desc' => 'Visible to readers right away'],
            'schedule' => ['label' => 'Schedule',    'icon' => 'bi-calendar-event', 'desc' => 'Goes live automatically at a set time'],
        ];
    @endphp

    <section class="ui-panel" id="publishPanel">
        <div class="ui-panel__head">
            <div class="ui-panel__icon"><i class="bi bi-send"></i></div>
            <div class="ui-panel__heading">
                <h4 class="ui-panel__title">Publishing settings</h4>
                <span class="ui-panel__sub">Choose when this article goes live</span>
            </div>
        </div>

        <div class="ui-panel__body">
            <div class="ui-field">
                <span class="ui-field__label">Article status</span>

                <div class="ui-choice-grid">
                    @foreach ($publishModes as $value => $m)
                        <label class="ui-choice">
                            <input type="radio" name="publish_mode" value="{{ $value }}" @checked($mode === $value)>
                            <span class="ui-choice__card">
                                <span class="ui-choice__icon"><i class="bi {{ $m['icon'] }}"></i></span>
                                <span class="ui-choice__text">
                                    <strong>{{ $m['label'] }}</strong>
                                    <small>{{ $m['desc'] }}</small>
                                </span>
                                <span class="ui-choice__check"><i class="bi bi-check-lg"></i></span>
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('publish_mode') <span class="ui-field__error">{{ $message }}</span> @enderror
            </div>

            {{-- shown only for Schedule --}}
            <div class="ui-field" id="scheduleField" hidden>
                <label for="published_at" class="ui-field__label">
                    Publish date and time <span class="ui-field__req">*</span>
                </label>
                <div class="ui-field__control">
                    <i class="bi bi-clock ui-field__icon"></i>
                    <input type="datetime-local" id="published_at" name="published_at" value="{{ $scheduledAt }}"
                        class="ui-field__input @error('published_at') is-invalid @enderror">
                </div>
                @error('published_at')
                    <span class="ui-field__error">{{ $message }}</span>
                @else
                    <span class="ui-field__hint">Uses your server's timezone ({{ config('app.timezone') }}).</span>
                @enderror
            </div>
        </div>
    </section>

    {{-- ==========================================
        SUBMIT
    ========================================== --}}
    <div class="ui-actions">
        <span class="ui-actions__hint" id="actionHint">
            <i class="bi bi-info-circle"></i> <span>Ready when you are</span>
        </span>

        <div class="ui-actions__buttons">
            <button type="submit" name="action" value="draft"
                    class="ui-dialog__btn ui-dialog__btn--ghost" id="saveDraftBtn">
                <i class="bi bi-floppy"></i> Save as draft
            </button>
            <button type="submit" name="action" value="save"
                    class="ui-dialog__btn ui-dialog__btn--primary" id="saveMainBtn">
                <i class="bi bi-check2"></i> <span id="saveMainLabel">Publish article</span>
            </button>
        </div>
    </div>

</form>


@endsection

@section('javascript')
<script src="{{asset('storage/js/admin-article.js')}}"></script>
@endsection
