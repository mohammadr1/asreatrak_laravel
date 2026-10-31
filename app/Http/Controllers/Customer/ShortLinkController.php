<?php

namespace App\Http\Controllers\Customer;

use App\Enums\NewsStatus;
use App\Http\Controllers\Controller;
use App\Models\News;
use App\Support\ShortLink;
use Illuminate\Http\RedirectResponse;

/**
 * مسیر لینک کوتاه خبر:  /short/{code}
 *
 * هم کدهای جدید (کد خبر، مثل 20261031124151) و هم کدهای ۵ کاراکتری
 * سایت قبلی (مثل eqYkB) از همین مسیر باز می‌شوند و به صفحه‌ی اصلی خبر
 * هدایت می‌شوند.
 */
class ShortLinkController extends Controller
{
    public function __invoke(string $code): RedirectResponse
    {
        $code = ShortLink::normalizeCode($code);

        abort_if($code === null, 404);

        $news = $this->findNews($code);

        abort_if($news === null, 404);

        // همان قاعده‌ی صفحه‌ی خبر: فقط خبرهای منتشرشده و رسیده به زمان انتشار
        abort_unless(
            $news->status === NewsStatus::Published
            && (! $news->published_at || $news->published_at->lte(now())),
            404
        );

        // اگر لینک کوتاه خبر خالی یا ناهماهنگ بود (مثلاً خبرهای قدیمی که با
        // دامنه‌ی اشتباه ذخیره شده‌اند) همان لحظه اصلاح می‌شود.
        $canonical = ShortLink::canonicalFor($news);

        if ($canonical !== null && $canonical !== $news->short_link) {
            News::whereKey($news->id)->toBase()->update(['short_link' => $canonical]);
        }

        return redirect()->route('news.show', ['news' => $news->slug], 301);
    }

    private function findNews(string $code): ?News
    {
        $candidates = News::query()
            ->where('news_code', $code)
            ->orWhere('short_link', 'like', '%/' . ShortLink::prefix() . '/' . $code . '%')
            ->limit(20)
            ->get();

        return $candidates->first(
            fn (News $news) => $news->news_code === $code
                || ShortLink::codeFromUrl($news->short_link) === $code
        );
    }
}
