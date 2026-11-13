@props(['items' => [], 'back' => null])

<div class="ui-crumbs-bar">
    <nav class="ui-crumbs" aria-label="Breadcrumb">
        <ol class="ui-crumbs__list">
            @foreach ($items as $item)
                <li class="ui-crumbs__item">
                    @if (!$loop->last && !empty($item['url']))
                        <a href="{{ $item['url'] }}" class="ui-crumbs__link">
                            @if (!empty($item['icon'])) <i class="bi {{ $item['icon'] }}"></i> @endif
                            <span>{{ $item['label'] }}</span>
                        </a>
                        <i class="bi bi-chevron-right ui-crumbs__sep" aria-hidden="true"></i>
                    @else
                        <span class="ui-crumbs__current" aria-current="page">{{ $item['label'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>

    @if ($back)
        <a href="{{ $back['url'] }}" class="ui-crumbs__back">
            <i class="bi bi-arrow-left"></i> <span>{{ $back['label'] ?? 'Back' }}</span>
        </a>
    @endif
</div>
