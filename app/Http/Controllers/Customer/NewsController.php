<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    // public function index(News $news)
    // {

    //     return view('Customer.news.index');
    // }

    public function show(News $news){

        abort_unless($news->status === \App\Enums\NewsStatus::Published
            && (! $news->published_at || $news->published_at->lte(now())), 404);

            $news->load(['reporter', 'categories', 'tags', 'featuredMedia', 'media']);
            $news->load(['relatedNews' => fn ($query) => $query->publiclyVisible()->with('featuredMedia')->orderByDesc('news.published_at')->limit(10)]);
            return view('Customer.news.video', [
                'news' => $news,
                'embedUrl' => $news->post_type === 'video' ? \App\Support\AparatVideo::embedUrl($news->aparat_video_id) : null,
            ]);

    }
    public function archive(){

        return view('Customer.news.archive');

    }
}
