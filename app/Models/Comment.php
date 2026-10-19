<?php

namespace App\Models;

use App\Enums\CommentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'news_id',
        'parent_id',
        'user_id',
        'name',
        'emailOrMobile',
        'body',
        'notify_me',
        'status',
        'approved_at',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'status'      => CommentStatus::class,
            'notify_me'   => 'boolean',
            'approved_at' => 'datetime',
        ];
    }

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeApproved(Builder $query): void
    {
        $query->where('status', CommentStatus::Approved);
    }

    /** آیا اطلاعات تماس واردشده، ایمیل است؟ (برای ارسال اطلاع‌رسانی ایمیلی) */
    public function getContactIsEmailAttribute(): bool
    {
        return filter_var($this->emailOrMobile, FILTER_VALIDATE_EMAIL) !== false;
    }
}
