{{-- Shared admin UI assets. Any input with data-jalali-date-input="true" gets the reusable picker. --}}
<script src="{{ asset('assets/js/news-content-editor.js') }}?v={{ filemtime(public_path('assets/js/news-content-editor.js')) }}" defer></script>
<link rel="stylesheet" href="{{ asset('assets/css/admin-jalali-picker.css') }}?v={{ filemtime(public_path('assets/css/admin-jalali-picker.css')) }}">
<script src="{{ asset('assets/js/admin-news-jalali-picker.js') }}?v={{ filemtime(public_path('assets/js/admin-news-jalali-picker.js')) }}" defer></script>
