<nav class="main-nav">
  <div class="container-xl">
    <div class="nav-inner">
      <!-- Hamburger -->
      <button class="nav-toggle" id="navToggle" aria-label="Toggle Navigation">
        <i class="bi bi-list"></i>
      </button>

      <!-- Nav Links -->
      <ul class="nav-links" id="navLinks">
        <li class="active"><a href="#"><i class="bi bi-house-fill"></i> خانه</a></li>
        <li>
          <a href="#">سیاسی <i class="bi bi-chevron-down" ></i></a>
          <div class="dropdown-menu">
            <a href="#">داخلی</a>
            <a href="#">خارجی</a>
            <a href="#">دیپلماسی</a>
          </div>
        </li>
        <li>
          <a href="#">اجتماعی <i class="bi bi-chevron-down" ></i></a>
          <div class="dropdown-menu">
            <a href="#">آموزش</a>
            <a href="#">بهداشت</a>
            <a href="#">شهری</a>
          </div>
        </li>
        <li><a href="#">اقتصادی</a></li>
        <li><a href="#">فرهنگی</a></li>
        <li><a href="#">ورزشی</a></li>
        <li><a href="#">حوادث</a></li>
        <li><a href="#">استانی</a></li>
        <li><a href="#">چندرسانه</a></li>
      </ul>

      <!-- Right section: datetime + social -->
      <div class="nav-right">
        <div class="nav-datetime">
          
          <button onclick="toggleTheme()" class="theme-btn">
            <i class="bi bi-moon"></i>
          </button>
          
        </div>
        <div class="nav-datetime">
          <span id="nav-date-str" ></span>
          <span class="dt-time" id="nav-clock">--:--:--</span>
        </div>
        <div class="nav-social">
          <a href="https://t.me/" target="_blank" title="تلگرام"><i class="bi bi-telegram"></i></a>
          <a href="https://instagram.com/" target="_blank" title="اینستاگرام"><i class="bi bi-instagram"></i></a>
          <a href="https://twitter.com/" target="_blank" title="توییتر/ایکس"><i class="bi bi-twitter-x"></i></a>
          <a href="https://www.aparat.com/" target="_blank" title="آپارات">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm0 4.5c4.142 0 7.5 3.358 7.5 7.5S16.142 19.5 12 19.5 4.5 16.142 4.5 12 7.858 4.5 12 4.5zm0 2.25c-2.9 0-5.25 2.35-5.25 5.25S9.1 17.25 12 17.25s5.25-2.35 5.25-5.25S14.9 6.75 12 6.75zm0 2.25a3 3 0 110 6 3 3 0 010-6z"/></svg>
          </a>
          <a href="https://wa.me/" target="_blank" title="واتساپ"><i class="bi bi-whatsapp"></i></a>
        </div>
      </div>
    </div>
  </div>
</nav>