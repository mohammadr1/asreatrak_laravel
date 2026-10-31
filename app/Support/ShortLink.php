<?php

namespace App\Support;

use App\Models\News;

/**
 * ساخت و خواندن لینک کوتاه خبر.
 *
 * ساختار جدید:  https://asreatrak.ir/short/{news_code}   مثل: /short/20261031124151
 * ساختار قدیمی: https://asreatrak.ir/short/eqYkB         (کد ۵ کاراکتری سایت قبلی)
 *
 * لینک قدیمی در همان ستون short_link ذخیره شده است؛ برای همین هر دو ساختار
 * باید از یک مسیر (‎/short/{code}‎) قابل دسترسی باشند.
 */
final class ShortLink
{
    /** الگوی مجاز کد لینک کوتاه (امن برای استفاده در مسیر و کوئری LIKE) */
    private const CODE_PATTERN = '/^[A-Za-z0-9_-]{1,64}$/';

    /** نشانی پایه‌ی لینک کوتاه؛ مثل https://asreatrak.ir */
    public static function base(): string
    {
        $configured = trim((string) config('news.short_link.base'));

        if ($configured !== '') {
            return rtrim($configured, '/');
        }

        $appUrl = rtrim((string) config('app.url'), '/');

        if ($appUrl !== '' && ! self::isLocalUrl($appUrl)) {
            return $appUrl;
        }

        return rtrim((string) config('news.short_link.fallback_base', 'https://asreatrak.ir'), '/');
    }

    /** آیا این نشانی مربوط به محیط لوکال/شبکه‌ی داخلی است؟ */
    private static function isLocalUrl(string $url): bool
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        if ($host === '') {
            return true;
        }

        if (in_array($host, ['localhost', '127.0.0.1', '::1', '0.0.0.0'], true)) {
            return true;
        }

        if (str_ends_with($host, '.local') || ! str_contains($host, '.')) {
            return true;
        }

        // رنج‌های IP داخلی: 10.x ، 192.168.x ، 172.16-31.x
        return preg_match('/^(10\.|192\.168\.|172\.(1[6-9]|2\d|3[01])\.)/', $host) === 1;
    }

    /** بخش ثابت مسیر؛ مثل short */
    public static function prefix(): string
    {
        $prefix = trim((string) config('news.short_link.prefix'), '/');

        return $prefix !== '' ? $prefix : 'short';
    }

    /** ساخت لینک کامل از روی کد */
    public static function make(?string $code): ?string
    {
        $code = self::normalizeCode($code);

        if ($code === null) {
            return null;
        }

        return self::base() . '/' . self::prefix() . '/' . $code;
    }

    /** مسیر نسبی لینک کوتاه؛ مثل /short/20261031124151 */
    public static function path(?string $code): ?string
    {
        $code = self::normalizeCode($code);

        return $code === null ? null : '/' . self::prefix() . '/' . $code;
    }

    /**
     * استخراج کد از یک مقدار ذخیره‌شده در دیتابیس.
     *
     * مقادیر موجود در دیتابیس یکدست نیستند (وجود اسلش اضافه، دامنه‌ی لوکال،
     * یا حتی فقط خود کد)؛ بنابراین همه‌ی حالت‌ها پشتیبانی می‌شوند.
     */
    public static function codeFromUrl(?string $url): ?string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return null;
        }

        // حذف query و fragment
        foreach (['?', '#'] as $separator) {
            $url = explode($separator, $url, 2)[0];
        }

        $prefix = preg_quote(self::prefix(), '~');

        if (preg_match('~/' . $prefix . '/([A-Za-z0-9_-]{1,64})/?$~', $url, $matches)) {
            return $matches[1];
        }

        // اگر کاربر/دیتابیس فقط خود کد را ذخیره کرده باشد
        return self::isValidCode($url) ? $url : null;
    }

    /** پاک‌سازی و اعتبارسنجی کد لینک کوتاه */
    public static function normalizeCode(?string $code): ?string
    {
        $code = trim((string) $code);

        if ($code === '') {
            return null;
        }

        if (self::isValidCode($code)) {
            return $code;
        }

        return self::codeFromUrl($code);
    }

    public static function isValidCode(?string $code): bool
    {
        return is_string($code) && preg_match(self::CODE_PATTERN, $code) === 1;
    }

    /** آیا کد خبر از نوع جدید (تاریخ و ساعت ثبت) است؟ مثل 20261031124151 */
    public static function isTimestampCode(?string $code): bool
    {
        return is_string($code) && preg_match('/^\d{14}$/', $code) === 1;
    }

    /** کد فعلی خبر: از لینک کوتاه ذخیره‌شده، وگرنه از کد خبر */
    public static function codeFor(News $news): ?string
    {
        return self::codeFromUrl($news->short_link)
            ?? self::normalizeCode($news->news_code);
    }

    /**
     * لینک کوتاه درست برای یک خبر.
     *
     * - خبرهای جدید (کد خبر تاریخ‌محور): همیشه از خود کد خبر ساخته می‌شود.
     * - خبرهای قدیمی: کد ۵ کاراکتری قبلی حفظ می‌شود و فقط دامنه/قالب اصلاح می‌شود.
     */
    public static function canonicalFor(News $news): ?string
    {
        if (self::isTimestampCode($news->news_code)) {
            return self::make($news->news_code);
        }

        return self::make(self::codeFor($news));
    }
}
