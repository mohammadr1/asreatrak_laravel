@inject('prayerService', 'App\Services\PrayerTimes')

@php
    $times = $prayerService->today();
    $fa = fn ($v) => \Morilog\Jalali\CalendarUtils::convertNumbers((string) $v);

    $items = [
        'Fajr'    => ['اذان صبح',   'bi-sunrise-fill'],
        'Sunrise' => ['طلوع آفتاب', 'bi-sun-fill'],
        'Dhuhr'   => ['اذان ظهر',   'bi-brightness-high-fill'],
        'Asr'     => ['اذان عصر',   'bi-sunset-fill'],
        'Maghrib' => ['اذان مغرب',  'bi-moon-fill'],
        'Isha'    => ['اذان عشا',   'bi-stars'],
    ];

    $today = \Morilog\Jalali\Jalalian::fromCarbon(now('Asia/Tehran'));
    $dateText = $fa($today->format('l j F Y'));
@endphp

<div class="sidebar-widget fade-in">
    <div class="widget-header">
        <i class="bi bi-moon-stars-fill"></i>
        اوقات شرعی
    </div>
    <div class="widget-body">
        <div class="prayer-date-header" id="prayer-date-header">{{ $dateText }}</div>

        @if ($times)
            <div class="prayer-grid" id="prayerGrid">
                @foreach ($items as $key => [$label, $icon])
                    <div class="prayer-item" data-time="{{ $times[$key] }}">
                        <i class="bi {{ $icon }}"></i>
                        <span class="prayer-name">{{ $label }}</span>
                        <span class="prayer-time">{{ $fa($times[$key]) }}</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-3 p-2 rounded">
                <i class="bi bi-geo-alt-fill"></i>
                بجنورد، خراسان شمالی
            </div>
        @else
            <p class="text-center py-3 mb-0">
                در حال حاضر امکان دریافت اوقات شرعی نیست.
            </p>
        @endif
    </div>
</div>

@once
    @push('scripts')
    <script>
    (function () {
        const grid = document.getElementById('prayerGrid');
        if (!grid) return;
        const items = [...grid.querySelectorAll('.prayer-item')];

        function tehranMinutes() {
            const t = new Date().toLocaleTimeString('en-GB', {
                timeZone: 'Asia/Tehran', hour: '2-digit', minute: '2-digit', hour12: false
            });
            const [h, m] = t.split(':').map(Number);
            return h * 60 + m;
        }

        function mark() {
            const now = tehranMinutes();
            const mins = items.map(el => {
                const [h, m] = el.dataset.time.split(':').map(Number);
                return h * 60 + m;
            });
            // اولین وقتی که هنوز نرسیده؛ بعد از عشا دوباره صبح فردا
            let next = mins.findIndex(v => v > now);
            if (next === -1) next = 0;
            items.forEach((el, i) => el.classList.toggle('highlight', i === next));
        }

        mark();
        setInterval(mark, 30000);
    })();
    </script>
    @endpush
@endonce