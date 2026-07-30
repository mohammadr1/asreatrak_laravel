<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsMedia extends Model
{
    use SoftDeletes;


    protected $fillable = [
        'news_id',
        'type',
        'path',
        'video_url',
        'provider',
        'title',
        'caption',
        'sort_order',
        'is_featured',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function news()
    {
        return $this->belongsTo(News::class);
    }
}
