<?php

namespace App\Observers;

use App\Models\News;
use App\Support\ShortLink;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NewsObserver
{
    public function creating(News $news): void
    {
        if (blank($news->slug)) {
            $news->slug = Str::slug($news->title . '-' . uniqid());
        }

        if (blank($news->news_code)) {
            $news->news_code = $this->generateNewsCode();
        }

        if (blank($news->created_by) && Auth::check()) {
            $news->created_by = Auth::id();
        }

        if (blank($news->reporter_id) && Auth::check()) {
            $news->reporter_id = Auth::id();
        }

        $this->syncShortLink($news);
    }

    public function updating(News $news): void
    {
        $this->syncShortLink($news);
    }

    /**
     * هر خبر همیشه یک لینک کوتاه هم‌خوان با کد خبر خود دارد:
     * https://asreatrak.ir/short/20261031124151
     *
     * برای خبرهای قدیمی (کد ۵ کاراکتری) فقط دامنه/قالب اصلاح می‌شود و
     * کد قبلی دست‌نخورده می‌ماند تا لینک‌های منتشرشده از کار نیفتند.
     */
    private function syncShortLink(News $news): void
    {
        $shortLink = ShortLink::canonicalFor($news);

        if ($shortLink !== null) {
            $news->short_link = $shortLink;
        }
    }

    private function generateNewsCode(): string
    {
        return 'BN-' . now()->format('ymd') . '-' . str_pad(
            News::count() + 1,
            5,
            '0',
            STR_PAD_LEFT
        );
    }
}