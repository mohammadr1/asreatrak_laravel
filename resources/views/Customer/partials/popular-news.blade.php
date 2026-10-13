<div class="sidebar-widget fade-in">
    <div class="widget-header">
        <i class="bi bi-fire"></i>
        پربازدیدترین‌ها
    </div>
    <div class="widget-body p-0">
        <ul class="list-unstyled m-0">
            @forelse ($popularNews as $i => $pn)
                <li>
                    <a href="{{ route('news.show', $pn) }}" class="d-flex align-items-start gap-3 p-3 hover-bg">
                        <span class="fw-bold text-white rounded-2 d-flex align-items-center justify-content-center flex-shrink-0">
                            {{ \Morilog\Jalali\CalendarUtils::convertNumbers((string) ($i + 1)) }}
                        </span>
                        <div>
                            <div>{{ $pn->title }}</div>
                            <div>
                                <i class="bi bi-eye me-1"></i>
                                {{ \Morilog\Jalali\CalendarUtils::convertNumbers(number_format($pn->views_count ?? 0)) }} بازدید
                            </div>
                        </div>
                    </a>
                </li>
            @empty
                <li class="p-3 text-center text-muted">هنوز خبری ثبت نشده است.</li>
            @endforelse
        </ul>
    </div>
</div>