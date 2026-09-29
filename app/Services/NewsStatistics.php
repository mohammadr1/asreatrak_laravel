<?php

namespace App\Services;

use App\Filament\Resources\NewsResource\Tables\NewsFilters;
use App\Models\ReportType;
use Illuminate\Database\Eloquent\Builder;

class NewsStatistics
{
    /** Aggregate the full scoped query, never only the current table page. */
    public function summarize(Builder $query): array
    {
        $rows = (clone $query)->reorder()->setEagerLoads([])
            ->select(['news.report_type', 'news.production_method'])
            ->selectRaw('COUNT(DISTINCT news.id) AS aggregate')
            ->groupBy('news.report_type', 'news.production_method')
            ->toBase()->get();

        $types = ReportType::query()->orderBy('name')->pluck('name', 'id')->all();
        $methods = NewsFilters::PRODUCTION_METHODS;
        $typeCounts = array_fill_keys(array_keys($types), 0);
        $methodCounts = array_fill_keys(array_keys($methods), 0);

        foreach ($rows as $row) {
            $type = $row->report_type ?? 'unspecified';
            $method = $row->production_method ?? 'unspecified';
            $types[$type] ??= 'نوع مطلب نامشخص';
            $methods[$method] ??= $row->production_method ?: 'نحوه تولید نامشخص';
            $typeCounts[$type] = ($typeCounts[$type] ?? 0) + (int) $row->aggregate;
            $methodCounts[$method] = ($methodCounts[$method] ?? 0) + (int) $row->aggregate;
        }

        $group = static fn (array $labels, array $counts): array => collect($labels)
            ->map(fn ($label, $key) => ['label' => $label, 'count' => $counts[$key] ?? 0])->values()->all();

        return [
            'total' => (int) $rows->sum('aggregate'),
            'groups' => [
                ['title' => 'به تفکیک نحوه تولید', 'icon' => 'heroicon-o-pencil-square', 'items' => $group($methods, $methodCounts)],
                ['title' => 'به تفکیک نوع مطلب', 'icon' => 'heroicon-o-document-text', 'items' => $group($types, $typeCounts)],
            ],
        ];
    }
}
