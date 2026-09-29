<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;
use Morilog\Jalali\Jalalian;

class NewsDateRange
{
    public static function parse(?string $value, ?string $timezone = null): ?Carbon
    {
        if (blank($value)) {
            return null;
        }

        $value = strtr(trim($value), array_combine(
            preg_split('//u', '۰۱۲۳۴۵۶۷۸۹٠١٢٣٤٥٦٧٨٩', -1, PREG_SPLIT_NO_EMPTY),
            str_split('01234567890123456789'),
        ));

        if (! preg_match('~^(1[34]\d{2})/(\d{2})/(\d{2})(?:\s+(\d{2}):(\d{2}))?$~', $value, $matches)) {
            throw new InvalidArgumentException('تاریخ را به شکل ۱۴۰۵/۰۷/۰۵ یا ۱۴۰۵/۰۷/۰۵ ۱۴:۳۰ وارد کنید.');
        }

        try {
            $hasTime = isset($matches[4]);
            $format = $hasTime ? 'Y/m/d H:i' : 'Y/m/d';
            $date = Jalalian::fromFormat($format, $value, new \DateTimeZone($timezone ?? config('app.timezone')));
            if ($date->format($format) !== $value) {
                throw new InvalidArgumentException();
            }

            return $hasTime ? $date->toCarbon()->second(0) : $date->toCarbon()->startOfDay();
        } catch (\Throwable $exception) {
            throw new InvalidArgumentException('تاریخ شمسی واردشده معتبر نیست.', previous: $exception);
        }
    }

    public static function endExclusive(?string $value): ?Carbon
    {
        $date = self::parse($value);

        return $date ? (str_contains((string) $value, ':') ? $date->addMinute() : $date->addDay()) : null;
    }

    public static function apply(Builder $query, array $data): Builder
    {
        try {
            $from = self::parse($data['from'] ?? null);
            $until = self::endExclusive($data['until'] ?? null);
        } catch (InvalidArgumentException) {
            return $query->whereRaw('1 = 0');
        }

        if ($from && $until && $from->gte($until)) {
            return $query->whereRaw('1 = 0');
        }

        $column = $query->qualifyColumn(($data['basis'] ?? null) === 'published_at' ? 'published_at' : 'created_at');

        return $query
            ->when($from, fn (Builder $query) => $query->where($column, '>=', $from))
            ->when($until, fn (Builder $query) => $query->where($column, '<', $until));
    }
}
