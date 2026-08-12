<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Media extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'disk',
        'directory',
        'filename',
        'original_path',

        'cropped_path',
        'watermarked_path',
        'crop_data',

        'extension',
        'mime_type',
        'size',
        'width',
        'height',
        'duration',
        'type',

        'title',
        'alt',
        'caption',
        'copyright',

        'uploaded_by',
        'is_active',

        'watermark_id',
        'watermark_type',
        'has_watermark',

        'hash',
        'visibility',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'has_watermark' => 'boolean',

        'size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'duration' => 'integer',

        'watermark_id' => 'integer',

        'crop_data' => 'array',
        'metadata' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'uploaded_by'
        );
    }

    public function watermark(): BelongsTo
    {
        return $this->belongsTo(
            Watermark::class,
            'watermark_id'
        );
    }

    public function news(): BelongsToMany
    {
        return $this->belongsToMany(
            News::class,
            'news_media',
            'media_id',
            'news_id'
        )
            ->withPivot([
                'sort_order',
                'is_featured',
            ])
            ->withTimestamps();
    }

    public function featuredNews()
    {
        return $this->hasMany(
            News::class,
            'featured_media_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeImages($query)
    {
        return $query->where(
            'type',
            'image'
        );
    }

    public function scopeActive($query)
    {
        return $query->where(
            'is_active',
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Variant URLs
    |--------------------------------------------------------------------------
    */

    /**
     * URL تصویر اصلی
     */
    public function getOriginalUrlAttribute(): string
    {
        if (! $this->original_path) {
            return '';
        }

        return asset(
            'storage/' .
            ltrim($this->original_path, '/')
        );
    }

    /**
     * URL تصویر کراپ‌شده
     */
    public function getCroppedUrlAttribute(): string
    {
        if (! $this->cropped_path) {
            return $this->original_url;
        }

        return asset(
            'storage/' .
            ltrim($this->cropped_path, '/')
        );
    }

    /**
     * URL تصویر واترمارک‌دار
     */
    public function getWatermarkedUrlAttribute(): string
    {
        if (! $this->watermarked_path) {
            return $this->cropped_url;
        }

        return asset(
            'storage/' .
            ltrim($this->watermarked_path, '/')
        );
    }

    /**
     * URL نسخه پیش‌فرض رسانه
     */
    public function getUrlAttribute(): string
    {
        return $this->watermarked_url;
    }

    /**
     * URL بر اساس نوع نسخه
     */
    public function variantUrl(
        string $variant = 'watermarked'
    ): string {

        return match ($variant) {

            'original' =>
                $this->original_url,

            'cropped' =>
                $this->cropped_url,

            'watermarked' =>
                $this->watermarked_url,

            default =>
                $this->watermarked_url,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function getThumbnailUrlAttribute(): string
    {
        return $this->watermarked_url;
    }

    public function getHumanSizeAttribute(): string
    {
        $bytes = $this->size ?? 0;

        if ($bytes >= 1073741824) {
            return number_format(
                $bytes / 1073741824,
                2
            ) . ' GB';
        }

        if ($bytes >= 1048576) {
            return number_format(
                $bytes / 1048576,
                2
            ) . ' MB';
        }

        if ($bytes >= 1024) {
            return number_format(
                $bytes / 1024,
                0
            ) . ' KB';
        }

        return $bytes . ' B';
    }
}