<div dir="rtl" class="w-full"
    x-data="{
        notifications: [],

        addNotification(event) {
            const detail = event.detail || {};

            const id = Date.now() + Math.random();

            this.notifications.push({
                id: id,
                type: detail.type || 'success',
                message: detail.message || 'عملیات با موفقیت انجام شد.'
            });

            setTimeout(() => {
                this.removeNotification(id);
            }, 4500);
        },

        removeNotification(id) {
            this.notifications = this.notifications.filter(
                notification => notification.id !== id
            );
        }
    }"
    x-on:notify.window="addNotification($event)"
    >


    {{-- ========================================================= --}}
{{-- Notifications / Toasts --}}
{{-- ========================================================= --}}

<div
    class="pointer-events-none fixed inset-x-0 top-4 z-[100000] flex justify-center px-4 sm:justify-start sm:px-6"
>
    <div class="flex w-full max-w-md flex-col gap-3 sm:w-auto">
        
        <template x-for="notification in notifications" :key="notification.id">

            <div
                x-transition:enter="transform ease-out duration-300"
                x-transition:enter-start="translate-y-[-20px] opacity-0 scale-95"
                x-transition:enter-end="translate-y-0 opacity-100 scale-100"
                x-transition:leave="transform ease-in duration-200"
                x-transition:leave-start="translate-y-0 opacity-100 scale-100"
                x-transition:leave-end="translate-y-[-10px] opacity-0 scale-95"
                class="pointer-events-auto overflow-hidden rounded-2xl border bg-white shadow-2xl ring-1 ring-black/5 dark:bg-slate-900 dark:ring-white/10"
            >

                <div class="flex items-start gap-3 p-4">

                    {{-- Icon --}}
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                        :class="{
                            'bg-emerald-100 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-400':
                                notification.type === 'success',

                            'bg-red-100 text-red-600 dark:bg-red-500/15 dark:text-red-400':
                                notification.type === 'error',

                            'bg-amber-100 text-amber-600 dark:bg-amber-500/15 dark:text-amber-400':
                                notification.type === 'warning',

                            'bg-indigo-100 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-400':
                                notification.type === 'info'
                        }"
                    >

                        {{-- Success --}}
                        <svg
                            x-show="notification.type === 'success'"
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.5"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m5 12 4 4L19 6"
                            />
                        </svg>

                        {{-- Error --}}
                        <svg
                            x-show="notification.type === 'error'"
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.5"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6 6l12 12M18 6 6 18"
                            />
                        </svg>

                        {{-- Warning --}}
                        <svg
                            x-show="notification.type === 'warning'"
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.5"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 9v4m0 4h.01M10.3 4.6 2.8 18a2 2 0 0 0 1.7 3h15a2 2 0 0 0 1.7-3L13.7 4.6a2 2 0 0 0-3.4 0Z"
                            />
                        </svg>

                        {{-- Info --}}
                        <svg
                            x-show="notification.type === 'info'"
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.5"
                        >
                            <circle cx="12" cy="12" r="9" />
                            <path
                                stroke-linecap="round"
                                d="M12 10v6"
                            />
                            <path
                                stroke-linecap="round"
                                d="M12 7h.01"
                            />
                        </svg>

                    </div>

                    {{-- Message --}}
                    <div class="min-w-0 flex-1 pt-1">

                        <p
                            class="text-sm font-bold leading-6 text-slate-800 dark:text-slate-100"
                            x-text="notification.message"
                        ></p>

                    </div>

                    {{-- Close --}}
                    <button
                        type="button"
                        @click="removeNotification(notification.id)"
                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:"
                        aria-label="بستن"
                    >
                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6 6l12 12M18 6 6 18"
                            />
                        </svg>
                    </button>

                </div>

                {{-- Progress --}}
                <div class="h-1 bg-slate-100 dark:bg-slate-800">
                    <div
                        class="h-full origin-right animate-[shrink_4.5s_linear_forwards]"
                        :class="{
                            'bg-emerald-500': notification.type === 'success',
                            'bg-red-500': notification.type === 'error',
                            'bg-amber-500': notification.type === 'warning',
                            'bg-indigo-500': notification.type === 'info'
                        }"
                    ></div>
                </div>

            </div>

        </template>
    </div>
