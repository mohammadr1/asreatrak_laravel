<?php

namespace App\Models;

use App\Enums\NewsStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\ReportType;


class News extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'lead',
        'uptitle',
        'slug',
        'content',
        'featured_media_id',
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
        ];
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
            'news_media'
        )->withPivot([
            'sort_order',
            'is_featured',
        ])->withTimestamps();
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'news_category');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'news_tag');
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


}
