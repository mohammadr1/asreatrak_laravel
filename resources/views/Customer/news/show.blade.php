@extends('Customer.layouts.master-one-col')

@section('head-tag')
<link rel="stylesheet" href="{{ asset('assets/css/style_single.css') }}">
<title>{{ env('APP_NAME') }}</title>
@endsection

@section('content')


@php
    use Morilog\Jalali\Jalalian;
    use Morilog\Jalali\CalendarUtils;

    $heroUrl = $news->featuredMedia?->variantUrl($news->featured_media_variant ?: 'watermarked')
        ?: $news->featuredMedia?->variantUrl('cropped')
        ?: $news->featuredMedia?->variantUrl('original');

    $shareUrl   = route('news.show', $news);
    $shareTitle = $news->title;
    $topCat     = $news->categories->firstWhere('pivot.is_top', true) ?? $news->categories->first();

    $fa = fn ($v) => CalendarUtils::convertNumbers((string) $v);
    $jDate = $news->published_at ? $fa(Jalalian::fromCarbon($news->published_at)->format('Y/m/d')) : null;
    $jTime = $news->published_at ? $fa(Jalalian::fromCarbon($news->published_at)->format('H:i')) : null;

    $reporterName = $news->reporter?->name;
@endphp

<!-- =====================
           COL-8: Article Content
      ===================== -->
