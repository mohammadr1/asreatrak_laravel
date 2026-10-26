
@if ($featuredNews->isNotEmpty())
    <div class="sidebar-widget featured-news-widget fade-in">
        <div class="widget-header">
            <i class="bi bi-star-fill"></i>
            اخبار ویژه
        </div>

        <div class="widget-body">
            <ul class="list-unstyled mb-0">
                @foreach ($featuredNews->take(2) as $news)
                    <li class="mb-2">
                        <a href="{{ route('news.show', $news) }}"
                        class="featured-news-item d-flex align-items-center gap-3 p-2 text-decoration-none">

                            <span class="featured-news-thumb flex-shrink-0 h-100">
                                <img
                                    src="{{ $news->featuredMedia?->variantUrl('watermarked') ?? asset('assets/images/default.png') }}"
                                    alt="{{ $news->title }}"
                                    loading="lazy"
                                >
                            </span>

                            <div class="min-w-0">
                                <div class="fw-semibold featured-news-title">
                                    {{ $news->title }}
                                </div>

                                @if ($news->published_at)
                                    <div class="featured-news-date mt-2">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ \Morilog\Jalali\Jalalian::fromDateTime($news->published_at)->format('Y/m/d') }}
                                    </div>
                                @endif
                            </div>

                        </a>
                    </li>
                @endforeach
            </ul>

        </div>
    </div>
@endif
