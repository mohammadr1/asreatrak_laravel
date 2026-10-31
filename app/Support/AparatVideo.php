<?php

namespace App\Support;

final class AparatVideo
{
    /**
     * میزبان‌های مجاز آپارات (دامنه رسمی با و بدون www).
     */
    private const HOSTS = ['aparat.com', 'www.aparat.com'];

    /**
     * شناسه ویدیو را از ورودی کاربر استخراج می‌کند.
     *
     * ورودی‌های پذیرفته‌شده:
     *  - شناسه خام: k93yms1
     *  - لینک صفحه: https://www.aparat.com/v/k93yms1
     *  - لینک صفحه همراه با پارامتر: https://www.aparat.com/v/bmm22pw?refererRef=channel_page
     *  - لینک بدون پروتکل: aparat.com/v/bmm22pw
     *  - لینک جاسازی/پخش: https://www.aparat.com/video/video/embed/videohash/bmm22pw/vt/frame
     *  - لینک آی‌فریم: https://www.aparat.com/iframe/bmm22pw
     *  - کد iframe چسبانده‌شده (فقط آدرس داخل src خوانده می‌شود)
     *
     * هر ورودی دیگری (دامنه مشابه، پورت، نام کاربری، اسکیپت و …) رد می‌شود.
     */
    public static function id(?string $input): ?string
    {
        $value = trim(html_entity_decode($input ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        if ($value === '') {
            return null;
        }

        if (preg_match('/\A[a-zA-Z0-9]{1,100}\z/', $value)) {
            return $value;
        }

        $url = self::normalize($value);

        if ($url === null) {
            return null;
        }

        $parts = parse_url($url);

        if (! is_array($parts)
            || ! in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true)
            || ! in_array(strtolower($parts['host'] ?? ''), self::HOSTS, true)
            || isset($parts['user']) || isset($parts['pass']) || isset($parts['port'])) {
            return null;
        }

        $path = (string) ($parts['path'] ?? '');

        foreach ([
            '~/v/([a-zA-Z0-9]{1,100})(?:/.*)?\z~',           // /v/{hash} و /v/{hash}/
            '~videohash/([a-zA-Z0-9]{1,100})(?:/.*)?\z~',    // /video/video/embed/videohash/{hash}/vt/frame
            '~/iframe/([a-zA-Z0-9]{1,100})(?:/.*)?\z~',      // /iframe/{hash}
        ] as $pattern) {
            if (preg_match($pattern, $path, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * ورودی کاربر را به یک URL کامل و قابل اعتماد آپارات تبدیل می‌کند.
     */
    private static function normalize(string $value): ?string
    {
        // کد iframe چسبانده‌شده: فقط آدرس src را برمی‌داریم.
        if (str_contains($value, '<')
            && preg_match('~<iframe\b[^>]*\bsrc\s*=\s*["\']\s*((?:https?:)?//[^"\']+)["\']~i', $value, $matches)) {
            $value = trim($matches[1]);
        }

        // حذف پروتکل (یا //) تا میزبان بدون ابهام بررسی شود.
        $value = preg_replace('~^(?:https?:)?//~i', '', trim($value)) ?? '';
        $value = ltrim($value);

        if ($value === '' || ! preg_match('~^(?:www\.)?aparat\.com(?:[/?#]|\z)~i', $value)) {
            return null;
        }

        return 'https://'.$value;
    }

    public static function embedUrl(?string $input): ?string
    {
        $id = self::id($input);

        return $id ? "https://www.aparat.com/video/video/embed/videohash/{$id}/vt/frame" : null;
    }
}
