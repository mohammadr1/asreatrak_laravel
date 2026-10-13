@extends('customer.layouts.master-one-col')

@section('head-tag')
<link rel="stylesheet" href="{{ asset('assets/css/media_card_home_gallery.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/home.css') }}">

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
                    ?: asset('assets/images/header.png');
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
                    <div class="hero-slide-meta">
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
             اخبار ویژه — Full Width
        ===================== -->
    <div class="special-news-section fade-in">
        <div class="section-header">
            <span class="section-title"><i class="bi bi-star-fill me-1"></i>اخبار ویژه</span>
        </div>
        <div class="row g-3">
            @forelse($featuredNews as $news)
            <div class="col-md-3 col-sm-6">
                <div class="news-card">
                    <div class="news-card-img">

                        <a href="{{ route('news.show', $news) }}">
                            <img src="{{ $news->featuredMedia->variantUrl('watermarked') ?? asset('assets/images/header.png') }}"
                                alt="{{ $news->title }}">
                        </a>

                        <span class="news-card-badge badge-red">
                            {{ $news->categories->first()?->name ?? 'خبر ویژه' }}
                        </span>
                        <div class="overlay-title">
                            <h5>
                                <a href="{{ route('news.show', $news) }}">
                                    {{ $news->title }}
                                </a>
                            </h5>
                            <div class="news-meta">
                                @if($news->published_at)
                                <i class="bi bi-calendar3"></i>
                                {{ \Morilog\Jalali\Jalalian::fromDateTime($news->published_at)->format('Y/m/d') }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <p class="text-center py-3">
                    هنوز خبری به‌عنوان خبر ویژه انتخاب نشده است.
                </p>
            </div>
            @endforelse

        </div>
    </div>
    <!-- /اخبار ویژه -->

    <!-- =====================
             NEWS LIST (بدنه خبر)
        ===================== -->
    <div class="row g-4">
    <div class="col-lg-8">
        <div class="news-list-section fade-in">
            @forelse ($latestNews as $news)
                @php
                    $newsImage = $news->featuredMedia?->variantUrl($news->featured_media_variant ?: 'watermarked')
                        ?: $news->featuredMedia?->variantUrl('cropped')
                        ?: asset('assets/images/header.png');

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

            <!-- گزارش -->
            @if (!empty($reportNews))
            <div class="sidebar-news-section fade-in">
                <div class="section-header">
                    <span class="section-title"><i class="bi bi-file-earmark-text-fill me-1"></i>گزارش</span>
                </div>
                <div class="sidebar-news-card">
                    <img src="{{ $reportNews->featuredMedia->variantUrl('watermarked') ?? asset('assets/images/header.png') }}"
                        alt="{{ $reportNews->title }}">
                    <div class="card-overlay">
                        <h5><a href="#">{{ $reportNews->title }}</a></h5>
                        <div class="meta"><i class="bi bi-calendar3"></i>
                            {{ \Morilog\Jalali\Jalalian::fromDateTime($reportNews->published_at)->format('Y/m/d') }}
                            &nbsp;|&nbsp; <i class="bi bi-clock"></i>
                            {{ \Morilog\Jalali\Jalalian::fromDateTime($reportNews->published_at)->format('H:i') }}</div>
                    </div>
                </div>
                <div>
                    <p class="text-justify">
                        {{ \Illuminate\Support\Str::limit(strip_tags($reportNews->lead), 100, '...') }}
                    </p>
                    <a class="w-100" href="{{ route('news.show', $reportNews) }}">ادامه ←</a>
                </div>
            </div>
            @endif


            <!-- گفتگو -->
            @if (!empty($conversationNews))
            <div class="sidebar-news-section fade-in">
                <div class="section-header">
                    <span class="section-title"><i class="bi bi-chat-quote-fill me-1"></i>گفتگو</span>
                </div>
                <div class="sidebar-news-card">
                    <img src="{{ $conversationNews->featuredMedia->variantUrl('watermarked') ?? asset('assets/images/header.png') }}"
                        alt="{{ $conversationNews->title }}">
                    <div class="card-overlay">
                        <h5><a href="#">{{ $conversationNews->title }}</a></h5>
                        <div class="meta"><i class="bi bi-calendar3"></i>
                            {{ \Morilog\Jalali\Jalalian::fromDateTime($conversationNews->published_at)->format('Y/m/d') }}
                            &nbsp;|&nbsp; <i class="bi bi-clock"></i>
                            {{ \Morilog\Jalali\Jalalian::fromDateTime($conversationNews->published_at)->format('H:i') }}
                        </div>
                    </div>
                </div>
                <div>
                    <p style="text-align: justify;">
                        {{ \Illuminate\Support\Str::limit(strip_tags($conversationNews->lead), 100, '...') }}
                    </p>
                    <a class="w-100" href="{{ route('news.show', $conversationNews) }}">ادامه ←</a>
                </div>
            </div>
            @endif
            <!-- یادداشت -->
            @if (!empty($noteNews))
            <div class="sidebar-news-section fade-in">
                <div class="section-header">
                    <span class="section-title"><i class="bi bi-pencil-square me-1"></i>یادداشت</span>
                </div>
                <div class="sidebar-news-card">
                    <img src="{{ $noteNews->featuredMedia->variantUrl('watermarked') ?? asset('assets/images/header.png') }}"
                        alt="{{ $noteNews->title }}">
                    <div class="card-overlay">
                        <h5><a href="#">{{ $noteNews->title }}</a></h5>
                        <div class="meta"><i class="bi bi-calendar3"></i>
                            {{ \Morilog\Jalali\Jalalian::fromDateTime($noteNews->published_at)->format('Y/m/d') }}
                            &nbsp;|&nbsp; <i class="bi bi-clock"></i>
                            {{ \Morilog\Jalali\Jalalian::fromDateTime($noteNews->published_at)->format('H:i') }}</div>
                    </div>
                </div>
                <div>
                    <p style="text-align: justify;">
                        {{ \Illuminate\Support\Str::limit(strip_tags($noteNews->lead), 100, '...') }}
                    </p>
                    <a class="w-100" href="{{ route('news.show', $noteNews) }}">ادامه ←</a>
                </div>
            </div>
            @endif

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

    <!-- اوقات شرعی -->
    <div class="sidebar-widget fade-in">
        <div class="widget-header">
            <i class="bi bi-moon-stars-fill"></i>
            اوقات شرعی
        </div>
        <div class="widget-body">
            <div class="prayer-date-header" id="prayer-date-header">
                بارگذاری...
            </div>
            <div class="prayer-grid">
                <div class="prayer-item">
                    <i class="bi bi-sunrise-fill"></i>
                    <span class="prayer-name">اذان صبح</span>
                    <span class="prayer-time">۰۴:۴۸</span>
                </div>
                <div class="prayer-item">
                    <i class="bi bi-sun-fill"></i>
                    <span class="prayer-name">طلوع آفتاب</span>
                    <span class="prayer-time">۰۶:۱۵</span>
                </div>
                <div class="prayer-item highlight">
                    <i class="bi bi-brightness-high-fill"></i>
                    <span class="prayer-name">اذان ظهر</span>
                    <span class="prayer-time">۱۳:۰۲</span>
                </div>
                <div class="prayer-item">
                    <i class="bi bi-sunset-fill"></i>
                    <span class="prayer-name">اذان عصر</span>
                    <span class="prayer-time">۱۶:۴۸</span>
                </div>
                <div class="prayer-item">
                    <i class="bi bi-moon-fill"></i>
                    <span class="prayer-name">اذان مغرب</span>
                    <span class="prayer-time">۲۰:۲۴</span>
                </div>
                <div class="prayer-item">
                    <i class="bi bi-stars"></i>
                    <span class="prayer-name">اذان عشا</span>
                    <span class="prayer-time">۲۱:۳۵</span>
                </div>
            </div>
            <div class="mt-3 p-2 rounded">
                <i class="bi bi-geo-alt-fill"></i>
                بجنورد، خراسان شمالی — اوقات شرعی محاسبه‌شده
            </div>
        </div>
    </div>

    <!-- آب و هوا -->
    <div class="sidebar-widget fade-in">
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
    </div>

    <!-- اخبار پربازدید -->
    @include('Customer.partials.popular-news', ['popularNews' => $popularNews])

    <!-- Newsletter Subscribe -->
    <div class="sidebar-widget">
        <div class="p-4 rounded shadow-lg">
            <div class="text-center mb-3">
                <i class="bi bi-envelope-paper-heart-fill fs-1 text-warning"></i>
                <h6 class="fw-bold mt-2">خبرنامه رایگان</h6>
                <p class="">آخرین اخبار را در ایمیل خود دریافت کنید</p>
            </div>
            <div class="input-group mb-3" dir="rtl">
                <input type="email" class="form-control form-control-sm rounded-start" placeholder="ایمیل شما..."
                    aria-label="ایمیل شما..." required />
                <button class="btn btn-sm btn-primary rounded-end px-4">
                    عضویت
                </button>
            </div>
            <!-- پیغام خطا یا موفقیت -->
            <div id="successMessage" class="text-success d-none">شما با موفقیت عضو شدید!</div>
            <div id="errorMessage" class="text-danger d-none">لطفاً یک ایمیل معتبر وارد کنید!</div>
        </div>
    </div>

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
