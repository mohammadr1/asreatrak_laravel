<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Tag;

class TagController extends Controller
{
    public function show(string $slug)
    {
        // پیدا کردن تگ بر اساس اسلاگ
        $tag = Tag::where('slug', $slug)->firstOrFail();

        // دریافت فقط اخبار مرتبط با همین تگ
        $news = $tag->news()
            ->with('featuredMedia')
            ->publiclyVisible()
            ->orderByRaw('COALESCE(news.published_at, news.created_at) DESC')
            ->paginate(12);

        return view('customer.tags.show', compact('tag', 'news'));
    }
}