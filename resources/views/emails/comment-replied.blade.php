<!DOCTYPE html>
<html lang="fa" dir="rtl">
<body style="font-family:Tahoma,Arial,sans-serif;line-height:2;direction:rtl;text-align:right">
    <p>{{ $comment->name }} عزیز، سلام</p>
    <p>به نظر شما درباره‌ی خبر «{{ $comment->news->title }}» پاسخ داده شد:</p>

    <blockquote style="margin:12px 0;padding:10px 14px;border-right:3px solid #0d6efd;background:#f3f4f6;color:#111">
        {!! nl2br(e($reply->body)) !!}
    </blockquote>

    <p><a href="{{ route('news.show', $comment->news) }}#comment-{{ $comment->id }}">مشاهده‌ی گفتگو در سایت</a></p>

    <p style="font-size:12px;color:#777">
        این ایمیل به این دلیل برایتان ارسال شد که هنگام ثبت نظر، گزینه‌ی «در صورت پاسخ به من اطلاع بده» را فعال کرده بودید.
    </p>
</body>
</html>