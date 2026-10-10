<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\News;

class HomeController extends Controller
{
    public function home()
    {
        $sliderNews = News::query()
            ->publiclyVisible()
            ->where('slider', true)
            ->with(['featuredMedia', 'categories'])
            ->latest('published_at')
            ->latest('id')
            ->limit(2)
            ->get();

        return view('customer.home', compact('sliderNews'));
    }
}