<div class="col-lg-8">

    <!-- start print article -->
    
    <div class="print-area">

        <div class="print-header print-only">
            <img src="{{ asset('assets/images/logo.png') }}" alt="خبرگزاری عصر اترک" class="print-logo">
            <div class="print-title">خبرگزاری عصر اترک</div>
            <div class="print-sub">پایگاه خبری تحلیلی خراسان شمالی</div>
        </div>

        <div class="print-divider"></div>

        <div class="print-content">

            {{-- رسانه‌ی اصلی: ویدیو یا تصویر --}}
            @if ($embedUrl)
                <div class="article-featured-image">
                    <div class="ratio ratio-16x9 bg-dark rounded overflow-hidden">
                        <iframe src="{{ $embedUrl }}" title="{{ $news->title }}"
                                allowfullscreen loading="lazy"
                                allow="autoplay; fullscreen" class="border-0"></iframe>
                    </div>
                </div>
            @elseif ($heroUrl)
                <div class="article-featured-image">
                    <img class="w-100 rounded" src="{{ $heroUrl }}" alt="{{ $news->featuredMedia->alt ?: $news->title }}">
                    <div class="fi-gradient"></div>
                    @if ($topCat)
                        <a href="{{ route('category.show', $topCat->slug) }}" class="article-category-badge">{{ $topCat->name }}</a>
                    @endif
                </div>
                @if ($news->featuredMedia->caption || $news->featuredMedia->copyright)
                    <div class="article-hero-caption">
                        <i class="bi bi-camera me-1"></i>
                        {{ $news->featuredMedia->caption }}
                        @if ($news->featuredMedia->copyright) — {{ $news->featuredMedia->copyright }} @endif
                    </div>
                @endif
            @endif

            <div class="article-header">
                @if ($topCat && ($embedUrl || ! $heroUrl))
                    <a href="{{ route('category.show', $topCat->slug) }}" class="article-category-badge">{{ $topCat->name }}</a>
                @endif

                @if ($news->uptitle)
                    <p class="article-kicker">{{ $news->uptitle }}</p>
                @endif

                <h1 class="article-title">{{ $news->title }}</h1>

                @if ($news->lead)
                    <p class="article-lead">{{ $news->lead }}</p>
                @endif

                <div class="article-meta-row">
                    @if ($reporterName)
                        <div class="author">
                            <span class="author-avatar d-inline-flex align-items-center justify-content-center fw-bold">
                                {{ mb_substr($reporterName, 0, 1) }}
                            </span>
                            <div>
                                <div style="font-size:.82rem;font-weight:600">{{ $reporterName }}</div>
                                <div style="font-size:.74rem;opacity:.65">خبرنگار</div>
                            </div>
                        </div>
                    @endif

                    @if ($jDate)
                        <span><i class="bi bi-calendar3"></i> {{ $jDate }}</span>
                        <span><i class="bi bi-clock"></i> {{ $jTime }}</span>
                    @endif
                    <span><i class="bi bi-eye"></i> {{ $fa(number_format($news->views_count ?? 0)) }} بازدید</span>

                    <div class="article-share ms-auto">
                        <span class="share-label">اشتراک‌گذاری:</span>
                        <a href="https://t.me/share/url?url={{ urlencode($shareUrl) }}&text={{ urlencode($shareTitle) }}"
                           class="share-btn telegram" title="تلگرام" target="_blank" rel="noopener"><i class="bi bi-telegram"></i></a>
                        <a href="https://wa.me/?text={{ urlencode($shareTitle . ' ' . $shareUrl) }}"
                           class="share-btn whatsapp" title="واتساپ" target="_blank" rel="noopener"><i class="bi bi-whatsapp"></i></a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode($shareUrl) }}&text={{ urlencode($shareTitle) }}"
                           class="share-btn twitter" title="ایکس" target="_blank" rel="noopener"><i class="bi bi-twitter-x"></i></a>
                        <button type="button" class="share-btn copy" title="کپی لینک" onclick="copyLink()"><i class="bi bi-link-45deg"></i></button>
                    </div>
                </div>
            </div>

            {{-- متن خبر --}}
            <div class="article-body">
                {!! $news->renderedContent() !!}
            </div>


            @if ($news->tags->isNotEmpty())
                <div class="article-tags">
                    <span class="tag-label"><i class="bi bi-tags me-1"></i>برچسب‌ها:</span>
                    @foreach ($news->tags as $tag)
                        <a href="{{ route('tag.show', $tag->slug) }}" class="tag-pill">{{ $tag->name }}</a>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
    <!-- end print article -->

    <!-- Actions Bar -->
    <div class="article-actions-bar">
        <a type="button" class="action-btn" onclick="window.print()"><i class="bi bi-printer"></i> چاپ خبر</a>
        <a type="button" class="action-btn" onclick="copyLink()"><i class="bi bi-link-45deg"></i> کپی لینک</a>
        <!-- <a href="#" class="action-btn" id="shareBtn"><i class="bi bi-send"></i> ارسال به دوستان</a> -->

        <div class="d-flex align-items-center gap-2 ms-auto">
            <span class="share-label" style="font-size:.8rem;opacity:.7">اشتراک‌گذاری:</span>
            <a href="https://t.me/share/url?url={{ urlencode($shareUrl) }}&text={{ urlencode($shareTitle) }}" class="share-btn telegram" title="تلگرام" target="_blank" rel="noopener"><i class="bi bi-telegram"></i></a>
            <a href="https://wa.me/?text={{ urlencode($shareTitle . ' ' . $shareUrl) }}" class="share-btn whatsapp" title="واتساپ" target="_blank" rel="noopener"><i class="bi bi-whatsapp"></i></a>
            <a href="https://twitter.com/intent/tweet?url={{ urlencode($shareUrl) }}&text={{ urlencode($shareTitle) }}" class="share-btn twitter" title="ایکس" target="_blank" rel="noopener"><i class="bi bi-twitter-x"></i></a>
        </div>
    </div>


    <!-- =====================
             Related News
        ===================== -->
    <div class="related-section mt-4">
        <div class="section-header">
            <span class="section-title"><i class="bi bi-newspaper me-1"></i> اخبار مرتبط</span>
        </div>

        <div>
            @forelse ($news->relatedNews as $related)
                @php
                    $rImg = $related->featuredMedia?->variantUrl($related->featured_media_variant ?: 'watermarked')
                        ?: $related->featuredMedia?->variantUrl('cropped')
                        ?: asset('assets/images/header.png');
                @endphp
                <a href="{{ route('news.show', $related) }}" class="related-card">
                    <img src="{{ $rImg }}" alt="{{ $related->featuredMedia?->alt ?: $related->title }}"
                         class="related-card-img" loading="lazy">
                    <div class="related-card-body">
                        <div class="related-card-cat">{{ $related->categories->first()?->name ?? 'عمومی' }}</div>
                        <div class="related-card-title">{{ $related->title }}</div>
                        <div class="related-card-meta">
                            @if ($related->jalali_published_at)
                                <i class="bi bi-clock me-1"></i>{{ $fa($related->jalali_published_at) }}
                            @endif
                            &nbsp;|&nbsp;
                            <i class="bi bi-eye me-1"></i>{{ $fa(number_format($related->views_count ?? 0)) }} بازدید
                        </div>
                    </div>
                </a>
            @empty
                <div class="text-center py-4"><span>خبر مرتبطی وجود ندارد.</span></div>
            @endforelse
        </div>
    </div>
    <!-- /Related News -->

    <!-- =====================
             Comments Section
        ===================== -->
    <div class="comments-section">
        <div class="section-header">
            <span class="section-title"><i class="bi bi-chat-left-text-fill me-1"></i>نظرات کاربران <span>(3 نظر)
                </span></span>
        </div>

        <!-- Comment 1 -->
        <div class="comment-box">
            <div class="comment-header">
                <div class="comment-avatar">م</div>
                <div class="comment-name">محمد رضایی</div>
                <div class="comment-date"><i class="bi bi-clock me-1"></i>۱۴۰۳/۰۲/۱۵ — ۱۶:۱۰</div>
            </div>
            <div class="comment-text">
                امیدوارم این نشست‌ها نتیجه عملی داشته باشد. استان ما نیاز جدی به توسعه دارد
                و مردم منتظر اقدامات ملموس هستند.
            </div>
        </div>

        <!-- Comment 2 -->
        <div class="comment-box">
            <div class="comment-header">
                <div class="comment-avatar">ز</div>
                <div class="comment-name">زهرا احمدی</div>
                <div class="comment-date"><i class="bi bi-clock me-1"></i>۱۴۰۳/۰۲/۱۵ — ۱۷:۳۵</div>
            </div>
            <div class="comment-text">
                خوشحال می‌شوم که پیگیری این مصوبات به صورت منظم اطلاع‌رسانی شود.
                شفافیت در اجرای تصمیمات بسیار مهم است.
            </div>
        </div>

        <!-- Comment 3 -->
        <div class="comment-box">
            <div class="comment-header">
                <div class="comment-avatar">ع</div>
                <div class="comment-name">علی نوری</div>
                <div class="comment-date"><i class="bi bi-clock me-1"></i>۱۴۰۳/۰۲/۱۵ — ۱۹:۰۰</div>
            </div>
            <div class="comment-text">
                موقعیت استراتژیک استان در مسیر کریدور شمال–جنوب واقعاً فرصت بزرگی است.
                باید از این ظرفیت به درستی استفاده شود.
            </div>
        </div>

        <!-- Comment Form -->
        <div class="comment-form-section mt-3">
            <h6>
                <i class="bi bi-pencil-square me-2"></i>ثبت نظر
            </h6>
            <p>
                <i class="bi bi-info-circle me-1"></i>
                نظرات پس از تأیید توسط مدیریت سایت منتشر می‌شوند. لطفاً نظرات خود را با ادب و احترام بیان فرمایید.
            </p>
            <div class="row g-3">
                <div class="col-md-6">
                    <input type="text" class="form-control form-control-sm" placeholder="نام و نام‌خانوادگی *" />
                </div>
                <div class="col-md-6">
                    <input type="email" class="form-control form-control-sm" placeholder="ایمیل (منتشر نمی‌شود)" />
                </div>
                <div class="col-12">
                    <textarea class="form-control form-control-sm" rows="4"
                        placeholder="متن نظر خود را بنویسید..."></textarea>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <input type="checkbox" id="notifyMe" class="form-check-input" />
                        <label for="notifyMe">در صورت پاسخ به من اطلاع بده</label>
                    </div>
                    <button class="btn-submit-comment">
                        <i class="bi bi-send me-1"></i> ارسال نظر
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- /Comments Section -->

