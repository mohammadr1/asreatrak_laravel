<footer class="site-footer">
  <div class="container-xl">
    <div class="row g-4">

      <!-- Col 1: درباره ما -->
      <div class="col-lg-3 col-md-6 footer-about">
        <div class="footer-widget-title">درباره ما</div>
        <p style="text-align: justify;">
          <strong style="color: var(--accent);">{{ config('app.display_name') }}</strong>
          به عنوان رسانه تخصصی خراسان شمالی، با هدف انعکاس اخبار و رویدادهای استان،
          ارائه تحلیل‌های عمیق و گزارش‌های میدانی فعالیت می‌کند.
          دقت، سرعت و امانتداری محور اصلی فعالیت‌های ما است.
        </p>
        
        
        @if($footerSocials->isNotEmpty())

        <div class="footer-social mt-3">

            @foreach($footerSocials as $social)

                @php
                    $hasImage = !empty($social['image'])
                        && file_exists(public_path($social['image']));
                @endphp

                <a
                    href="{{ $social['url'] }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    title="{{ $social['title'] }}"
                    aria-label="{{ $social['title'] }}"
                    style="--social-color: {{ $social['color'] ?? '#0d6efd' }};"
                >
                    @if($hasImage)
                        <img
                            src="{{ asset($social['image']) }}"
                            alt="{{ $social['title'] }}"
                            loading="lazy"
                        >
                    @elseif(!empty($social['icon']))
                        <i class="bi {{ $social['icon'] }}"></i>
                    @else
                        {{ mb_substr($social['title'], 0, 1) }}
                    @endif
                </a>

            @endforeach

        </div>

    @endif


      </div>

      <!-- Col 2: دسترسی سریع -->
      <div class="col-lg-3 col-md-6">
        <div class="footer-widget-title">دسترسی سریع</div>

        <ul class="footer-links">

            @forelse($footerCategories as $category)

                <li>
                    <a href="{{ route('category.show', $category->slug) }}">
                        <i class="bi bi-chevron-left"></i>
                        {{ $category->name }}
                    </a>
                </li>

            @empty

                <li>
                    <span>دسته‌بندی فعالی وجود ندارد.</span>
                </li>

            @endforelse

            <li>
                <a>
                    <i class="bi bi-chevron-left"></i>
                    ارتباط با ما
                </a>
            </li>

        </ul>
    </div>

      <!-- Col 3: تماس با ما -->
      <div class="col-lg-3 col-md-6">
        <div class="footer-widget-title">تماس با ما</div>
        <div class="footer-contact-item">
          <i class="bi bi-geo-alt-fill"></i>
          <span>بجنورد، خیابان شهید بهشتی، ساختمان رسانه، طبقه سوم</span>
        </div>
        <div class="footer-contact-item">
          <i class="bi bi-telephone-fill"></i>
          <span dir="ltr">۰۵۸-۳۲۲۲۱۱۰۰</span>
        </div>
        <div class="footer-contact-item">
          <i class="bi bi-envelope-fill"></i>
          <span>info@bojnourdnews.ir</span>
        </div>
        <div class="footer-contact-item">
          <i class="bi bi-globe2"></i>
          <span>www.bojnourdnews.ir</span>
        </div>
        <div class="footer-contact-item">
          <i class="bi bi-clock-fill"></i>
          <span>شنبه تا چهارشنبه: ۸ تا ۱۷ | پنج‌شنبه: ۸ تا ۱۳</span>
        </div>
      </div>

      <!-- Col 4: لوگوها -->
      <div class="col-lg-3 col-md-6">
        <div class="footer-widget-title">نشان‌ها و مجوزها</div>
        <div class="footer-logos">
         <div class="footer-license-grid">

                <a href="https://www.e-rasaneh.ir/Certificate/76599"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="license-card"
                   title="سامانه رسانه‌های دیجیتال">

                    <div class="license-card-image">
                        <img
                            src="{{ asset('assets/images/e-resaneh.jpg') }}"
                            alt="نشان سامانه رسانه‌های دیجیتال"
                            loading="lazy">
                    </div>

                    <span>سامانه رسانه‌های دیجیتال</span>
                </a>

                {{-- نشان دوم: در صورت داشتن تصویر واقعی، مسیر را جایگزین کن --}}
                <!-- <a href="https://www.e-rasaneh.ir/Certificate/76599"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="license-card"
                   title="وزارت فرهنگ و ارشاد اسلامی">

                    <div class="license-card-image">
                        <img
                            src="{{ asset('assets/images/ministry-license.png') }}"
                            alt="نشان وزارت فرهنگ و ارشاد اسلامی"
                            loading="lazy">
                    </div>

                    <span>وزارت فرهنگ و ارشاد اسلامی</span>
                </a> -->

            </div>
          
        </div>


      </div>

    </div>
  </div>

  <div class="footer-bottom">
    <div class="container-xl">
        کلیه حقوق برای <strong>{{ config('app.display_name') }}</strong> محفوظ است
        &copy; <span id="footer-year">۱۴۰۳</span>
        &nbsp;|&nbsp; طراحی و توسعه: تیم فناوری خبرگزاری(محمد رحیمی)
    </div>
  </div>
</footer>
