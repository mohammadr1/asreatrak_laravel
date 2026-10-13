<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PrayerTimes
{
    private const LAT = 37.4747;   // بجنورد
    private const LNG = 57.3290;
    private const TZ  = 'Asia/Tehran';

    /** @return array<string,string>|null  کلیدها: Fajr, Sunrise, Dhuhr, Asr, Maghrib, Isha */
    public function today(): ?array
    {
        $now = now(self::TZ);
        $key = 'prayer_times_bojnord_' . $now->format('Y-m-d');

        if ($cached = Cache::get($key)) {
            return $cached;
        }

        // اگر چند لحظه‌ی پیش خطا گرفتیم، هر درخواست را به API نزنیم
        if (Cache::has($key . '_failed')) {
            return null;
        }

        try {
            $response = Http::timeout(5)->retry(2, 200)->get(
                'https://api.aladhan.com/v1/timings/' . $now->format('d-m-Y'),
                [
                    'latitude'       => self::LAT,
                    'longitude'      => self::LNG,
                    'method'         => 7, // مؤسسه ژئوفیزیک دانشگاه تهران (برای جعفری: 0)
                    'timezonestring' => self::TZ,
                ]
            );

            $t = $response->json('data.timings');

            if (! $response->ok() || ! is_array($t)) {
                throw new \RuntimeException('Bad prayer API response');
            }

            $times = [];
            foreach (['Fajr', 'Sunrise', 'Dhuhr', 'Asr', 'Maghrib', 'Isha'] as $name) {
                $times[$name] = substr($t[$name], 0, 5); // "04:48 (+0330)" -> "04:48"
            }

            Cache::put($key, $times, $now->copy()->endOfDay());

            return $times;
        } catch (\Throwable $e) {
            Log::warning('Prayer times fetch failed: ' . $e->getMessage());
            Cache::put($key . '_failed', true, now()->addMinutes(5));

            return null;
        }
    }
}