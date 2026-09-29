<x-filament-panels::page class="fi-resource-list-records-page news-dashboard">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-news.css') }}?v={{ filemtime(public_path('assets/css/admin-news.css')) }}">

    @if($this->canViewNewsStatistics())
        @php($statistics = $this->getNewsStatistics())

        @include('filament.resources.news.statistics', [
            'statistics' => $statistics['overview'],
            'id' => 'news-overview',
            'title' => 'نمای کلی تحریریه',
            'description' => 'آمار تمام اخبار قابل دسترسی شما؛ مستقل از جستجو و فیلترها.',
            'filtered' => false,
        ])
    @endif

    <div class="news-dashboard-table">
        <p class="news-filter-help">فیلترها هم‌زمان اعمال می‌شوند. بدون انتخاب ساعت، کل روز لحاظ می‌شود؛ با انتخاب ساعت، بازه تا پایان دقیقه انتخاب‌شده محاسبه می‌شود.</p>
        <x-filament-panels::resources.tabs />
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE, scopes: $this->getRenderHookScopes()) }}
        {{ $this->table }}
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_AFTER, scopes: $this->getRenderHookScopes()) }}
    </div>

    @if($this->canViewNewsStatistics())
        @include('filament.resources.news.statistics', [
            'statistics' => $statistics['filtered'],
            'id' => 'news-filtered',
            'title' => 'گزارش نتایج انتخاب‌شده',
            'description' => 'آمار تمام نتایج جستجو و فیلترهای اعمال‌شده، در همه صفحات جدول.',
            'filtered' => true,
        ])
    @endif
</x-filament-panels::page>
