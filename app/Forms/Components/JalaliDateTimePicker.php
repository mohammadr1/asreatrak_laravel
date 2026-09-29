<?php

namespace App\Forms\Components;

use App\Support\NewsDateRange;
use Carbon\Carbon;
use Closure;
use Filament\Forms\Components\TextInput;
use InvalidArgumentException;
use Morilog\Jalali\Jalalian;

/** Jalali display in Tehran time; Gregorian storage in the application timezone. */
class JalaliDateTimePicker extends TextInput
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->readOnly()
            ->maxLength(16)
            ->placeholder('انتخاب تاریخ و ساعت شمسی')
            ->suffixIcon('heroicon-o-calendar-days')
            ->helperText('تاریخ شمسی و ساعت انتشار به وقت تهران؛ پس از انتخاب، تأیید را بزنید.')
            ->extraInputAttributes([
                'dir' => 'ltr',
                'autocomplete' => 'off',
                'data-jalali-date-input' => 'true',
                'data-jalali-time' => 'true',
                'data-jalali-time-required' => 'true',
            ])
            ->formatStateUsing(fn ($state) => static::displayValue($state))
            ->dehydrateStateUsing(fn ($state) => static::storageValue($state))
            ->rules([fn (): Closure => function (string $attribute, $value, Closure $fail): void {
                try {
                    static::storageValue($value);
                } catch (InvalidArgumentException $exception) {
                    $fail($exception->getMessage());
                }
            }]);
    }

    public static function displayValue($value): ?string
    {
        if (blank($value)) {
            return null;
        }

        // Filling a form with existing Jalali input must not convert it twice.
        if (is_string($value) && preg_match('~^[1۱][34۳۴][0-9۰-۹]{2}/~u', $value)) {
            return $value;
        }

        return Jalalian::fromCarbon(Carbon::parse($value, config('app.timezone'))->setTimezone('Asia/Tehran'))
            ->format('Y/m/d H:i');
    }

    public static function storageValue(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        if (! str_contains($value, ':')) {
            throw new InvalidArgumentException('ساعت و دقیقه انتشار را نیز انتخاب کنید.');
        }

        return NewsDateRange::parse($value, 'Asia/Tehran')
            ->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s');
    }
}
