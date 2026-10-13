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

        /*
         * اخبار ویژه: حداکثر ۴ خبر آخر
         */
        $featuredNews = $publicNews()
            ->where('featured', true)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        /*
         * فهرست اصلی اخبار: ۶ خبر آخر
         */
        $latestNews = $publicNews()
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        /*
         * دریافت آخرین خبر از یک یا چند نوع گزارش.
         */
        $latestByReportType = function (array $names) use ($publicNews) {
            $typeIds = ReportType::query()
                ->whereIn('name', $names)
                ->select('id');

            return $publicNews()
                ->whereIn('report_type', $typeIds)
                ->orderByDesc('published_at')
                ->first();
        };

        $reportNews = $latestByReportType(['گزارش']);
        $conversationNews = $latestByReportType(['گفتگوی تفصیلی']);
        $noteNews = $latestByReportType(['یادداشت']);

        /*
         * پربازدیدترین خبرها
         */
        $popularNews = $publicNews()
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
            'sliderNews',
            'featuredNews',
            'latestNews',
            'reportNews',
            'conversationNews',
            'noteNews',
            'multimediaItems',
            'popularNews',
        ));
    }
}