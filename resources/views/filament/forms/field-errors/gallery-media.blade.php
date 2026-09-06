@error('gallery_media')
    <div
        class="mt-3 flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-700 shadow-sm dark:border-red-900/60 dark:bg-red-950/30 dark:text-red-300"
        dir="rtl"
    >
        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-red-100 text-red-600 dark:bg-red-900/50 dark:text-red-300">
            <x-heroicon-o-exclamation-triangle class="h-4 w-4" />
        </span>
        <span>{{ $message }}</span>
    </div>
@enderror