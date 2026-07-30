<?php

namespace App\Observers;

use App\Models\News;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NewsObserver
{
    public function creating(News $news): void
    {
        if (blank($news->slug)) {
            $news->slug = Str::slug($news->title . '-' . uniqid());
        }

        if (blank($news->news_code)) {
            $news->news_code = $this->generateNewsCode();
        }

        if (blank($news->created_by) && Auth::check()) {
            $news->created_by = Auth::id();
        }

        if (blank($news->reporter_id) && Auth::check()) {
            $news->reporter_id = Auth::id();
        }
    }

    private function generateNewsCode(): string
    {
        return 'BN-' . now()->format('ymd') . '-' . str_pad(
            News::count() + 1,
            5,
            '0',
            STR_PAD_LEFT
        );
    }
}