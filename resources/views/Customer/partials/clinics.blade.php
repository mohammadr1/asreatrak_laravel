
@php
    $clinics = config('clinics', []);
@endphp

@if (count($clinics))
    <section class="sidebar-widget clinics-widget fade-in"
             aria-labelledby="clinics-title">

        <div class="clinics-header">
            <div class="clinics-heading-icon" aria-hidden="true">
                <i class="bi bi-hospital"></i>
            </div>

            <div class="clinics-heading-text">
                <h2 id="clinics-title">درمانگاه‌های خراسان شمالی</h2>
                <p>دسترسی سریع به مراکز و نوبت‌دهی</p>
            </div>

            <span class="clinics-count">
                {{ count($clinics) }}
            </span>
        </div>

        <div class="clinics-list">
            @foreach ($clinics as $clinic)
                <a
                    class="clinic-link"
                    href="{{ $clinic['url'] }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="ورود به سایت {{ $clinic['title'] }}"
                >
                    <span class="clinic-number" aria-hidden="true">
                        {{ sprintf('%02d', $loop->iteration) }}
                    </span>

                    <span class="clinic-title">
                        {{ $clinic['title'] }}
                    </span>

                    <span class="clinic-arrow" aria-hidden="true">
                        <i class="bi bi-arrow-up-left"></i>
                    </span>
                </a>
            @endforeach
        </div>

        <div class="clinics-footer">
            <i class="bi bi-info-circle" aria-hidden="true"></i>
            <span>برای ورود به سامانه هر مرکز، روی عنوان آن کلیک کنید.</span>
        </div>

    </section>
@endif
