<?php

namespace App\Filament\Widgets;

use App\Models\News;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class NewsStatusStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('منتشر شده', News::where('status', 'published')->count())
                ->description('اخبار فعال روی سایت')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('در انتظار بررسی', News::where('status', 'pending')->count())
                ->description('نیاز به تایید سردبیر')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('زمان‌بندی شده', News::where('status', 'scheduled')->count())
                ->description('انتشار خودکار در آینده')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            Stat::make('پیش‌نویس', News::where('status', 'draft')->count())
                ->description('اخبار تکمیل‌نشده')
                ->descriptionIcon('heroicon-m-pencil-square')
                ->color('gray'),

            Stat::make('تایید شده', News::where('status', 'approved')->count())
                ->description('تایید نهایی، آماده نشر')
                ->descriptionIcon('heroicon-m-hand-thumb-up')
                ->color('primary'),

            Stat::make('رد شده', News::where('status', 'rejected')->count())
                ->description('عدم تایید سردبیری')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
