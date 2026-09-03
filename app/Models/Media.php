<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

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
        return $this->mediaPathUrl(
            $this->original_path
        ) ?? '';
    }

    public function getCroppedUrlAttribute(): string
    {
        return $this->mediaPathUrl(
            $this->cropped_path
        ) ?? '';
    }

    public function getWatermarkedUrlAttribute(): string
    {
        return $this->mediaPathUrl(
            $this->watermarked_path
        ) ?? '';
    }

    public function getUrlAttribute(): string
    {
        return $this->watermarked_url;
    }

    /**
     * Return a URL for the exact requested media variant.
     *
     * مهم:
     * این متد دیگر از یک variant به variant دیگر fallback نمی‌کند.
     */
    public function variantUrl(
        string $variant = 'watermarked'
    ): ?string {
        return match ($variant) {

            'watermarked' =>
                $this->mediaPathUrl(
                    $this->watermarked_path
                ),

            'cropped' =>
                $this->mediaPathUrl(
                    $this->cropped_path
                ),

            'original' =>
                $this->mediaPathUrl(
                    $this->original_path
                ),

            default => null,
        };
    }

    /**
     * Generate a media URL that works correctly
     * when the Laravel application is opened from
     * another device on the local network.
     */
    protected function mediaPathUrl(
        ?string $path
    ): ?string {
        if (! $path) {
            return null;
        }

        $diskName = $this->disk ?: 'public';

        /*
        |--------------------------------------------------------------------------
        | Local public disk
        |--------------------------------------------------------------------------
        |
        | Do NOT return:
        | http://localhost:8000/storage/...
        |
        | because localhost on a phone points to the phone itself.
        |
        | Relative URL works from:
        |
        | Desktop:
        | http://localhost:8000
        |
        | Mobile:
        | http://192.168.x.x:8000
        |
        */

        if ($diskName === 'public') {
            return '/storage/' . ltrim(
                $path,
                '/'
            );
        }

        return Storage::disk(
            $diskName
        )->url(
            $path
        );
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