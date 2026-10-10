
<div dir="rtl" class="news-preview">
    <article class="news-preview__article">

        @if (!empty($news['uptitle']))
            <div class="news-preview__uptitle">
                {{ $news['uptitle'] }}
            </div>
        @endif

        <h1 class="news-preview__title">
            {{ $news['title'] ?: 'عنوان خبر وارد نشده است' }}
        </h1>

        @if (!empty($news['lead']))
            <p class="news-preview__lead">
                {{ $news['lead'] }}
            </p>
        @endif

        <div class="news-preview__content">
            {{-- فعلاً محتوای HTML را عمداً اجرا نمی‌کنیم.
                 در مرحله بعد، بعد از پاک‌سازی امن HTML،
                 محتوای قالب‌بندی‌شده را نمایش می‌دهیم. --}}
            <pre class="news-preview__raw">{!! $news['content'] !!}</pre>
        </div>

    </article>
</div>

<style>
    .news-preview {
        direction: rtl;
        color: #222;
        background: #fff;
        padding: 24px;
        border-radius: 10px;
        font-family: inherit;
    }

    .news-preview__article {
        max-width: 850px;
        margin: 0 auto;
    }

    .news-preview__uptitle {
        color: #777;
        font-size: 14px;
        margin-bottom: 12px;
    }

    .news-preview__title {
        font-size: 28px;
        line-height: 1.8;
        font-weight: 800;
        margin: 0 0 18px;
    }

    .news-preview__lead {
        font-size: 17px;
        font-weight: 600;
        line-height: 2;
        color: #555;
        margin-bottom: 28px;
    }

    .news-preview__content {
        font-size: 16px;
        line-height: 2.2;
    }

    .news-preview__raw {
        white-space: pre-wrap;
        overflow-wrap: anywhere;
        font: inherit;
    }
</style>