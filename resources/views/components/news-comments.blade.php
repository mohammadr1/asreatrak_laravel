@php
    use Morilog\Jalali\CalendarUtils;
    use Morilog\Jalali\Jalalian;

    $fa   = fn ($v) => CalendarUtils::convertNumbers((string) $v);
    $when = fn ($c) => $fa(Jalalian::fromCarbon($c->created_at)->format('Y/m/d — H:i'));
@endphp

<div class="comments-section" id="comments">

    <div class="section-header">
        <span class="section-title">
            <i class="bi bi-chat-left-text-fill me-1"></i>نظرات کاربران
            <span>({{ $fa($total) }} نظر)</span>
        </span>
    </div>

    @if(session('comment_status'))
        <div class="alert alert-success" role="status">{{ session('comment_status') }}</div>
    @endif

    {{-- لیست نظرها --}}
    @forelse($comments as $comment)
        <div class="comment-box" id="comment-{{ $comment->id }}">
            <div class="comment-header">
                <div class="comment-avatar">{{ mb_substr($comment->name, 0, 1) }}</div>
                <div class="comment-name">{{ $comment->name }}</div>
                <div class="comment-date"><i class="bi bi-clock me-1"></i>{{ $when($comment) }}</div>
            </div>
            <div class="comment-text">{!! nl2br(e($comment->body)) !!}</div>

            @foreach($comment->replies as $reply)
                <div class="comment-reply" id="comment-{{ $reply->id }}">
                    <div class="comment-header">
                        <div class="comment-avatar"><i class="bi bi-person-fill"></i></div>
                        <div class="comment-name">{{ $reply->name }} <span class="comment-badge">پاسخ مدیریت</span></div>
                        <div class="comment-date"><i class="bi bi-clock me-1"></i>{{ $when($reply) }}</div>
                    </div>
                    <div class="comment-text">{!! nl2br(e($reply->body)) !!}</div>
                </div>
            @endforeach
        </div>
    @empty
        <p class="text-center py-3" style="opacity:.7">هنوز نظری ثبت نشده است. اولین نفر باشید.</p>
    @endforelse

    {{-- فرم ثبت نظر --}}
    <div class="comment-form-section mt-3" id="comment-form">
        <h6>
            <i class="bi bi-pencil-square me-2"></i>ثبت نظر
        </h6>
        <p>
            <i class="bi bi-info-circle me-1"></i>
            نظرات پس از تأیید توسط مدیریت سایت منتشر می‌شوند. لطفاً نظرات خود را با ادب و احترام بیان فرمایید.
        </p>

        @if($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('comments.store', $news) }}"
              onsubmit="this.querySelector('button[type=submit]').disabled = true">
            @csrf

            {{-- تله‌ی ربات‌ها: برای کاربر دیده نمی‌شود --}}
            <div class="comment-hp" aria-hidden="true">
                <label>وب‌سایت
                    <input type="text" name="website" tabindex="-1" autocomplete="off">
                </label>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="name" value="{{ old('name') }}" maxlength="100" required
                           class="form-control form-control-sm @error('name') is-invalid @enderror"
                           placeholder="نام و نام‌خانوادگی *" aria-label="نام و نام‌خانوادگی">
                </div>
                <div class="col-md-6">
                    <input type="text" name="emailOrMobile" value="{{ old('emailOrMobile') }}" maxlength="150" required
                        autocomplete="email"
                        class="form-control form-control-sm @error('emailOrMobile') is-invalid @enderror"
                        placeholder="ایمیل یا شماره موبایل * (منتشر نمی‌شود)"
                        aria-label="ایمیل یا شماره موبایل">
                </div>
                <div class="col-12">
                    <textarea name="body" rows="4" maxlength="2000" required
                              class="form-control form-control-sm @error('body') is-invalid @enderror"
                              placeholder="متن نظر خود را بنویسید..." aria-label="متن نظر">{{ old('body') }}</textarea>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <input type="checkbox" id="notifyMe" name="notify_me" value="1"
                               class="form-check-input" @checked(old('notify_me'))>
                        <label for="notifyMe">در صورت پاسخ به من اطلاع بده (فقط با ایمیل)</label>
                    </div>
                    <button type="submit" class="btn-submit-comment">
                        <i class="bi bi-send me-1"></i> ارسال نظر
                    </button>
                </div>
            </div>
        </form>
    </div>

</div>