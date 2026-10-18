@php
    $fa = fn ($v) => \Morilog\Jalali\CalendarUtils::convertNumbers((string) $v);
@endphp

@if ($paginator->hasPages())
<nav class="archive-pagination fade-in" role="navigation" aria-label="صفحه‌بندی">

    {{-- قبلی (در راست‌به‌چپ، فلش راست) --}}
    @if ($paginator->onFirstPage())
        <button class="page-btn" disabled aria-label="صفحه‌ی قبل"><i class="bi bi-chevron-right"></i></button>
    @else
        <a class="page-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="صفحه‌ی قبل"><i class="bi bi-chevron-right"></i></a>
    @endif

    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="page-btn dots">{{ $element }}</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="page-btn active" aria-current="page">{{ $fa($page) }}</span>
                @else
                    <a class="page-btn" href="{{ $url }}">{{ $fa($page) }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- بعدی --}}
    @if ($paginator->hasMorePages())
        <a class="page-btn" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="صفحه‌ی بعد"><i class="bi bi-chevron-left"></i></a>
    @else
        <button class="page-btn" disabled aria-label="صفحه‌ی بعد"><i class="bi bi-chevron-left"></i></button>
    @endif

</nav>
@endif