<?php

namespace App\Filament\Resources\NewsResource\Tables;

use App\Models\User;
use App\Support\NewsDateRange;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;

class NewsFilters
{
    public const PRODUCTION_METHODS = [
        'بازنشری' => 'بازنشری',
        'پوششی' => 'پوششی',
        'تولیدی' => 'تولیدی',
        'دریافتی' => 'دریافتی',
    ];

    public static function make(): array
    {
        return [
            SelectFilter::make('reporter')
                ->label('خبرنگار')
                ->relationship('reporter', 'last_name', fn (Builder $query) => $query
                    ->when(auth()->user()->hasRole('Reporter'), fn (Builder $query) => $query->whereKey(auth()->id())))
                ->getOptionLabelFromRecordUsing(fn (User $record) => $record->name)
                ->searchable(['first_name', 'last_name'])
                ->getSearchResultsUsing(function (string $search): array {
                    $query = User::query()
                        ->when(auth()->user()->hasRole('Reporter'), fn (Builder $query) => $query->whereKey(auth()->id()));
                    foreach (preg_split('/\s+/u', trim($search), -1, PREG_SPLIT_NO_EMPTY) as $word) {
                        $query->where(fn (Builder $query) => $query
                            ->where('first_name', 'like', "%{$word}%")
                            ->orWhere('last_name', 'like', "%{$word}%"));
                    }

                    return $query->orderBy('last_name')->limit(50)->get()->pluck('name', 'id')->all();
                })
                ->multiple()->preload()
                ->indicateUsing(fn (array $state) => filled($state['values'] ?? [])
                    ? 'خبرنگار: '.User::query()
                        ->when(auth()->user()->hasRole('Reporter'), fn (Builder $query) => $query->whereKey(auth()->id()))
                        ->whereKey($state['values'])->get()->pluck('name')->join('، ')
                    : null),
            SelectFilter::make('report_type')
                ->label('نوع مطلب')->relationship('reportType', 'name')
                ->multiple()->searchable()->preload(),
            SelectFilter::make('production_method')
                ->label('نحوه تولید')->options(self::PRODUCTION_METHODS)
                ->multiple()->searchable(),
            SelectFilter::make('categories')
                ->label('دسته‌بندی')->relationship('categories', 'name')
                ->multiple()->searchable()->preload(),
            SelectFilter::make('tags')
                ->label('برچسب‌ها')->relationship('tags', 'name')
                ->multiple()->searchable(),
            Filter::make('date_range')
                ->label('بازه زمانی')->columnSpanFull()
                ->form([
                    Select::make('basis')->label('مبنای بازه زمانی')
                        ->options(['created_at' => 'تاریخ ثبت خبر', 'published_at' => 'تاریخ انتشار'])
                        ->default('created_at')->placeholder('تاریخ ثبت خبر')->native(false),
                    self::dateInput('from', 'از تاریخ (شمسی)'),
                    self::dateInput('until', 'تا تاریخ (شمسی)')
                        ->rules([fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                            try {
                                $from = NewsDateRange::parse($get('from'));
                                $until = NewsDateRange::endExclusive($value);
                                if ($from && $until && $from->gte($until)) {
                                    $fail('تاریخ و ساعت پایان باید برابر یا بعد از شروع باشد.');
                                }
                            } catch (InvalidArgumentException) {
                                // Each input reports its own invalid date.
                            }
                        }]),
                ])->columns(['default' => 1, 'md' => 3])
                ->query(fn (Builder $query, array $data) => NewsDateRange::apply($query, $data))
                ->indicateUsing(function (array $data): array {
                    $basis = ($data['basis'] ?? null) === 'published_at' ? 'انتشار' : 'ثبت';
                    $indicators = [];
                    foreach (['from' => 'از', 'until' => 'تا'] as $key => $label) {
                        if (filled($data[$key] ?? null)) {
                            $indicators[] = Indicator::make("{$basis} {$label} {$data[$key]}")->removeField($key);
                        }
                    }

                    return $indicators;
                }),
        ];
    }

    private static function dateInput(string $name, string $label): TextInput
    {
        return TextInput::make($name)->label($label)
            ->placeholder('انتخاب تاریخ و ساعت')->maxLength(16)
            ->extraInputAttributes([
                'dir' => 'ltr',
                'readonly' => true,
                'data-jalali-date-input' => 'true',
                'data-jalali-range' => 'news-date-range',
                'data-jalali-role' => $name,
                'data-jalali-time' => 'true',
                'autocomplete' => 'off',
            ])
            ->helperText('تقویم شمسی؛ ساعت اختیاری است. بدون ساعت، کل روز لحاظ می‌شود.')
            ->rules([fn (): Closure => function (string $attribute, $value, Closure $fail) {
                try {
                    NewsDateRange::parse($value);
                } catch (InvalidArgumentException $exception) {
                    $fail($exception->getMessage());
                }
            }]);
    }
}
