<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\News;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Morilog\Jalali\CalendarUtils;
use Morilog\Jalali\Jalalian;

class NewsController extends Controller
{
    public function show(News $news)
    {
        abort_unless(
            $news->status === \App\Enums\NewsStatus::Published
            && (! $news->published_at || $news->published_at->lte(now())),
            404
        );

            // شمارنده‌ی بازدید (بدون تغییر updated_at)
        // $viewKey = 'viewed_news_' . $news->id;
        // if (! session()->has($viewKey)) {
        //     News::whereKey($news->id)->toBase()->increment('views_count');
        //     $news->views_count = ($news->views_count ?? 0) + 1;
        //     session()->put($viewKey, true);
        // }

        News::whereKey($news->id)->toBase()->increment('views_count');
        $news->views_count = ($news->views_count ?? 0) + 1;
        
        $news->load([
            'reporter',
            'categories',
            'tags',
            'featuredMedia',
            'media',
            'relatedNews' => fn ($query) => $query
                ->publiclyVisible()
                ->with('featuredMedia')
                ->whereKeyNot($news->id)
                ->orderByDesc('news.published_at')
                ->limit(10),
        ]);


        $news->relatedNews->each(function ($related) {
                $related->jalali_published_at = $related->published_at
                    ? Jalalian::fromCarbon($related->published_at)->format('Y/m/d')
                    : null;
            });
        $news->relatedNews->each(function ($related) {
            $related->jalali_published_at = $related->published_at
                ? Jalalian::fromCarbon($related->published_at)->format('Y/m/d')
                : null;
        });

         $news->relatedNews->each(function ($related) {
            $related->jalali_published_at = $related->published_at
                ? Jalalian::fromCarbon($related->published_at)->format('Y/m/d')
                : null;
        });

        $popularNews = News::query()
            ->publiclyVisible()
            ->whereKeyNot($news->id)
            ->orderByDesc('views_count')
            ->limit(4)
            ->get();



        return view('Customer.news.show', [
            'news'        => $news,
            'popularNews' => $popularNews,
            'embedUrl'    => $news->post_type === 'video'
                ? \App\Support\AparatVideo::embedUrl($news->aparat_video_id)
                : null,
    ]);

    }

    /** تاریخ مؤثر خبر: تاریخ انتشار، و اگر نبود تاریخ ساخت */
private const DATE_EXPR = 'COALESCE(news.published_at, news.created_at)';
    public function archive(Request $request)
    {

        $str = fn (string $key): string => is_string($v = $request->query($key)) ? trim($v) : '';

        $categoryRaw = $request->query('category');

        $filters = [
            'q'        => mb_substr($str('q'), 0, 100),
            'category' => is_string($categoryRaw) && ctype_digit($categoryRaw) ? (int) $categoryRaw : null,
            'from'     => $str('from'),
            'to'       => $str('to'),
            'sort'     => in_array($request->query('sort'), ['latest', 'oldest', 'popular', 'relevant'], true)
                            ? $request->query('sort') : 'latest',
        ];

        $from = $this->parseJalali($filters['from']);
        $to   = $this->parseJalali($filters['to']);

        // اگر کاربر تاریخ‌ها را برعکس انتخاب کرد، جابه‌جا شوند
        if ($from && $to && $from->gt($to)) {
            [$from, $to] = [$to, $from];
        }

        $query = News::query()
            ->publiclyVisible()
            ->with(['featuredMedia', 'categories']); // جلوگیری از N+1

        // جستجو در عنوان، لید و روتیتر
        $like = null;
        if ($filters['q'] !== '') {
            $like = '%' . addcslashes($filters['q'], '%_\\') . '%';
            $query->where(fn ($q) => $q
                ->where('news.title', 'like', $like)
                ->orWhere('news.lead', 'like', $like)
                ->orWhere('news.uptitle', 'like', $like));
        }

        // تغییر ۱: فیلتر تاریخ قبل از فیلتر دسته آمد
        if ($from) {
            $query->whereRaw(self::DATE_EXPR . ' >= ?', [$from->startOfDay()]);
        }
        if ($to) {
            $query->whereRaw(self::DATE_EXPR . ' <= ?', [$to->endOfDay()]);
        }

        // نسخه‌ی پایه: فقط فیلتر «متن و تاریخ» (برای شمارنده‌ی کپسول‌ها)
        $baseQuery = clone $query;

        // فیلتر دسته فقط روی لیست اعمال می‌شود
        if ($filters['category']) {
            $query->whereHas('categories', fn ($q) => $q->where('categories.id', $filters['category']));
        }

        // مرتب‌سازی
        $byDateDesc = self::DATE_EXPR . ' DESC';

        match ($filters['sort']) {
            'oldest'   => $query->orderByRaw(self::DATE_EXPR . ' ASC'),
            'popular'  => $query->orderByDesc('news.views_count')->orderByRaw($byDateDesc),
            'relevant' => $like
                ? $query->orderByRaw('(news.title LIKE ?) DESC', [$like])->orderByRaw($byDateDesc)
                : $query->orderByRaw($byDateDesc),
            default    => $query->orderByRaw($byDateDesc),
        };
        $query->orderByDesc('news.id'); // ترتیب پایدار برای صفحه‌بندی

        $news = $query->paginate(12)->withQueryString();

        // تغییر ۲: شمارنده‌ی کپسول‌ها بر اساس فیلتر متن و تاریخ
        $computeCounts = fn () => [
            'total' => (clone $baseQuery)->count(),

            'by_category' => (clone $baseQuery)
                ->join('news_category', 'news_category.news_id', '=', 'news.id')
                ->groupBy('news_category.category_id')
                ->selectRaw('news_category.category_id as category_id, COUNT(DISTINCT news.id) as total')
                ->pluck('total', 'category_id')
                ->all(),
        ];

        // تغییر ۳: بدون جستجو/تاریخ → کش ۱۰ دقیقه‌ای؛ با آن‌ها → زنده
        $counts = ($like || $from || $to)
            ? $computeCounts()
            : Cache::remember('archive.counts', now()->addMinutes(10), $computeCounts);

        $categories = Cache::remember(
            'archive.categories',
            now()->addMinutes(10),
            fn () => Category::query()->orderBy('sort_order')->orderBy('id')->get()
        );

        // همان ساختار قبلی، پس ویو بدون تغییر کار می‌کند
        $stats = [
            'total'      => $counts['total'],
            'categories' => $categories->map(function ($c) use ($counts) {
                $c->published_count = (int) ($counts['by_category'][$c->id] ?? 0);
                return $c;
            }),
        ];

        // برای سایدبار (پارشیال «اخبار پربازدید»)
        $popularNews = News::query()
            ->publiclyVisible()
            ->with('featuredMedia')
            ->orderByDesc('views_count')
            ->limit(5)
            ->get();

        return view('Customer.news.archive', compact('news', 'filters', 'stats', 'popularNews'));
    }

    /** "۱۴۰۳/۰۲/۱۵" یا "1403-2-15" → Carbon (یا null اگر نامعتبر بود) */
    private function parseJalali(string $value): ?Carbon
    {
        if ($value === '') {
            return null;
        }

        $value = strtr($value, [
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
            '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
            '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
        ]);
        $value = str_replace('-', '/', $value);

        try {
            return Carbon::instance(Jalalian::fromFormat('Y/m/d', $value)->toCarbon());
        } catch (\Throwable) {
            return null;
        }

    }
}