</div>
<!-- /COL-8 -->


<!-- =====================
           COL-4: Sidebar
      ===================== -->
<div class="col-lg-4">

    <!-- News Info Box -->
        <div class="sidebar-widget">
        <div class="widget-header"><i class="bi bi-info-circle-fill"></i> اطلاعات خبر</div>
        <div class="widget-body">
            <div class="news-code-box" style="border:none;border-radius:0;margin:0">
                @if ($news->news_code)
                    <div class="code-row"><span>کد خبر</span><strong>{{ $news->news_code }}</strong></div>
                @endif
                @if ($jDate)
                    <div class="code-row"><span>تاریخ انتشار</span><strong>{{ $jDate }}</strong></div>
                    <div class="code-row"><span>ساعت انتشار</span><strong>{{ $jTime }}</strong></div>
                @endif
                @if ($news->categories->isNotEmpty())
                    <div class="code-row"><span>دسته‌بندی</span><strong>{{ $news->categories->pluck('name')->implode(' — ') }}</strong></div>
                @endif
                <div class="code-row"><span>تعداد بازدید</span><strong>{{ $fa(number_format($news->views_count ?? 0)) }}</strong></div>
                @if ($reporterName)
                    <div class="code-row"><span>خبرنگار</span><strong>{{ $reporterName }}</strong></div>
                @endif
            </div>
        </div>
    </div>

    <!-- اوقات شرعی -->
    @include('Customer.partials.prayer-times')


    <!-- آب و هوا -->
    <div class="sidebar-widget">
        <div class="widget-header">
            <i class="bi bi-cloud-sun-fill"></i>
            آب‌وهوای بجنورد
        </div>
        <div class="widget-body">
            <div class="weather-main">
                <div>
                    <div class="weather-city">بجنورد</div>
                    <div class="weather-icon">🌤️</div>
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
                    <div class="forecast-icon">☁️</div>
                    <div class="forecast-temp">۱۹°</div>
                </div>
                <div class="forecast-item">
                    <div class="forecast-day">سه‌شنبه</div>
                    <div class="forecast-icon">🌦️</div>
                    <div class="forecast-temp">۱۵°</div>
                </div>
                <div class="forecast-item">
                    <div class="forecast-day">چهارشنبه</div>
                    <div class="forecast-icon">🌧️</div>
                    <div class="forecast-temp">۱۳°</div>
                </div>
                <div class="forecast-item">
                    <div class="forecast-day">پنج‌شنبه</div>
                    <div class="forecast-icon">🌤️</div>
                    <div class="forecast-temp">۲۱°</div>
                </div>
                <div class="forecast-item">
                    <div class="forecast-day">جمعه</div>
                    <div class="forecast-icon">☀️</div>
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



@endsection



@section('scripts')

<!-- کپی لینک کوتاه شده خبر -->
<script>
    const shareData = {
        title: @json($news->title),
        url: @json(route('news.show', $news)),
    };

    async function copyLink() {
        try {
            await navigator.clipboard.writeText(shareData.url);
        } catch (e) {
            const t = document.createElement('textarea');
            t.value = shareData.url;
            document.body.appendChild(t);
            t.select();
            document.execCommand('copy');
            t.remove();
        }
        alert('لینک خبر کپی شد');
    }

    document.getElementById('shareBtn')?.addEventListener('click', async function (e) {
        e.preventDefault();
        if (navigator.share) {
            try { await navigator.share(shareData); } catch (_) {}
        } else {
            copyLink();
        }
    });
</script>

@endsection
