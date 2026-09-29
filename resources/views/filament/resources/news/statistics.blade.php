<section class="news-insights {{ $filtered ? 'news-insights--filtered' : '' }}" aria-labelledby="{{ $id }}-title"
    x-data="{ expanded: false }" wire:key="{{ $id }}"
    wire:loading.class="news-insights--loading" wire:target="applyTableFilters,tableSearch,removeTableFilter,removeTableFilters,resetTableFiltersForm">
    <header class="news-insights-heading">
        <div>
            <span class="news-insights-eyebrow">{{ $filtered ? 'گزارش فیلترشده' : 'آمار اخبار' }}</span>
            <h2 id="{{ $id }}-title">{{ $title }}</h2>
        </div>
        <button type="button" class="news-section-toggle" x-on:click="expanded = ! expanded"
            aria-expanded="false" x-bind:aria-expanded="expanded.toString()" aria-controls="{{ $id }}-content">
            <span x-text="expanded ? 'جمع کردن' : 'باز کردن'">باز کردن</span>
            <span class="sr-only">{{ $title }}</span>
            <x-filament::icon icon="heroicon-m-chevron-down" aria-hidden="true" x-bind:class="{ 'news-toggle-expanded': expanded }" />
        </button>
    </header>

    <div id="{{ $id }}-content" x-show="expanded" x-cloak>
        <div class="news-insights-summary">
        <p>{{ $description }}</p>
        <div class="news-insights-total" @if($filtered) role="status" aria-live="polite" aria-atomic="true" @endif>
            <span>{{ $filtered ? 'خبر در نتایج' : 'مجموع اخبار' }}</span>
            <strong>{{ \Morilog\Jalali\CalendarUtils::convertNumbers(number_format($statistics['total'])) }}</strong>
        </div>
        </div>

    @if($filtered && $statistics['total'] === 0)
        <p class="news-insights-empty">خبری با این شرایط پیدا نشد. بازه زمانی یا فیلترها را تغییر دهید.</p>
    @endif

    @foreach($statistics['groups'] as $group)
        <div class="news-insights-group">
            <h3><x-filament::icon :icon="$group['icon']" aria-hidden="true" />{{ $group['title'] }}</h3>
            <dl class="news-stat-grid">
                @foreach($group['items'] as $item)
                    <div class="news-stat-card">
                        <dt>{{ $item['label'] }}</dt>
                        <dd>
                            <strong>{{ \Morilog\Jalali\CalendarUtils::convertNumbers(number_format($item['count'])) }}</strong>
                            <span>خبر</span>
                        </dd>
                        @if($filtered)
                            @php($percentage = $statistics['total'] ? round($item['count'] / $statistics['total'] * 100) : 0)
                            <div class="news-stat-share">
                                <span>{{ \Morilog\Jalali\CalendarUtils::convertNumbers((string) $percentage) }}٪ از نتایج</span>
                                <div class="news-stat-track" aria-hidden="true"><span style="width: {{ $percentage }}%"></span></div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </dl>
        </div>
    @endforeach
    </div>
</section>
