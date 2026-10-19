<?php

namespace App\View\Components;

use App\Models\Comment;
use App\Models\News;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class NewsComments extends Component
{
    public Collection $comments;
    public int $total;

    public function __construct(public News $news)
    {
        // نظرهای اصلیِ تأییدشده، تازه‌ترین اول، همراه با پاسخ‌های تأییدشده‌شان
        $this->comments = Comment::query()
            ->approved()
            ->where('news_id', $news->id)
            ->whereNull('parent_id')
            ->with(['replies' => fn ($q) => $q->approved()->oldest()])
            ->latest()
            ->limit(50)
            ->get();

        $this->total = Comment::approved()->where('news_id', $news->id)->count();
    }

    public function render(): View
    {
        return view('components.news-comments');
    }
}
