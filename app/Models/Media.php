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
        'original_path'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'duration' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function news(): BelongsToMany
    {
        return $this->belongsToMany(
            News::class,
            'news_media'
        )
            ->withPivot([
                'sort_order',
                'is_featured',
            ])
            ->withTimestamps();
    }


    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeImages($query)
    {
        return $query->where('type', 'image');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getUrlAttribute(): string
    {
        return asset(
            'storage/' .
            trim($this->directory, '/') .
            '/' .
            $this->filename
        );
    }

    public function getThumbnailUrlAttribute(): string
    {
        return $this->url;
    }

    public function getHumanSizeAttribute(): string
    {
        $bytes = $this->size;

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        }

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' B';
    }

    public function featuredNews()
    {
        return $this->hasMany(News::class, 'featured_media_id');
    }

}
