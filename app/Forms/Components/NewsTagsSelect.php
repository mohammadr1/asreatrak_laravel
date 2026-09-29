<?php

namespace App\Forms\Components;

use App\Models\News;
use App\Models\Tag;
use Closure;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class NewsTagsSelect extends Select
{
    private const PREFIX = 'new-tag:';

    protected function setUp(): void
    {
        parent::setUp();

        $this->relationship('tags', 'name')->multiple()->searchable()->preload()
            ->searchDebounce(250)
            ->helperText('نام برچسب را جستجو کنید؛ برای برچسب جدید، گزینه افزودن را با Enter انتخاب کنید. ثبت نهایی با ذخیره خبر انجام می‌شود.')
            ->getSearchResultsUsing(fn (string $search) => static::search($search))
            ->getOptionLabelsUsing(function (array $values): array {
                $labels = Tag::whereKey(array_filter($values, fn ($value) => ctype_digit((string) $value)))->pluck('name', 'id')->all();
                foreach ($values as $value) {
                    if (is_string($value) && str_starts_with($value, self::PREFIX)) {
                        $labels[$value] = static::normalize(substr($value, strlen(self::PREFIX))).' (جدید)';
                    }
                }
                return $labels;
            })
            ->rules([fn (): Closure => function (string $attribute, $value, Closure $fail): void {
                foreach ((array) $value as $item) {
                    if (! is_scalar($item)) { $fail('برچسب نامعتبر است.'); return; }
                    if (str_starts_with((string) $item, self::PREFIX)) {
                        $name = static::normalize(substr($item, strlen(self::PREFIX)));
                        if ($name === '' || mb_strlen($name) > 100) $fail('نام برچسب باید بین ۱ تا ۱۰۰ کاراکتر باشد.');
                    } elseif (! ctype_digit((string) $item) || ! Tag::whereKey($item)->exists()) {
                        $fail('برچسب انتخاب‌شده دیگر موجود نیست.');
                    }
                }
            }])
            ->saveRelationshipsUsing(function (NewsTagsSelect $component, News $record, $state): void {
                $ids = DB::transaction(function () use ($record, $state) {
                    $ids = [];
                    foreach ((array) $state as $value) {
                        if (str_starts_with((string) $value, self::PREFIX)) {
                            $name = static::normalize(substr($value, strlen(self::PREFIX)));
                            if ($name === '' || mb_strlen($name) > 100) {
                                throw ValidationException::withMessages(['data.tags' => 'نام برچسب نامعتبر است.']);
                            }
                            $tag = Tag::where('name', $name)->first();
                            if (! $tag) {
                                // A stable, unique slug also prevents concurrent duplicate creation.
                                $slug = 'tag-'.hash('sha256', mb_strtolower($name));
                                $slugOwner = Tag::withTrashed()->where('slug', $slug)->first();
                                if ($slugOwner?->trashed()) $slug .= '-'.Str::lower(Str::random(8));
                                $tag = Tag::firstOrCreate(['slug' => $slug], ['name' => $name, 'is_active' => true]);
                            }
                            $ids[] = $tag->id;
                        } else {
                            $ids[] = Tag::findOrFail($value)->id;
                        }
                    }
                    $ids = array_values(array_unique($ids));
                    $record->tags()->sync($ids);
                    return $ids;
                });
                $component->state(array_map('strval', $ids));
            });
    }

    public static function normalize(string $name): string
    {
        return trim(preg_replace('/\s+/u', ' ', strtr($name, ['ي' => 'ی', 'ك' => 'ک'])));
    }

    public static function search(string $search): array
    {
        $name = static::normalize($search);
        $results = Tag::where('name', 'like', '%'.$name.'%')->orderBy('name')->limit(50)->pluck('name', 'id')->all();
        $exact = Tag::where('name', $name)->first();
        if ($exact) return [$exact->id => $exact->name] + $results;
        if ($name !== '' && mb_strlen($name) <= 100) {
            return [self::PREFIX.$name => 'افزودن «'.$name.'» (Enter)'] + $results;
        }
        return $results;
    }
}
