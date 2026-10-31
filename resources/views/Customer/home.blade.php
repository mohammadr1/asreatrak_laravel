@extends('customer.layouts.master-one-col')

@section('head-tag')

<style>
  /* start gallery media */
 .mg-loader {
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: .75rem;
    background: #111318;
    color: #cfd4da;
    text-align: center;
    padding: 1rem;
    font-size: .9rem;
}
.mg-spinner {
    width: 44px;
    height: 44px;
    border: 4px solid rgba(255, 255, 255, .15);
    border-top-color: #fff;
    border-radius: 50%;
    animation: mg-spin .8s linear infinite;
}
@keyframes mg-spin { to { transform: rotate(360deg); } }
.mg-loader a { color: #8ab4ff; }

@media (prefers-reduced-motion: reduce) {
    .mg-spinner { animation-duration: 2s; }
}

  /* end gallery media */



  /* start  */
  .news-row-img {
      position: relative;
  }

  .news-row-badges {
      position: absolute;
      top: 8px;
      inset-inline-start: 8px;
      display: flex;
      flex-wrap: wrap;
      gap: 4px;
      z-index: 2;
      pointer-events: none;
  }

  /* داخل ظرف، بج دیگر absolute نباشد تا کنار هم بنشینند */
  .news-row-badges .news-card-badge {
      position: static;
  }

  .news-row-badges .news-card-badge {
      font-size: .72rem;
      padding: .15rem .55rem;
  }
  /* end */

</style>
@endsection

@section('content')

<!-- =====================
           COL-8: Main Content
      ===================== -->
<div class="col-lg-8">

    <!-- TOP NEWS SLIDER -->
    <div class="hero-slider-wrap fade-in">
        <div class="hero-slider" id="heroSlider">

            @forelse($sliderNews as $index => $news)
            <div class="hero-slide {{ $index === 0 ? 'active' : '' }}">
                <div class="hero-slide-img">
                    @php
                    $image = $news->featuredMedia?->variantUrl($news->featured_media_variant ?: 'watermarked')
                    ?: $news->featuredMedia?->variantUrl('cropped')
                    ?: asset('assets/images/default.png');
                    @endphp
                    <img src="{{ $image }}" alt="{{ $news->title }}" />
                    <span class="category-badge">{{ $news->categories->first()?->name ?? 'خبر' }}</span>
                </div>
                <div class="hero-slide-body">
                    <div>
                        <div class="overtitle">{{ $news->post_type === 'video' ? 'خبر ویدیویی' : 'خبر' }}</div>
                        <h2><a href="{{ route('news.show', $news) }}">{{ $news->title }}</a></h2>
                        <p class="lead-text">{{ $news->lead }}</p>
                    </div>
                    <div class="hero-slide-meta w-100">
                        @if($news->published_at)<span><i class="bi bi-calendar3"></i>
                            {{ \Morilog\Jalali\Jalalian::fromDateTime($news->published_at)->format('Y/m/d') }}</span>@endif
                        @if($news->published_at)<span><i class="bi bi-clock"></i>
                            {{ \Morilog\Jalali\Jalalian::fromDateTime($news->published_at)->format('H:i') }}</span>@endif
                        <span><i class="bi bi-eye"></i> {{ number_format($news->views_count) }}</span>
                        <a href="{{ route('news.show', $news) }}" class="read-more">ادامه خبر ←</a>
                    </div>
                </div>
            </div>
            @empty
            <div class="hero-slide active">
                <div class="hero-slide-body">
                    <h2>هنوز خبری برای اسلایدر انتخاب نشده است.</h2>
                </div>
            </div>
            @endforelse
        </div><!-- /.hero-slider -->

        <!-- Slider Controls -->
        <div class="slider-controls" role="group" aria-label="کنترل اخبار اسلایدر">
            <button class="slider-btn" id="prevSlide" type="button" aria-label="خبر قبلی"><i class="bi bi-chevron-right"
                    aria-hidden="true"></i></button>
            <div class="slider-dots">
                @foreach($sliderNews as $index => $news)<button class="slider-dot {{ $index === 0 ? 'active' : '' }}"
                    type="button" onclick="goToSlide({{ $index }})"
                    aria-label="نمایش اسلاید {{ $index + 1 }}"></button>@endforeach
            </div>
            <button class="slider-btn" id="nextSlide" type="button" aria-label="خبر بعدی"><i class="bi bi-chevron-left"
                    aria-hidden="true"></i></button>
        </div>
    </div>
    <!-- /HERO SLIDER -->

    


    <!-- =====================
             NEWS LIST (بدنه خبر)
        ===================== -->
    <div class="row g-4">
        <div class="col-lg-8">
        <div class="news-list-section fade-in" style="margin-bottom: 25px; ">
            @forelse ($latestNews as $news)
                @php
                    $newsImage = $news->featuredMedia?->variantUrl($news->featured_media_variant ?: 'watermarked')
                        ?: $news->featuredMedia?->variantUrl('cropped')
                        ?: asset('assets/images/default.png');

                    $rowCategories = $news->categories->take(2);
                @endphp

                <div class="news-row-item">
                    <div class="news-row-img">
                        <a href="{{ route('news.show', $news) }}">
                            <img src="{{ $newsImage }}"
                                alt="{{ $news->featuredMedia?->alt ?: $news->title }}"
                                loading="lazy" decoding="async" />
                        </a>

                        <div class="news-row-badges">
                            @foreach ($news->categories->take(2) as $cat)
                                <span class="news-card-badge badge-red" style="font-size: 6px;">{{ $cat->name }}</span>
                            @endforeach
                        </div>
                    </div>

                    <div class="news-row-body">
                        @if ($news->uptitle)
                            <div class="news-row-overtitle">{{ $news->uptitle }}</div>
                        @endif

                        <a href="{{ route('news.show', $news) }}" class="news-row-title">{{ $news->title }}</a>
                        <p class="news-row-lead">
                            {{ \Illuminate\Support\Str::limit(strip_tags($news->lead), 140, '...') }}
                        </p>
                        <div class="news-row-meta">
                            @if ($news->published_at)
                                <span><i class="bi bi-calendar3"></i>
                                    {{ \Morilog\Jalali\Jalalian::fromDateTime($news->published_at)->format('Y/m/d') }}</span>
                                <span><i class="bi bi-clock"></i>
                                    {{ \Morilog\Jalali\Jalalian::fromDateTime($news->published_at)->format('H:i') }}</span>
                            @endif
                            <span><i class="bi bi-eye"></i> {{ number_format($news->views_count ?? 0) }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center text-muted py-3">هنوز خبری منتشر نشده است.</p>
            @endforelse


            @if ($latestNews->isNotEmpty())
                <div class="news-list-more" style="padding: 30px 0px 0 0; ">
                    <a href="{{ route('news.archive') }}" class="news-more-btn">
                        مشاهده‌ی آرشیو اخبار
                        <i class="bi bi-arrow-left"></i>
                    </a>
                </div>
            @endif
            
        </div>
    </div>

        <!-- =====================
               COL-4 SIDE SECTIONS (گزارش/گفتگو/یادداشت)
          ===================== -->
            
        <div class="col-lg-4">

            @php
                $sidebarNewsGroups = [
                    [
                        'title' => 'گزارش',
                        'icon' => 'bi-file-earmark-text-fill',
                        'items' => $reportNewsItems ?? collect(),
                    ],
                    [
                        'title' => 'گفتگو',
                        'icon' => 'bi-chat-quote-fill',
                        'items' => $conversationNewsItems ?? collect(),
                    ],
                    [
                        'title' => 'یادداشت',
                        'icon' => 'bi-pencil-square',
                        'items' => $noteNewsItems ?? collect(),
                    ],
                ];
            @endphp

            @foreach ($sidebarNewsGroups as $group)
                @php
                    $items = $group['items'];
                    $featured = $items->first();
                    $otherNews = $items->skip(1)->take(3);
                @endphp

                @if ($featured)
                    <section class="sidebar-news-section sidebar-news-modern fade-in">

                        <div class="sidebar-news-modern-header">
                            <div class="sidebar-news-modern-heading">
                                <span class="sidebar-news-modern-icon">
                                    <i class="bi {{ $group['icon'] }}" aria-hidden="true"></i>
                                </span>

                                <h2>{{ $group['title'] }}</h2>
                            </div>

                            <span class="sidebar-news-modern-label">آخرین مطالب</span>
                        </div>

                        {{-- خبر اصلی --}}
                        <article class="sidebar-featured-news">

                            @php
                                $featuredImage =
                                    $featured->featuredMedia?->variantUrl(
                                        $featured->featured_media_variant ?: 'watermarked'
                                    )
                                    ?: $featured->featuredMedia?->variantUrl('cropped')
                                    ?: asset('assets/images/default.png');
                            @endphp

                            <a
                                href="{{ route('news.show', $featured) }}"
                                class="sidebar-featured-image"
                                aria-label="{{ $featured->title }}"
                            >
                                <img
                                    src="{{ $featuredImage }}"
                                    alt="{{ $featured->featuredMedia?->alt ?: $featured->title }}"
                                    loading="lazy"
                                    decoding="async"
                                >

                                <span class="sidebar-featured-image-overlay"></span>

                                <span class="sidebar-featured-image-title">
                                    {{ $featured->title }}
                                </span>
                            </a>

                            <div class="sidebar-featured-content">
                                <p>
                                    {{ \Illuminate\Support\Str::limit(
                                        strip_tags($featured->lead ?? ''),
                                        150,
                                        '…'
                                    ) }}
                                </p>

                                <div class="sidebar-featured-bottom">
                                    @if ($featured->published_at)
                                        <span class="sidebar-featured-date">
                                            <i class="bi bi-calendar3" aria-hidden="true"></i>
                                            {{ \Morilog\Jalali\Jalalian::fromDateTime($featured->published_at)->format('Y/m/d') }}
                                        </span>
                                    @endif

                                    <a
                                        href="{{ route('news.show', $featured) }}"
                                        class="sidebar-featured-readmore"
                                    >
                                        ادامه خبر
                                        <i class="bi bi-arrow-left" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </div>
                        </article>

                        {{-- سه خبر مکمل، بدون عکس --}}
                        @if ($otherNews->isNotEmpty())
                            <div class="sidebar-compact-news">
                                @foreach ($otherNews as $item)
                                    <a
                                        href="{{ route('news.show', $item) }}"
                                        class="sidebar-compact-news-item"
                                    >
                                        <span class="sidebar-compact-news-dot"></span>

                                        <span class="sidebar-compact-news-title">
                                            {{ $item->title }}
                                        </span>

                                        <i
                                            class="bi bi-chevron-left sidebar-compact-news-arrow"
                                            aria-hidden="true"
                                        ></i>
                                    </a>
                                @endforeach
                            </div>
                        @endif

                    </section>
                @endif
            @endforeach

        </div>

    </div>
    <!-- /NEWS LIST + SIDE SECTIONS -->

    <!-- =====================
             چندرسانه
        ===================== -->

    @php
        $firstPhotoIdx = $multimediaItems->where('type', 'photo')->keys()->first();
        $firstVideoIdx = $multimediaItems->where('type', 'video')->keys()->first();
        $cardIdx = array_filter([$firstPhotoIdx, $firstVideoIdx], fn ($i) => $i !== null);
    @endphp

    <div class="multimedia-section fade-in">

        <div class="section-header">
            <span class="section-title">
                <i class="bi bi-collection-play-fill me-1"></i>
                چندرسانه
            </span>
        </div>

        <div class="media-tabs">
            <button type="button" class="media-tab active" onclick="setMediaFilter(this, 'all')">همه</button>
            <button type="button" class="media-tab" onclick="setMediaFilter(this, 'photo')"><i class="bi bi-image me-1"></i> تصویر</button>
            <button type="button" class="media-tab" onclick="setMediaFilter(this, 'video')"><i class="bi bi-play-circle me-1"></i> ویدیو</button>
        </div>

        <div class="row g-3">
            @forelse ($cardIdx as $i)
                @php $media = $multimediaItems[$i]; @endphp
                <div class="col-md-6" data-type="{{ $media['type'] }}">
                    <a href="#" class="text-decoration-none text-reset"
                      onclick="openMediaGallery({{ $i }}); return false;"
                      aria-label="{{ $media['title'] }}">
                        <div class="media-card {{ $media['type'] }}-card">
                            <img src="{{ $media['src'] }}" alt="{{ $media['alt'] }}" loading="lazy">

                            <div class="media-overlay">
                                <div class="media-meta">
                                    <i class="bi bi-{{ $media['type'] === 'video' ? 'play-circle' : 'image' }}"></i>
                                    {{ $media['type'] === 'video' ? 'ویدیو' : 'تصویر' }}
                                    &nbsp;|&nbsp;
                                    <i class="bi bi-eye"></i> {{ number_format($media['views']) }}
                                </div>
                                <div class="media-title">{{ $media['title'] }}</div>
                            </div>

                            @if ($media['type'] === 'video')
                                <div class="play-btn"><i class="bi bi-play-fill"></i></div>
                            @endif

                            <div class="media-type-badge">
                                <i class="bi bi-{{ $media['type'] === 'video' ? 'camera-video' : 'image' }} me-1"></i>
                                {{ $media['type'] === 'video' ? 'ویدیو' : 'تصویر' }}
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12">
                    <p class="text-muted text-center">در حال حاضر محتوای چندرسانه‌ای منتشرشده‌ای وجود ندارد.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- مودال گالری --}}
    <div class="modal fade" id="mediaGalleryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">گالری چندرسانه</h5>
                    <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="modal" aria-label="بستن"></button>
                </div>
                <div class="modal-body">

                    <div id="mgViewer" class="mb-3 d-none">
                        <div id="mgStage" class="ratio ratio-16x9 bg-dark rounded overflow-hidden"></div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <strong id="mgTitle"></strong>
                            <a id="mgLink" href="#" class="btn btn-sm btn-outline-primary">مشاهده خبر ←</a>
                        </div>
                    </div>

                    <div class="row g-2" id="mgGrid">
                        @foreach ($multimediaItems as $i => $m)
                            <div class="col-6 col-md-3 mg-item" data-type="{{ $m['type'] }}">
                                <a href="#" class="d-block position-relative" onclick="showMedia({{ $i }}); return false;">
                                    <img src="{{ $m['src'] }}" alt="{{ $m['alt'] }}" loading="lazy"
                                        class="img-fluid rounded w-100" style="aspect-ratio:4/3;object-fit:cover;">
                                    @if ($m['type'] === 'video')
                                        <span class="position-absolute top-50 start-50 translate-middle text-white fs-1">
                                            <i class="bi bi-play-circle-fill"></i>
                                        </span>
                                    @endif
                                </a>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- /چندرسانه -->

