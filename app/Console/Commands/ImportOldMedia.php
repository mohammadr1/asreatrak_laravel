<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportOldMedia extends Command
{
    protected $signature = 'media:import-old
        {--source= : پوشه‌ی images سایت قدیم (پیش‌فرض: comparison/old_site/.../public/images)}
        {--old-database=old_asreatrak : نام دیتابیس قدیمی}
        {--limit=0 : حداکثر تعداد خبر برای پردازش (۰ = همه)}
        {--dry-run : فقط بررسی و گزارش، بدون هیچ تغییری}
        {--force : کاورهایی که از قبل دارند را هم دوباره وصل کن}
        {--order=asc : ترتیب پردازش؛ asc = قدیمی‌ها اول، desc = جدیدها اول}
        {--year= : فقط تصاویر یک سال خاص، مثلاً 2026}
        {--download : فایل‌های غایب را از سایت قدیمی دانلود کن}
        {--scrape : برای خبرهایی که ردیف تصویر ندارند، تصویر را از صفحه‌ی سایت قدیمی بردار}
        {--remote=https://asreatrak.ir : آدرس سایت قدیمی برای دانلود}
        {--avatars : آواتار کاربران را هم منتقل کن}';

    protected $description = 'انتقال تصاویر (کاور) خبرهای سایت قدیم به جدول media و اتصال آن‌ها به news';

    private string $sourceRoot = '';

    private string $oldDatabase = 'old_asreatrak';

    /** @var array<int,int> old_image_id => media_id */
    private array $imported = [];

    private int $copied = 0;

    private int $linked = 0;

    private int $already = 0;

    private int $missing = 0;

    private int $failed = 0;

    private int $downloaded = 0;

    /** @var array<int,array{old_image_id:int,path:string,post_id:int}> */
    private array $missingRows = [];

    public function handle(): int
    {
        $this->oldDatabase = (string) $this->option('old-database');

        if (! $this->resolveSource()) {
            $this->error('پوشه‌ی تصاویر سایت قدیم پیدا نشد. با --source مسیر آن را بدهید.');
            $this->line('مثال: php artisan media:import-old --source="D:\\old_site\\public\\images" --dry-run');

            return self::FAILURE;
        }

        $dry = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');
        $limit = max(0, (int) $this->option('limit'));
        $order = strtolower((string) $this->option('order')) === 'desc' ? 'desc' : 'asc';

        $this->info(($dry ? '[DRY-RUN] ' : '').'منبع فایل‌ها: '.$this->sourceRoot);

        if (! $this->oldTableExists('images')) {
            $this->error('جدول images در دیتابیس '.$this->oldDatabase.' وجود ندارد. اول آن را از بکاپ ایمپورت کنید.');
            $this->line('نمونه: mysql -u root old_asreatrak < images_table.sql');

            return self::FAILURE;
        }

        if (! $dry) {
            $this->ensureMapTable();
            $this->loadMap();
        }

        $query = DB::table($this->oldDatabase.'.posts as p')
            ->leftJoin($this->oldDatabase.'.images as i', 'i.id', '=', 'p.image_id')
            ->leftJoin('news as n', 'n.id', '=', 'p.id')
            ->whereNotNull('p.image_id')
            ->when($this->option('year'), fn ($q, $year) => $q->where('i.original_image', 'like', 'images/'.$year.'/%'))
            ->when(! $force, fn ($q) => $q->whereNull('n.featured_media_id'))
            ->select([
                'p.id as post_id',
                'p.image_id',
                'i.id as image_id_real',
                'i.original_image',
                'i.big_image',
                'i.medium_image',
                'i.title',
                'i.alt',
                'i.created_at as image_created_at',
            ])
            ->orderBy('p.id', $order);

        $total = (clone $query)->count();
        $this->line('خبرهای نیازمند تصویر: '.$total.($limit ? ' (محدود به '.$limit.')' : ''));

        if ($this->option('download') && ! $dry) {
            $this->prefetchDownloads(clone $query, $limit);
        }

        $bar = $this->output->createProgressBar($limit ?: $total);
        $bar->start();

        $processed = 0;

        $query->chunk(500, function ($rows) use (&$processed, $limit, $dry, $force, $bar) {
            foreach ($rows as $row) {
                if ($limit && $processed >= $limit) {
                    return false;
                }

                $processed++;
                $bar->advance();

                if ($row->image_id_real === null) {
                    // posts.image_id به ردیف موجود در images اشاره نمی‌کند
                    $this->missing++;
                    $this->missingRows[] = [
                        'old_image_id' => (int) $row->image_id,
                        'path' => '(بدون ردیف در جدول images)',
                        'post_id' => (int) $row->post_id,
                    ];

                    continue;
                }

                try {
                    $this->importOne($row, $dry, $force);
                } catch (\Throwable $e) {
                    $this->failed++;
                    $this->warn("\n#{$row->post_id}: ".$e->getMessage());
                }
            }

            return ! ($limit && $processed >= $limit);
        });

        $bar->finish();
        $this->newLine(2);

        $this->table(['نتیجه', 'تعداد'], [
            ['فایل کپی‌شده و ردیف media ساخته‌شده', $this->copied],
            ['از آن، دانلودشده از سایت قدیمی', $this->downloaded],
            ['کاور وصل‌شده به خبر', $this->linked],
            ['از قبل انجام شده (رد شد)', $this->already],
            ['فایل تصویر پیدا نشد', $this->missing],
            ['خطا', $this->failed],
        ]);

        if ($this->missingRows) {
            $path = storage_path('app/import-old-media-missing.csv');
            if (! $dry) {
                $handle = fopen($path, 'w');
                fwrite($handle, "post_id,old_image_id,path\n");
                foreach ($this->missingRows as $item) {
                    fwrite($handle, $item['post_id'].','.$item['old_image_id'].','.$item['path']."\n");
                }
                fclose($handle);
                $this->warn('فهرست فایل‌های پیدا نشده: '.$path);
            }
        }

        if ($this->option('avatars')) {
            $this->importAvatars($dry);
        }

        if ($this->option('scrape')) {
            $this->scrapeMissing($dry, $limit);
        }

        if ($dry) {
            $this->comment('این اجرا آزمایشی بود؛ هیچ فایل یا رکوردی تغییر نکرد. برای اجرای واقعی --dry-run را بردارید.');
        }

        return self::SUCCESS;
    }

    private function importOne(object $row, bool $dry, bool $force): void
    {
        $oldId = (int) $row->image_id_real;

        if (! $dry && isset($this->imported[$oldId])) {
            $mediaId = $this->imported[$oldId];

            if (DB::table('media')->where('id', $mediaId)->exists()) {
                $this->linkNews((int) $row->post_id, $mediaId);
                $this->already++;

                return;
            }
        }

        $source = $this->resolveSourceFile($row);
        $remote = null;

        if ($source === null && $this->option('download')) {
            $remote = $this->resolveRemoteUrl($row);
        }

        if ($source === null && $remote === null) {
            $this->missing++;
            $this->missingRows[] = [
                'old_image_id' => $oldId,
                'path' => (string) $row->original_image,
                'post_id' => (int) $row->post_id,
            ];

            return;
        }

        if ($dry) {
            $this->copied++;

            return;
        }

        $relative = $remote !== null
            ? $this->stripImagesPrefix((string) $row->original_image)
            : $this->relativeSourcePath($source);

        $destination = 'media/old/'.$relative;
        $absolute = Storage::disk('public')->path($destination);

        if (! is_file($absolute)) {
            Storage::disk('public')->makeDirectory(dirname($destination));

            if ($remote !== null) {
                $body = $this->fetchRemote($remote);

                if ($body === null) {
                    $this->missing++;
                    $this->missingRows[] = [
                        'old_image_id' => $oldId,
                        'path' => (string) $row->original_image,
                        'post_id' => (int) $row->post_id,
                    ];

                    return;
                }

                file_put_contents($absolute, $body);
                $this->downloaded++;
            } elseif (! @copy($source, $absolute)) {
                throw new \RuntimeException('کپی فایل انجام نشد: '.$source);
            }

            $this->copied++;
        } else {
            $this->copied++;
        }

        $size = @getimagesize($absolute);
        $mediaId = DB::table('media')->insertGetId([
            'uuid' => (string) Str::uuid(),
            'disk' => 'public',
            'directory' => 'media/old/'.dirname($relative),
            'filename' => basename($relative),
            'original_path' => $destination,
            'cropped_path' => null,
            'watermarked_path' => $destination,
            'crop_data' => null,
            'extension' => strtolower(pathinfo($destination, PATHINFO_EXTENSION)),
            'mime_type' => $size['mime'] ?? 'image/jpeg',
            'size' => filesize($absolute) ?: 0,
            'width' => $size[0] ?? null,
            'height' => $size[1] ?? null,
            'duration' => null,
            'type' => 'image',
            'title' => $this->limitText($row->title ?? null),
            'alt' => $this->limitText($row->alt ?? null),
            'caption' => null,
            'copyright' => null,
            'uploaded_by' => null,
            'is_active' => 1,
            'watermark_id' => null,
            'watermark_type' => null,
            'has_watermark' => 0,
            'hash' => null,
            'visibility' => 'public',
            'metadata' => json_encode([]),
            'created_at' => $row->image_created_at ?? now(),
            'updated_at' => $row->image_created_at ?? now(),
        ]);

        DB::table('old_media_map')->updateOrInsert(
            ['old_image_id' => $oldId],
            [
                'media_id' => $mediaId,
                'source_path' => $row->original_image ?? $relative,
                'status' => 'imported',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $this->imported[$oldId] = $mediaId;

        $this->linkNews((int) $row->post_id, $mediaId);
    }

    private function linkNews(int $newsId, int $mediaId): void
    {
        $updated = DB::table('news')
            ->where('id', $newsId)
            ->update([
                'featured_media_id' => $mediaId,
                'featured_media_variant' => 'original',
            ]);

        DB::table('news_media')->updateOrInsert(
            ['news_id' => $newsId, 'media_id' => $mediaId],
            ['sort_order' => 0, 'is_featured' => 1, 'updated_at' => now(), 'created_at' => now()]
        );

        if ($updated > 0) {
            $this->linked++;
        }
    }

    private function fetchMany(array $urls, int $concurrency = 12): array
    {
        $results = [];
        $queue = array_values(array_unique($urls));

        while ($queue) {
            $batch = array_splice($queue, 0, $concurrency);
            $multi = curl_multi_init();
            $handles = [];

            foreach ($batch as $index => $url) {
                $handle = curl_init($url);
                curl_setopt_array($handle, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_MAXREDIRS => 3,
                    CURLOPT_TIMEOUT => 90,
                    CURLOPT_CONNECTTIMEOUT => 20,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; AsreatrakImporter/1.0)',
                ]);
                curl_multi_add_handle($multi, $handle);
                $handles[$index] = $handle;
            }

            $running = null;
            do {
                curl_multi_exec($multi, $running);
                curl_multi_select($multi, 1.0);
            } while ($running > 0);

            foreach ($handles as $index => $handle) {
                $body = curl_multi_getcontent($handle);
                $code = (int) curl_getinfo($handle, CURLINFO_HTTP_CODE);
                $results[$batch[$index]] = ($code >= 200 && $code < 300 && is_string($body) && strlen($body) > 200)
                    ? $body
                    : null;
                curl_multi_remove_handle($multi, $handle);
                curl_close($handle);
            }

            curl_multi_close($multi);
        }

        return $results;
    }

    /**
     * دانلود موازی فایل‌های تصویری که محلی موجود نیستند.
     */
    private function prefetchDownloads(object $query, int $limit): void
    {
        $base = rtrim((string) $this->option('remote'), '/');
        $pending = [];
        $seen = [];

        $query->chunk(500, function ($rows) use (&$pending, &$seen, $base, $limit) {
            foreach ($rows as $row) {
                if ($limit && count($seen) >= $limit) {
                    return false;
                }

                if ($row->image_id_real === null || ! $row->original_image) {
                    continue;
                }

                if (isset($seen[$row->image_id_real])) {
                    continue;
                }

                $seen[$row->image_id_real] = true;
                $relative = $this->stripImagesPrefix((string) $row->original_image);

                if (is_file(Storage::disk('public')->path('media/old/'.$relative))) {
                    continue;
                }

                $pending[$relative] = $base.'/'.ltrim((string) $row->original_image, '/');
            }

            return true;
        });

        if (! $pending) {
            return;
        }

        $this->info('دانلود موازی '.count($pending).' تصویر از سایت قدیمی...');
        $bar = $this->output->createProgressBar(count($pending));
        $bar->start();

        foreach (array_chunk($pending, 12, true) as $batch) {
            $bodies = $this->fetchMany(array_values($batch), 12);

            foreach ($batch as $relative => $url) {
                $body = $bodies[$url] ?? null;
                $bar->advance();

                if ($body === null) {
                    continue;
                }

                $destination = 'media/old/'.$relative;
                Storage::disk('public')->makeDirectory(dirname($destination));
                file_put_contents(Storage::disk('public')->path($destination), $body);
                $this->downloaded++;
            }
        }

        $bar->finish();
        $this->newLine();
    }

    private function resolveRemoteUrl(object $row): ?string
    {
        $base = rtrim((string) $this->option('remote'), '/');

        foreach ([$row->original_image ?? null, $row->big_image ?? null, $row->medium_image ?? null] as $candidate) {
            if ($candidate) {
                return $base.'/'.ltrim((string) $candidate, '/');
            }
        }

        return null;
    }

    private function fetchRemote(string $url, int $timeout = 30): ?string
    {
        $context = stream_context_create([
            'http' => [
                'timeout' => $timeout,
                'follow_location' => 1,
                'max_redirects' => 3,
                'user-agent' => 'AsreatrakImporter/1.0',
                'ignore_errors' => true,
            ],
            'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
        ]);

        $body = @file_get_contents($url, false, $context);

        if (! is_string($body) || strlen($body) < 200) {
            return null;
        }

        return $body;
    }

    private function resolveSourceFile(object $row): ?string
    {
        foreach ([$row->original_image ?? null, $row->big_image ?? null, $row->medium_image ?? null] as $candidate) {
            if (! $candidate) {
                continue;
            }

            $path = $this->sourceRoot.DIRECTORY_SEPARATOR
                .str_replace('/', DIRECTORY_SEPARATOR, $this->stripImagesPrefix($candidate));

            if (is_file($path)) {
                return $path;
            }

            // Images copied manually into storage/app/public/media/old are valid sources too.
            $relative = $this->stripImagesPrefix((string) $candidate);
            $existingDestination = Storage::disk('public')->path('media/old/'.$relative);

            if (is_file($existingDestination)) {
                return $existingDestination;
            }
        }

        // اگر نام دقیق نبود، هم‌نام‌های _original را در همان پوشه بگرد
        if ($row->original_image) {
            $relative = $this->stripImagesPrefix($row->original_image);
            $directory = dirname($relative);
            $base = basename($relative);

            if (preg_match('~^(.+?)_original~', $base, $matches)) {
                $pattern = $this->sourceRoot.DIRECTORY_SEPARATOR
                    .str_replace('/', DIRECTORY_SEPARATOR, $directory)
                    .DIRECTORY_SEPARATOR.$matches[1].'_original*';

                foreach (glob($pattern) ?: [] as $found) {
                    return $found;
                }
            }
        }

        return null;
    }

    private function relativePath(string $absolute): string
    {
        $root = rtrim($this->sourceRoot, '\\/');

        return str_replace('\\', '/', ltrim(substr($absolute, strlen($root)), '\\/'));
    }

    private function relativeSourcePath(string $absolute): string
    {
        $normalized = str_replace('\\', '/', $absolute);
        $destinationRoot = rtrim(str_replace('\\', '/', storage_path('app/public/media/old')), '/');

        if (str_starts_with($normalized, $destinationRoot.'/')) {
            return ltrim(substr($normalized, strlen($destinationRoot)), '/');
        }

        return $this->relativePath($absolute);
    }

    private function stripImagesPrefix(string $path): string
    {
        $path = ltrim(str_replace('\\', '/', trim($path)), '/');

        return preg_replace('~^images/~i', '', $path) ?? $path;
    }

    private function limitText(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : mb_substr($value, 0, 255);
    }

    private function resolveSource(): bool
    {
        $candidates = array_filter([
            $this->option('source'),
            base_path('comparison/old_site/domains/site/public_html/public/images'),
            base_path('comparison/old_site/domains/site/public_html/storage/app/images'),
            storage_path('app/public/images'),
        ]);

        foreach ($candidates as $candidate) {
            $path = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, (string) $candidate), '\\/');
            if (is_dir($path)) {
                $this->sourceRoot = $path;

                return true;
            }
        }

        return false;
    }

    private function oldTableExists(string $table): bool
    {
        return DB::selectOne(
            'SELECT COUNT(*) AS c FROM information_schema.tables WHERE table_schema = ? AND table_name = ?',
            [$this->oldDatabase, $table]
        )->c > 0;
    }

    private function ensureMapTable(): void
    {
        DB::statement('CREATE TABLE IF NOT EXISTS old_media_map (
            old_image_id BIGINT UNSIGNED NOT NULL PRIMARY KEY,
            media_id BIGINT UNSIGNED NULL,
            source_path VARCHAR(255) NULL,
            status VARCHAR(20) NOT NULL DEFAULT "pending",
            note VARCHAR(255) NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )');
    }

    private function loadMap(): void
    {
        $this->imported = DB::table('old_media_map')
            ->whereNotNull('media_id')
            ->where('status', 'imported')
            ->pluck('media_id', 'old_image_id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /**
     * برای خبرهایی که ردیف تصویرشان در جدول images نیست (بکاپ قدیمی‌تر از پست‌هاست)
     * تصویر شاخص را از HTML صفحه‌ی همان خبر در سایت قدیمی برمی‌دارد.
     */
    private function scrapeMissing(bool $dry, int $limit = 0): void
    {
        $base = rtrim((string) $this->option('remote'), '/');

        $ids = DB::table('news as n')
            ->join($this->oldDatabase.'.posts as p', 'p.id', '=', 'n.id')
            ->whereNull('n.featured_media_id')
            ->orderByDesc('n.id')
            ->when($limit > 0, fn ($q) => $q->limit($limit))
            ->pluck('n.id')
            ->all();

        if (! $ids) {
            return;
        }

        $this->newLine();
        $this->info('برداشتن تصویر از صفحه‌ی خبرهای بدون تصویر: '.count($ids));

        $bar = $this->output->createProgressBar(count($ids));
        $bar->start();

        $done = 0;
        $failed = 0;
        $created = [];

        foreach (array_chunk($ids, 10) as $chunk) {
            $pages = $this->fetchMany(
                array_map(fn ($id) => $base.'/news/'.$id, $chunk),
                10
            );

            $images = [];

            foreach ($chunk as $id) {
                $bar->advance();
                $html = $pages[$base.'/news/'.$id] ?? null;

                if ($html === null) {
                    $failed++;

                    continue;
                }

                $url = $this->pickCoverFromHtml($html);

                if ($url === null) {
                    $failed++;

                    continue;
                }

                $images[$id] = $url;
            }

            if (! $images) {
                continue;
            }

            if ($dry) {
                $done += count($images);

                continue;
            }

            $downloads = $this->fetchMany(array_values($images), 10);

            foreach ($images as $id => $url) {
                $relative = $this->stripImagesPrefix((string) parse_url($url, PHP_URL_PATH));
                $destination = 'media/old/'.$relative;
                $absolute = Storage::disk('public')->path($destination);

                if (! isset($created[$destination])) {
                    $body = $downloads[$url] ?? (is_file($absolute) ? (string) file_get_contents($absolute) : null);

                    if ($body === null) {
                        $failed++;

                        continue;
                    }

                    if (! is_file($absolute)) {
                        Storage::disk('public')->makeDirectory(dirname($destination));
                        file_put_contents($absolute, $body);
                    }

                    $size = @getimagesize($absolute);

                    $created[$destination] = DB::table('media')->insertGetId([
                        'uuid' => (string) Str::uuid(),
                        'disk' => 'public',
                        'directory' => 'media/old/'.dirname($relative),
                        'filename' => basename($relative),
                        'original_path' => $destination,
                        'cropped_path' => null,
                        'watermarked_path' => $destination,
                        'crop_data' => null,
                        'extension' => strtolower(pathinfo($destination, PATHINFO_EXTENSION)),
                        'mime_type' => $size['mime'] ?? 'image/jpeg',
                        'size' => filesize($absolute) ?: 0,
                        'width' => $size[0] ?? null,
                        'height' => $size[1] ?? null,
                        'duration' => null,
                        'type' => 'image',
                        'title' => null,
                        'alt' => null,
                        'caption' => null,
                        'copyright' => null,
                        'uploaded_by' => null,
                        'is_active' => 1,
                        'watermark_id' => null,
                        'watermark_type' => null,
                        'has_watermark' => 0,
                        'hash' => null,
                        'visibility' => 'public',
                        'metadata' => json_encode([]),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $this->linkNews((int) $id, $created[$destination]);
                $done++;
            }
        }

        $bar->finish();
        $this->newLine(2);
        $this->table(['نتیجه', 'تعداد'], [
            ['تصویر پیدا و وصل شد', $done],
            ['صفحه/تصویری پیدا نشد', $failed],
        ]);
    }

    private function pickCoverFromHtml(string $html): ?string
    {
        preg_match_all('~https?://[^"\'<>\s]+/(images/\d{4}/\d{1,2}/[^"\'<>\s]+\.(?:jpe?g|png|webp))~i', $html, $matches);

        $candidates = array_values(array_unique($matches[0] ?? []));

        foreach (['_big_1080x1000_', '_big_730x400_', '_original_', '_original.'] as $preferred) {
            foreach ($candidates as $url) {
                if (str_contains($url, $preferred)) {
                    return $url;
                }
            }
        }

        foreach ($candidates as $url) {
            if (! str_contains($url, 'favicon') && ! str_contains($url, 'logo')) {
                return $url;
            }
        }

        return null;
    }

    private function importAvatars(bool $dry): void
    {
        $this->info('آواتار کاربران:');

        $users = DB::table($this->oldDatabase.'.users')
            ->whereNotNull('profile_image')
            ->where('profile_image', '<>', '')
            ->get(['id', 'profile_image']);

        $done = 0;
        $skip = 0;

        foreach ($users as $user) {
            $source = $this->resolveSourceFile((object) [
                'original_image' => $user->profile_image,
                'big_image' => null,
                'medium_image' => null,
            ]);

            if (! $source) {
                $skip++;

                continue;
            }

            if ($dry) {
                $done++;

                continue;
            }

            $name = basename($source);
            $destination = 'users/legacy/'.$name;

            if (! is_file(Storage::disk('public')->path($destination))) {
                Storage::disk('public')->makeDirectory('users/legacy');
                @copy($source, Storage::disk('public')->path($destination));
            }

            DB::table('users')->where('id', $user->id)->update(['profile_image' => $destination]);
            $done++;
        }

        $this->line("منتقل‌شده: {$done} — پیدا نشد: {$skip}");
    }
}

