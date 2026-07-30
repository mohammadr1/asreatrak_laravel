<?php

namespace App\Services\Dashboard;

use App\Enums\NewsStatus;
use App\Models\News;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class DashboardService
{
    /**
     * شمارش اخبار
     */
    public static function countNews(
        ?NewsStatus $status = null,
        ?Carbon $from = null,
        ?Carbon $to = null,
        string $dateColumn = 'created_at'
    ): int {

        $query = News::query();

        if ($status !== null) {
            $query->where('status', $status);
        }

        if ($from !== null) {
            $query->where($dateColumn, '>=', $from);
        }

        if ($to !== null) {
            $query->where($dateColumn, '<=', $to);
        }

        return $query->count();
    }
}