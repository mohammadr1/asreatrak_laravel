<div
    dir="rtl"
    class="min-h-screen rounded-3xl bg-slate-50 p-4 sm:p-6 dark:bg-slate-950"
>
    <div class="mx-auto max-w-7xl">

        {{-- Header --}}
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-black text-slate-900 sm:text-2xl dark:text-white">
                    کتابخانه رسانه
                </h2>

                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    فایل موردنظر را جستجو، آپلود یا انتخاب کنید.
                </p>
            </div>

            <div
                class="inline-flex w-fit items-center gap-2 rounded-full bg-white px-4 py-2
                       text-sm font-medium text-slate-600 shadow-sm ring-1 ring-slate-200
                       dark:bg-slate-900 dark:text-slate-300 dark:ring-slate-800"
            >
                <svg
                    class="h-4 w-4 text-indigo-500"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z"/>
                </svg>

                <span>
                    مدیریت فایل‌ها
                </span>
            </div>
        </div>

        {{-- Tools --}}
        <div
            class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white
                   shadow-sm dark:border-slate-800 dark:bg-slate-900"
        >
            {{-- Search and filters --}}
            <div class="grid gap-3 border-b border-slate-100 p-4 md:grid-cols-[1fr_180px] sm:p-5 dark:border-slate-800">

                {{-- Search --}}
                <div class="relative">
                    <svg
                        class="pointer-events-none absolute right-4 top-1/2 h-5 w-5
                               -translate-y-1/2 text-slate-400"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>

                    <input
                        type="search"
                        wire:model.live.debounce.400ms="search"
                        placeholder="جستجو بر اساس نام فایل..."
                        class="h-12 w-full rounded-xl border-0 bg-slate-100 pr-12 pl-4
                               text-sm text-slate-900 outline-none ring-1 ring-transparent
                               transition placeholder:text-slate-400
                               focus:bg-white focus:ring-2 focus:ring-indigo-500
                               dark:bg-slate-800 dark:text-white dark:focus:bg-slate-900"
                    >

                    <div
                        wire:loading
                        wire:target="search"
                        class="absolute left-4 top-1/2 -translate-y-1/2"
                    >
                        <svg
                            class="h-4 w-4 animate-spin text-indigo-500"
                            viewBox="0 0 24 24"
                            fill="none"
                        >
                            <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"
                            />
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4Z"
                            />
                        </svg>
                    </div>
                </div>

                {{-- Type filter --}}
                <div class="relative">
                    <select
                        wire:model.live="type"
                        class="h-12 w-full appearance-none rounded-xl border-0 bg-slate-100
                               px-4 pl-10 text-sm font-semibold text-slate-700 outline-none
                               ring-1 ring-transparent transition focus:bg-white
                               focus:ring-2 focus:ring-indigo-500
                               dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900"
                    >
                        <option value="image">تصاویر</option>
                        <option value="video">ویدئوها</option>
                        <option value="document">اسناد</option>
                        <option value="audio">فایل‌های صوتی</option>
                    </select>

                    <svg
                        class="pointer-events-none absolute left-4 top-1/2 h-4 w-4
                               -translate-y-1/2 text-slate-400"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="m6 9 6 6 6-6"/>
                    </svg>
                </div>
            </div>

            {{-- Upload --}}
            <div
                x-data="{ uploading: false, progress: 0 }"
                x-on:livewire-upload-start="uploading = true"
                x-on:livewire-upload-finish="uploading = false; progress = 0"
                x-on:livewire-upload-cancel="uploading = false; progress = 0"
                x-on:livewire-upload-error="uploading = false; progress = 0"
                x-on:livewire-upload-progress="progress = $event.detail.progress"
                class="p-4 sm:p-5"
            >
                <label
                    class="group relative flex min-h-44 cursor-pointer flex-col items-center
                           justify-center overflow-hidden rounded-2xl border-2 border-dashed
                           border-slate-300 bg-slate-50 p-6 text-center transition
                           hover:border-indigo-400 hover:bg-indigo-50/50
                           dark:border-slate-700 dark:bg-slate-950/50
                           dark:hover:border-indigo-500 dark:hover:bg-indigo-950/20"
                >
                    <input
                        type="file"
                        wire:model="upload"
                        accept="{{ match($type) {
                            'video' => 'video/*',
                            'audio' => 'audio/*',
                            'document' => '.pdf,.doc,.docx,.xls,.xlsx,.txt',
                            default => 'image/*'
                        } }}"
                        class="absolute inset-0 z-10 h-full w-full cursor-pointer opacity-0"
                    >

                    <div
                        class="mb-3 flex h-14 w-14 items-center justify-center rounded-2xl
                               bg-indigo-100 text-indigo-600 transition
                               group-hover:scale-110 group-hover:bg-indigo-600
                               group-hover:text-white dark:bg-indigo-950 dark:text-indigo-400"
                    >
                        <svg
                            class="h-7 w-7"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M12 16V4"/>
                            <path d="m7 9 5-5 5 5"/>
                            <path d="M20 15v4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-4"/>
                        </svg>
                    </div>

                    <div class="font-bold text-slate-800 dark:text-slate-100">
                        فایل را اینجا رها کنید
                    </div>

                    <div class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        یا برای انتخاب فایل کلیک کنید
                    </div>

                    <div
                        wire:loading
                        wire:target="upload"
                        class="mt-4 text-sm font-semibold text-indigo-600 dark:text-indigo-400"
                    >
                        در حال آماده‌سازی فایل...
                    </div>
                </label>

                {{-- Upload progress --}}
                <div
                    x-cloak
                    x-show="uploading"
                    class="mt-4"
                >
                    <div class="mb-2 flex items-center justify-between text-xs">
                        <span class="font-semibold text-slate-600 dark:text-slate-300">
                            در حال آپلود
                        </span>

                        <span
                            class="font-bold text-indigo-600 dark:text-indigo-400"
                            x-text="`${progress}%`"
                        ></span>
                    </div>

                    <div class="h-2 overflow-hidden rounded-full bg-slate-200 dark:bg-slate-800">
                        <div
                            class="h-full rounded-full bg-gradient-to-l from-indigo-600 to-violet-500
                                   transition-all duration-300"
                            :style="`width: ${progress}%`"
                        ></div>
                    </div>
                </div>

                @error('upload')
                    <div
                        class="mt-4 flex items-start gap-2 rounded-xl border border-red-200
                               bg-red-50 p-3 text-sm text-red-700
                               dark:border-red-900/50 dark:bg-red-950/30 dark:text-red-400"
                    >
                        <svg
                            class="mt-0.5 h-5 w-5 shrink-0"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 8v4"/>
                            <path d="M12 16h.01"/>
                        </svg>

                        <span>{{ $message }}</span>
                    </div>
                @enderror
            </div>
        </div>

        {{-- Loading skeleton --}}
        <div
            wire:loading.grid
            wire:target="search,type"
            class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5"
        >
            @for($i = 0; $i < 10; $i++)
                <div
                    class="animate-pulse overflow-hidden rounded-2xl border border-slate-200
                           bg-white p-2 dark:border-slate-800 dark:bg-slate-900"
                >
                    <div class="h-36 rounded-xl bg-slate-200 dark:bg-slate-800"></div>

                    <div class="p-2">
                        <div class="h-3 w-4/5 rounded bg-slate-200 dark:bg-slate-800"></div>
                        <div class="mt-3 h-2 w-2/5 rounded bg-slate-100 dark:bg-slate-800"></div>
                    </div>
                </div>
            @endfor
        </div>

        {{-- Media grid --}}
        <div
            wire:loading.remove
            wire:target="search,type"
            class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5"
        >
            @forelse($this->media as $item)
                @php
                    $isSelected = $selected == $item->id;
                    $title = $item->title ?: $item->filename;
                    $extension = strtoupper(pathinfo($item->filename, PATHINFO_EXTENSION));
                @endphp

                <button
                    type="button"
                    wire:key="media-{{ $item->id }}"
                    wire:click="select({{ $item->id }})"
                    @class([
                        'group relative overflow-hidden rounded-2xl border bg-white p-2 text-right',
                        'shadow-sm transition-all duration-300',
                        'hover:-translate-y-1 hover:shadow-xl',
                        'dark:bg-slate-900',
                        'border-indigo-500 ring-4 ring-indigo-500/15' => $isSelected,
                        'border-slate-200 hover:border-indigo-300 dark:border-slate-800 dark:hover:border-indigo-700' => !$isSelected,
                    ])
                >
                    {{-- Preview --}}
                    <div class="relative h-36 overflow-hidden rounded-xl bg-slate-100 dark:bg-slate-800">
                        @if($type === 'image')
                            <img
                                src="{{ asset('storage/'.$item->original_path) }}"
                                alt="{{ $title }}"
                                loading="lazy"
                                class="h-full w-full object-cover transition duration-500
                                       group-hover:scale-110"
                            >
                        @elseif($type === 'video')
                            <video
                                preload="metadata"
                                class="h-full w-full object-cover transition duration-500
                                       group-hover:scale-105"
                            >
                                <source src="{{ asset('storage/'.$item->original_path) }}">
                            </video>

                            <div class="absolute inset-0 flex items-center justify-center bg-black/20">
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-full
                                           bg-white/90 text-indigo-600 shadow-lg backdrop-blur"
                                >
                                    <svg class="mr-0.5 h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M8 5v14l11-7L8 5Z"/>
                                    </svg>
                                </div>
                            </div>
                        @else
                            <div class="flex h-full flex-col items-center justify-center">
                                <div
                                    class="flex h-14 w-14 items-center justify-center rounded-2xl
                                           bg-indigo-100 text-indigo-600
                                           dark:bg-indigo-950 dark:text-indigo-400"
                                >
                                    @if($type === 'audio')
                                        <svg
                                            class="h-7 w-7"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path d="M9 18V5l12-2v13"/>
                                            <circle cx="6" cy="18" r="3"/>
                                            <circle cx="18" cy="16" r="3"/>
                                        </svg>
                                    @else
                                        <svg
                                            class="h-7 w-7"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/>
                                            <path d="M14 2v6h6"/>
                                            <path d="M8 13h8"/>
                                            <path d="M8 17h8"/>
                                        </svg>
                                    @endif
                                </div>

                                <span class="mt-2 text-xs font-black text-slate-400">
                                    {{ $extension }}
                                </span>
                            </div>
                        @endif

                        {{-- Image overlay --}}
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-transparent
                                   to-transparent opacity-0 transition group-hover:opacity-100"
                        ></div>

                        {{-- Selected badge --}}
                        @if($isSelected)
                            <div
                                class="absolute right-2 top-2 flex items-center gap-1 rounded-full
                                       bg-indigo-600 px-2.5 py-1 text-[11px] font-bold text-white
                                       shadow-lg"
                            >
                                <svg
                                    class="h-3.5 w-3.5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="3"
                                >
                                    <path d="m5 12 4 4L19 6"/>
                                </svg>

                                انتخاب شد
                            </div>
                        @endif

                        {{-- Extension badge --}}
                        @if($extension)
                            <span
                                class="absolute bottom-2 left-2 rounded-md bg-slate-950/70 px-2
                                       py-1 text-[10px] font-bold text-white backdrop-blur"
                            >
                                {{ $extension }}
                            </span>
                        @endif
                    </div>

                    {{-- Information --}}
                    <div class="px-1 pb-1 pt-3">
                        <div
                            class="truncate text-sm font-bold text-slate-800
                                   dark:text-slate-100"
                            title="{{ $title }}"
                        >
                            {{ $title }}
                        </div>

                        <div class="mt-2 flex items-center justify-between gap-2">
                            <span class="text-xs text-slate-400">
                                {{ number_format($item->size / 1024, 0) }} KB
                            </span>

                            <span
                                @class([
                                    'h-2 w-2 rounded-full transition',
                                    'bg-indigo-500 ring-4 ring-indigo-100 dark:ring-indigo-950' => $isSelected,
                                    'bg-slate-300 dark:bg-slate-700' => !$isSelected,
                                ])
                            ></span>
                        </div>
                    </div>
                </button>
            @empty
                <div
                    class="col-span-full flex min-h-72 flex-col items-center justify-center
                           rounded-2xl border-2 border-dashed border-slate-200 bg-white p-8
                           text-center dark:border-slate-800 dark:bg-slate-900"
                >
                    <div
                        class="flex h-20 w-20 items-center justify-center rounded-full
                               bg-slate-100 text-slate-400 dark:bg-slate-800"
                    >
                        <svg
                            class="h-9 w-9"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.5"
                        >
                            <rect width="18" height="18" x="3" y="3" rx="2"/>
                            <circle cx="9" cy="9" r="2"/>
                            <path d="m21 15-5-5L5 21"/>
                        </svg>
                    </div>

                    <h3 class="mt-5 font-bold text-slate-800 dark:text-slate-100">
                        رسانه‌ای پیدا نشد
                    </h3>

                    <p class="mt-2 max-w-sm text-sm leading-6 text-slate-500 dark:text-slate-400">
                        فایل جدیدی آپلود کنید یا عبارت جستجو و نوع رسانه را تغییر دهید.
                    </p>

                    @if($search)
                        <button
                            type="button"
                            wire:click="$set('search', '')"
                            class="mt-5 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-bold
                                   text-white transition hover:bg-indigo-700"
                        >
                            پاک کردن جستجو
                        </button>
                    @endif
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($this->media->hasPages())
            <div
                class="mt-8 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm
                       dark:border-slate-800 dark:bg-slate-900"
            >
                {{ $this->media->links() }}
            </div>
        @endif
    </div>
</div>