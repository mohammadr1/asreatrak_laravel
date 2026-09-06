@if ($errors->any()) <div
     class="mb-6 overflow-hidden rounded-2xl border border-red-300 bg-red-50 shadow-sm dark:border-red-800 dark:bg-red-950/30"
     dir="rtl"
 >


    {{-- Header --}}
    <div class="flex items-center gap-3 border-b border-red-200 bg-red-100 px-5 py-4 dark:border-red-800 dark:bg-red-900/30">

        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-600 shadow">
            <x-heroicon-o-exclamation-triangle class="h-6 w-6" />
        </div>

        <div>
            <h3 class="text-base font-black text-red-800 dark:text-red-200">
                خطا در اطلاعات فرم
            </h3>

            <p class="mt-1 text-xs font-medium text-red-600 dark:text-red-300">
                لطفاً موارد زیر را اصلاح کنید.
            </p>
        </div>

    </div>

    {{-- Error List --}}
    <div class="p-4">

        <div class="space-y-2">

            @foreach ($errors->all() as $message)

                <div class="flex items-center gap-3 rounded-xl border border-red-200 bg-white px-4 py-3 text-sm font-bold text-red-700 shadow-sm dark:border-red-900 dark:bg-gray-900 dark:text-red-300">

                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600 dark:bg-red-900/50 dark:text-red-300">
                        <x-heroicon-s-x-mark class="h-4 w-4" />
                    </span>

                    <span>
                        {{ $message }}
                    </span>

                </div>

            @endforeach

        </div>

    </div>

</div>


@endif