</div>
<!-- /COL-8 -->

<!-- =====================
           COL-4: Sidebar
      ===================== -->
<div class="col-lg-4">

    <!-- اخبار ویژه -->
    @include('Customer.partials.featured-news')


    <!-- آب و هوا -->
    <!-- <div class="sidebar-widget fade-in">
        <div class="widget-header">
            <i class="bi bi-cloud-sun-fill"></i>
            آب‌وهوای بجنورد
        </div>
        <div class="widget-body">
            <div class="weather-main">
                <div>
                    <div class="weather-city">بجنورد</div>
                    <div class="weather-icon" aria-hidden="true"><i class="bi bi-cloud-sun-fill"></i></div>
                    <div class="weather-desc">کمی ابری، خوشگذران</div>
                </div>
                <div class="text-end">
                    <div class="weather-temp">۲۲<sup>°C</sup></div>
                    <div>حس می‌شود ۲۰°</div>
                </div>
            </div>
            <div class="weather-details">
                <div class="weather-detail-item">
                    <i class="bi bi-droplet-half"></i>
                    <div>
                        <div class="weather-detail-label">رطوبت</div>
                        <div class="weather-detail-value">۴۵٪</div>
                    </div>
                </div>
                <div class="weather-detail-item">
                    <i class="bi bi-wind"></i>
                    <div>
                        <div class="weather-detail-label">وزش باد</div>
                        <div class="weather-detail-value">۱۸ km/h</div>
                    </div>
                </div>
                <div class="weather-detail-item">
                    <i class="bi bi-eye-fill"></i>
                    <div>
                        <div class="weather-detail-label">دید افق</div>
                        <div class="weather-detail-value">۱۰ km</div>
                    </div>
                </div>
                <div class="weather-detail-item">
                    <i class="bi bi-thermometer-half"></i>
                    <div>
                        <div class="weather-detail-label">دما (شب)</div>
                        <div class="weather-detail-value">۱۲°C</div>
                    </div>
                </div>
            </div>
            <div class="weather-forecast">
                <div class="forecast-item">
                    <div class="forecast-day">دوشنبه</div>
                    <div class="forecast-icon" aria-hidden="true"><i class="bi bi-cloud-fill"></i></div>
                    <div class="forecast-temp">۱۹°</div>
                </div>
                <div class="forecast-item">
                    <div class="forecast-day">سه‌شنبه</div>
                    <div class="forecast-icon" aria-hidden="true"><i class="bi bi-cloud-sun-fill"></i></div>
                    <div class="forecast-temp">۱۵°</div>
                </div>
                <div class="forecast-item">
                    <div class="forecast-day">چهارشنبه</div>
                    <div class="forecast-icon" aria-hidden="true"><i class="bi bi-cloud-rain-fill"></i></div>
                    <div class="forecast-temp">۱۳°</div>
                </div>
                <div class="forecast-item">
                    <div class="forecast-day">پنج‌شنبه</div>
                    <div class="forecast-icon">🌤️</div>
                    <div class="forecast-temp">۲۱°</div>
                </div>
                <div class="forecast-item">
                    <div class="forecast-day">جمعه</div>
                    <div class="forecast-icon" aria-hidden="true"><i class="bi bi-sun-fill"></i></div>
                    <div class="forecast-temp">۲۵°</div>
                </div>
            </div>
        </div>
    </div> -->
    
    <!-- کلینیک های خراسان شمالی -->
    @include('Customer.partials.clinics')

    <!-- اخبار پربازدید -->
    @include('Customer.partials.popular-news', ['popularNews' => $popularNews])

    
    <!-- اوقات شرعی -->
    @include('Customer.partials.prayer-times')


    <!-- Newsletter Subscribe -->
    @include('Customer.partials.social-join')

