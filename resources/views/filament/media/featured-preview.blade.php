@if($url)

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-100 dark:border-slate-700 dark:bg-slate-800">

        <img
            src="{{ $url }}"
            alt="{{ $media->alt ?: $media->title ?: 'تصویر شاخص' }}"
            class="block h-auto max-h-[420px] w-full object-contain"
        >

    </div>

    <div class="mt-2 text-xs text-slate-500">
        نسخه انتخاب‌شده:
        <span class="font-bold">
            {{ $variant }}
        </span>
    </div>

@else

    <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        نسخه انتخاب‌شده برای این تصویر موجود نیست.
    </div>

@endif