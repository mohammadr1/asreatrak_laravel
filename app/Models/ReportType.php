<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportType extends Model
{
    protected $table = 'report_type';

    protected $fillable = [
        'name',
        'status'
    ];

    public function news(): HasMany
    {
        return $this->hasMany(
            News::class,
            'report_type'
        );
    }
}