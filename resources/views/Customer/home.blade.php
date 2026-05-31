@extends('customer.layouts.master-one-col')

<link rel="stylesheet" href="{{ asset('assets/css/media_card_home_gallery.css') }}">
@section('head-tag')
<title>{{ env('APP_NAME') }}</title>
@endsection

@section('content')

      <!-- =====================
           COL-8: Main Content
      ===================== -->
      <div class="col-lg-8">

        <!-- TOP NEWS SLIDER -->
        <div class="hero-slider-wrap fade-in">
          <div class="hero-slider" id="heroSlider">

            <!-- Slide 1 -->
            <div class="hero-slide active">
              <div class="hero-slide-img">
                <img src="{{ asset('assets/images/header.png') }}" alt="خبر ویژه ۱"/>
                <span class="category-badge">سیاسی</span>
              </div>
              <div class="hero-slide-body">
                <div>
                  <div class="overtitle">رویداد ویژه</div>
                  <h2><a href="#">نشست فوق‌العاده شورای عالی استان‌ها برای بررسی مسائل اقتصادی خراسان شمالی برگزار شد</a></h2>
                  <p class="lead-text">
                    در این نشست که با حضور استانداران و نمایندگان مجلس برگزار شد، به بررسی چالش‌های اقتصادی
                    منطقه و راه‌حل‌های پیشنهادی پرداخته شد. تأکید ویژه‌ای بر توسعه زیرساخت‌های استان و
                    جذب سرمایه‌گذاری خارجی صورت گرفت.
                  </p>
                </div>
                <div class="hero-slide-meta">
                  <span><i class="bi bi-calendar3"></i> ۱۴۰۳/۰۲/۱۵</span>
                  <span><i class="bi bi-clock"></i> ۱۴:۳۰</span>
                  <span><i class="bi bi-eye"></i> ۲,۴۸۵</span>
                  <a href="#" class="read-more">ادامه خبر ←</a>
                </div>
              </div>
            </div>

            <!-- Slide 2 -->
            <div class="hero-slide">
              <div class="hero-slide-img">
                <img src="{{ asset('assets/images/header.png') }}" alt="خبر ویژه ۲"/>
                <span class="category-badge" >فناوری</span>
              </div>
              <div class="hero-slide-body">
                <div>
                  <div class="overtitle">اختصاصی</div>
                  <h2><a href="#">راه‌اندازی مرکز نوآوری و فناوری استان خراسان شمالی در بجنورد</a></h2>
                  <p class="lead-text">
                    مرکز نوآوری بجنورد با ظرفیت پذیرش ۵۰ استارتاپ فعال، به عنوان بزرگترین مرکز
                    رشد فناوری در شمال شرق کشور آغاز به کار کرد. این مرکز با همکاری دانشگاه آزاد
                    و وزارت علوم تأسیس شده است.
                  </p>
                </div>
                <div class="hero-slide-meta">
                  <span><i class="bi bi-calendar3"></i> ۱۴۰۳/۰۲/۱۴</span>
                  <span><i class="bi bi-clock"></i> ۱۰:۱۵</span>
                  <span><i class="bi bi-eye"></i> ۱,۸۷۲</span>
                  <a href="#" class="read-more">ادامه خبر ←</a>
                </div>
              </div>
            </div>

          </div><!-- /.hero-slider -->

          <!-- Slider Controls -->
          <div class="slider-controls">
            <div class="slider-dots">
              <button class="slider-dot active" onclick="goToSlide(0)"></button>
              <button class="slider-dot" onclick="goToSlide(1)"></button>
            </div>
            <button class="slider-btn" id="prevSlide"><i class="bi bi-chevron-right"></i></button>
            <button class="slider-btn" id="nextSlide"><i class="bi bi-chevron-left"></i></button>
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
            <div class="col-md-3 col-sm-6">
              <div class="news-card">
                <div class="news-card-img">
                  <img src="{{ asset('assets/images/header.png') }}" alt="خبر ویژه"/>
                  <span class="news-card-badge badge-red">سیاسی</span>
                  <div class="overlay-title">
                    <h5><a href="#">سفر هیئت دولت به استان خراسان شمالی و افتتاح پروژه‌ها</a></h5>
                    <div class="news-meta"><i class="bi bi-clock"></i> ۱۴:۲۰ &nbsp;|&nbsp; <i class="bi bi-calendar3"></i> ۱۴۰۳/۰۲/۱۵</div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3 col-sm-6">
              <div class="news-card">
                <div class="news-card-img">
                  <img src="{{ asset('assets/images/header.png') }}" alt="خبر ویژه"/>
                  <span class="news-card-badge badge-blue">اقتصادی</span>
                  <div class="overlay-title">
                    <h5><a href="#">رشد ۲۵ درصدی تولیدات صنعتی بجنورد در سال جاری</a></h5>
                    <div class="news-meta"><i class="bi bi-clock"></i> ۱۱:۴۵ &nbsp;|&nbsp; <i class="bi bi-calendar3"></i> ۱۴۰۳/۰۲/۱۴</div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3 col-sm-6">
              <div class="news-card">
                <div class="news-card-img">
                  <img src="{{ asset('assets/images/header.png') }}" alt="خبر ویژه"/>
                  <span class="news-card-badge badge-green">اجتماعی</span>
                  <div class="overlay-title">
                    <h5><a href="#">بهره‌برداری از مجتمع درمانی جدید در شیروان</a></h5>
                    <div class="news-meta"><i class="bi bi-clock"></i> ۰۹:۳۰ &nbsp;|&nbsp; <i class="bi bi-calendar3"></i> ۱۴۰۳/۰۲/۱۳</div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3 col-sm-6">
              <div class="news-card">
                <div class="news-card-img">
                  <img src="{{ asset('assets/images/header.png') }}" alt="خبر ویژه"/>
                  <span class="news-card-badge badge-orange">فرهنگی</span>
                  <div class="overlay-title">
                    <h5><a href="#">جشنواره فرهنگی آینه‌خانه در بجنورد برگزار می‌شود</a></h5>
                    <div class="news-meta"><i class="bi bi-clock"></i> ۱۶:۰۰ &nbsp;|&nbsp; <i class="bi bi-calendar3"></i> ۱۴۰۳/۰۲/۱۲</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /اخبار ویژه -->

        <!-- =====================
             NEWS LIST (بدنه خبر)
        ===================== -->
        <div class="row g-4">
          <div class="col-lg-8">
            <div class="news-list-section fade-in">
              <!-- Item 1 -->
              <div class="news-row-item">
                <div class="news-r{{ asset('assets/images/header.png') }}>
                <div class="news-row-body">
                  <div class="news-row-overtitle">سیاسی — داخلی</div>
                  <a href="#" class="news-row-title">استاندار خراسان شمالی: توسعه زیرساخت‌های استان در اولویت برنامه‌های دولت قرار دارد</a>
                  <p class="news-row-lead">در نشست هم‌اندیشی مدیران استانی که با حضور مقامات ارشد برگزار شد، بر تسریع در اجرای پروژه‌های عمرانی تأکید شد.</p>
                  <div class="news-row-meta">
                    <span><i class="bi bi-calendar3"></i> ۱۴۰۳/۰۲/۱۵</span>
                    <span><i class="bi bi-clock"></i> ۱۵:۴۵</span>
                    <span><i class="bi bi-eye"></i> ۱,۲۳۰</span>
                  </div>
                </div>
              </div>
              <!-- Item 2 -->
              <div class="news-row-item">
                <div class="news-r{{ asset('assets/images/header.png') }}>
                <div class="news-row-body">
                  <div class="news-row-overtitle">اقتصادی</div>
                  <a href="#" class="news-row-title">نرخ بیکاری استان به پایین‌ترین رقم در ۵ سال اخیر رسید</a>
                  <p class="news-row-lead">آمار رسمی مرکز آمار ایران نشان می‌دهد نرخ بیکاری در استان خراسان شمالی با کاهش ۲ درصدی مواجه شده است.</p>
                  <div class="news-row-meta">
                    <span><i class="bi bi-calendar3"></i> ۱۴۰۳/۰۲/۱۴</span>
                    <span><i class="bi bi-clock"></i> ۱۲:۱۰</span>
                    <span><i class="bi bi-eye"></i> ۸۷۵</span>
                  </div>
                </div>
              </div>
              <!-- Item 3 -->
              <div class="news-row-item">
                <div class="news-row-img"><img src="{{ asset('assets/images/header.png') }}" alt=""/></div>
                <div class="news-row-body">
                  <div class="news-row-overtitle">فرهنگی — هنری</div>
                  <a href="#" class="news-row-title">نمایشگاه بین‌المللی صنایع دستی خراسان شمالی در بجنورد گشایش یافت</a>
                  <p class="news-row-lead">این نمایشگاه با شرکت ۱۲۰ هنرمند از سراسر کشور و چند کشور همسایه از امروز در محل دائمی نمایشگاه‌های استان برپا شد.</p>
                  <div class="news-row-meta">
                    <span><i class="bi bi-calendar3"></i> ۱۴۰۳/۰۲/۱۳</span>
                    <span><i class="bi bi-clock"></i> ۱۰:۰۰</span>
                    <span><i class="bi bi-eye"></i> ۶۴۲</span>
                  </div>
                </div>
              </div>
              <!-- Item 4 -->
              <div class="news-row-item">
                <div class="news-row-img"><img src="{{ asset('assets/images/header.png') }}" alt=""/></div>
                <div class="news-row-body">
                  <div class="news-row-overtitle">فناوری</div>
                  <a href="#" class="news-row-title">راه‌اندازی شبکه فیبر نوری پرسرعت در ۳۰ روستای استان</a>
                  <p class="news-row-lead">طرح توسعه اینترنت پرسرعت روستایی با هدف کاهش شکاف دیجیتال در مناطق کمترتوسعه‌یافته آغاز به کار کرد.</p>
                  <div class="news-row-meta">
                    <span><i class="bi bi-calendar3"></i> ۱۴۰۳/۰۲/۱۲</span>
                    <span><i class="bi bi-clock"></i> ۱۶:۳۰</span>
                    <span><i class="bi bi-eye"></i> ۱,۱۰۳</span>
                  </div>
                </div>
              </div>
              <!-- Item 5 -->
              <div class="news-row-item">
                <div class="news-row-img"><img src="{{ asset('assets/images/header.png') }}" alt=""/></div>
                <div class="news-row-body">
                  <div class="news-row-overtitle">ورزشی</div>
                  <a href="#" class="news-row-title">تیم فوتبال شاهین بجنورد به لیگ دسته اول صعود کرد</a>
                  <p class="news-row-lead">با برتری مقابل حریف خود در آخرین بازی فصل، تیم شاهین موفق به کسب سهمیه صعود به لیگ دسته اول شد.</p>
                  <div class="news-row-meta">
                    <span><i class="bi bi-calendar3"></i> ۱۴۰۳/۰۲/۱۱</span>
                    <span><i class="bi bi-clock"></i> ۲۱:۰۰</span>
                    <span><i class="bi bi-eye"></i> ۳,۴۵۶</span>
                  </div>
                </div>
              </div>
              <!-- Item 6 -->
              <div class="news-row-item">
                <div class="news-row-img"><img src="{{ asset('assets/images/header.png') }}" alt=""/></div>
                <div class="news-row-body">
                  <div class="news-row-overtitle">اجتماعی</div>
                  <a href="#" class="news-row-title">اجرای طرح آب‌رسانی به ۱۵ روستای محروم شمال استان</a>
                  <p class="news-row-lead">این طرح با اعتباری بالغ بر ۳۰ میلیارد تومان از محل اعتبارات ملی، مشکل کمبود آب شرب روستاهای هدف را برطرف می‌کند.</p>
                  <div class="news-row-meta">
                    <span><i class="bi bi-calendar3"></i> ۱۴۰۳/۰۲/۱۰</span>
                    <span><i class="bi bi-clock"></i> ۰۹:۱۵</span>
                    <span><i class="bi bi-eye"></i> ۷۲۸</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- =====================
               COL-4 SIDE SECTIONS (گزارش/گفتگو/یادداشت)
          ===================== -->
          <div class="col-lg-4">

            <!-- گزارش -->
            <div class="sidebar-news-section fade-in">
              <div class="section-header">
                <span class="section-title"><i class="bi bi-file-earmark-text-fill me-1"></i>گزارش</span>
              </div>
              <div class="sidebar-news-card">
                <img src="{{ asset('assets/images/header.png') }}" alt="گزارش"/>
                <div class="card-overlay">
                  <h5><a href="#" >گزارش ویژه: وضعیت کشاورزی خراسان شمالی در سال آبی جدید</a></h5>
                  <div class="meta"><i class="bi bi-calendar3"></i> ۱۴۰۳/۰۲/۱۵ &nbsp;|&nbsp; <i class="bi bi-clock"></i> ۱۳:۰۰</div>
                </div>
              </div>
              <div>
                بررسی میدانی از وضعیت منابع آبی، میزان بارندگی و تأثیر آن بر محصولات کشاورزی استان در فصل بهار...
                <a href="#"  >ادامه ←</a>
              </div>
            </div>

            <!-- گفتگو -->
            <div class="sidebar-news-section fade-in">
              <div class="section-header">
                <span class="section-title"><i class="bi bi-chat-quote-fill me-1"></i>گفتگو</span>
              </div>
              <div class="sidebar-news-card">
                <img src="{{ asset('assets/images/header.png') }}" alt="گفتگو"/>
                <div class="card-overlay">
                  <h5><a href="#" >گفتگو با رئیس اتاق بازرگانی استان درباره آینده تجارت منطقه</a></h5>
                  <div class="meta"><i class="bi bi-calendar3"></i> ۱۴۰۳/۰۲/۱۴ &nbsp;|&nbsp; <i class="bi bi-clock"></i> ۱۱:۳۰</div>
                </div>
              </div>
              <div >
                رئیس اتاق بازرگانی بجنورد در گفتگوی اختصاصی با پایگاه ما از چشم‌انداز اقتصادی استان می‌گوید...
                <a href="#" >ادامه ←</a>
              </div>
            </div>

            <!-- یادداشت -->
            <div class="sidebar-news-section fade-in">
              <div class="section-header">
                <span class="section-title"><i class="bi bi-pencil-square me-1"></i>یادداشت</span>
              </div>
              <div class="sidebar-news-card">
                <img src="{{ asset('assets/images/header.png') }}" alt="یادداشت"/>
                <div class="card-overlay">
                  <h5><a href="#">ضرورت توجه به توسعه پایدار در استان‌های مرزی</a></h5>
                  <div class="meta"><i class="bi bi-calendar3"></i> ۱۴۰۳/۰۲/۱۳ &nbsp;|&nbsp; <i class="bi bi-clock"></i> ۰۸:۰۰</div>
                </div>
              </div>
              <div >
                دکتر احمد کریمی — استاد اقتصاد دانشگاه بجنورد: توسعه پایدار نیازمند سیاست‌گذاری بلندمدت است...
                <a href="#" >ادامه ←</a>
              </div>
            </div>

          </div>
        </div>
        <!-- /NEWS LIST + SIDE SECTIONS -->

        <!-- =====================
             چندرسانه
        ===================== -->
        <div class="multimedia-section fade-in">
          <div class="section-header">
            <span class="section-title"><i class="bi bi-collection-play-fill me-1"></i>چندرسانه</span>
          </div>
          <div class="media-tabs">
            <button class="media-tab active" onclick="filterMedia(this,'all')">همه</button>
            <button class="media-tab" onclick="filterMedia(this,'photo')"><i class="bi bi-image me-1"></i>تصویر</button>
            <button class="media-tab" onclick="filterMedia(this,'video')"><i class="bi bi-play-circle me-1"></i>ویدیو</button>
          </div>
          <div class="row g-3">
            <!-- Photo Card -->
            <div class="col-md-6" data-type="photo">
              <div class="media-card"
                onclick="openMedia('{{ asset('assets/images/header.png') }}','image')">
                <img src="{{ asset('assets/images/header.png') }}" alt="تصویر"/>
                <div class="media-overlay">
                  <div class="media-meta"><i class="bi bi-image"></i> تصویر &nbsp;|&nbsp; <i class="bi bi-eye"></i> ۲,۱۰۰</div>
                  <div class="media-title">تصویری از جشنواره فرهنگی بجنورد — بهار ۱۴۰۳</div>
                </div>
                <div class="media-type-badge"><i class="bi bi-image me-1"></i>گالری تصویر</div>
              </div>
            </div>
            <!-- Video Card -->
            <div class="col-md-6" data-type="video">
              <div class="media-card video-card"
                onclick="openMedia('{{ asset('assets/videos/test.mp4') }}','video')">
                <img src="{{ asset('assets/images/header.png') }}" alt="ویدیو"/>
                <div class="media-overlay">
                  <div class="media-meta"><i class="bi bi-play-circle"></i> ویدیو &nbsp;|&nbsp; <i class="bi bi-clock"></i> ۰۳:۴۲</div>
                  <div class="media-title">گزارش ویدیویی: افتتاح مرکز نوآوری بجنورد</div>
                </div>
                <div class="play-btn"><i class="bi bi-play-fill"></i></div>
                <div class="media-type-badge"><i class="bi bi-camera-video me-1"></i>ویدیو</div>
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
                <i class="bi bi-sunrise-fill" ></i>
                <span class="prayer-name">اذان صبح</span>
                <span class="prayer-time">۰۴:۴۸</span>
              </div>
              <div class="prayer-item">
                <i class="bi bi-sun-fill" ></i>
                <span class="prayer-name">طلوع آفتاب</span>
                <span class="prayer-time">۰۶:۱۵</span>
              </div>
              <div class="prayer-item highlight">
                <i class="bi bi-brightness-high-fill"></i>
                <span class="prayer-name">اذان ظهر</span>
                <span class="prayer-time">۱۳:۰۲</span>
              </div>
              <div class="prayer-item">
                <i class="bi bi-sunset-fill" ></i>
                <span class="prayer-name">اذان عصر</span>
                <span class="prayer-time">۱۶:۴۸</span>
              </div>
              <div class="prayer-item">
                <i class="bi bi-moon-fill" ></i>
                <span class="prayer-name">اذان مغرب</span>
                <span class="prayer-time">۲۰:۲۴</span>
              </div>
              <div class="prayer-item">
                <i class="bi bi-stars" ></i>
                <span class="prayer-name">اذان عشا</span>
                <span class="prayer-time">۲۱:۳۵</span>
              </div>
            </div>
            <div class="mt-3 p-2 rounded" >
              <i class="bi bi-geo-alt-fill" ></i>
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
                <div class="weather-icon">🌤️</div>
                <div class="weather-desc">کمی ابری، خوشگذران</div>
              </div>
              <div class="text-end">
                <div class="weather-temp">۲۲<sup>°C</sup></div>
                <div >حس می‌شود ۲۰°</div>
              </div>
            </div>
            <div class="weather-details">
              <div class="weather-detail-item">
                <i class="bi bi-droplet-half" ></i>
                <div>
                  <div class="weather-detail-label">رطوبت</div>
                  <div class="weather-detail-value">۴۵٪</div>
                </div>
              </div>
              <div class="weather-detail-item">
                <i class="bi bi-wind" ></i>
                <div>
                  <div class="weather-detail-label">وزش باد</div>
                  <div class="weather-detail-value">۱۸ km/h</div>
                </div>
              </div>
              <div class="weather-detail-item">
                <i class="bi bi-eye-fill" ></i>
                <div>
                  <div class="weather-detail-label">دید افق</div>
                  <div class="weather-detail-value">۱۰ km</div>
                </div>
              </div>
              <div class="weather-detail-item">
                <i class="bi bi-thermometer-half" ></i>
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
        <div class="sidebar-widget fade-in">
          <div class="widget-header">
            <i class="bi bi-fire"></i>
            پربازدیدترین‌ها
          </div>
          <div class="widget-body p-0">
            <ul class="list-unstyled m-0">
              <li >
                <a href="#" class="d-flex align-items-start gap-3 p-3 hover-bg" >
                  <span class="fw-bold text-white rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" >۱</span>
                  <div>
                    <div >صعود تیم فوتبال شاهین به لیگ دسته اول</div>
                    <div ><i class="bi bi-eye me-1"></i>۳,۴۵۶ بازدید</div>
                  </div>
                </a>
              </li>
              <li >
                <a href="#" class="d-flex align-items-start gap-3 p-3" >
                  <span class="fw-bold text-white rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" >۲</span>
                  <div>
                    <div >افتتاح مرکز نوآوری و فناوری بجنورد</div>
                    <div ><i class="bi bi-eye me-1"></i>۲,۴۸۵ بازدید</div>
                  </div>
                </a>
              </li>
              <li >
                <a href="#" class="d-flex align-items-start gap-3 p-3" >
                  <span class="fw-bold text-white rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" >۳</span>
                  <div>
                    <div >جشنواره فرهنگی آینه‌خانه در بجنورد</div>
                    <div ><i class="bi bi-eye me-1"></i>۱,۸۷۲ بازدید</div>
                  </div>
                </a>
              </li>
              <li>
                <a href="#" class="d-flex align-items-start gap-3 p-3" >
                  <span class="fw-bold text-white rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" >۴</span>
                  <div>
                    <div >بارش باران بهاره در خراسان شمالی</div>
                    <div ><i class="bi bi-eye me-1"></i>۱,۱۰۳ بازدید</div>
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



    <script src="{{ asset('assets/js/media_card_home_gallery.js') }}"></script>
@endsection




@section('scripts')
<script src="{{ asset('assets/js/main_slider.js') }}"></script>
<script src="{{ asset('assets/js/main_multimedia_filter.js') }}"></script>
@endsection
