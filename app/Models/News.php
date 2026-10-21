<?php

namespace App\Models;

use App\Enums\NewsStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\ReportType;
use App\Support\AparatVideo;
use Illuminate\Support\Facades\DB;


class News extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'post_type',
        'aparat_video_id',
        'featured',
        'slider',
        'lead',
        'uptitle',
        'slug',
        'content',
        'featured_media_id',
        'featured_media_variant',
        'news_code',
        'reporter_id',
        'views_count',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'short_link',
        'report_type',
        'production_method',
        'meta_title',
        'meta_description',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => NewsStatus::class,
            'approved_at' => 'datetime',
            'published_at' => 'datetime',
            'featured_media_id' => 'integer',
            'featured' => 'boolean',
            'slider' => 'boolean',
        ];
    }



    public function renderedContent(): string
    {
        $html = (string) $this->content;

        $player = function (?string $id): ?string {
            $embed = AparatVideo::embedUrl($id);

            if (! $embed) {
                return null;
            }

            return '<div class="article-video ratio ratio-16x9">'
                . '<iframe src="' . e($embed) . '" title="ویدیوی آپارات" '
                . 'allowfullscreen loading="lazy" allow="autoplay; fullscreen" '
                . 'style="border:0"></iframe></div>';
        };

        // ۱) حالت لینک: <a href="https://www.aparat.com/v/k93yms1">[ویدیوی آپارات: k93yms1]</a>
        //    (ویرایشگر معمولاً شورت‌کد را داخل تگ <a> و گاهی <p> می‌گذارد)
        $html = preg_replace_callback(
            '~(?:<p[^>]*>\s*)?<a\b[^>]*href=["\']([^"\']*aparat\.com/v/[A-Za-z0-9]+[^"\']*)["\'][^>]*>.*?</a>(?:\s*</p>)?~isu',
            function ($m) use ($player) {
                $id = AparatVideo::id(html_entity_decode($m[1]));
                // اگر لینک query یا چیز اضافه داشت، فقط شناسه را از مسیر می‌گیریم
                if (! $id && preg_match('~aparat\.com/v/([A-Za-z0-9]+)~', $m[1], $mm)) {
                    $id = $mm[1];
                }
                return $player($id) ?? $m[0];
            },
            $html
        );

        // ۲) حالت متن ساده: [ویدیوی آپارات: k93yms1]
        $html = preg_replace_callback(
            '~\[\s*ویدیوی\s*آپارات\s*:\s*([A-Za-z0-9]+)\s*\]~u',
            fn ($m) => $player($m[1]) ?? $m[0],
            $html
        );

        return $html;
    }



    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // public function media()
    // {
    //     return $this->hasMany(NewsMedia::class);
    // }

    public function media()
    {
        return $this->belongsToMany(
            Media::class,
            'news_media',
            'news_id',
            'media_id'
        )
            ->withPivot([
                'sort_order',
                'is_featured',
            ])
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    public function categories()
    {
        return $this->belongsToMany(
            Category::class, 
            'news_category',
            'news_id',
            'category_id'
        )->withPivot('is_top');
    }

    public function syncTopCategory(?int $categoryId): void
    {
        if ($categoryId !== null && Category::whereKey($categoryId)->exists()) {
            $this->categories()->syncWithoutDetaching([$categoryId]);
        }
        foreach ($this->categories()->pluck('categories.id') as $id) {
            $this->categories()->updateExistingPivot($id, ['is_top' => (int) $id === $categoryId]);
        }
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'news_tag');
    }

    public function relatedNews(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(self::class, 'news_related', 'news_id', 'related_news_id')->withTimestamps();
    }

    public function scopePubliclyVisible(\Illuminate\Database\Eloquent\Builder $query): void
    {
        $query->where('news.status', NewsStatus::Published)
            ->where(fn ($query) => $query->whereNull('news.published_at')->orWhere('news.published_at', '<=', now()));
    }

    public function canTransitionTo(NewsStatus $status, User $user): bool
    {
        return match ($this->status) {

            NewsStatus::Draft, NewsStatus::Rejected => $status === NewsStatus::Pending && $user->hasRole('Reporter'),

            NewsStatus::Pending =>
                in_array($status, [NewsStatus::Approved, NewsStatus::Rejected], true)
                && $user->can('news.approve'),

            NewsStatus::Approved =>
                in_array(
                    $status,
                    [
                        NewsStatus::Published,
                        NewsStatus::Scheduled
                    ],
                    true
                )
                && $user->can('news.publish'),

            NewsStatus::Scheduled =>
                $status === NewsStatus::Published
                && $user->can('news.publish'),
            
            default => false,
        };
    }

    public function transitionTo(NewsStatus $status, User $user, array $extra = []): void    {
        if (! $this->canTransitionTo($status, $user)) {
            abort(403, 'Transition not allowed');
        }

        $data = array_merge(['status' => $status], $extra);

        $this->update($data);
    }

    public function featuredMedia()
    {
        return $this->belongsTo(Media::class, 'featured_media_id');
    }

    public function reportType(): BelongsTo
    {
        return $this->belongsTo(
            ReportType::class,
            'report_type'
        );
    }


    public function bodyImages(): array
    {
        if (blank($this->content)) {
            return [];
        }

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $this->content);
        libxml_clear_errors();

        $images = [];
        foreach ($dom->getElementsByTagName('img') as $img) {
            $src = $img->getAttribute('src');
            if ($src) {
                $images[] = ['src' => $src, 'alt' => $img->getAttribute('alt')];
            }
        }

        return $images;
    }
    

    public function getCoverUrlAttribute(): string
    {
        return $this->featuredMedia?->variantUrl($this->featured_media_variant ?: 'watermarked')
            ?: $this->featuredMedia?->variantUrl('cropped')
            ?: $this->featuredMedia?->variantUrl('original')
            ?: asset('assets/images/default.png');
    }
    
    public function getJalaliPublishedAtAttribute(): ?string
    {
        $date = $this->published_at ?? $this->created_at;

        return $date
            ? \Morilog\Jalali\Jalalian::fromCarbon($date)->format('Y/m/d H:i')
            : null;
    }



    /** وضعیت‌هایی که خبرنگار هنوز می‌تواند خبر خودش را ویرایش کند */
    public const REPORTER_EDITABLE_STATUSES = [
        NewsStatus::Draft,
        // NewsStatus::Pending,
        NewsStatus::Rejected,
    ];

    public function canBeEditedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        // ادمین همیشه
        if ($user->hasRole('Admin')) {
            return true;
        }

        // خبرنگار: فقط خبر خودش، تا قبل از تأیید سردبیر
        if ($user->hasRole('Reporter')) {
            $isOwner = (int) $this->reporter_id === (int) $user->id
                || (int) $this->created_by === (int) $user->id;

            return $isOwner && in_array($this->status, self::REPORTER_EDITABLE_STATUSES, true);
        }

        // اگر می‌خواهید سردبیر هم خبرِ «در انتظار بررسی» را ویرایش کند، این را باز کنید:
        if ($user->can('news.approve')) {
            return $this->status === NewsStatus::Pending;
        }

        return false;
    }

    public function comments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
