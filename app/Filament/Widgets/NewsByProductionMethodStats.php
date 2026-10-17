<?php

namespace App\Filament\Widgets;

use App\Models\News;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class NewsByProductionMethodStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('تولیدی', News::where('production_method', 'تولیدی')->count())
                ->description('محتوای اختصاصی و تالیفی')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('success'),

            Stat::make('پوششی', News::where('production_method', 'پوششی')->count())
                ->description('پوشش رویدادها و جلسات')
                ->descriptionIcon('heroicon-m-camera')
                ->color('info'),

            Stat::make('دریافتی', News::where('production_method', 'دریافتی')->count())
                ->description('ارسالی از روابط عمومی‌ها/مخاطبان')
                ->descriptionIcon('heroicon-m-inbox-arrow-down')
                ->color('warning'),

            Stat::make('بازنشری', News::where('production_method', 'بازنشری')->count())
                ->description('نقل از سایر خبرگزاری‌ها')
                ->descriptionIcon('heroicon-m-arrow-path-rounded-square')
                ->color('gray'),
        ];
    }
}
