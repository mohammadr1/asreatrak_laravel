<?php

namespace Database\Seeders;

use App\Models\ReportType;
use Illuminate\Database\Seeder;

class ReportTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'یادداشت',
            'گفتگوی تفصیلی',
            'مصاحبه تحلیلی',
            'بازنشر',
            'پوششی',
            'دریافتی',
            'گزارش',
            'گزارش تصویری',
            'میزگرد',
            'خبرتولیدی',
            'فیلم تدوین شده',
            'مصاحبه خبری',
            'فیلم موبایلی',
        ];

        foreach ($types as $type) {
            ReportType::firstOrCreate([
                'name' => $type,
            ]);
        }
    }
}