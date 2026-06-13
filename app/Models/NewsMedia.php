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
        'source',
        'provider',
        'caption',
        'sort_order',
        'is_featured',
    ];

    public function news()
    {
        return $this->belongsTo(News::class);
    }
}
