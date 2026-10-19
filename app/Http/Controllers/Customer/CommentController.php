<?php

namespace App\Http\Controllers\customer;

use App\Enums\CommentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Models\News;

class CommentController extends Controller
{
        public function store(StoreCommentRequest $request, News $news)
    {
        // فقط برای خبرهای منتشرشده می‌شود نظر گذاشت
        abort_unless(News::publiclyVisible()->whereKey($news->getKey())->exists(), 404);

        $message = 'نظر شما ثبت شد و پس از تأیید مدیریت نمایش داده می‌شود.';
        $back    = route('news.show', $news) . '#comments';

        // تله‌ی ربات‌ها (honeypot): انسان این فیلد مخفی را پر نمی‌کند
        if (filled($request->input('website'))) {
            return redirect($back)->with('comment_status', $message);
        }

        $contact = $request->input('emailOrMobile');
        $isEmail = filter_var($contact, FILTER_VALIDATE_EMAIL) !== false;

        $news->comments()->create([
            'name'          => $request->input('name'),
            'emailOrMobile' => $contact,
            'body'          => $request->input('body'),
            // اطلاع‌رسانی فعلاً فقط با ایمیل ممکن است
            'notify_me'     => $isEmail && $request->boolean('notify_me'),
            'status'        => CommentStatus::Pending,
            'ip_address'    => $request->ip(),
        ]);

        return redirect($back)->with('comment_status', $message);
    }
}
