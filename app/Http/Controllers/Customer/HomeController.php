<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\News;
use App\Models\ReportType;
use App\Support\AparatVideo;

class HomeController extends Controller
{
    public function home()
    {
        /*
         * کوئری پایه:
         * فقط خبرهای قابل نمایش عمومی، همراه با تصویر و دسته‌بندی.
         */
        $publicNews = fn () => News::query()
            ->publiclyVisible()
            ->with(['featuredMedia', 'categories']);

        $sliderNews = News::query()
            ->publiclyVisible()
            ->where('slider', true)
            ->with(['featuredMedia', 'categories'])
            ->latest('published_at')
            ->latest('id')
            ->limit(2)
            ->get();

        $breakingNews = News::query()
            ->publiclyVisible()
            ->where('is_breaking', true)
            ->with('categories')
            ->latest('published_at')
            ->latest('id')
            ->limit(10)
            ->get();

        /*
         * اخبار ویژه: حداکثر 2 خبر آخر
         */
        $featuredNews = $publicNews()
            ->where('featured', true)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(2)
            ->get();

        /*
         * فهرست اصلی اخبار: ۶ خبر آخر
         */
        $latestNews = $publicNews()
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(12)
            ->get();


        /*
        * دریافت آخرین اخبار از یک یا چند نوع گزارش.
        * خروجی همیشه Collection است.
        */
        $latestByReportType = function (
            array $names,
            int $limit = 4
        ) use ($publicNews) {
            $typeIds = ReportType::query()
                ->whereIn('name', $names)
                ->select('id');

            return $publicNews()
                ->whereIn('report_type', $typeIds)
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->limit($limit)
                ->get();
        };

        /*
        * خبر اصلی + حداکثر سه خبر مکمل برای هر بخش
        */
        $reportNewsItems = $latestByReportType(['گزارش'], 4);

        $conversationNewsItems = $latestByReportType(
            ['گفتگوی تفصیلی'],
            4
        );

        $noteNewsItems = $latestByReportType(['یادداشت'], 4);

        /*
        * برای حفظ سازگاری با سایر بخش‌های احتمالی قالب
        */
        $reportNews = $reportNewsItems->first();

        $conversationNews = $conversationNewsItems->first();

        $noteNews = $noteNewsItems->first();

        $reportNewsItems = $latestByReportType(['گزارش'], 4);
        $conversationNewsItems = $latestByReportType(['گفتگوی تفصیلی'], 4);
        $noteNewsItems = $latestByReportType(['یادداشت'], 4);

        $reportNews = $reportNewsItems->first();
        $conversationNews = $conversationNewsItems->first();
        $noteNews = $noteNewsItems->first();
        /*
         * پربازدیدترین خبرها
         */
        $popularNews = $publicNews()
            ->where('published_at', '>=', now()->subDays(3))
            ->where('published_at', '<=', now())
            ->orderByDesc('views_count')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        /*
         * چندرسانه
         */
        $multimediaCategoryId = Category::where('name', 'چندرسانه‌ای')->value('id');

        $multimediaNews = $publicNews()
            ->whereHas('categories', fn ($q) => $q->where('categories.id', $multimediaCategoryId))
            ->with('media')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        $multimediaItems = collect();

        foreach ($multimediaNews as $item) {
            $link     = route('news.show', $item);
            $embedUrl = AparatVideo::embedUrl($item->aparat_video_id);
            $isVideo  = $item->post_type === 'video' && $embedUrl;

            $push = function (string $type, ?string $src, ?string $alt, ?string $aparat = null) use (&$multimediaItems, $item, $link) {
                if (! $src) {
                    return;
                }
                $multimediaItems->push([
                    'type'   => $type,
                    'src'    => $src,
                    'alt'    => $alt ?: $item->title,
                    'title'  => $item->title,
                    'link'   => $link,
                    'views'  => $item->views_count ?? 0,
                    'aparat' => $aparat,
                ]);
            };

            $featuredUrl = $item->featuredMedia?->variantUrl($item->featured_media_variant ?: 'watermarked')
                ?: $item->featuredMedia?->variantUrl('cropped')
                ?: $item->featuredMedia?->variantUrl('original');

            if ($isVideo) {
                $push('video', $featuredUrl, $item->featuredMedia?->alt, $embedUrl);
                continue;
            }

            $push('photo', $featuredUrl, $item->featuredMedia?->alt);

            foreach ($item->media as $m) {
                $url = $m->variantUrl('watermarked') ?: $m->variantUrl('cropped') ?: $m->variantUrl('original');
                if ($url && $url !== $featuredUrl) {
                    $push('photo', $url, $m->alt);
                }
            }

            foreach ($item->bodyImages() as $img) {
                if ($img['src'] !== $featuredUrl) {
                    $push('photo', $img['src'], $img['alt']);
                }
            }
        }

        $multimediaItems = $multimediaItems->unique('src')->values()->take(60);

        return view('customer.home', compact(
            'breakingNews',
            'sliderNews',
            'featuredNews',
            'latestNews',
            'reportNews',
            'reportNewsItems',
            'conversationNews',
            'conversationNewsItems',
            'noteNews',
            'noteNewsItems',
            'multimediaItems',
            'popularNews',
        ));
    }
}