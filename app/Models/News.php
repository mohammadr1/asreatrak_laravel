<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
        use SoftDeletes;

    protected $fillable = [
        'title',
        'lead',
        'uptitle',
        'slug',
        'content',
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

    public function media()
    {
        return $this->hasMany(NewsMedia::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