</div>




    {{-- ========================================================= --}}
    {{-- Header --}}
    {{-- ========================================================= --}}

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h2 class="text-2xl font-black text-slate-900 dark:">
                مدیریت رسانه
            </h2>

            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                تصویر موردنظر را انتخاب یا رسانه جدیدی آپلود کنید.
            </p>
        </div>

        {{-- Selection status --}}

        @if($selectionMode === 'single')

        <div class="rounded-xl bg-indigo-50 px-4 py-2 text-sm font-bold text-indigo-700
                       dark:bg-indigo-950/40 dark:text-indigo-300">
            انتخاب یک تصویر
        </div>

        @else

        <div class="rounded-xl bg-indigo-50 px-4 py-2 text-sm font-bold text-indigo-700
                       dark:bg-indigo-950/40 dark:text-indigo-300">
            انتخاب چند تصویر
            <span class="mr-1">
                ({{ $this->selectedCount }} از {{ $maxSelection }})
            </span>
        </div>

        @endif

    </div>


    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">

        <div>
            @if($deleteMode)

            <div class="text-sm font-bold text-red-600 dark:text-red-400">
                حالت حذف فعال است.
                روی رسانه‌هایی که می‌خواهید حذف شوند کلیک کنید.
            </div>

            @else

            <div class="text-sm text-slate-500 dark:text-slate-400">
                برای انتخاب چند رسانه و حذف هم‌زمان، حالت حذف را فعال کنید.
            </div>

            @endif
        </div>


        <div class="flex flex-wrap gap-2">

            @if($deleteMode)

            <button type="button" wire:click="toggleDeleteMode" class="
                        rounded-xl
                        border border-slate-300
                        bg-white
                        px-4 py-2.5
                        text-sm font-bold
                        text-slate-700
                        transition
                        hover:bg-slate-100
                        focus:outline-none
                        focus-visible:ring-2
                        focus-visible:ring-slate-400
                        active:scale-95
                        dark:border-slate-700
                        dark:bg-slate-800
                        dark:
                        dark:hover:bg-slate-700
                        dark:focus-visible:ring-slate-500
                    ">
                خروج از حالت حذف
            </button>


            @if($this->deleteSelectedCount > 0)

            <button type="button" wire:click="deleteSelectedMedia"
                wire:confirm="آیا از حذف همه رسانه‌های انتخاب‌شده مطمئن هستید؟" wire:loading.attr="disabled"
                wire:target="deleteSelectedMedia" class="
                            rounded-xl
                            bg-red-600
                            px-5 py-2.5
                            text-sm font-black
                            
                            transition
                            hover:bg-red-700
                            focus:outline-none
                            focus-visible:ring-2
                            focus-visible:ring-red-400
                            focus-visible:ring-offset-2
                            active:scale-95
                            disabled:cursor-not-allowed
                            disabled:opacity-50
                            dark:focus-visible:ring-offset-slate-900
                        ">

                <span wire:loading.remove wire:target="deleteSelectedMedia">
                    حذف {{ $this->deleteSelectedCount }} رسانه
                </span>

                <span wire:loading wire:target="deleteSelectedMedia">
                    در حال حذف...
                </span>

            </button>

            @endif

            @else

            <button type="button" wire:click="toggleDeleteMode" class="
                        rounded-xl
                        border border-red-200
                        bg-red-50
                        px-4 py-2.5
                        text-sm font-bold
                        text-red-600
                        transition
                        hover:bg-red-100
                        focus:outline-none
                        focus-visible:ring-2
                        focus-visible:ring-red-400
                        active:scale-95
                        dark:border-red-900/50
                        dark:bg-red-950/20
                        dark:text-red-400
                        dark:hover:bg-red-900/30
                    ">
                انتخاب برای حذف چندتایی
            </button>

            @endif

        </div>

    </div>

    {{-- ========================================================= --}}
    {{-- Media Variant --}}
    {{-- ========================================================= --}}

    <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-2 shadow-sm
               dark:border-slate-800 dark:bg-slate-900">

        <div class="grid grid-cols-3 gap-2">

            {{-- Watermarked --}}

            <button type="button" wire:click="setMediaVariant('watermarked')" class="
                    rounded-xl px-4 py-3 text-sm font-bold transition
                    focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400
                " @class([ 'bg-indigo-600  shadow-md'=> $mediaVariant === 'watermarked',

                'bg-slate-100 text-slate-600 hover:bg-slate-200
                dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700'
                => $mediaVariant !== 'watermarked',
                ])
                >
                <div>
                    واترمارک‌دار
                </div>

                <div class="mt-1 text-[11px] font-normal opacity-80">
                    نسخه آماده انتشار
                </div>
            </button>


            {{-- Cropped --}}

            <button type="button" wire:click="setMediaVariant('cropped')" class="
                    rounded-xl px-4 py-3 text-sm font-bold transition
                    focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400
                " @class([ 'bg-indigo-600  shadow-md'=> $mediaVariant === 'cropped',

                'bg-slate-100 text-slate-600 hover:bg-slate-200
                dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700'
                => $mediaVariant !== 'cropped',
                ])
                >
                <div>
                    بدون واترمارک
                </div>

                <div class="mt-1 text-[11px] font-normal opacity-80">
                    نسخه کراپ‌شده
                </div>
            </button>


            {{-- Original --}}

            <!-- <button type="button" wire:click="setMediaVariant('original')" class="
                    rounded-xl px-4 py-3 text-sm font-bold transition
                " @class([ 'bg-indigo-600  shadow-md'=> $mediaVariant === 'original',

                'bg-slate-100 text-slate-600 hover:bg-slate-200
                dark:bg-slate-800 dark:text-slate-300'
                => $mediaVariant !== 'original',
                ])
                >
                <div>
                    تصویر اصلی
                </div>

                <div class="mt-1 text-[11px] font-normal opacity-80">
                    بدون پردازش
                </div>
            </button> -->

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- Search --}}
    {{-- ========================================================= --}}

    <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm
               dark:border-slate-800 dark:bg-slate-900">

        <div class="relative">

            <input type="text" wire:model.live.debounce.400ms="search" placeholder="جستجو در نام تصویر یا فایل..."
                class="
                    w-full rounded-xl border border-slate-200 bg-slate-50
                    px-4 py-3 text-sm text-slate-800 outline-none transition
                    placeholder:text-slate-400
                    focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20
                    dark:border-slate-700 dark:bg-slate-950
                    dark: dark:placeholder:text-slate-500
                    dark:focus:border-indigo-500 dark:focus:ring-indigo-500/30
                ">

        </div>

    </div>





    {{-- ========================================================= --}}
    {{-- Upload --}}
    {{-- ========================================================= --}}

    <div
        class="
            mb-8 rounded-2xl border-2 border-dashed
            border-slate-300 bg-slate-50 p-6
            transition hover:border-indigo-400
            dark:border-slate-700 dark:bg-slate-950
            dark:hover:border-indigo-500
        "
    >

        <div class="mb-5">

            <label
                for="media-title"
                class="
                    mb-2 block
                    text-sm font-black
                    text-slate-800
                    dark:
                "
            >
                عنوان رسانه

                <span class="mr-1 text-xs font-normal text-slate-400 dark:text-slate-500">
                    (اختیاری)
                </span>
            </label>

            <input
                id="media-title"
                type="text"
                wire:model.live="uploadTitle"
                placeholder="مثلاً افتتاح پروژه فرهنگی بجنورد"
                class="
                    w-full rounded-xl
                    border border-slate-200
                    bg-white
                    px-4 py-3
                    text-sm
                    text-slate-800
                    outline-none
                    transition
                    placeholder:text-slate-400
                    focus:border-indigo-500
                    focus:ring-2
                    focus:ring-indigo-500/20
                    dark:border-slate-700
                    dark:bg-slate-900
                    dark:
                    dark:placeholder:text-slate-500
                    dark:focus:border-indigo-500
                    dark:focus:ring-indigo-500/30
                "
            >

        </div>


        <label
            for="media-upload"
            class="
                flex cursor-pointer
                flex-col items-center
                justify-center
                rounded-2xl
                border border-dashed
                border-slate-300
                bg-white
                px-6 py-8
                text-center
                transition
                hover:border-indigo-500
                hover:bg-indigo-50/30
                dark:border-slate-700
                dark:bg-slate-900
                dark:hover:border-indigo-500
                dark:hover:bg-indigo-950/20
            "
        >

            <div
                class="
                    text-base font-black
                    text-slate-800
                    dark:
                "
            >
                انتخاب تصویر
            </div>

            <div
                class="
                    mt-2 text-sm
                    text-slate-500
                    dark:text-slate-400
                "
            >
                یک یا چند تصویر انتخاب کنید.
            </div>

            <input
                id="media-upload"
                type="file"
                wire:model="uploads"
                multiple
                accept="image/*"
                class="hidden"
            >

        </label>


        <div
            wire:loading
            wire:target="uploads"
            class="
                mt-4 text-center
                text-sm font-bold
                text-indigo-600
                dark:text-indigo-400
            "
        >
            در حال دریافت فایل‌ها...
        </div>


        @if(count($uploads))

            <div
                class="
                    mt-4 rounded-xl
                    border border-emerald-200
                    bg-emerald-50
                    p-4
                    text-sm
                    font-bold
                    text-emerald-700
                    dark:border-emerald-900/50
                    dark:bg-emerald-950/20
                    dark:text-emerald-400
                "
            >
                {{ count($uploads) }}
                فایل انتخاب شده است.
            </div>


            <button
                type="button"
                wire:click="startUpload"
                wire:loading.attr="disabled"
                wire:target="startUpload,uploads"
                class="
                    mt-4 w-full
                    rounded-xl
                    bg-indigo-600
                    px-5 py-3
                    text-sm font-black
                    
                    transition
                    hover:bg-indigo-700
                    focus:outline-none
                    focus-visible:ring-2
                    focus-visible:ring-indigo-400
                    focus-visible:ring-offset-2
                    active:scale-[0.99]
                    disabled:cursor-not-allowed
                    disabled:opacity-50
                    dark:focus-visible:ring-offset-slate-950
                "
            >

                <span
                    wire:loading.remove
                    wire:target="startUpload"
                    class="inline-flex items-center justify-center gap-2 "
                >
                    <svg
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.5"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 4v10m0-10 4 4m-4-4-4 4M5 20h14"
                        />
                    </svg>

                    شروع آپلود و پردازش
                </span>

                <span
                    wire:loading
                    wire:target="startUpload"
                >
                    در حال آماده‌سازی تصاویر...
                </span>

            </button>

        @endif


        @error('uploads')

            <div
                class="
                    mt-4 rounded-xl
                    border border-red-200
                    bg-red-50 p-3
                    text-sm font-bold
                    text-red-700
                    dark:border-red-900/50
                    dark:bg-red-950/30
                    dark:text-red-400
                "
            >
                {{ $message }}
            </div>

        @enderror


        @error('uploads.*')

            <div
                class="
                    mt-4 rounded-xl
                    border border-red-200
                    bg-red-50 p-3
                    text-sm font-bold
                    text-red-700
                    dark:border-red-900/50
                    dark:bg-red-950/30
                    dark:text-red-400
                "
            >
                {{ $message }}
            </div>

        @enderror

    </div>


    {{-- ========================================================= --}}
    {{-- Media Grid --}}
    {{-- ========================================================= --}}

    <div wire:loading.remove wire:target="search"
        class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6">

        @forelse($this->media as $item)

        @php

        $isSelected = $this->isSelected($item->id);

        $isDeleteSelected = $this->isDeleteSelected($item->id);

        $title =
            $item->title
            ?: $item->filename;

        $extension =
            strtoupper(
                pathinfo(
                    $item->filename,
                    PATHINFO_EXTENSION
                )
            );

        $preview =
            $item->variantUrl(
                $mediaVariant
            );

        @endphp


        <div
            wire:key="media-card-{{ $item->id }}"
            class="
                group relative overflow-hidden rounded-2xl
                border bg-white p-2 shadow-sm transition
                hover:-translate-y-1 hover:shadow-xl
                dark:bg-slate-900 dark:shadow-none dark:hover:shadow-black/40
            "
            @class([
                'border-indigo-500 ring-4 ring-indigo-500/10 dark:ring-indigo-500/20'
                    => $isSelected && ! $deleteMode,

                'border-red-500 ring-4 ring-red-500/10 dark:ring-red-500/20'
                    => $isDeleteSelected && $deleteMode,

                'border-slate-200 dark:border-slate-800'
                    => ! $isSelected && ! $isDeleteSelected,
            ])
        >

            {{-- ================================================= --}}
            {{-- Image --}}
            {{-- ================================================= --}}

            <button
                type="button"
                wire:click="toggleSelection({{ $item->id }})"
                class="block w-full rounded-xl text-right focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400"
            >

                <div class="
                    relative aspect-[16/10] overflow-hidden
                    rounded-xl bg-slate-100
                    dark:bg-slate-800
                ">

                    @if($preview)

                            <img
                                src="{{ $preview }}"
                                alt="{{ $title }}"
                                loading="lazy"
                                class="
                                    block
                                    h-full
                                    w-full
                                    object-cover
                                    transition
                                    duration-500
                                    group-hover:scale-105
                                "
                                draggable="false"
                            >

                        @else

                            <div class="
                                flex
                                h-full
                                w-full
                                items-center
                                justify-center
                                bg-slate-100
                                px-4
                                text-center
                                dark:bg-slate-800
                            ">
                                <div>
                                    <div class="text-xs font-black text-slate-500 dark:text-slate-400">
                                        نسخه انتخاب‌شده موجود نیست
                                    </div>

                                    @if($mediaVariant === 'watermarked')
                                        <div class="mt-1 text-[10px] text-slate-400">
                                            نسخه واترمارک‌دار برای این تصویر وجود ندارد
                                        </div>
                                    @elseif($mediaVariant === 'cropped')
                                        <div class="mt-1 text-[10px] text-slate-400">
                                            نسخه کراپ‌شده برای این تصویر وجود ندارد
                                        </div>
                                    @endif
                                </div>
                            </div>

                        @endif


                    {{-- Selection overlay --}}

                    @if($isSelected)

                    <div class="
                                    absolute inset-0 flex items-center
                                    justify-center bg-indigo-600/25
                                ">

                        <div class="
                                        flex h-12 w-12 items-center
                                        justify-center rounded-full
                                        bg-indigo-600 
                                        shadow-xl
                                        ring-4 ring-white/30
                                    ">

                            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                <path d="m5 12 4 4L19 6" />
                            </svg>

                        </div>

                    </div>

                    @endif


                    {{-- Variant badge --}}

                    <div class="
                                absolute right-2 top-2 rounded-lg
                                bg-black/70 px-2 py-1
                                text-[10px] font-bold 
                                backdrop-blur
                            ">

                        @switch($mediaVariant)

                        @case('watermarked')
                        واترمارک
                        @break

                        @case('cropped')
                        کراپ‌شده
                        @break

                        @default
                        اصلی

                        @endswitch

                    </div>


                    {{-- Extension --}}

                    <div class="
                                absolute bottom-2 left-2 rounded-md
                                bg-black/70 px-2 py-1
                                text-[10px] font-bold 
                            ">
                        {{ $extension }}
                    </div>

                </div>


                {{-- Title --}}

                <div class="mt-2 px-1">

                    <div class="
                                truncate text-sm font-bold
                                text-slate-800
                                dark:text-slate-100
                            " title="{{ $title }}">
                        {{ $title }}
                    </div>

                    <div class="
                                mt-1 text-xs text-slate-400
                                dark:text-slate-500
                            ">
                        {{ $item->width }} × {{ $item->height }}
                    </div>

                </div>

            </button>


            {{-- ================================================= --}}
            {{-- Select Button --}}
            {{-- ================================================= --}}


            <button
                type="button"
                wire:click.stop="deleteMedia({{ $item->id }})"
                wire:confirm="آیا از حذف این رسانه مطمئن هستید؟"
                wire:loading.attr="disabled"
                wire:target="deleteMedia({{ $item->id }})"
                class="
                    absolute left-3 top-3 z-20
                    flex h-9 w-9
                    items-center justify-center
                    rounded-xl
                    bg-red-600
                    
                    shadow-lg
                    ring-2 ring-white/40
                    opacity-100
                    transition
                    hover:bg-red-700
                    focus:outline-none
                    focus-visible:ring-2
                    focus-visible:ring-red-400
                    active:scale-95
                    disabled:cursor-not-allowed
                    disabled:opacity-50
                    sm:opacity-0
                    sm:group-hover:opacity-100
                    sm:focus-visible:opacity-100
                    dark:ring-slate-900/60
                "
                title="حذف رسانه"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" />
                </svg>
            </button>

            <button type="button" wire:click="toggleSelection({{ $item->id }})"
                class="mt-2 flex w-full items-center justify-center gap-1.5 rounded-xl px-2.5 py-2 text-[11px] font-black transition
                    focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400"
                @class([ 'bg-slate-950  hover:bg-slate-800
                    dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200'=> $isSelected,

                'bg-slate-100 text-slate-700 hover:bg-slate-200
                dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700'
                => ! $isSelected,
                ])
                >
                @if($isSelected)

                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="m5 12 4 4L19 6" />
                </svg>

                انتخاب شد

                @else

                انتخاب تصویر

                @endif
            </button>

        </div>

        @empty

        <div class="
                    col-span-full flex min-h-72
                    flex-col items-center justify-center
                    rounded-2xl border-2 border-dashed
                    border-slate-200 bg-white p-8 text-center
                    dark:border-slate-800 dark:bg-slate-900
                ">

            <div class="
                        flex h-20 w-20 items-center justify-center
                        rounded-full bg-slate-100 text-slate-400
                        dark:bg-slate-800 dark:text-slate-500
                    ">

                <svg class="h-9 w-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect width="18" height="18" x="3" y="3" rx="2" />

                    <circle cx="9" cy="9" r="2" />

                    <path d="m21 15-5-5L5 21" />

                </svg>

            </div>

            <h3 class="
                        mt-5 font-black text-slate-800
                        dark:text-slate-100
                    ">
                رسانه‌ای پیدا نشد
            </h3>

            <p class="
                        mt-2 max-w-sm text-sm leading-6
                        text-slate-500 dark:text-slate-400
                    ">
                فایل جدیدی آپلود کنید یا عبارت جستجو را تغییر دهید.
            </p>

        </div>

        @endforelse

    </div>


    {{-- ========================================================= --}}
    {{-- Pagination --}}
    {{-- ========================================================= --}}

    @if($this->media->hasPages())

    <div class="
                mt-8 rounded-2xl border border-slate-200
                bg-white p-4 shadow-sm
                dark:border-slate-800 dark:bg-slate-900
            ">
        {{ $this->media->links() }}
    </div>

    @endif


    {{-- ========================================================= --}}
    {{-- Selection Footer --}}
    {{-- ========================================================= --}}

    @if(
    ($selectionMode === 'single' && $selected)
    ||
    ($selectionMode === 'multiple' && count($selectedMediaIds))
    )


    {{-- تایید انتخاب رسانه --}}
    <div
        class="sticky bottom-0 z-20 mt-4 flex items-center justify-between gap-3 rounded-2xl border border-slate-300 bg-white/95 p-4 shadow-lg backdrop-blur dark:border-slate-700 dark:bg-slate-800/95 bg-white">

        <div class="text-sm font-bold text-slate-600 dark:text-slate-300">

            @if($selectionMode === 'single')

            @if($selected)
            <span class="text-emerald-600 dark:text-emerald-400">
                یک تصویر انتخاب شده است
            </span>
            @else
            <span>
                هنوز تصویری انتخاب نشده است
            </span>
            @endif

            @else

            <span>
                {{ $this->selectedCount }}
                تصویر از
                {{ $maxSelection }}
                تصویر انتخاب شده است
            </span>

            @endif

        </div>


        <button type="button" wire:click="choose" wire:loading.attr="disabled" @disabled( $selectionMode==='single' ? !
            $selected : empty($selectedMediaIds) )
            class="rounded-xl bg-indigo-600 px-6 py-3 font-black  transition hover:bg-indigo-700
                focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400 focus-visible:ring-offset-2
                active:scale-[0.98]
                disabled:cursor-not-allowed disabled:opacity-40
                dark:focus-visible:ring-offset-slate-800">
            <span wire:loading.remove wire:target="choose">
                تأیید انتخاب
            </span>

            <span wire:loading wire:target="choose">
                در حال انتخاب...
            </span>
        </button>

    </div>

    @endif


    <!-- {{-- ========================================================= --}}
    {{-- Crop Modal --}}
    {{-- ========================================================= --}}

    @if($showCropModal)

    <div class="fixed inset-0 z-[99999] flex items-center justify-center bg-slate-950/70 p-2 backdrop-blur-sm sm:p-4 dark:bg-black/85">

        <div wire:key="crop-modal-{{ $cropMediaId }}" wire:ignore x-data="{
                    cropper: null,

                    init() {
                        this.$nextTick(() => {
                            this.initCropper();
                        });
                    },

                    initCropper() {

                        this.$nextTick(() => {

                            const image = document.getElementById('cropper-image');
                            const container = document.getElementById('cropper-container');

                            if (!image || !container) {
                                console.error('Cropper image or container not found');
                                return;
                            }

                            const createCropper = () => {

                                if (this.cropper) {
                                    this.cropper.destroy();
                                    this.cropper = null;
                                }

                                console.log('Image size:', image.naturalWidth, image.naturalHeight);
                                console.log('Container size:', container.clientWidth, container.clientHeight);

                                if (
                                    image.naturalWidth <= 0 ||
                                    image.naturalHeight <= 0
                                ) {
                                    console.error('Image has invalid dimensions');
                                    return;
                                }

                                if (
                                    container.clientWidth <= 0 ||
                                    container.clientHeight <= 0
                                ) {
                                    console.error('Cropper container has invalid dimensions');

                                    setTimeout(() => {
                                        this.initCropper();
                                    }, 300);

                                    return;
                                }

                                this.cropper = new Cropper(image, {

                                    aspectRatio: 16 / 9,

                                    viewMode: 1,

                                    dragMode: 'move',

                                    autoCropArea: 1,

                                    responsive: true,

                                    restore: false,

                                    background: false,

                                    modal: true,

                                    guides: true,

                                    center: true,

                                    highlight: false,

                                    movable: true,

                                    rotatable: true,

                                    scalable: true,

                                    zoomable: true,

                                    zoomOnWheel: true,

                                    zoomOnTouch: true,

                                    cropBoxMovable: true,

                                    cropBoxResizable: true,

                                    toggleDragModeOnDblclick: false,

                                    ready() {

                                        console.log('CROPPER READY');

                                    },

                                });

                            };


                            if (
                                image.complete &&
                                image.naturalWidth > 0
                            ) {

                                setTimeout(() => {

                                    createCropper();

                                }, 200);

                            } else {

                                image.onload = () => {

                                    setTimeout(() => {

                                        createCropper();

                                    }, 200);

                                };

                                image.onerror = () => {

                                    console.error(
                                        'CROP IMAGE FAILED TO LOAD:',
                                        image.src
                                    );

                                };

                            }

                        });

                    },

                    save() {
                        if (!this.cropper) {
                            console.error('Cropper is not initialized');
                            return;
                        }

                        const crop = this.cropper.getData(true);

                        if (!crop || !crop.width || !crop.height) {
                            console.error('Invalid crop data', crop);
                            return;
                        }

                        console.log('SAVING CROP', crop);

                        Livewire.dispatch('saveCrop', {
                            crop: {
                                x: Math.round(crop.x),
                                y: Math.round(crop.y),
                                width: Math.round(crop.width),
                                height: Math.round(crop.height),
                                rotate: crop.rotate ?? 0,
                                scaleX: crop.scaleX ?? 1,
                                scaleY: crop.scaleY ?? 1,
                            }
                        });
                    },

                    rotateLeft() {

                        if (!this.cropper) {
                            return;
                        }

                        this.cropper.rotate(-90);
                    },

                    rotateRight() {

                        if (!this.cropper) {
                            return;
                        }

                        this.cropper.rotate(90);
                    },

                    zoomIn() {

                        if (!this.cropper) {
                            return;
                        }

                        this.cropper.zoom(0.1);
                    },

                    zoomOut() {

                        if (!this.cropper) {
                            return;
                        }

                        this.cropper.zoom(-0.1);
                    },

                    reset() {

                        if (!this.cropper) {
                            return;
                        }

                        this.cropper.reset();
                    },

                    destroy() {

                        if (this.cropper) {

                            this.cropper.destroy();

                            this.cropper = null;
                        }
                    }
                }" x-init="init()" x-on:close-cropper.window="destroy()" class="
                    flex
                    h-[calc(100dvh-1rem)]
                    max-h-[calc(100dvh-1rem)]
                    w-full
                    max-w-6xl
                    flex-col
                    overflow-hidden
                    rounded-3xl
                    bg-white
                    shadow-2xl
                    ring-1 ring-black/5
                    dark:bg-slate-900
                    dark:ring-white/10

                    sm:h-[calc(100dvh-2rem)]
                    sm:max-h-[calc(100dvh-2rem)]
                ">

            {{-- ================================================= --}}
            {{-- Header --}}
            {{-- ================================================= --}}

            <div class="
                        flex
                        shrink-0
                        items-center
                        justify-between
                        gap-3
                        border-b
                        border-slate-200
                        bg-white
                        px-4
                        py-3
                        sm:px-6
                        sm:py-4
                        dark:border-slate-800
                        dark:bg-slate-900
                    ">

                <div class="min-w-0">

                    <h2 class="
                                text-lg
                                font-black
                                text-slate-900
                                sm:text-xl
                                dark:
                            ">
                        برش تصویر
                    </h2>

                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                        محدوده مورد نظر برای برش را جابه‌جا یا تغییر اندازه دهید
                    </p>

                    @if($processingQueue)

                    <div class="
                                    mt-2 inline-flex items-center gap-1.5
                                    rounded-full bg-indigo-50 px-2.5 py-1
                                    text-[11px] font-bold text-indigo-700
                                    dark:bg-indigo-950/40 dark:text-indigo-300
                                ">
                        تصویر
                        {{ $queueIndex + 1 }}
                        از
                        {{ count($queue) }}
                    </div>

                    @endif

                </div>

                <button
                    type="button"
                    wire:click="$set('showCropModal', false)"
                    x-on:click="destroy()"
                    class="
                        flex h-9 w-9 shrink-0 items-center justify-center
                        rounded-full text-slate-400 transition
                        hover:bg-slate-100 hover:text-slate-700
                        focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400
                        dark:text-slate-500 dark:hover:bg-slate-800 dark:hover:
                    "
                    aria-label="بستن"
                    title="بستن"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" />
                    </svg>
                </button>

            </div>


            {{-- ================================================= --}}
            {{-- Image / Crop Area --}}
            {{-- ================================================= --}}

            <div class="relative flex-1 overflow-hidden bg-slate-950 dark:bg-black">

                <div id="cropper-container" class="
                            relative
                            h-full
                            min-h-[250px]
                            w-full
                            overflow-hidden
                        ">
                    <img id="cropper-image" src="{{ $cropImage }}" alt="Crop image" class="
                                block
                                h-auto
                                max-h-full
                                w-full
                                object-contain
                            ">
                </div>

            </div>

            {{-- ================================================= --}}
            {{-- Tools --}}
            {{-- ================================================= --}}

            <div class="
                        flex
                        shrink-0
                        items-center
                        justify-center
                        gap-2
                        border-t
                        border-slate-200
                        bg-slate-50
                        px-3
                        py-3
                        dark:border-slate-800
                        dark:bg-slate-900
                    ">

                <div class="
                            flex flex-wrap items-center justify-center gap-1
                            rounded-2xl bg-white p-1.5
                            shadow-sm ring-1 ring-slate-200
                            dark:bg-slate-800 dark:ring-slate-700
                        ">

                    <button type="button" x-on:click="rotateLeft()" title="چرخش به چپ" class="
                                inline-flex items-center gap-1.5 rounded-xl px-3 py-2
                                text-xs font-bold text-slate-700 transition
                                hover:bg-slate-100
                                focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400
                                active:scale-95
                                sm:text-sm
                                dark:text-slate-200 dark:hover:bg-slate-700
                            ">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10a7 7 0 1 1 2.05 4.95M3 10v5m0-5h5" />
                        </svg>
                        <span class="hidden sm:inline">چرخش چپ</span>
                    </button>

                    <button type="button" x-on:click="rotateRight()" title="چرخش به راست" class="
                                inline-flex items-center gap-1.5 rounded-xl px-3 py-2
                                text-xs font-bold text-slate-700 transition
                                hover:bg-slate-100
                                focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400
                                active:scale-95
                                sm:text-sm
                                dark:text-slate-200 dark:hover:bg-slate-700
                            ">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 10a7 7 0 1 0-2.05 4.95M21 10v5m0-5h-5" />
                        </svg>
                        <span class="hidden sm:inline">چرخش راست</span>
                    </button>

                    <div class="mx-1 h-6 w-px bg-slate-200 dark:bg-slate-700"></div>

                    <button type="button" x-on:click="zoomOut()" aria-label="کوچک‌نمایی" title="کوچک‌نمایی" class="
                                flex h-9 w-9 items-center justify-center rounded-xl
                                text-slate-700 transition
                                hover:bg-slate-100
                                focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400
                                active:scale-95
                                dark:text-slate-200 dark:hover:bg-slate-700
                            ">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" d="M5 12h14" />
                        </svg>
                    </button>

                    <button type="button" x-on:click="zoomIn()" aria-label="بزرگ‌نمایی" title="بزرگ‌نمایی" class="
                                flex h-9 w-9 items-center justify-center rounded-xl
                                text-slate-700 transition
                                hover:bg-slate-100
                                focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400
                                active:scale-95
                                dark:text-slate-200 dark:hover:bg-slate-700
                            ">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" d="M12 5v14M5 12h14" />
                        </svg>
                    </button>

                    <div class="mx-1 h-6 w-px bg-slate-200 dark:bg-slate-700"></div>

                    <button type="button" x-on:click="reset()" title="بازنشانی" class="
                                inline-flex items-center gap-1.5 rounded-xl px-3 py-2
                                text-xs font-bold text-slate-700 transition
                                hover:bg-slate-100
                                focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400
                                active:scale-95
                                sm:text-sm
                                dark:text-slate-200 dark:hover:bg-slate-700
                            ">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h5M20 20v-5h-5M4.5 9a8 8 0 0 1 14.13-3.36M19.5 15a8 8 0 0 1-14.13 3.36" />
                        </svg>
                        <span class="hidden sm:inline">بازنشانی</span>
                    </button>

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- Footer --}}
            {{-- ================================================= --}}

            <div class="
                        flex
                        shrink-0
                        flex-col
                        gap-2
                        border-t
                        border-slate-200
                        bg-white
                        px-3
                        py-3
                        sm:flex-row
                        sm:items-center
                        sm:justify-between
                        sm:px-6
                        dark:border-slate-800
                        dark:bg-slate-900
                    ">

                <button type="button" wire:click="$set('showCropModal', false)" x-on:click="destroy()" class="
                            order-2
                            w-full
                            rounded-xl
                            border
                            border-slate-300
                            bg-white
                            px-5
                            py-2.5
                            text-sm
                            font-bold
                            text-slate-700
                            transition
                            hover:bg-slate-50
                            focus:outline-none
                            focus-visible:ring-2
                            focus-visible:ring-slate-400
                            active:scale-[0.98]
                            sm:order-1
                            sm:w-auto
                            dark:border-slate-700
                            dark:bg-slate-800
                            dark:text-slate-200
                            dark:hover:bg-slate-700
                        ">
                    لغو
                </button>


                <button type="button" x-on:click="save()" wire:loading.attr="disabled" wire:target="saveCrop" class="
                            order-1
                            flex
                            w-full
                            items-center
                            justify-center
                            gap-2
                            rounded-xl
                            bg-indigo-600
                            px-7
                            py-2.5
                            text-sm
                            font-black
                            
                            shadow-lg
                            shadow-indigo-600/20
                            transition
                            hover:bg-indigo-700
                            focus:outline-none
                            focus-visible:ring-2
                            focus-visible:ring-indigo-400
                            focus-visible:ring-offset-2
                            active:scale-[0.98]
                            disabled:cursor-not-allowed
                            disabled:opacity-50
                            sm:order-2
                            sm:w-auto
                            dark:shadow-indigo-950/40
                            dark:hover:bg-indigo-500
                            dark:focus-visible:ring-offset-slate-900
                        ">

                    <span wire:loading.remove wire:target="saveCrop" class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" />
                        </svg>
                        تأیید و ادامه
                    </span>

                    <span wire:loading wire:target="saveCrop" class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3"></circle>
                            <path class="opacity-90" fill="currentColor" d="M21 12a9 9 0 0 0-9-9V0c6.63 0 12 5.37 12 12h-3Z"></path>
                        </svg>
                        در حال پردازش...
                    </span>

                </button>

            </div>

        </div>

    </div>

    @endif -->




    {{-- ========================================================= --}}
        {{-- Crop Modal --}}
        {{-- ========================================================= --}}

        @if($showCropModal)

        <div
            class="
                fixed
                inset-0
                z-[99999]
                flex
                items-center
                justify-center
                bg-slate-950/80
                p-2
                backdrop-blur-sm
                sm:p-4
            "
        >

            <div
                wire:key="crop-modal-{{ $cropMediaId }}"
                wire:ignore
                x-data="{
                    cropper: null,

                    init() {
                        this.$nextTick(() => {
                            this.initCropper();
                        });
                    },

                    initCropper() {

                        this.$nextTick(() => {

                            const image =
                                this.$el.querySelector('#cropper-image');

                            const container =
                                this.$el.querySelector('#cropper-container');

                            if (!image || !container) {
                                console.error(
                                    'Cropper image or container not found'
                                );

                                return;
                            }

                            const createCropper = () => {

                                if (this.cropper) {
                                    this.cropper.destroy();
                                    this.cropper = null;
                                }

                                const width =
                                    container.clientWidth;

                                const height =
                                    container.clientHeight;

                                if (
                                    width <= 0 ||
                                    height <= 0
                                ) {
                                    setTimeout(() => {
                                        this.initCropper();
                                    }, 200);

                                    return;
                                }

                                if (
                                    image.naturalWidth <= 0 ||
                                    image.naturalHeight <= 0
                                ) {
                                    console.error(
                                        'Invalid image dimensions'
                                    );

                                    return;
                                }

                                this.cropper =
                                    new Cropper(
                                        image,
                                        {

                                            aspectRatio: 16 / 9,

                                            /*
                                            |--------------------------------------------------------------------------
                                            | Keep everything inside the visible container.
                                            |--------------------------------------------------------------------------
                                            */

                                            viewMode: 2,

                                            dragMode: 'move',

                                            autoCropArea: 0.9,

                                            responsive: true,

                                            restore: false,

                                            background: false,

                                            modal: true,

                                            guides: true,

                                            center: true,

                                            highlight: false,

                                            movable: true,

                                            rotatable: true,

                                            scalable: true,

                                            zoomable: true,

                                            zoomOnWheel: true,

                                            zoomOnTouch: true,

                                            cropBoxMovable: true,

                                            cropBoxResizable: true,

                                            toggleDragModeOnDblclick: false,

                                            /*
                                            |--------------------------------------------------------------------------
                                            | Do not allow the cropper to create
                                            | an unnecessarily huge initial view.
                                            |--------------------------------------------------------------------------
                                            */

                                            minContainerWidth: 100,

                                            minContainerHeight: 100,

                                            ready() {

                                                this.cropper
                                                    ?.setData({
                                                        x: undefined,
                                                        y: undefined,
                                                    });

                                            },

                                        }
                                    );
                            };

                            const load = () => {

                                setTimeout(() => {
                                    createCropper();
                                }, 100);

                            };

                            if (
                                image.complete &&
                                image.naturalWidth > 0
                            ) {

                                load();

                            } else {

                                image.onload = load;

                                image.onerror = () => {

                                    console.error(
                                        'CROP IMAGE FAILED TO LOAD:',
                                        image.src
                                    );

                                };
                            }

                        });
                    },

                    save() {

                        if (!this.cropper) {
                            console.error(
                                'Cropper is not initialized'
                            );

                            return;
                        }

                        const crop =
                            this.cropper.getData(true);

                        if (
                            !crop ||
                            !crop.width ||
                            !crop.height
                        ) {
                            console.error(
                                'Invalid crop data',
                                crop
                            );

                            return;
                        }

                        Livewire.dispatch(
                            'saveCrop',
                            {
                                crop: {
                                    x: Math.round(crop.x),
                                    y: Math.round(crop.y),
                                    width: Math.round(crop.width),
                                    height: Math.round(crop.height),

                                    rotate:
                                        crop.rotate ?? 0,

                                    scaleX:
                                        crop.scaleX ?? 1,

                                    scaleY:
                                        crop.scaleY ?? 1,
                                }
                            }
                        );
                    },

                    rotateLeft() {

                        if (!this.cropper) {
                            return;
                        }

                        this.cropper.rotate(-90);
                    },

                    rotateRight() {

                        if (!this.cropper) {
                            return;
                        }

                        this.cropper.rotate(90);
                    },

                    zoomIn() {

                        if (!this.cropper) {
                            return;
                        }

                        this.cropper.zoom(0.1);
                    },

                    zoomOut() {

                        if (!this.cropper) {
                            return;
                        }

                        this.cropper.zoom(-0.1);
                    },

                    reset() {

                        if (!this.cropper) {
                            return;
                        }

                        this.cropper.reset();
                    },

                    destroy() {

                        if (this.cropper) {

                            this.cropper.destroy();

                            this.cropper = null;
                        }
                    }
                }"

                x-init="init()"

                x-on:close-cropper.window="destroy()"

                class="
                    flex
                    h-[calc(100dvh-1rem)]
                    max-h-[calc(100dvh-1rem)]

                    w-full
                    max-w-7xl

                    flex-col
                    overflow-hidden

                    rounded-2xl
                    bg-white
                    shadow-2xl

                    ring-1
                    ring-black/10

                    dark:bg-slate-900

                    sm:h-[calc(100dvh-2rem)]
                    sm:max-h-[calc(100dvh-2rem)]
                    sm:rounded-3xl
                "
            >

                {{-- ================================================= --}}
                {{-- Header --}}
                {{-- ================================================= --}}

                <div
                    class="
                        flex
                        shrink-0
                        items-center
                        justify-between
                        gap-3

                        border-b
                        border-slate-200
                        bg-white

                        px-3
                        py-3

                        dark:border-slate-800
                        dark:bg-slate-900

                        sm:px-5
                        sm:py-4
                    "
                >

                    <div class="min-w-0">

                        <h3
                            class="
                                truncate
                                text-base
                                font-black
                                text-slate-900

                                dark:text-slate-100

                                sm:text-lg
                            "
                        >
                            برش تصویر
                        </h3>

                        <p
                            class="
                                mt-0.5
                                truncate
                                text-[11px]
                                text-slate-500

                                dark:text-slate-400

                                sm:text-xs
                            "
                        >
                            محدوده موردنظر را برای تصویر خبر انتخاب کنید.
                        </p>

                        @if($processingQueue)

                        <div
                            class="
                                mt-2
                                inline-flex
                                items-center
                                gap-1.5
                                rounded-full
                                bg-indigo-50
                                px-2.5
                                py-1
                                text-[10px]
                                font-bold
                                text-indigo-700

                                dark:bg-indigo-950/40
                                dark:text-indigo-300
                            "
                        >
                            تصویر
                            {{ $queueIndex + 1 }}
                            از
                            {{ count($queue) }}
                        </div>

                        @endif

                    </div>

                    <button
                        type="button"
                        wire:click="$set('showCropModal', false)"
                        x-on:click="destroy()"
                        class="
                            flex
                            h-9
                            w-9
                            shrink-0
                            items-center
                            justify-center

                            rounded-full

                            text-slate-400

                            transition

                            hover:bg-slate-100
                            hover:text-slate-700

                            focus:outline-none
                            focus-visible:ring-2
                            focus-visible:ring-indigo-400

                            dark:text-slate-500
                            dark:hover:bg-slate-800
                            dark:hover:text-slate-200
                        "
                        aria-label="بستن"
                    >

                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6 6l12 12M18 6L6 18"
                            />
                        </svg>

                    </button>

                </div>


                {{-- ================================================= --}}
                {{-- Crop Area --}}
                {{-- ================================================= --}}

                <div
                    class="
                        min-h-0
                        flex-1
                        overflow-hidden
                        bg-slate-950
                    "
                >

                    <div
                        id="cropper-container"
                        class="
                            relative
                            h-full
                            min-h-0
                            w-full
                            overflow-hidden
                        "
                    >

                        <img
                            id="cropper-image"
                            src="{{ $cropImage }}"
                            alt="Crop image"
                            draggable="false"
                            class="
                                block
                                max-w-none
                                select-none
                            "
                        >

                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- Tools --}}
                {{-- ================================================= --}}

                <div
                    class="
                        flex
                        shrink-0
                        flex-wrap
                        items-center
                        justify-center
                        gap-2

                        border-t
                        border-slate-200
                        bg-white

                        px-3
                        py-3

                        dark:border-slate-800
                        dark:bg-slate-900

                        sm:gap-2.5
                        sm:px-5
                        sm:py-3
                    "
                >

                    {{-- Rotate Left --}}

                    <button
                        type="button"
                        x-on:click="rotateLeft()"
                        class="
                            flex
                            h-10
                            w-10
                            items-center
                            justify-center

                            rounded-xl

                            border
                            border-slate-200
                            bg-slate-50

                            text-slate-700

                            transition

                            hover:bg-slate-100

                            focus:outline-none
                            focus-visible:ring-2
                            focus-visible:ring-indigo-400

                            dark:border-slate-700
                            dark:bg-slate-800
                            dark:text-slate-200
                            dark:hover:bg-slate-700
                        "
                        title="چرخش به چپ"
                    >

                        ↶

                    </button>


                    {{-- Rotate Right --}}

                    <button
                        type="button"
                        x-on:click="rotateRight()"
                        class="
                            flex
                            h-10
                            w-10
                            items-center
                            justify-center

                            rounded-xl

                            border
                            border-slate-200
                            bg-slate-50

                            text-slate-700

                            transition

                            hover:bg-slate-100

                            focus:outline-none
                            focus-visible:ring-2
                            focus-visible:ring-indigo-400

                            dark:border-slate-700
                            dark:bg-slate-800
                            dark:text-slate-200
                            dark:hover:bg-slate-700
                        "
                        title="چرخش به راست"
                    >

                        ↷

                    </button>


                    {{-- Zoom Out --}}

                    <button
                        type="button"
                        x-on:click="zoomOut()"
                        class="
                            flex
                            h-10
                            w-10
                            items-center
                            justify-center

                            rounded-xl

                            border
                            border-slate-200
                            bg-slate-50

                            text-lg
                            font-black
                            text-slate-700

                            transition

                            hover:bg-slate-100

                            focus:outline-none
                            focus-visible:ring-2
                            focus-visible:ring-indigo-400

                            dark:border-slate-700
                            dark:bg-slate-800
                            dark:text-slate-200
                            dark:hover:bg-slate-700
                        "
                        title="کوچک‌نمایی"
                    >

                        −

                    </button>


                    {{-- Zoom In --}}

                    <button
                        type="button"
                        x-on:click="zoomIn()"
                        class="
                            flex
                            h-10
                            w-10
                            items-center
                            justify-center

                            rounded-xl

                            border
                            border-slate-200
                            bg-slate-50

                            text-lg
                            font-black
                            text-slate-700

                            transition

                            hover:bg-slate-100

                            focus:outline-none
                            focus-visible:ring-2
                            focus-visible:ring-indigo-400

                            dark:border-slate-700
                            dark:bg-slate-800
                            dark:text-slate-200
                            dark:hover:bg-slate-700
                        "
                        title="بزرگ‌نمایی"
                    >

                        +

                    </button>


                    {{-- Reset --}}

                    <button
                        type="button"
                        x-on:click="reset()"
                        class="
                            rounded-xl
                            border
                            border-slate-200
                            bg-slate-50

                            px-4
                            py-2.5

                            text-xs
                            font-black
                            text-slate-700

                            transition

                            hover:bg-slate-100

                            focus:outline-none
                            focus-visible:ring-2
                            focus-visible:ring-indigo-400

                            dark:border-slate-700
                            dark:bg-slate-800
                            dark:text-slate-200
                            dark:hover:bg-slate-700
                        "
                    >
                        بازنشانی
                    </button>


                    {{-- Spacer --}}

                    <div class="hidden flex-1 sm:block"></div>


                    {{-- Cancel --}}

                    <button
                        type="button"
                        wire:click="$set('showCropModal', false)"
                        x-on:click="destroy()"
                        class="
                            order-2
                            w-full

                            rounded-xl

                            border
                            border-slate-300
                            bg-white

                            px-5
                            py-2.5

                            text-sm
                            font-bold
                            text-slate-700

                            transition

                            hover:bg-slate-50

                            focus:outline-none
                            focus-visible:ring-2
                            focus-visible:ring-indigo-400

                            dark:border-slate-700
                            dark:bg-slate-800
                            dark:text-slate-200
                            dark:hover:bg-slate-700

                            sm:order-none
                            sm:w-auto
                        "
                    >
                        لغو
                    </button>


                    {{-- Save --}}

                    <button
                        type="button"
                        x-on:click="save()"
                        wire:loading.attr="disabled"
                        wire:target="saveCrop"
                        class="
                            order-1
                            flex
                            w-full
                            items-center
                            justify-center
                            gap-2

                            rounded-xl

                            bg-indigo-600

                            px-6
                            py-2.5

                            text-sm
                            font-black
                            text-white

                            shadow-lg
                            shadow-indigo-600/20

                            transition

                            hover:bg-indigo-700

                            focus:outline-none
                            focus-visible:ring-2
                            focus-visible:ring-indigo-400

                            active:scale-[0.98]

                            disabled:cursor-not-allowed
                            disabled:opacity-50

                            sm:order-none
                            sm:w-auto
                        "
                    >

                        <span
                            wire:loading.remove
                            wire:target="saveCrop"
                        >
                            تأیید و ادامه
                        </span>

                        <span
                            wire:loading
                            wire:target="saveCrop"
                        >
                            در حال پردازش...
                        </span>

                    </button>

                </div>

            </div>

        </div>

        @endif

    {{-- ========================================================= --}}
    {{-- Watermark Modal --}}
    {{-- ========================================================= --}}

    @if($showWatermarkModal)

    <div class="
            fixed inset-0 z-[99999]
            flex items-center justify-center
            bg-slate-950/70 p-4 backdrop-blur-sm
            dark:bg-black/85
        ">

        <div class="
                flex
                w-full max-w-xl
                max-h-[90vh]
                flex-col
                overflow-hidden
                rounded-3xl bg-white
                shadow-2xl
                ring-1 ring-black/5
                dark:bg-slate-900
                dark:ring-white/10
            ">

            {{-- Header --}}

            <div class="
                    flex shrink-0 items-start justify-between gap-3
                    border-b border-slate-200 px-6 py-5
                    dark:border-slate-800
                ">

                <div>

                    <h2 class="
                            text-xl font-black
                            text-slate-900
                            dark:
                        ">
                        انتخاب واترمارک
                    </h2>

                    <p class="
                            mt-1.5 text-sm
                            text-slate-500
                            dark:text-slate-400
                        ">
                        مشخص کنید نسخه نهایی تصاویر با چه واترمارکی ذخیره شود.
                    </p>

                </div>

                <button
                    type="button"
                    wire:click="$set('showWatermarkModal', false)"
                    class="
                        flex h-9 w-9 shrink-0 items-center justify-center
                        rounded-full text-slate-400 transition
                        hover:bg-slate-100 hover:text-slate-700
                        focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400
                        dark:text-slate-500 dark:hover:bg-slate-800 dark:hover:
                    "
                    aria-label="بستن"
                    title="بستن"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" />
                    </svg>
                </button>

            </div>


            {{-- Body (scrollable) --}}

            <div class="flex-1 overflow-y-auto px-6 py-5">

                {{-- ================================================= --}}
                {{-- Main Options --}}
                {{-- ================================================= --}}

                <div class="space-y-3">

                    {{-- بدون واترمارک --}}

                    <button type="button" wire:click="setWatermarkType('none')" class="
                            group flex w-full items-center gap-3
                            rounded-2xl border-2 p-4
                            text-right transition-all
                            focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400
                            active:scale-[0.99]
                        " @class([ 'border-indigo-500 bg-indigo-50/70 ring-2 ring-indigo-500/10
                            dark:border-indigo-400 dark:bg-indigo-950/30 dark:ring-indigo-400/10'=> $watermarkType === 'none',

                        'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50
                        dark:border-slate-700 dark:bg-slate-900 dark:hover:border-slate-600 dark:hover:bg-slate-800/60'
                        => $watermarkType !== 'none',
                        ])
                        >

                        <span class="
                                flex h-11 w-11 shrink-0 items-center justify-center rounded-xl
                                transition-colors
                            " @class([ 'bg-indigo-100 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-300'=> $watermarkType === 'none',
                            'bg-slate-100 text-slate-400 dark:bg-slate-800 dark:text-slate-500' => $watermarkType !== 'none',
                            ])>
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="14" rx="2" />
                                <circle cx="9" cy="10" r="1.5" />
                                <path stroke-linecap="round" d="m4 15 4-3 4 3 3-2 4 3M3 3l18 18" />
                            </svg>
                        </span>

                        <span class="min-w-0 flex-1">

                            <span class="
                                    block font-black
                                    text-slate-900
                                    dark:
                                ">
                                بدون واترمارک
                            </span>

                            <span class="
                                    mt-0.5 block text-xs
                                    text-slate-500
                                    dark:text-slate-400
                                ">
                                فقط نسخه کراپ‌شده ذخیره می‌شود.
                            </span>

                        </span>

                        <span class="
                                flex h-6 w-6 shrink-0 items-center justify-center rounded-full border-2
                            " @class([ 'border-indigo-600 bg-indigo-600 dark:border-indigo-400 dark:bg-indigo-400'=> $watermarkType === 'none',
                            'border-slate-300 dark:border-slate-600' => $watermarkType !== 'none',
                            ])>
                            @if($watermarkType === 'none')
                            <svg class="h-3.5 w-3.5  dark:text-slate-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" />
                            </svg>
                            @endif
                        </span>

                    </button>


                    {{-- ================================================= --}}
                    {{-- واترمارک عمومی --}}
                    {{-- ================================================= --}}

                    @php
                    $systemWatermark =
                    collect($watermarks)
                    ->firstWhere('type', 'system');
                    @endphp

                    <button type="button" wire:click="setWatermarkType('system')" class="
                            group flex w-full items-center gap-3
                            rounded-2xl border-2 p-4
                            text-right transition-all
                            focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400
                            active:scale-[0.99]
                        " @class([ 'border-indigo-500 bg-indigo-50/70 ring-2 ring-indigo-500/10
                            dark:border-indigo-400 dark:bg-indigo-950/30 dark:ring-indigo-400/10'=> $watermarkType === 'system',

                        'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50
                        dark:border-slate-700 dark:bg-slate-900 dark:hover:border-slate-600 dark:hover:bg-slate-800/60'
                        => $watermarkType !== 'system',
                        ])
                        >

                        <span class="
                                flex h-11 w-11 shrink-0 items-center justify-center rounded-xl
                                transition-colors
                            " @class([ 'bg-indigo-100 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-300'=> $watermarkType === 'system',
                            'bg-slate-100 text-slate-400 dark:bg-slate-800 dark:text-slate-500' => $watermarkType !== 'system',
                            ])>
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3 4 6v6c0 4.5 3.2 7.7 8 9 4.8-1.3 8-4.5 8-9V6l-8-3Z" />
                            </svg>
                        </span>

                        <span class="min-w-0 flex-1">

                            <span class="
                                    block font-black
                                    text-slate-900
                                    dark:
                                ">
                                واترمارک عمومی
                            </span>

                            <span class="
                                    mt-0.5 block text-xs
                                    text-slate-500
                                    dark:text-slate-400
                                ">
                                واترمارک عمومی سامانه
                            </span>

                        </span>

                        <span class="
                                flex h-6 w-6 shrink-0 items-center justify-center rounded-full border-2
                            " @class([ 'border-indigo-600 bg-indigo-600 dark:border-indigo-400 dark:bg-indigo-400'=> $watermarkType === 'system',
                            'border-slate-300 dark:border-slate-600' => $watermarkType !== 'system',
                            ])>
                            @if($watermarkType === 'system')
                            <svg class="h-3.5 w-3.5  dark:text-slate-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" />
                            </svg>
                            @endif
                        </span>

                    </button>


                    {{-- ================================================= --}}
                    {{-- ADMIN: انتخاب خبرنگار --}}
                    {{-- ================================================= --}}

                    @if(auth()->user()?->hasRole('Admin'))

                    <button type="button" wire:click="setWatermarkType('reporter')" class="
                                flex w-full items-center gap-3
                                rounded-2xl border-2 p-4
                                text-right transition-all
                                focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400
                                active:scale-[0.99]
                            " @class([ 'border-indigo-500 bg-indigo-50/70 ring-2 ring-indigo-500/10
                            dark:border-indigo-400 dark:bg-indigo-950/30 dark:ring-indigo-400/10'=> $watermarkType === 'reporter',

                        'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50
                        dark:border-slate-700 dark:bg-slate-900 dark:hover:border-slate-600 dark:hover:bg-slate-800/60'
                        => $watermarkType !== 'reporter',
                        ])
                        >

                        <span class="
                                flex h-11 w-11 shrink-0 items-center justify-center rounded-xl
                                transition-colors
                            " @class([ 'bg-indigo-100 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-300'=> $watermarkType === 'reporter',
                            'bg-slate-100 text-slate-400 dark:bg-slate-800 dark:text-slate-500' => $watermarkType !== 'reporter',
                            ])>
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="8" cy="9" r="3" />
                                <path stroke-linecap="round" d="M2 19c0-3 2.7-5 6-5s6 2 6 5M16 8.5a2.5 2.5 0 1 0 0-5M22 19c0-2.5-2-4.2-4.5-4.8" />
                            </svg>
                        </span>

                        <span class="min-w-0 flex-1">

                            <span class="
                                        block font-black
                                        text-slate-900
                                        dark:
                                    ">
                                واترمارک خبرنگار
                            </span>

                            <span class="
                                        mt-0.5 block truncate text-xs
                                        text-slate-500
                                        dark:text-slate-400
                                    ">

                                @if($selectedReporterId)

                                @php
                                $selectedReporter = collect($reporterWatermarks)
                                ->firstWhere('user_id', $selectedReporterId);
                                @endphp

                                {{ $selectedReporter['user_name'] ?? 'خبرنگار انتخاب‌شده' }}

                                @else

                                برای انتخاب خبرنگار کلیک کنید

                                @endif

                            </span>

                        </span>

                        <svg class="h-4 w-4 shrink-0 text-slate-400 transition-transform dark:text-slate-500 @if($showReporterWatermarks) -rotate-90 @endif" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                        </svg>

                    </button>


                    {{-- Reporter List --}}

                    @if($showReporterWatermarks)

                    <div class="
                                    -mt-1 rounded-2xl border border-indigo-100 bg-indigo-50/60 p-3
                                    dark:border-indigo-900/60 dark:bg-indigo-950/20
                                ">

                        <div class="mb-2 px-1 text-xs font-black text-slate-500
                                        dark:text-slate-400">
                            انتخاب خبرنگار
                        </div>

                        <div class="max-h-56 space-y-1.5 overflow-y-auto pl-1">

                            @forelse($reporterWatermarks as $watermark)

                            <button type="button" wire:click="selectReporterWatermark({{ $watermark['id'] }})" class="
                                            flex w-full items-center gap-3 rounded-xl p-2.5
                                            text-right transition
                                            focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400
                                        "
                                @class([
                                    'bg-white ring-1 ring-indigo-300 dark:bg-slate-800 dark:ring-indigo-500/40' => $watermarkId == $watermark['id'],
                                    'bg-white/60 hover:bg-white dark:bg-slate-800/40 dark:hover:bg-slate-800' => $watermarkId != $watermark['id'],
                                ])
                            >

                                <div class="flex h-9 w-9 shrink-0 items-center justify-center
                                                    rounded-full bg-indigo-100 text-sm font-black text-indigo-700
                                                    dark:bg-indigo-950 dark:text-indigo-300">
                                    {{ mb_substr($watermark['user_name'], 0, 1) }}
                                </div>

                                <div class="min-w-0 flex-1">

                                    <div class="truncate text-sm font-black text-slate-900 dark:">
                                        {{ $watermark['user_name'] }}
                                    </div>

                                    <div class="mt-0.5 truncate text-xs text-slate-500 dark:text-slate-400">
                                        {{ $watermark['title'] }}
                                    </div>

                                </div>

                                @if($watermarkId == $watermark['id'])

                                <svg class="h-4.5 w-4.5 shrink-0 text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" />
                                </svg>

                                @endif

                            </button>

                            @empty

                            <div class="rounded-xl bg-white p-3 text-center
                                                text-sm font-bold text-slate-500
                                                dark:bg-slate-800 dark:text-slate-400">
                                هیچ واترمارک خبرنگاری ثبت نشده است.
                            </div>

                            @endforelse

                        </div>

                    </div>

                    @endif

                    @endif


                    {{-- ================================================= --}}
                    {{-- REPORTER: واترمارک اختصاصی خودم --}}
                    {{-- ================================================= --}}

                    @if(auth()->user()?->hasRole('Reporter'))

                    <button type="button" wire:click="setWatermarkType('personal')" class="
                            flex w-full items-center gap-3
                            rounded-2xl border-2 p-4
                            text-right transition-all
                            focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400
                            active:scale-[0.99]
                        " @class([ 'border-indigo-500 bg-indigo-50/70 ring-2 ring-indigo-500/10
                            dark:border-indigo-400 dark:bg-indigo-950/30 dark:ring-indigo-400/10'=> $watermarkType === 'personal',

                        'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50
                        dark:border-slate-700 dark:bg-slate-900 dark:hover:border-slate-600 dark:hover:bg-slate-800/60'
                        => $watermarkType !== 'personal',
                        ])
                        >

                        <span class="
                                flex h-11 w-11 shrink-0 items-center justify-center rounded-xl
                                transition-colors
                            " @class([ 'bg-indigo-100 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-300'=> $watermarkType === 'personal',
                            'bg-slate-100 text-slate-400 dark:bg-slate-800 dark:text-slate-500' => $watermarkType !== 'personal',
                            ])>
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="8" r="3.2" />
                                <path stroke-linecap="round" d="M5 20c0-3.3 3.1-6 7-6s7 2.7 7 6" />
                            </svg>
                        </span>

                        <span class="min-w-0 flex-1">

                            <span class="
                                    block font-black
                                    text-slate-900
                                    dark:
                                ">
                                واترمارک اختصاصی خودم
                            </span>

                            <span class="
                                    mt-0.5 block text-xs
                                    text-slate-500
                                    dark:text-slate-400
                                ">
                                واترمارک اختصاصی حساب کاربری شما
                            </span>

                        </span>

                        <span class="
                                flex h-6 w-6 shrink-0 items-center justify-center rounded-full border-2
                            " @class([ 'border-indigo-600 bg-indigo-600 dark:border-indigo-400 dark:bg-indigo-400'=> $watermarkType === 'personal',
                            'border-slate-300 dark:border-slate-600' => $watermarkType !== 'personal',
                            ])>
                            @if($watermarkType === 'personal')
                            <svg class="h-3.5 w-3.5  dark:text-slate-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" />
                            </svg>
                            @endif
                        </span>

                    </button>

                    @endif

                </div>


                {{-- Error --}}

                @error('watermarkId')

                <div class="
                            mt-4 rounded-xl
                            border border-red-200
                            bg-red-50 p-3
                            text-sm font-bold
                            text-red-700
                            dark:border-red-900/50
                            dark:bg-red-950/30
                            dark:text-red-400
                        ">
                    {{ $message }}
                </div>

                @enderror

            </div>


            {{-- ================================================= --}}
            {{-- Footer --}}
            {{-- ================================================= --}}

            <div class="
                    flex shrink-0 flex-col-reverse gap-3
                    border-t border-slate-200 bg-white px-6 py-4
                    sm:flex-row sm:justify-end
                    dark:border-slate-800 dark:bg-slate-900
                ">

                <button type="button" wire:click="$set('showWatermarkModal', false)" class="
                        w-full rounded-xl
                        border border-slate-300
                        bg-white
                        px-5 py-3
                        text-sm font-bold
                        text-slate-700
                        transition
                        hover:bg-slate-50
                        focus:outline-none
                        focus-visible:ring-2
                        focus-visible:ring-slate-400
                        active:scale-[0.98]
                        sm:w-auto
                        dark:border-slate-700
                        dark:bg-slate-800
                        dark:text-slate-200
                        dark:hover:bg-slate-700
                    ">
                    انصراف
                </button>

                <button type="button" wire:click="applyWatermark" wire:loading.attr="disabled" wire:target="applyWatermark" class="
                        flex w-full items-center justify-center gap-2
                        rounded-xl
                        bg-indigo-600
                        px-6 py-3
                        text-sm font-black
                        
                        shadow-lg
                        shadow-indigo-600/20
                        transition
                        hover:bg-indigo-700
                        focus:outline-none
                        focus-visible:ring-2
                        focus-visible:ring-indigo-400
                        focus-visible:ring-offset-2
                        active:scale-[0.98]
                        disabled:cursor-not-allowed
                        disabled:opacity-50
                        sm:w-auto
                        dark:shadow-indigo-950/40
                        dark:hover:bg-indigo-500
                        dark:focus-visible:ring-offset-slate-900
                    ">

                    <span wire:loading.remove wire:target="applyWatermark" class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" />
                        </svg>
                        اعمال و ذخیره
                    </span>

                    <span wire:loading wire:target="applyWatermark" class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3"></circle>
                            <path class="opacity-90" fill="currentColor" d="M21 12a9 9 0 0 0-9-9V0c6.63 0 12 5.37 12 12h-3Z"></path>
                        </svg>
                        در حال پردازش...
                    </span>

                </button>

            </div>

        </div>

    </div>

    @endif

</div>