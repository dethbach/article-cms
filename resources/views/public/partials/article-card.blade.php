@php $home = config('site.home_url'); @endphp

<article class="ui-lcard">
    @include('public.partials.thumb', ['article' => $article])

    <div class="ui-lcard__body">
        @if (($showCategory ?? true) && $article->category)
            <a href="{{ $home }}/category/{{ $article->category->slug }}" class="ui-ptag ui-ptag--soft">
                {{ $article->category->name }}
            </a>
        @endif

        <h3 class="ui-lcard__title">
            <a href="{{ $home }}/{{ $article->slug }}">{{ $article->title }}</a>
        </h3>

        @if ($article->excerpt)
            <p class="ui-lcard__excerpt">{{ $article->excerpt }}</p>
        @endif

        <div class="ui-fcard__meta">
            <span><i class="bi bi-person"></i> {{ $article->user->name ?? 'Team' }}</span>
            <span class="ui-fcard__dot"></span>
            <span>{{ $article->published_at->format('d M Y') }}</span>
            <span class="ui-fcard__dot"></span>
            <span>{{ $article->reading_time }} min</span>
        </div>
    </div>
</article>