</div>
<!-- /COL-4 -->



<script src="{{ asset('assets/js/media_card_home_gallery.js') }}"></script>
@endsection




@section('scripts')
<script src="{{ asset('assets/js/main_slider.js') }}"></script>
<script src="{{ asset('assets/js/main_multimedia_filter.js') }}"></script>
<!-- start gallery modal -->
<script>
    const mediaItems = @json($multimediaItems);
    let currentFilter = 'all';
    let loadToken = 0;

    const modalEl = document.getElementById('mediaGalleryModal');

    // تب‌های صفحه + فیلتر مودال هم‌زمان
    function setMediaFilter(btn, type) {
        currentFilter = type;
        filterMedia(btn, type);
    }

    // مودال فقط نوع انتخاب‌شده را نشان بدهد
    function applyModalFilter() {
        document.querySelectorAll('#mgGrid .mg-item').forEach(el => {
            const hide = currentFilter !== 'all' && el.dataset.type !== currentFilter;
            el.classList.toggle('d-none', hide);
        });

        const titles = { all: 'گالری چندرسانه', photo: 'گالری تصاویر', video: 'گالری ویدیوها' };
        document.querySelector('#mediaGalleryModal .modal-title').textContent = titles[currentFilter];
    }

    function buildLoader(text, withSpinner = true) {
        const l = document.createElement('div');
        l.className = 'mg-loader';
        l.setAttribute('role', 'status');
        l.setAttribute('aria-live', 'polite');
        l.innerHTML = (withSpinner ? '<div class="mg-spinner" aria-hidden="true"></div>' : '') + '<div>' + text + '</div>';
        return l;
    }

    function showMedia(i) {
        const m = mediaItems[i];
        if (!m) return;

        const token = ++loadToken;
        const stage = document.getElementById('mgStage');
        stage.innerHTML = '';

        const isVideo = m.type === 'video' && m.aparat;
        const loader = buildLoader(isVideo
            ? 'در حال دریافت ویدیو از آپارات…<br><small>ممکن است چند لحظه طول بکشد</small>'
            : 'در حال بارگذاری تصویر…');
        stage.appendChild(loader);

        let timer = null;
        const done = () => {
            if (token !== loadToken) return;
            clearTimeout(timer);
            loader.remove();
        };

        if (isVideo) {
            const f = document.createElement('iframe');
            f.className = 'border-0';
            f.allowFullscreen = true;
            f.setAttribute('allow', 'autoplay; fullscreen');
            f.setAttribute('title', m.title);
            f.onload = done;
            f.src = m.aparat;
            stage.insertBefore(f, loader);

            // اگر دیر شد، راه جایگزین نشان بده
            timer = setTimeout(() => {
                if (token !== loadToken) return;
                loader.innerHTML = '<div>بارگذاری ویدیو طولانی شد.<br>' +
                    '<a href="' + m.link + '">مشاهده در صفحه‌ی خبر</a></div>';
            }, 15000);
        } else {
            const img = document.createElement('img');
            img.alt = m.alt;
            img.style.objectFit = 'contain';
            img.onload = done;
            img.onerror = () => { if (token === loadToken) loader.innerHTML = '<div>تصویر بارگذاری نشد</div>'; };
            img.src = m.src;
            stage.insertBefore(img, loader);
        }

        document.getElementById('mgTitle').textContent = m.title;
        document.getElementById('mgLink').href = m.link;
        document.getElementById('mgViewer').classList.remove('d-none');
        document.getElementById('mgViewer').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function openMediaGallery(i) {
        applyModalFilter();
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
        showMedia(i);
    }

    // با بستن مودال، ویدیو متوقف شود
    modalEl.addEventListener('hidden.bs.modal', () => {
        loadToken++;
        document.getElementById('mgStage').innerHTML = '';
        document.getElementById('mgViewer').classList.add('d-none');
    });
</script>

@endsection
