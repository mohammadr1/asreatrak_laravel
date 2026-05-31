<!doctype html>
<html lang="fa" dir="rtl" data-bs-theme="auto">
<head>


</head>

<body>

<div class="topbar-mini text-white d-flex justify-content-between align-items-center px-3">
    <div id="datetime" class="text-center"></div>
</div>

<style>
    #datetime{
        font-size: 12px;
        margin: 0 auto;
    }

    #datetime > span{
        margin-right: 15px;
    }


     @media (min-width: 768px) {
        #datetime{
            font-size: 11px !important;
            margin: 0 auto !important;
            margin-right: 0 !important;
        }
      }
</style>


<script>
function toHijri(gDate) {
    let jd = Math.floor((gDate - new Date(622, 6, 16)) / 86400000) + 1948439.5;
    let l = jd - 1948440 + 10632;
    let n = Math.floor((l - 1) / 10631);
    l = l - 10631 * n + 354;
    let j = (Math.floor((10985 - l) / 5316)) * (Math.floor((50 * l) / 17719)) + (Math.floor(l / 5670)) * (Math.floor((43 * l) / 15238));
    l = l - (Math.floor((30 - j) / 15)) * (Math.floor((17719 * j) / 50)) - (Math.floor(j / 16)) * (Math.floor((15238 * j) / 43)) + 29;
    let m = Math.floor((24 * l) / 709);
    let d = l - Math.floor((709 * m) / 24);
    let y = 30 * n + j - 30;
    return `${d}/${m}/${y}`;
}

function updateDateTime() {
    const now = new Date();

    const faDate = now.toLocaleDateString('fa-IR');
    const enDate = now.toLocaleDateString('en-GB');
    const hijriDate = toHijri(now);
    const timeStr = now.toLocaleTimeString('fa-IR');

    document.getElementById('datetime').innerHTML = `
        <span><i class="fas fa-calendar-alt"></i> شمسی: ${faDate}</span>
        <span><i class="fas fa-calendar-day"></i> میلادی: ${enDate}</span>
        <span><i class="fas fa-moon"></i> قمری: ${hijriDate}</span>
        <span><i class="fas fa-clock"></i> ${timeStr}</span>
    `;
}
setInterval(updateDateTime, 1000);
updateDateTime();
</script>


    @include('customer.layouts.theme')

    <header>
        {{-- <nav class="navbar navbar-expand-md navbar-dark fixed-top p-0"> --}}
            @include('customer.layouts.header')
        {{-- </nav> --}}
    </header>




    <main>
        <div class="container">

            @yield('content')

        </div>
    </main>



    <!-- FOOTER -->
    <footer class="footer pt-4 pb-2 mt-5 border-top">
        <div class="container">
            @include('customer.layouts.footer')
        </div>
    </footer>


    </main>

    <!-- دکمه بالابر -->
    {{-- <button id="scrollToTopBtn" class="btn-scroll-top" title="بازگشت به بالا">
        <i class="fas fa-arrow-up"></i>
    </button> --}}

    @include('customer.layouts.scripts')
    @yield('scripts')
    {{-- start sweetalert --}}
    <script src="{{ asset('assets/js/sweetalert2@11.js') }}"></script>
<script>
    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'موفق!',
            text: '{{ session('success') }}',
            confirmButtonText: 'باشه'
        });
    @endif

    @if (session('error'))
        Swal.fire({
            icon: 'error',
            title: 'خطا!',
            text: '{{ session('error') }}',
            confirmButtonText: 'باشه'
        });
    @endif

    @if ($errors->any())
        Swal.fire({
            icon: 'warning',
            title: 'خطا در ارسال فرم!',
            html: `{!! implode('<br>', $errors->all()) !!}`,
            confirmButtonText: 'باشه'
        });
    @endif
</script>
    {{-- end sweetalert --}}

</body>

</html>
