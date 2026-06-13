<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'language',
        'is_featured',
        'meta_description',
        'meta_keywords',
        'sort_order',
        'top_news_id',
    ];

    public function news()
    {
        return $this->belongsToMany(News::class);
    }
}
