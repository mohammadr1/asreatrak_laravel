@extends('Customer.layouts.master-one-col')

@section('head-tag')
<link rel="stylesheet" href="{{ asset('assets/css/style_single.css') }}">
<title>{{ env('APP_NAME') }}</title>
@endsection

@section('content')


<!-- =====================
           COL-8: Article Content
      ===================== -->
<div class="col-lg-8">

    <!-- start print article -->
    <div class="print-area">

        <!-- هدر مخصوص چاپ -->
        <div class="print-header print-only">
            <img src="{{ asset('assets/images/logo.png') }}" alt="خبرگزاری عصر اترک" class="print-logo">
            <div class="print-title">خبرگزاری عصر اترک</div>
            <div class="print-sub">پایگاه خبری تحلیلی خراسان شمالی</div>
        </div>

        <!-- خط جداکننده -->
        <div class="print-divider"></div>

        <!-- شروع خبر -->
        <div class="print-content">



            <!-- Featured Image / Hero -->
            <div class="article-featured-image">
                <img src="{{ asset('assets/images/header.png') }}" alt="نشست فوق‌العاده =شورای عالی استان‌ها" />
                <div class="fi-gradient"></div>
                <span class="article-category-badge">سیاسی</span>
            </div>


            <style>
                .article-featured-image {
                    position: relative;
                    width: 100%;
                }

                .article-kicker {
                    font-size: .85rem;
                    font-weight: 700;

                    color: var(--primary);

                    margin-top: 14px;
                    margin-bottom: 10px;

                    position: relative;

                    padding-right: 14px;
                }

                .article-kicker::before {
                    /* content: ''; */

                    position: absolute;

                    right: 0;
                    top: 50%;

                    transform: translateY(-50%);

                    width: 6px;
                    height: 6px;

                    border-radius: 50%;

                    background: var(--primary);
                }

            </style>
            <!-- Article Header -->
            <div class="article-header">
                <span class="article-category-badge">سیاسی</span>
                <p class="article-kicker">
                    این یک نمونه لید خبر است
                </p>
                <h1 class="article-title">
                    نشست فوق‌العاده شورای عالی استان‌ها برای بررسی مسائل اقتصادی خراسان شمالی برگزار شد
                </h1>
                <p class="article-lead">
                    در این نشست که با حضور استانداران و نمایندگان مجلس برگزار شد، به بررسی چالش‌های اقتصادی
                    منطقه و راه‌حل‌های پیشنهادی پرداخته شد. تأکید ویژه‌ای بر توسعه زیرساخت‌های استان و
                    جذب سرمایه‌گذاری خارجی صورت گرفت.
                </p>

                <!-- Meta Row -->
                <div class="article-meta-row">
                    <div class="author">
                        <img src="https://ui-avatars.com/api/?name=Ahmad+Karimi&background=c0392b&color=fff&size=64"
                            alt="احمد کریمی" class="author-avatar" />
                        <div>
                            <div style="font-size:.82rem;font-weight:600;color:var(--heading-color,#ddd)">احمد کریمی
                            </div>
                            <div style="font-size:.74rem;opacity:.65">خبرنگار سیاسی</div>
                        </div>
                    </div>
                    <span><i class="bi bi-calendar3"></i> ۱۴۰۳/۰۲/۱۵</span>
                    <span><i class="bi bi-clock"></i> ۱۴:۳۰</span>
                    <span><i class="bi bi-eye"></i> ۲,۴۸۵ بازدید</span>
                    <span><i class="bi bi-chat-dots"></i> ۱۲ نظر</span>
                    <span><i class="bi bi-share"></i> ۳۴ اشتراک‌گذاری</span>

                    <!-- Share Buttons -->
                    <div class="article-share ms-auto">
                        <span class="share-label">اشتراک‌گذاری:</span>
                        <a href="#" class="share-btn telegram" title="تلگرام"><i class="bi bi-telegram"></i></a>
                        <a href="#" class="share-btn whatsapp" title="واتساپ"><i class="bi bi-whatsapp"></i></a>
                        <a href="#" class="share-btn twitter" title="توییتر"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="share-btn instagram" title="اینستاگرام"><i class="bi bi-instagram"></i></a>
                        <button class="share-btn copy" title="کپی لینک" onclick="copyLink()"><i
                                class="bi bi-link-45deg"></i></button>
                    </div>
                </div>
            </div>
            <!-- /Article Header -->

            <!-- Article Hero Image -->
            <div class="article-hero-wrap">
                <img src="https://images.unsplash.com/photo-1546085020-407431a34851?w=900&q=85"
                    alt="نشست فوق‌العاده شورای عالی استان‌ها" />
                <div class="article-hero-caption">
                    <i class="bi bi-camera me-1"></i>
                    نمایی از جلسه شورای عالی استان‌ها — عکس: خبرگزاری بجنورد
                </div>
            </div>

            <!-- Article Body -->
            <div class="article-body">
                <p>
                    نشست فوق‌العاده شورای عالی استان‌ها با حضور استاندار خراسان شمالی، نمایندگان مجلس شورای اسلامی
                    و مسئولان ارشد دستگاه‌های اجرایی استان، روز گذشته در محل استانداری بجنورد برگزار شد. در این
                    نشست به تفصیل درباره چالش‌های اقتصادی پیش‌روی استان و راهکارهای مقابله با آن‌ها بحث و تبادل‌نظر
                    صورت گرفت.
                </p>

                <h3>محورهای اصلی نشست</h3>
                <p>
                    استاندار خراسان شمالی در ابتدای این نشست با اشاره به ظرفیت‌های بالقوه استان اعلام کرد که توسعه
                    زیرساخت‌های حمل‌ونقل، انرژی و فناوری اطلاعات در اولویت اول دولت قرار دارد. وی همچنین از
                    برنامه‌ریزی برای جذب سرمایه‌گذاری خارجی از کشورهای همسایه خبر داد.
                </p>

                <blockquote>
                    «توسعه پایدار استان‌های مرزی نیازمند توجه ویژه و سیاست‌گذاری بلندمدت است.
                    ما آماده‌ایم تا با بهره‌گیری از توان بخش خصوصی و سرمایه‌گذاران داخلی و خارجی،
                    گام‌های مؤثری در راستای رشد اقتصادی استان برداریم.»
                    <br /><cite>— استاندار خراسان شمالی</cite>
                </blockquote>

                <p>
                    در ادامه این نشست، رئیس سازمان برنامه و بودجه استان گزارش تفصیلی درباره وضعیت پروژه‌های
                    عمرانی در حال اجرا ارائه داد. بر اساس این گزارش، در حال حاضر ۴۷ پروژه زیرساختی در استان
                    در مرحله اجراست که بخش قابل توجهی از آن‌ها تا پایان سال جاری به بهره‌برداری خواهد رسید.
                </p>

                <h3>تأکید بر جذب سرمایه‌گذاری</h3>
                <p>
                    نمایندگان مجلس شورای اسلامی در این نشست ضمن ابراز رضایت از روند پیشرفت پروژه‌های استانی،
                    بر ضرورت تسریع در رفع موانع اداری و بوروکراتیک برای جذب سرمایه‌گذاران تأکید کردند.
                    در این راستا مقرر شد که کارگروه ویژه‌ای با محوریت اتاق بازرگانی و سازمان سرمایه‌گذاری
                    استان تشکیل گردد.
                </p>

                <ul>
                    <li>تسریع در صدور مجوزهای سرمایه‌گذاری</li>
                    <li>ایجاد پنجره واحد خدمات سرمایه‌گذاری در استانداری</li>
                    <li>برگزاری همایش فرصت‌های سرمایه‌گذاری استان تا پایان تابستان</li>
                    <li>تخصیص اعتبارات ویژه برای توسعه مناطق آزاد</li>
                </ul>

                <h3>برنامه‌های آینده</h3>
                <p>
                    در پایان جلسه، جمع‌بندی نهایی حاکی از این بود که استان خراسان شمالی با داشتن مرز مشترک
                    با ترکمنستان و قرار گرفتن در مسیر کریدور شمال–جنوب، از موقعیت استراتژیک ویژه‌ای برخوردار
                    است که می‌تواند به توسعه اقتصادی چشمگیر این منطقه کمک کند.
                </p>
                <p>
                    مقرر شد نتایج این نشست طی یک گزارش رسمی به هیئت دولت ارسال شود و پیگیری مصوبات به
                    معاون هماهنگی امور عمرانی استانداری سپرده شد.
                </p>
            </div>
            <!-- /Article Body -->

            <!-- Tags -->
            <div class="article-tags">
                <span class="tag-label"><i class="bi bi-tags me-1"></i>برچسب‌ها:</span>
                <a href="#" class="tag-pill">شورای عالی استان‌ها</a>
                <a href="#" class="tag-pill">خراسان شمالی</a>
                <a href="#" class="tag-pill">بجنورد</a>
                <a href="#" class="tag-pill">اقتصادی</a>
                <a href="#" class="tag-pill">سرمایه‌گذاری</a>
                <a href="#" class="tag-pill">استانداری</a>
                <a href="#" class="tag-pill">مجلس</a>
            </div>


        </div>
    </div>
    <!-- end print article -->

    <!-- Actions Bar -->
    <div class="article-actions-bar">
        <button class="action-btn" onclick="window.print()">
            <i class="bi bi-printer"></i> چاپ خبر
        </button>
        <button class="action-btn" onclick="copyLink()">
            <i class="bi bi-link-45deg"></i> کپی لینک
        </button>
        <a href="#" class="action-btn">
            <i class="bi bi-flag"></i> گزارش تخلف
        </a>
        <a href="#" class="action-btn" id="shareBtn">
            <i class="bi bi-send"></i> ارسال به دوستان
        </a>
        <!-- Share again at bottom -->
        <div class="d-flex align-items-center gap-2 ms-auto">
            <span class="share-label" style="font-size:.8rem;opacity:.7">اشتراک‌گذاری:</span>
            <a href="#" class="share-btn telegram" title="تلگرام"><i class="bi bi-telegram"></i></a>
            <a href="#" class="share-btn whatsapp" title="واتساپ"><i class="bi bi-whatsapp"></i></a>
            <a href="#" class="share-btn twitter" title="توییتر"><i class="bi bi-twitter-x"></i></a>
        </div>
    </div>

    <!-- =====================
             Related News
        ===================== -->
    <div class="related-section mt-4">
        <div class="section-header">
            <span class="section-title"><i class="bi bi-newspaper me-1"></i>اخبار مرتبط</span>
        </div>
        <div>
            <a href="#" class="related-card">
                <img src="https://images.unsplash.com/photo-1611244763972-aa9c8368ef14?w=300&q=75" alt="خبر مرتبط ۱"
                    class="related-card-img" />
                <div class="related-card-body">
                    <div class="related-card-cat">سیاسی</div>
                    <div class="related-card-title">سفر هیئت دولت به استان خراسان شمالی و افتتاح پروژه‌های عمرانی
                    </div>
                    <div class="related-card-meta"><i class="bi bi-clock me-1"></i>۱۴۰۳/۰۲/۱۵ &nbsp;|&nbsp; <i
                            class="bi bi-eye me-1"></i>۱,۸۰۰ بازدید</div>
                </div>
            </a>
            <a href="#" class="related-card">
                <img src="https://images.unsplash.com/photo-1604646357333-ecb1f24b2d21?w=300&q=75" alt="خبر مرتبط ۲"
                    class="related-card-img" />
                <div class="related-card-body">
                    <div class="related-card-cat">اقتصادی</div>
                    <div class="related-card-title">رشد ۲۵ درصدی تولیدات صنعتی بجنورد در سال جاری</div>
                    <div class="related-card-meta"><i class="bi bi-clock me-1"></i>۱۴۰۳/۰۲/۱۴ &nbsp;|&nbsp; <i
                            class="bi bi-eye me-1"></i>۹۵۰ بازدید</div>
                </div>
            </a>
            <a href="#" class="related-card">
                <img src="https://images.unsplash.com/photo-1613441589134-3fc7f95a3e16?w=300&q=75" alt="خبر مرتبط ۳"
                    class="related-card-img" />
                <div class="related-card-body">
                    <div class="related-card-cat">استانی</div>
                    <div class="related-card-title">استاندار خراسان شمالی: توسعه زیرساخت‌ها در اولویت برنامه‌های
                        دولت قرار
                        دارد</div>
                    <div class="related-card-meta"><i class="bi bi-clock me-1"></i>۱۴۰۳/۰۲/۱۳ &nbsp;|&nbsp; <i
                            class="bi bi-eye me-1"></i>۱,۲۳۰ بازدید</div>
                </div>
            </a>
            <a href="#" class="related-card">
                <img src="https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=300&q=75" alt="خبر مرتبط ۴"
                    class="related-card-img" />
                <div class="related-card-body">
                    <div class="related-card-cat">فناوری</div>
                    <div class="related-card-title">راه‌اندازی مرکز نوآوری و فناوری استان خراسان شمالی در بجنورد
                    </div>
                    <div class="related-card-meta"><i class="bi bi-clock me-1"></i>۱۴۰۳/۰۲/۱۲ &nbsp;|&nbsp; <i
                            class="bi bi-eye me-1"></i>۲,۱۰۰ بازدید</div>
                </div>
            </a>
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
        <div class="widget-header">
            <i class="bi bi-info-circle-fill"></i>
            اطلاعات خبر
        </div>
        <div class="widget-body">
            <div class="news-code-box" style="border:none;border-radius:0;margin:0">
                <div class="code-row">
                    <span>کد خبر</span>
                    <strong>BN-140315-0042</strong>
                </div>
                <div class="code-row">
                    <span>منبع</span>
                    <strong>خبرگزاری بجنورد</strong>
                </div>
                <div class="code-row">
                    <span>تاریخ انتشار</span>
                    <strong>۱۴۰۳/۰۲/۱۵</strong>
                </div>
                <div class="code-row">
                    <span>ساعت انتشار</span>
                    <strong>۱۴:۳۰</strong>
                </div>
                <div class="code-row">
                    <span>دسته‌بندی</span>
                    <strong>سیاسی — داخلی</strong>
                </div>
                <div class="code-row">
                    <span>تعداد بازدید</span>
                    <strong>۲,۴۸۵</strong>
                </div>
                <div class="code-row">
                    <span>خبرنگار</span>
                    <strong>احمد کریمی</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- اوقات شرعی -->
    <div class="sidebar-widget">
        <div class="widget-header">
            <i class="bi bi-moon-stars-fill"></i>
            اوقات شرعی
        </div>
        <div class="widget-body">
            <div class="prayer-date-header" id="prayer-date-header">بارگذاری...</div>
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
    <div class="sidebar-widget">
        <div class="widget-header">
            <i class="bi bi-fire"></i>
            پربازدیدترین‌ها
        </div>
        <div class="widget-body p-0">
            <ul class="list-unstyled m-0">
                <li>
                    <a href="#" class="d-flex align-items-start gap-3 p-3 hover-bg">
                        <span
                            class="fw-bold text-white rounded-2 d-flex align-items-center justify-content-center flex-shrink-0">۱</span>
                        <div>
                            <div>صعود تیم فوتبال شاهین به لیگ دسته اول</div>
                            <div><i class="bi bi-eye me-1"></i>۳,۴۵۶ بازدید</div>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="#" class="d-flex align-items-start gap-3 p-3">
                        <span
                            class="fw-bold text-white rounded-2 d-flex align-items-center justify-content-center flex-shrink-0">۲</span>
                        <div>
                            <div>افتتاح مرکز نوآوری و فناوری بجنورد</div>
                            <div><i class="bi bi-eye me-1"></i>۲,۴۸۵ بازدید</div>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="#" class="d-flex align-items-start gap-3 p-3">
                        <span
                            class="fw-bold text-white rounded-2 d-flex align-items-center justify-content-center flex-shrink-0">۳</span>
                        <div>
                            <div>جشنواره فرهنگی آینه‌خانه در بجنورد</div>
                            <div><i class="bi bi-eye me-1"></i>۱,۸۷۲ بازدید</div>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="#" class="d-flex align-items-start gap-3 p-3">
                        <span
                            class="fw-bold text-white rounded-2 d-flex align-items-center justify-content-center flex-shrink-0">۴</span>
                        <div>
                            <div>بارش باران بهاره در خراسان شمالی</div>
                            <div><i class="bi bi-eye me-1"></i>۱,۱۰۳ بازدید</div>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
    </div>

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
document.getElementById('shareBtn').addEventListener('click', async function (e) {
    e.preventDefault();

    // const shortUrl = "@{{ url('/news/' . $news->short_link) }}";

    if (navigator.share) {
        navigator.share({
            // title: "@{{ $news->title }}",
            // text: "@{{ $news->title }}",
            url: shortUrl
        });
    } else {
        navigator.clipboard.writeText(shortUrl);
        alert('لینک خبر کپی شد');
    }
});
</script>

@endsection
