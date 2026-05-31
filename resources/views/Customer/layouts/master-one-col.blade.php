<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    @include('customer.layouts.head-tag')
    @yield('head-tag')
</head>

<body>

<!-- ========================
     TOP BAR
========================= -->

@include('customer.layouts.top_bar')

<!-- ========================
     HEADER
========================= -->

@include('customer.layouts.header')

<!-- ========================
     MAIN NAVIGATION
========================= -->

@include('customer.layouts.navbar')

<!-- ========================
     BREAKING NEWS TICKER
========================= -->

@include('customer.layouts.breaking_bar')

<!-- ========================
     MAIN CONTENT
========================= -->
<main class="main-content">
  <div class="container-xl">
    <div class="row g-4">

        @yield('content')

    </div><!-- /.row -->
  </div><!-- /.container-xl -->
</main>

<!-- ========================
     FOOTER
========================= -->
<!-- <footer class="site-footer">
  <div class="container-xl">
    <div class="row g-4"> -->

@include('customer.layouts.footer')



<!-- Scroll to Top -->
<button id="scrollTop" onclick="window.scrollTo({top:0,behavior:'smooth'})">
  <i class="bi bi-chevron-up"></i>
</button>


     <!-- Media Modal -->
<div id="mediaModal" class="media-modal d-none">
     <span class="close-modal">&times;</span>

     <img id="modalImage" class="modal-content-img" />

     <video id="modalVideo" class="modal-content-video" controls>
          <source id="modalVideoSource" src="" type="video/mp4">
     </video>
</div>



<script src="{{ asset('assets/js/main_mobile_nav_toggle.js') }}"></script>
@include('customer.layouts.scripts')

@yield('scripts')

</body>

</html>