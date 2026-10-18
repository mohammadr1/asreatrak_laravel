<nav class="main-nav">
  <div class="container-xl">
    <div class="nav-inner">
      <!-- Hamburger -->
      <button class="nav-toggle" id="navToggle" type="button" aria-label="باز کردن منوی اصلی" aria-controls="navLinks" aria-expanded="false">
        <i class="bi bi-list" aria-hidden="true"></i>
      </button>

      <!-- Nav Links -->
      <!-- <ul class="nav-links" id="navLinks">
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
      </ul> -->

      <ul class="nav-links" id="navLinks">
        <li class="{{ request()->routeIs('customer.home') ? 'active' : '' }}">
          <a href="{{ route('customer.home') }}"><i class="bi bi-house-fill"></i> خانه</a>
        </li>

        @foreach($navCategories as $category)
          <li class="{{ request()->routeIs('category.show') && request()->route('category')?->id === $category->id ? 'active' : '' }}">
            <a href="{{ route('category.show', $category) }}">{{ $category->name }}</a>
          </li>
        @endforeach
      </ul>

      <!-- Search -->
      <form class="nav-search d-none d-lg-block" action="{{ route('news.archive') }}" method="GET" role="search">
        <input type="hidden" name="sort" value="relevant">
        <input type="search" name="q" value="{{ request('q') }}"
              placeholder="جستجو در اخبار..." aria-label="جستجو در اخبار"
              maxlength="100" autocomplete="off" required>
        <button type="submit" aria-label="جستجو"><i class="bi bi-search" aria-hidden="true"></i></button>
      </form>

      <!-- جستجو در موبایل: به صفحه‌ی آرشیف (که فیلتر و جستجو دارد) می‌رود -->
      <a href="{{ route('news.archive') }}" class="nav-search-link d-lg-none" aria-label="جستجو در اخبار">
        <i class="bi bi-search" aria-hidden="true"></i>
      </a>
      
      <!-- Right section: datetime + social -->
      <div class="nav-right">
        <div class="nav-datetime">
          
          <button onclick="toggleTheme()" class="theme-btn" type="button" aria-label="تغییر حالت روشن و تیره">
            <i class="bi bi-moon" aria-hidden="true"></i>
          </button>
          
        </div>
        <div class="nav-datetime">
          <span id="nav-date-str" ></span>
          <span class="dt-time" id="nav-clock">--:--:--</span>
        </div>
        <div class="nav-social">
          @foreach(config('social.links') as $s)
            @if(filled($s['url']) && ! empty($s['icon']))
              <a href="{{ $s['url'] }}" target="_blank" rel="noopener noreferrer" title="{{ $s['title'] }}">
                <i class="bi {{ $s['icon'] }}"></i>
              </a>
            @endif
          @endforeach
        </div>
      </div>
    </div>
  </div>
</nav>
