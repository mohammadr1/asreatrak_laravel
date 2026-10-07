<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NewsText implements ValidationRule
{
    public function __construct(private bool $rich = false) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $name = match (\Illuminate\Support\Str::afterLast($attribute, '.')) {
            'title' => 'عنوان خبر', 'uptitle' => 'روتیتر', 'lead' => 'لید خبر',
            'content' => 'متن خبر', 'meta_title' => 'عنوان سئو', 'meta_description' => 'توضیحات سئو',
            default => 'این بخش',
        };
        if (! is_string($value)) { $fail("برای {$name} متن معتبر وارد کنید."); return; }
        $source = $this->rich ? \Illuminate\Support\Str::sanitizeHtml($value) : $value;
        $plain = html_entity_decode(strip_tags($source), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if (! preg_match('/[\p{L}\p{N}]/u', $plain)) {
            $fail("برای {$name} متن بنویسید؛ فاصله، علامت یا قالب‌بندی خالی کافی نیست.");
        }
        if (! $this->rich && $value !== strip_tags($value)) {
            $fail("در {$name} فقط متن ساده وارد کنید؛ کد HTML مجاز نیست.");
        }
    }
}
