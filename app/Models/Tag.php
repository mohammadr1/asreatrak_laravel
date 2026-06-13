<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'usage_count',
        'is_active',
    ];

    public function news()
    {
        return $this->belongsToMany(News::class);
    }
}
