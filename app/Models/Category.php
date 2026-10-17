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
        return $this->belongsToMany(
            News::class,
            'news_category',
            'category_id',
            'news_id'
        )->withPivot('is_top');
    }

    protected static function booted(): void
    {
        // وقتی دسته‌ای اضافه، ویرایش یا حذف شد، کش نوار بالا پاک شود
        $clear = fn () => \Illuminate\Support\Facades\Cache::forget('nav.categories');

        static::saved($clear);
        static::deleted($clear);
    }
}
