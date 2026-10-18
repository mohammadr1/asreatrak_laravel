@extends('Customer.layouts.master-one-col')

@section('news-seo-title', e('آرشیف اخبار | ' . config('app.name')))

@section('head-tag')
<link rel="stylesheet" href="{{ asset('assets/css/style_archive.css') }}">
@endsection

@section('content')
@php
    use Illuminate\Support\Str;
    use Morilog\Jalali\CalendarUtils;
    use Morilog\Jalali\Jalalian;

    $fa = fn ($v) => CalendarUtils::convertNumbers((string) $v);
    $badges = ['badge-blue', 'badge-teal', 'badge-orange', 'badge-green', 'badge-purple', 'badge-red'];

    $hasFilters = $filters['q'] !== '' || $filters['category'] || $filters['from'] !== '' || $filters['to'] !== '';

    // آماده‌سازی داده‌ی کارت‌ها (برای نمای شبکه‌ای و لیستی، یک‌بار)
    $cards = collect($news->items())->map(function ($item) use ($fa, $badges) {
        $cat = $item->categories->firstWhere('pivot.is_top', true) ?? $item->categories->first();
        $d   = Jalalian::fromCarbon($item->published_at ?? $item->created_at);

        return (object) [
            'news'     => $item,
            'cat'      => $cat,
            'badge'    => $cat ? $badges[$cat->id % count($badges)] : $badges[0],
            'overtitle'=> $item->uptitle ?: $cat?->name,
            'date'     => $fa($d->format('Y/m/d')),
            'time'     => $fa($d->format('H:i')),
            'views'    => $fa(number_format($item->views_count ?? 0)),
        ];
    });
@endphp

<!-- =====================
           COL-8: Main Content
      ===================== -->
