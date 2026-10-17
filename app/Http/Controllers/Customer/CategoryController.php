<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show(Category $category)
    {
        $news = $category->news()
            ->where('status', 'published')
            ->latest()          // جدیدترین اول
            ->paginate(12);     // ۱۲ خبر در هر صفحه
// dd($news);
        return view('Customer.category.show', compact('category', 'news'));
    }
}
