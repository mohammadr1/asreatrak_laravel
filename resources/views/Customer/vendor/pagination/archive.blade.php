@php
    $fa = fn ($v) => \Morilog\Jalali\CalendarUtils::convertNumbers((string) $v);
@endphp

@if ($paginator->hasPages())
<nav class="archive-pagination fade-in" role="navigation" aria-label="صفحه‌بندی">

    {{-- قبلی (در راست‌به‌چپ، فلش راست) --}}
    @if ($paginator->onFirstPage())
        <span class="page-btn page-btn--direction is-disabled" aria-disabled="true" aria-label="صفحه‌ی قبلی">
            <i class="bi bi-chevron-right" aria-hidden="true"></i>
            <span>قبلی</span>
        </span>
    @else
        <a class="page-btn page-btn--direction" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="صفحه‌ی قبلی">
            <i class="bi bi-chevron-right" aria-hidden="true"></i>
            <span>قبلی</span>
        </a>
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
        <a class="page-btn page-btn--direction" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="صفحه‌ی بعدی">
            <span>بعدی</span>
            <i class="bi bi-chevron-left" aria-hidden="true"></i>
        </a>
    @else
        <span class="page-btn page-btn--direction is-disabled" aria-disabled="true" aria-label="صفحه‌ی بعدی">
            <span>بعدی</span>
            <i class="bi bi-chevron-left" aria-hidden="true"></i>
        </span>
    @endif

</nav>
@endif