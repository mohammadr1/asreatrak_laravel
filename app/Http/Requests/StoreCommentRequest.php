<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $clean = fn ($v) => is_string($v) ? trim(strip_tags($v)) : '';

        $this->merge([
            'name'          => $clean($this->input('name')),
            'emailOrMobile' => $this->normalizeContact($clean($this->input('emailOrMobile'))),
            'body'          => $clean($this->input('body')),
        ]);
    }

    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'min:3', 'max:100'],
            'emailOrMobile' => [
                'bail', 'required', 'string', 'max:150',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $isEmail  = filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
                    $isMobile = preg_match('/^09\d{9}$/', (string) $value) === 1;

                    if (! $isEmail && ! $isMobile) {
                        $fail('ایمیل یا شماره‌ی موبایل معتبر وارد کنید (مثلاً 09121234567).');
                    }
                },
            ],
            'body'          => ['required', 'string', 'min:5', 'max:2000', 'not_regex:~(https?://|www\.)~i'],
            'notify_me'     => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'          => 'نام خود را وارد کنید.',
            'name.min'               => 'نام باید حداقل ۳ حرف باشد.',
            'name.max'               => 'نام نباید بیشتر از ۱۰۰ حرف باشد.',
            'emailOrMobile.required' => 'ایمیل یا شماره‌ی موبایل خود را وارد کنید.',
            'emailOrMobile.max'      => 'ایمیل یا شماره‌ی موبایل خیلی طولانی است.',
            'body.required'          => 'متن نظر را بنویسید.',
            'body.min'               => 'متن نظر خیلی کوتاه است.',
            'body.max'               => 'متن نظر نباید بیشتر از ۲۰۰۰ حرف باشد.',
            'body.not_regex'         => 'درج لینک در نظر مجاز نیست.',
        ];
    }

    /** بعد از خطا، صفحه روی فرم اسکرول شود */
    protected function getRedirectUrl(): string
    {
        return $this->redirector->getUrlGenerator()->previous() . '#comment-form';
    }

    /**
     * ایمیل → حروف کوچک.
     * موبایل → ارقام لاتین و قالب 09xxxxxxxxx
     * (۰۹۱۲…، 0912…، +98 912…، 0098912… همه قبول می‌شوند)
     */
    private function normalizeContact(string $value): string
    {
        $value = strtr($value, [
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
            '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
            '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
        ]);

        if (str_contains($value, '@')) {
            return mb_strtolower($value);
        }

        $digits = preg_replace('/[\s\-\(\)\+]/', '', $value);

        if (str_starts_with($digits, '0098')) {
            $digits = '0' . substr($digits, 4);
        } elseif (str_starts_with($digits, '98') && strlen($digits) === 12) {
            $digits = '0' . substr($digits, 2);
        } elseif (strlen($digits) === 10 && str_starts_with($digits, '9')) {
            $digits = '0' . $digits;
        }

        return $digits;
    }
}