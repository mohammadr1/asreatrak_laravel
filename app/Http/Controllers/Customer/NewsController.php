<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\News;
use Illuminate\Http\Request;
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

    public function archive(Request $request)
    {
        $q        = trim((string) $request->query('q', ''));
        $category = $request->integer('category') ?: null;
        $sort     = in_array($request->query('sort'), ['latest', 'oldest', 'popular'], true)
            ? $request->query('sort')
            : 'latest';
        $view     = $request->query('view') === 'list' ? 'list' : 'grid';

        // تاریخ شمسی (1403/02/15) -> کاربن؛ ارقام فارسی هم پشتیبانی می‌شود
        $parseJalali = function (?string $value, bool $endOfDay = false) {
            if (! $value) {
                return null;
            }
            try {
                $value = CalendarUtils::convertNumbers(trim($value), true);
                $date  = Jalalian::fromFormat('Y/m/d', $value)->toCarbon();

                return $endOfDay ? $date->endOfDay() : $date->startOfDay();
            } catch (\Throwable $e) {
                return null;
            }
        };

        $from = $parseJalali($request->query('from'));
        $to   = $parseJalali($request->query('to'), true);

        $query = News::query()
            ->publiclyVisible()
            ->with(['featuredMedia', 'categories'])
            ->when($q !== '', function ($qb) use ($q) {
                $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $q) . '%';
                $qb->where(fn ($w) => $w
                    ->where('news.title', 'like', $like)
                    ->orWhere('news.lead', 'like', $like)
                    ->orWhere('news.uptitle', 'like', $like));
            })
            ->when($category, fn ($qb) => $qb->whereHas(
                'categories',
                fn ($c) => $c->where('categories.id', $category)
            ))
            ->when($from, fn ($qb) => $qb->where('news.published_at', '>=', $from))
            ->when($to, fn ($qb) => $qb->where('news.published_at', '<=', $to));

        match ($sort) {
            'oldest'  => $query->orderBy('news.published_at')->orderBy('news.id'),
            'popular' => $query->orderByDesc('news.views_count')->orderByDesc('news.id'),
            default   => $query->orderByDesc('news.published_at')->orderByDesc('news.id'),
        };

        $news = $query->paginate(12)->withQueryString();

        $categories = Category::query()
            ->withCount(['news' => fn ($n) => $n->publiclyVisible()])
            ->orderBy('name')
            ->get();

        $popularNews = News::query()
            ->publiclyVisible()
            ->orderByDesc('views_count')
            ->limit(4)
            ->get();

        return view('Customer.news.archive', compact(
            'news', 'categories', 'popularNews', 'q', 'category', 'sort', 'view'
        ));
    }
}