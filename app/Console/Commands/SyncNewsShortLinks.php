<?php

namespace App\Console\Commands;

use App\Models\News;
use App\Support\ShortLink;
use Illuminate\Console\Command;

/**
 * ساخت/اصلاح لینک کوتاه همه‌ی خبرها.
 *
 * ساختار جدید:  https://asreatrak.ir/short/20261031124151  (کد خبر)
 * خبرهای قدیمی: کد ۵ کاراکتری قبلی حفظ می‌شود، فقط دامنه و قالب اصلاح می‌شود.
 *
 * نمونه اجرا:
 *   php artisan news:sync-short-links --dry-run
 *   php artisan news:sync-short-links
 */
class SyncNewsShortLinks extends Command
{
    protected $signature = 'news:sync-short-links
                            {--dry-run : فقط گزارش بده و چیزی در دیتابیس ذخیره نکن}
                            {--chunk=500 : تعداد رکورد در هر دسته}';

    protected $description = 'ساخت و اصلاح لینک کوتاه خبرها بر اساس کد خبر';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $chunkSize = max(50, (int) $this->option('chunk'));

        $scanned = 0;
        $unchanged = 0;
        $updated = 0;
        $withoutCode = 0;
        $samples = [];

        News::withTrashed()
            ->select(['id', 'news_code', 'short_link'])
            ->chunkById($chunkSize, function ($news) use (
                $dryRun,
                &$scanned,
                &$unchanged,
                &$updated,
                &$withoutCode,
                &$samples
            ): void {
                foreach ($news as $item) {
                    $scanned++;

                    $target = ShortLink::canonicalFor($item);

                    if ($target === null) {
                        $withoutCode++;
                        continue;
                    }

                    if ($target === $item->short_link) {
                        $unchanged++;
                        continue;
                    }

                    $updated++;

                    if (count($samples) < 10) {
                        $samples[] = [
                            $item->id,
                            (string) $item->short_link,
                            $target,
                        ];
                    }

                    if (! $dryRun) {
                        News::withTrashed()
                            ->whereKey($item->id)
                            ->toBase()
                            ->update(['short_link' => $target]);
                    }
                }
            });

        if ($samples !== []) {
            $this->table(
                ['شناسه', 'مقدار قبلی', 'لینک کوتاه جدید'],
                array_map(fn (array $row) => [
                    $row[0],
                    $row[1] === '' ? '—' : $row[1],
                    $row[2],
                ], $samples)
            );

            if ($updated > count($samples)) {
                $this->line('  ... و ' . ($updated - count($samples)) . ' رکورد دیگر');
            }
        }

        $this->newLine();
        $this->line('بررسی‌شده: ' . $scanned);
        $this->line('بدون تغییر: ' . $unchanged);
        $this->line(($dryRun ? 'قابل اصلاح: ' : 'اصلاح‌شده: ') . $updated);

        if ($withoutCode > 0) {
            $this->warn('بدون کد خبر (امکان ساخت لینک نبود): ' . $withoutCode);
        }

        if ($dryRun && $updated > 0) {
            $this->newLine();
            $this->comment('حالت آزمایشی: هیچ تغییری ذخیره نشد. برای اعمال، دستور را بدون --dry-run اجرا کنید.');
        }

        return self::SUCCESS;
    }
}
