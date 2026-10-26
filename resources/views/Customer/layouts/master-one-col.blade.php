<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    @include('Customer.layouts.head-tag')
    {{-- Shared public-site theme: home, article and archive use the same shell. --}}
    <link rel="stylesheet" href="{{ asset('assets/css/media_card_home_gallery.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/home.css') }}">
    @yield('head-tag')
</head>

<body>

<!-- ========================
     TOP BAR
========================= -->

@include('Customer.layouts.top_bar')

<!-- ========================
     HEADER
========================= -->

@include('Customer.layouts.header')


<!-- ========================
     MAIN NAVIGATION
========================= -->

@include('Customer.layouts.navbar')



@include('Customer.layouts.breaking_bar', ['news' => $news])


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

@include('Customer.layouts.footer')



<!-- Scroll to Top -->
<button id="scrollTop" type="button" aria-label="بازگشت به ابتدای صفحه" onclick="window.scrollTo({top:0,behavior:'smooth'})">
  <i class="bi bi-chevron-up" aria-hidden="true"></i>
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
@include('Customer.layouts.scripts')

@yield('scripts')

</body>

</html>
