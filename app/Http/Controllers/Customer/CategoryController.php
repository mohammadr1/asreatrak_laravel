<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    public function show(Category $category)
    {
        $order = 'COALESCE(news.published_at, news.created_at) DESC';

        // فقط خبرهای منتشرشده‌ی همین دسته، با تصویر (جلوگیری از N+1)
        $published = fn () => $category->news()
            ->with('featuredMedia')
            ->publiclyVisible();

        // خبر شاخص: تازه‌ترین خبر منتشرشده‌ای که برای این دسته تیک تاپ دارد
        $hero = $published()
            ->wherePivot('is_top', true)
            ->orderByRaw($order)
            ->first();

        // اگر خبر تاپی نبود، تازه‌ترین خبر دسته
        $hero ??= $published()->orderByRaw($order)->first();

        // بقیه‌ی خبرها (تاپ قبلی هم اینجا، به ترتیب تاریخ، می‌آید)
        $news = $published()
            ->when($hero, fn ($q) => $q->where('news.id', '!=', $hero->id))
            ->orderByRaw($order)
            ->paginate(12);

        return view('customer.category.show', compact('category', 'hero', 'news'));
    }
}