<div class="col-lg-8">

    <!-- FILTER BAR -->
    <form method="GET" action="{{ route('news.archive') }}" id="archiveFilterForm">
    <div class="filter-bar fade-in">
        <div class="filter-label"><i class="bi bi-funnel-fill"></i> فیلتر و جستجو</div>
        <div class="filter-row">
            <div class="filter-group">
                <label for="filterQ">جستجو در متن</label>
                <input type="text" id="filterQ" name="q" value="{{ $filters['q'] }}"
                       placeholder="عنوان یا کلیدواژه..." maxlength="100">
            </div>
            <div class="filter-group">
                <label for="filterCategory">دسته‌بندی</label>
                <select id="filterCategory" name="category">
                    <option value="">همه دسته‌ها</option>
                    @foreach($stats['categories'] as $c)
                        <option value="{{ $c->id }}" @selected($filters['category'] === $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group" style="position:relative">
                <label>از تاریخ</label>
                <div class="jalali-input-wrap" id="fromWrap">
                    <input type="text" id="fromDate" name="from" value="{{ $filters['from'] }}"
                           placeholder="انتخاب تاریخ" readonly class="jalali-input">
                    <i class="bi bi-calendar3"
                        style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text-muted);pointer-events:none;margin-top:10px"></i>
                </div>
                <div class="jdp" id="fromPicker"></div>
            </div>
            <div class="filter-group" style="position:relative">
                <label>تا تاریخ</label>
                <div class="jalali-input-wrap" id="toWrap">
                    <input type="text" id="toDate" name="to" value="{{ $filters['to'] }}"
                           placeholder="انتخاب تاریخ" readonly class="jalali-input">
                    <i class="bi bi-calendar3"
                        style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text-muted);pointer-events:none;margin-top:10px"></i>
                </div>
                <div class="jdp" id="toPicker"></div>
            </div>
            <button type="submit" class="btn-filter"><i class="bi bi-search"></i> جستجو</button>
            @if($hasFilters)
                <a href="{{ route('news.archive') }}" class="btn-reset">پاک کردن</a>
            @endif
        </div>

        <!-- Category Pills -->
        <div class="cat-pills">
            <a href="{{ request()->fullUrlWithoutQuery(['category', 'page']) }}"
               class="cat-pill {{ $filters['category'] ? '' : 'active' }}">
                همه <span class="count">{{ $fa(number_format($stats['total'])) }}</span>
            </a>
            @foreach($stats['categories'] as $c)
                <a href="{{ request()->fullUrlWithQuery(['category' => $c->id, 'page' => null]) }}"
                   class="cat-pill {{ $filters['category'] === $c->id ? 'active' : '' }} {{ $c->published_count === 0 ? 'is-empty' : '' }}">
                    {{ $c->name }} <span class="count">{{ $fa(number_format($c->published_count)) }}</span>
                </a>
            @endforeach
        </div>
    </div>
    </form>

    <!-- RESULTS BAR -->
    <div class="results-bar fade-in">
        <div class="results-count">
            @if($news->total())
                نمایش <strong>{{ $fa($news->firstItem()) }}–{{ $fa($news->lastItem()) }}</strong>
                از <strong>{{ $fa(number_format($news->total())) }}</strong> خبر
            @else
                <!-- خبری پیدا نشد -->
            @endif
        </div>
        <div class="d-flex align-items-center gap-2">
            <select class="sort-select" name="sort" form="archiveFilterForm" onchange="this.form.submit()" aria-label="مرتب‌سازی">
                <option value="latest"   @selected($filters['sort'] === 'latest')>جدیدترین</option>
                <option value="oldest"   @selected($filters['sort'] === 'oldest')>قدیمی‌ترین</option>
                <option value="popular"  @selected($filters['sort'] === 'popular')>پربازدیدترین</option>
                <option value="relevant" @selected($filters['sort'] === 'relevant')>مرتبط‌ترین</option>
            </select>
            <div class="view-toggle">
                <button type="button" class="view-btn active" id="gridBtn" onclick="setView('grid')" title="نمایش شبکه‌ای">
                    <i class="bi bi-grid-3x3-gap-fill"></i>
                </button>
                <button type="button" class="view-btn" id="listBtn" onclick="setView('list')" title="نمایش لیستی">
                    <i class="bi bi-list-ul"></i>
                </button>
            </div>
        </div>
    </div>

    @if($cards->isEmpty())
        <div class="text-center py-5 border" style="opacity:.7">
            <i class="bi bi-inbox d-block mb-2" style="font-size:3rem"></i>
            خبری با این مشخصات پیدا نشد. فیلترها را تغییر دهید.
        </div>
    @else

        <!-- GRID VIEW -->
        <div class="archive-grid" id="archiveGrid">
            @foreach($cards as $c)
                <div class="archive-card fade-in">
                    <div class="archive-card-img">
                        <img src="{{ $c->news->cover_url }}"
                             alt="{{ $c->news->featuredMedia?->alt ?: $c->news->title }}" loading="lazy">
                        @if($c->cat)
                            <span class="archive-card-badge {{ $c->badge }}">{{ $c->cat->name }}</span>
                        @endif
                    </div>
                    <div class="archive-card-body">
                        @if($c->overtitle)
                            <div class="archive-card-overtitle">{{ $c->overtitle }}</div>
                        @endif
                        <a href="{{ route('news.show', $c->news) }}" class="archive-card-title">{{ $c->news->title }}</a>
                        @if($c->news->lead)
                            <p class="archive-card-lead">{{ Str::limit($c->news->lead, 140) }}</p>
                        @endif
                        <div class="archive-card-meta">
                            <span><i class="bi bi-calendar3"></i> {{ $c->date }}</span>
                            <span><i class="bi bi-clock"></i> {{ $c->time }}</span>
                            <span><i class="bi bi-eye"></i> {{ $c->views }}</span>
                            <a href="{{ route('news.show', $c->news) }}" class="read-more">ادامه ←</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div><!-- /archive-grid -->

        <!-- LIST VIEW (hidden by default) -->
        <div class="archive-list" id="archiveList">
            @foreach($cards as $c)
                <div class="archive-list-item fade-in">
                    <div class="archive-list-img">
                        <img src="{{ $c->news->cover_url }}"
                             alt="{{ $c->news->featuredMedia?->alt ?: $c->news->title }}" loading="lazy">
                        @if($c->cat)
                            <span class="archive-list-badge {{ $c->badge }}">{{ $c->cat->name }}</span>
                        @endif
                    </div>
                    <div class="archive-list-body">
                        <div>
                            @if($c->overtitle)
                                <div class="archive-list-overtitle">{{ $c->overtitle }}</div>
                            @endif
                            <a href="{{ route('news.show', $c->news) }}" class="archive-list-title">{{ $c->news->title }}</a>
                            @if($c->news->lead)
                                <p class="archive-list-lead">{{ Str::limit($c->news->lead, 220) }}</p>
                            @endif
                        </div>
                        <div class="archive-list-meta">
                            <span><i class="bi bi-calendar3"></i> {{ $c->date }}</span>
                            <span><i class="bi bi-clock"></i> {{ $c->time }}</span>
                            <span><i class="bi bi-eye"></i> {{ $c->views }}</span>
                            <a href="{{ route('news.show', $c->news) }}" class="read-more">ادامه مطلب ←</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div><!-- /archive-list -->

        <!-- PAGINATION -->
        {{ $news->links('Customer.vendor.pagination.archive') }}

    @endif

</div>
<!-- /COL-8 -->

<!-- =====================
           COL-4: Sidebar
      ===================== -->
<div class="col-lg-4">

    <!-- اوقات شرعی -->
    @include('Customer.partials.prayer-times')

    <!-- اخبار پربازدید -->
    @include('Customer.partials.popular-news', ['popularNews' => $popularNews])

    <!-- Newsletter Subscribe -->
    @include('Customer.partials.social-join')


</div>
<!-- /COL-4 -->

@endsection

@section('scripts')
<script src="{{ asset('assets/js/script_archive.js') }}"></script>
@endsection