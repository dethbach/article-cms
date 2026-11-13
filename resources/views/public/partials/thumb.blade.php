@php $tpl = $article->templateArticle; @endphp

<div class="ui-pthumb">
    @if ($article->thumbnail_type === 'template' && $tpl)
        <img src="{{ $tpl->image_url }}" alt="" loading="{{ $lazy ?? true ? 'lazy' : 'eager' }}">

        @if ($tpl->overlay_color)
            <span class="ui-pthumb__tint"
                  style="background: {{ $tpl->overlay_color }}; opacity: {{ $tpl->overlay_opacity / 100 }};"></span>
        @endif

        <div class="ui-pthumb__text ui-pthumb__text--{{ $tpl->text_position }}"
             style="color: {{ $tpl->text_color }};">
            <span>{{ $article->title }}</span>
        </div>
    @elseif ($article->thumbnail_path)
        <img src="{{ asset('storage/' . $article->thumbnail_path) }}"
             alt="{{ $article->title }}" loading="{{ $lazy ?? true ? 'lazy' : 'eager' }}">
    @else
        <div class="ui-pthumb__empty"><i class="bi bi-image"></i></div>
    @endif
</div>
