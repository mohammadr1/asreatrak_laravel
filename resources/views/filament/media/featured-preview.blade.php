@if($url)

    <div dir="rtl" class="overflow-hidden rounded-3xl border border-slate-200 bg-slate-100 shadow-sm ring-1 ring-slate-900/[0.04] dark:border-slate-700 dark:bg-slate-800">

        <img
            src="{{ $url }}"
            alt="{{ $media->alt ?: $media->title ?: 'تصویر شاخص' }}"
            class="block aspect-[4/3] h-auto max-h-[420px] w-full object-contain transition duration-500 hover:scale-[1.02]"
        >

    </div>

    <div class="mt-3 flex items-center justify-between gap-3 rounded-2xl bg-slate-50 px-3 py-2 text-xs text-slate-500 dark:bg-slate-900/70 dark:text-slate-400">
        نسخه انتخاب‌شده:
        <span class="rounded-full bg-indigo-100 px-2 py-1 font-bold text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-300">
            {{ $variant }}
        </span>
    </div>

@else

    <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        نسخه انتخاب‌شده برای این تصویر موجود نیست.
    </div>

@endif
