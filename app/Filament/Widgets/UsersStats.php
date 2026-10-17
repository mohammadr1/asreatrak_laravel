<?php

namespace App\Filament\Widgets;

use App\Models\News;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UsersStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('تعداد کاربران', User::count())
                ->description('کل کاربران ثبت‌شده')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('تعداد خبرنگاران', User::role('reporter')->count())
                ->description('کل خبرنگاران')
                ->descriptionIcon('heroicon-m-identification')
                ->color('info'),

            Stat::make('تعداد کل خبرها', News::count())
                ->description('همه‌ی انواع خبر')
                ->descriptionIcon('heroicon-m-newspaper')
                ->color('success'),
        ];
    }
}
