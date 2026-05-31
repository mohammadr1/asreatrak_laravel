@extends('Customer.layouts.master-one-col')

@section('head-tag')

<link rel="stylesheet" href="{{ asset('assets/css/style_archive.css') }}">
<title>{{ env('APP_NAME') }}</title>
@endsection

@section('content')


<!-- =====================
           COL-8: Main Content
      ===================== -->
<div class="col-lg-8">


    <!-- FILTER BAR -->
    <div class="filter-bar fade-in">
        <div class="filter-label"><i class="bi bi-funnel-fill"></i> فیلتر و جستجو</div>
        <div class="filter-row">
            <div class="filter-group">
                <label>جستجو در متن</label>
                <input type="text" placeholder="عنوان یا کلیدواژه...">
            </div>
            <div class="filter-group">
                <label>دسته‌بندی</label>
                <select>
                    <option value="">همه دسته‌ها</option>
                    <option>سیاسی</option>
                    <option>اقتصادی</option>
                    <option>اجتماعی</option>
                    <option>فرهنگی</option>
                    <option>ورزشی</option>
                    <option>حوادث</option>
                    <option>فناوری</option>
                    <option>استانی</option>
                </select>
            </div>
            <div class="filter-group" style="position:relative">
                <label>از تاریخ</label>
                <div class="jalali-input-wrap" id="fromWrap">
                    <input type="text" id="fromDate" placeholder="انتخاب تاریخ" readonly class="jalali-input">
                    <i class="bi bi-calendar3"
                        style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text-muted);pointer-events:none;margin-top:10px"></i>
                </div>
                <div class="jdp" id="fromPicker"></div>
            </div>
            <div class="filter-group" style="position:relative">
                <label>تا تاریخ</label>
                <div class="jalali-input-wrap" id="toWrap">
                    <input type="text" id="toDate" placeholder="انتخاب تاریخ" readonly class="jalali-input">
                    <i class="bi bi-calendar3"
                        style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text-muted);pointer-events:none;margin-top:10px"></i>
                </div>
                <div class="jdp" id="toPicker"></div>
            </div>
            <button class="btn-filter"><i class="bi bi-search"></i> جستجو</button>
            <!-- <button class="btn-reset">پاک کردن</button> -->
        </div>

        <!-- Category Pills -->
        <div class="cat-pills">
            <span class="cat-pill active">همه <span class="count">۲۴۸۶</span></span>
            <span class="cat-pill">سیاسی <span class="count">۴۳۲</span></span>
            <span class="cat-pill">اقتصادی <span class="count">۳۸۷</span></span>
            <span class="cat-pill">اجتماعی <span class="count">۳۴۱</span></span>
            <span class="cat-pill">فرهنگی <span class="count">۲۹۵</span></span>
            <span class="cat-pill">ورزشی <span class="count">۲۶۸</span></span>
            <span class="cat-pill">حوادث <span class="count">۱۹۴</span></span>
            <span class="cat-pill">فناوری <span class="count">۱۵۷</span></span>
            <span class="cat-pill">استانی <span class="count">۴۱۲</span></span>
        </div>
    </div>

    <!-- RESULTS BAR -->
    <div class="results-bar fade-in">
        <div class="results-count">
            نمایش <strong>۱–۱۲</strong> از <strong>۲۴۸۶</strong> خبر
        </div>
        <div class="d-flex align-items-center gap-2">
            <select class="sort-select">
                <option>جدیدترین</option>
                <option>قدیمی‌ترین</option>
                <option>پربازدیدترین</option>
                <option>مرتبط‌ترین</option>
            </select>
            <div class="view-toggle">
                <button class="view-btn active" id="gridBtn" onclick="setView('grid')" title="نمایش شبکه‌ای">
                    <i class="bi bi-grid-3x3-gap-fill"></i>
                </button>
                <button class="view-btn" id="listBtn" onclick="setView('list')" title="نمایش لیستی">
                    <i class="bi bi-list-ul"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- GRID VIEW -->
    <div class="archive-grid" id="archiveGrid">

        <!-- Card 1 -->
        <div class="archive-card fade-in">
            <div class="archive-card-img">
                <img src="https://images.unsplash.com/photo-1546085020-407431a34851?w=600&q=80" alt="خبر">
                <span class="archive-card-badge badge-blue">سیاسی</span>
            </div>
            <div class="archive-card-body">
                <div class="archive-card-overtitle">داخلی — رویداد ویژه</div>
                <a href="#" class="archive-card-title">نشست فوق‌العاده شورای عالی استان‌ها برای بررسی مسائل اقتصادی
                    خراسان شمالی</a>
                <p class="archive-card-lead">در این نشست که با حضور استانداران و نمایندگان مجلس برگزار شد، به بررسی
                    چالش‌های اقتصادی منطقه پرداخته شد.</p>
                <div class="archive-card-meta">
                    <span><i class="bi bi-calendar3"></i> ۱۴۰۳/۰۲/۱۵</span>
                    <span><i class="bi bi-clock"></i> ۱۴:۳۰</span>
                    <span><i class="bi bi-eye"></i> ۲,۴۸۵</span>
                    <a href="#" class="read-more">ادامه ←</a>
                </div>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="archive-card fade-in">
            <div class="archive-card-img">
                <img src="https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=600&q=80" alt="خبر">
                <span class="archive-card-badge badge-teal">فناوری</span>
            </div>
            <div class="archive-card-body">
                <div class="archive-card-overtitle">فناوری — اختصاصی</div>
                <a href="#" class="archive-card-title">راه‌اندازی مرکز نوآوری و فناوری استان خراسان شمالی در بجنورد</a>
                <p class="archive-card-lead">مرکز نوآوری بجنورد با ظرفیت پذیرش ۵۰ استارتاپ، به عنوان بزرگترین مرکز رشد
                    فناوری در شمال شرق کشور آغاز به کار کرد.</p>
                <div class="archive-card-meta">
                    <span><i class="bi bi-calendar3"></i> ۱۴۰۳/۰۲/۱۴</span>
                    <span><i class="bi bi-clock"></i> ۱۰:۱۵</span>
                    <span><i class="bi bi-eye"></i> ۱,۸۷۲</span>
                    <a href="#" class="read-more">ادامه ←</a>
                </div>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="archive-card fade-in">
            <div class="archive-card-img">
                <img src="https://images.unsplash.com/photo-1611244763972-aa9c8368ef14?w=400&q=75" alt="خبر">
                <span class="archive-card-badge badge-blue">سیاسی</span>
            </div>
            <div class="archive-card-body">
                <div class="archive-card-overtitle">سیاسی — داخلی</div>
                <a href="#" class="archive-card-title">سفر هیئت دولت به استان خراسان شمالی و افتتاح پروژه‌های عمرانی</a>
                <p class="archive-card-lead">هیئت دولت با حضور در استان، چندین پروژه زیرساختی مهم را افتتاح کرد و با
                    مردم دیدار نمود.</p>
                <div class="archive-card-meta">
                    <span><i class="bi bi-calendar3"></i> ۱۴۰۳/۰۲/۱۵</span>
                    <span><i class="bi bi-clock"></i> ۱۴:۲۰</span>
                    <span><i class="bi bi-eye"></i> ۱,۲۳۰</span>
                    <a href="#" class="read-more">ادامه ←</a>
                </div>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="archive-card fade-in">
            <div class="archive-card-img">
                <img src="https://images.unsplash.com/photo-1604646357333-ecb1f24b2d21?w=400&q=75" alt="خبر">
                <span class="archive-card-badge badge-orange">اقتصادی</span>
            </div>
            <div class="archive-card-body">
                <div class="archive-card-overtitle">اقتصادی</div>
                <a href="#" class="archive-card-title">رشد ۲۵ درصدی تولیدات صنعتی بجنورد در سال جاری</a>
                <p class="archive-card-lead">آمار منتشر شده نشان می‌دهد تولیدات صنعتی شهرستان بجنورد با رشد چشمگیری
                    روبرو بوده است.</p>
                <div class="archive-card-meta">
                    <span><i class="bi bi-calendar3"></i> ۱۴۰۳/۰۲/۱۴</span>
                    <span><i class="bi bi-clock"></i> ۱۱:۴۵</span>
                    <span><i class="bi bi-eye"></i> ۸۷۵</span>
                    <a href="#" class="read-more">ادامه ←</a>
                </div>
            </div>
        </div>

        <!-- Card 5 -->
        <div class="archive-card fade-in">
            <div class="archive-card-img">
                <img src="https://images.unsplash.com/photo-1681393993243-f7ef6a79291d?w=400&q=75" alt="خبر">
                <span class="archive-card-badge badge-green">اجتماعی</span>
            </div>
            <div class="archive-card-body">
                <div class="archive-card-overtitle">اجتماعی — بهداشت</div>
                <a href="#" class="archive-card-title">بهره‌برداری از مجتمع درمانی جدید در شیروان با ظرفیت ۱۵۰ تخت</a>
                <p class="archive-card-lead">این مجتمع درمانی با جدیدترین تجهیزات پزشکی آماده ارائه خدمات به شهروندان
                    شد.</p>
                <div class="archive-card-meta">
                    <span><i class="bi bi-calendar3"></i> ۱۴۰۳/۰۲/۱۳</span>
                    <span><i class="bi bi-clock"></i> ۰۹:۳۰</span>
                    <span><i class="bi bi-eye"></i> ۶۴۲</span>
                    <a href="#" class="read-more">ادامه ←</a>
                </div>
            </div>
        </div>

        <!-- Card 6 -->
        <div class="archive-card fade-in">
            <div class="archive-card-img">
                <img src="https://images.unsplash.com/photo-1624421980204-22e40117494f?w=600&q=80" alt="خبر">
                <span class="archive-card-badge badge-purple">فرهنگی</span>
            </div>
            <div class="archive-card-body">
                <div class="archive-card-overtitle">فرهنگی — هنری</div>
                <a href="#" class="archive-card-title">جشنواره فرهنگی آینه‌خانه در بجنورد با شرکت هنرمندان سراسر
                    کشور</a>
                <p class="archive-card-lead">این جشنواره با هدف معرفی فرهنگ و هنر خراسان شمالی برگزار می‌شود.</p>
                <div class="archive-card-meta">
                    <span><i class="bi bi-calendar3"></i> ۱۴۰۳/۰۲/۱۲</span>
                    <span><i class="bi bi-clock"></i> ۱۶:۰۰</span>
                    <span><i class="bi bi-eye"></i> ۱,۱۰۳</span>
                    <a href="#" class="read-more">ادامه ←</a>
                </div>
            </div>
        </div>

        <!-- Card 7 -->
        <div class="archive-card fade-in">
            <div class="archive-card-img">
                <img src="https://images.unsplash.com/photo-1613441589134-3fc7f95a3e16?w=300&q=70" alt="خبر">
                <span class="archive-card-badge badge-blue">سیاسی</span>
            </div>
            <div class="archive-card-body">
                <div class="archive-card-overtitle">سیاسی — داخلی</div>
                <a href="#" class="archive-card-title">استاندار خراسان شمالی: توسعه زیرساخت‌ها در اولویت است</a>
                <p class="archive-card-lead">در نشست هم‌اندیشی مدیران استانی بر تسریع در اجرای پروژه‌های عمرانی تأکید
                    شد.</p>
                <div class="archive-card-meta">
                    <span><i class="bi bi-calendar3"></i> ۱۴۰۳/۰۲/۱۵</span>
                    <span><i class="bi bi-clock"></i> ۱۵:۴۵</span>
                    <span><i class="bi bi-eye"></i> ۱,۲۳۰</span>
                    <a href="#" class="read-more">ادامه ←</a>
                </div>
            </div>
        </div>

        <!-- Card 8 -->
        <div class="archive-card fade-in">
            <div class="archive-card-img">
                <img src="https://images.unsplash.com/photo-1730992907035-65ec4eaea035?w=300&q=70" alt="خبر">
                <span class="archive-card-badge badge-orange">اقتصادی</span>
            </div>
            <div class="archive-card-body">
                <div class="archive-card-overtitle">اقتصادی — آمار</div>
                <a href="#" class="archive-card-title">نرخ بیکاری استان به پایین‌ترین رقم در ۵ سال اخیر رسید</a>
                <p class="archive-card-lead">آمار رسمی نشان می‌دهد نرخ بیکاری در خراسان شمالی با کاهش ۲ درصدی مواجه شده
                    است.</p>
                <div class="archive-card-meta">
                    <span><i class="bi bi-calendar3"></i> ۱۴۰۳/۰۲/۱۴</span>
                    <span><i class="bi bi-clock"></i> ۱۲:۱۰</span>
                    <span><i class="bi bi-eye"></i> ۸۷۵</span>
                    <a href="#" class="read-more">ادامه ←</a>
                </div>
            </div>
        </div>

        <!-- Card 9 -->
        <div class="archive-card fade-in">
            <div class="archive-card-img">
                <img src="https://images.unsplash.com/photo-1613441586223-f56ec7d17465?w=300&q=70" alt="خبر">
                <span class="archive-card-badge badge-red">ورزشی</span>
            </div>
            <div class="archive-card-body">
                <div class="archive-card-overtitle">ورزشی — فوتبال</div>
                <a href="#" class="archive-card-title">تیم فوتبال شاهین بجنورد به لیگ دسته اول صعود کرد</a>
                <p class="archive-card-lead">با برتری مقابل حریف در آخرین بازی فصل، شاهین موفق به کسب سهمیه صعود شد.</p>
                <div class="archive-card-meta">
                    <span><i class="bi bi-calendar3"></i> ۱۴۰۳/۰۲/۱۱</span>
                    <span><i class="bi bi-clock"></i> ۲۱:۰۰</span>
                    <span><i class="bi bi-eye"></i> ۳,۴۵۶</span>
                    <a href="#" class="read-more">ادامه ←</a>
                </div>
            </div>
        </div>

        <!-- Card 10 -->
        <div class="archive-card fade-in">
            <div class="archive-card-img">
                <img src="https://images.unsplash.com/photo-1677442135732-00cab8f454e1?w=300&q=70" alt="خبر">
                <span class="archive-card-badge badge-teal">فناوری</span>
            </div>
            <div class="archive-card-body">
                <div class="archive-card-overtitle">فناوری — ارتباطات</div>
                <a href="#" class="archive-card-title">راه‌اندازی شبکه فیبر نوری پرسرعت در ۳۰ روستای استان</a>
                <p class="archive-card-lead">طرح توسعه اینترنت پرسرعت روستایی با هدف کاهش شکاف دیجیتال آغاز به کار کرد.
                </p>
                <div class="archive-card-meta">
                    <span><i class="bi bi-calendar3"></i> ۱۴۰۳/۰۲/۱۲</span>
                    <span><i class="bi bi-clock"></i> ۱۶:۳۰</span>
                    <span><i class="bi bi-eye"></i> ۱,۱۰۳</span>
                    <a href="#" class="read-more">ادامه ←</a>
                </div>
            </div>
        </div>

        <!-- Card 11 -->
        <div class="archive-card fade-in">
            <div class="archive-card-img">
                <img src="https://images.unsplash.com/photo-1650700592969-17894c365f6a?w=300&q=70" alt="خبر">
                <span class="archive-card-badge badge-green">اجتماعی</span>
            </div>
            <div class="archive-card-body">
                <div class="archive-card-overtitle">اجتماعی — زیرساخت</div>
                <a href="#" class="archive-card-title">اجرای طرح آب‌رسانی به ۱۵ روستای محروم شمال استان</a>
                <p class="archive-card-lead">این طرح با اعتبار ۳۰ میلیارد تومان از محل اعتبارات ملی اجرا می‌شود.</p>
                <div class="archive-card-meta">
                    <span><i class="bi bi-calendar3"></i> ۱۴۰۳/۰۲/۱۰</span>
                    <span><i class="bi bi-clock"></i> ۰۹:۱۵</span>
                    <span><i class="bi bi-eye"></i> ۷۲۸</span>
                    <a href="#" class="read-more">ادامه ←</a>
                </div>
            </div>
        </div>

        <!-- Card 12 -->
        <div class="archive-card fade-in">
            <div class="archive-card-img">
                <img src="https://images.unsplash.com/photo-1585621243952-f68eae991dd6?w=500&q=80" alt="خبر">
                <span class="archive-card-badge badge-purple">فرهنگی</span>
            </div>
            <div class="archive-card-body">
                <div class="archive-card-overtitle">فرهنگی — میراث</div>
                <a href="#" class="archive-card-title">نمایشگاه بین‌المللی صنایع دستی خراسان شمالی در بجنورد گشایش
                    یافت</a>
                <p class="archive-card-lead">این نمایشگاه با شرکت ۱۲۰ هنرمند از سراسر کشور و چند کشور همسایه برپا شد.
                </p>
                <div class="archive-card-meta">
                    <span><i class="bi bi-calendar3"></i> ۱۴۰۳/۰۲/۱۳</span>
                    <span><i class="bi bi-clock"></i> ۱۰:۰۰</span>
                    <span><i class="bi bi-eye"></i> ۶۴۲</span>
                    <a href="#" class="read-more">ادامه ←</a>
                </div>
            </div>
        </div>

    </div><!-- /archive-grid -->

    <!-- LIST VIEW (hidden by default) -->
    <div class="archive-list" id="archiveList">

        <div class="archive-list-item fade-in">
            <div class="archive-list-img">
                <img src="https://images.unsplash.com/photo-1546085020-407431a34851?w=600&q=80" alt="خبر">
                <span class="archive-list-badge badge-blue">سیاسی</span>
            </div>
            <div class="archive-list-body">
                <div>
                    <div class="archive-list-overtitle">داخلی — رویداد ویژه</div>
                    <a href="#" class="archive-list-title">نشست فوق‌العاده شورای عالی استان‌ها برای بررسی مسائل اقتصادی
                        خراسان شمالی</a>
                    <p class="archive-list-lead">در این نشست که با حضور استانداران و نمایندگان مجلس برگزار شد، به بررسی
                        چالش‌های اقتصادی منطقه و راه‌حل‌های پیشنهادی پرداخته شد.</p>
                </div>
                <div class="archive-list-meta">
                    <span><i class="bi bi-calendar3"></i> ۱۴۰۳/۰۲/۱۵</span>
                    <span><i class="bi bi-clock"></i> ۱۴:۳۰</span>
                    <span><i class="bi bi-eye"></i> ۲,۴۸۵</span>
                    <a href="#" class="read-more">ادامه مطلب ←</a>
                </div>
            </div>
        </div>

        <div class="archive-list-item fade-in">
            <div class="archive-list-img">
                <img src="https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=600&q=80" alt="خبر">
                <span class="archive-list-badge badge-teal">فناوری</span>
            </div>
            <div class="archive-list-body">
                <div>
                    <div class="archive-list-overtitle">فناوری — اختصاصی</div>
                    <a href="#" class="archive-list-title">راه‌اندازی مرکز نوآوری و فناوری استان خراسان شمالی در
                        بجنورد</a>
                    <p class="archive-list-lead">مرکز نوآوری بجنورد با ظرفیت پذیرش ۵۰ استارتاپ فعال، به عنوان بزرگترین
                        مرکز رشد فناوری در شمال شرق کشور آغاز به کار کرد.</p>
                </div>
                <div class="archive-list-meta">
                    <span><i class="bi bi-calendar3"></i> ۱۴۰۳/۰۲/۱۴</span>
                    <span><i class="bi bi-clock"></i> ۱۰:۱۵</span>
                    <span><i class="bi bi-eye"></i> ۱,۸۷۲</span>
                    <a href="#" class="read-more">ادامه مطلب ←</a>
                </div>
            </div>
        </div>

        <div class="archive-list-item fade-in">
            <div class="archive-list-img">
                <img src="https://images.unsplash.com/photo-1613441586223-f56ec7d17465?w=300&q=70" alt="خبر">
                <span class="archive-list-badge badge-red">ورزشی</span>
            </div>
            <div class="archive-list-body">
                <div>
                    <div class="archive-list-overtitle">ورزشی — فوتبال</div>
                    <a href="#" class="archive-list-title">تیم فوتبال شاهین بجنورد به لیگ دسته اول صعود کرد</a>
                    <p class="archive-list-lead">با برتری مقابل حریف خود در آخرین بازی فصل، تیم شاهین موفق به کسب سهمیه
                        صعود به لیگ دسته اول شد.</p>
                </div>
                <div class="archive-list-meta">
                    <span><i class="bi bi-calendar3"></i> ۱۴۰۳/۰۲/۱۱</span>
                    <span><i class="bi bi-clock"></i> ۲۱:۰۰</span>
                    <span><i class="bi bi-eye"></i> ۳,۴۵۶</span>
                    <a href="#" class="read-more">ادامه مطلب ←</a>
                </div>
            </div>
        </div>

        <div class="archive-list-item fade-in">
            <div class="archive-list-img">
                <img src="https://images.unsplash.com/photo-1604646357333-ecb1f24b2d21?w=400&q=75" alt="خبر">
                <span class="archive-list-badge badge-orange">اقتصادی</span>
            </div>
            <div class="archive-list-body">
                <div>
                    <div class="archive-list-overtitle">اقتصادی</div>
                    <a href="#" class="archive-list-title">رشد ۲۵ درصدی تولیدات صنعتی بجنورد در سال جاری</a>
                    <p class="archive-list-lead">آمار منتشر شده نشان می‌دهد تولیدات صنعتی شهرستان بجنورد با رشد چشمگیری
                        روبرو بوده است.</p>
                </div>
                <div class="archive-list-meta">
                    <span><i class="bi bi-calendar3"></i> ۱۴۰۳/۰۲/۱۴</span>
                    <span><i class="bi bi-clock"></i> ۱۱:۴۵</span>
                    <span><i class="bi bi-eye"></i> ۸۷۵</span>
                    <a href="#" class="read-more">ادامه مطلب ←</a>
                </div>
            </div>
        </div>

        <div class="archive-list-item fade-in">
            <div class="archive-list-img">
                <img src="https://images.unsplash.com/photo-1681393993243-f7ef6a79291d?w=400&q=75" alt="خبر">
                <span class="archive-list-badge badge-green">اجتماعی</span>
            </div>
            <div class="archive-list-body">
                <div>
                    <div class="archive-list-overtitle">اجتماعی — بهداشت</div>
                    <a href="#" class="archive-list-title">بهره‌برداری از مجتمع درمانی جدید در شیروان</a>
                    <p class="archive-list-lead">این مجتمع درمانی با جدیدترین تجهیزات پزشکی آماده ارائه خدمات به
                        شهروندان
                        شد.</p>
                </div>
                <div class="archive-list-meta">
                    <span><i class="bi bi-calendar3"></i> ۱۴۰۳/۰۲/۱۳</span>
                    <span><i class="bi bi-clock"></i> ۰۹:۳۰</span>
                    <span><i class="bi bi-eye"></i> ۶۴۲</span>
                    <a href="#" class="read-more">ادامه مطلب ←</a>
                </div>
            </div>
        </div>

        <div class="archive-list-item fade-in">
            <div class="archive-list-img">
                <img src="https://images.unsplash.com/photo-1650700592969-17894c365f6a?w=300&q=70" alt="خبر">
                <span class="archive-list-badge badge-green">اجتماعی</span>
            </div>
            <div class="archive-list-body">
                <div>
                    <div class="archive-list-overtitle">اجتماعی — زیرساخت</div>
                    <a href="#" class="archive-list-title">اجرای طرح آب‌رسانی به ۱۵ روستای محروم شمال استان</a>
                    <p class="archive-list-lead">این طرح با اعتبار بالغ بر ۳۰ میلیارد تومان از محل اعتبارات ملی اجرا
                        می‌شود.</p>
                </div>
                <div class="archive-list-meta">
                    <span><i class="bi bi-calendar3"></i> ۱۴۰۳/۰۲/۱۰</span>
                    <span><i class="bi bi-clock"></i> ۰۹:۱۵</span>
                    <span><i class="bi bi-eye"></i> ۷۲۸</span>
                    <a href="#" class="read-more">ادامه مطلب ←</a>
                </div>
            </div>
        </div>

    </div><!-- /archive-list -->

    <!-- PAGINATION -->
    <div class="archive-pagination fade-in">
        <button class="page-btn" disabled><i class="bi bi-chevron-right"></i></button>
        <button class="page-btn active">۱</button>
        <button class="page-btn">۲</button>
        <button class="page-btn">۳</button>
        <button class="page-btn">۴</button>
        <button class="page-btn">۵</button>
        <button class="page-btn dots">...</button>
        <button class="page-btn">۲۰۸</button>
        <button class="page-btn"><i class="bi bi-chevron-left"></i></button>
    </div>

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
    <div class="sidebar-widget fade-in">
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

<script>
    
</script>
<script src="{{ asset('assets/js/script_archive.js') }}"></script>

@endsection
