<div dir="rtl" class="w-full">

    {{-- ========================================================= --}}
    {{-- Header --}}
    {{-- ========================================================= --}}

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white">
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


    {{-- ========================================================= --}}
    {{-- Media Variant --}}
    {{-- ========================================================= --}}

    <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-2 shadow-sm
               dark:border-slate-800 dark:bg-slate-900">

        <div class="grid grid-cols-3 gap-2">

            {{-- Watermarked --}}

            <button type="button" wire:click="setMediaVariant('watermarked')" class="
                    rounded-xl px-4 py-3 text-sm font-bold transition
                " @class([ 'bg-indigo-600 text-white shadow-md'=> $mediaVariant === 'watermarked',

                'bg-slate-100 text-slate-600 hover:bg-slate-200
                dark:bg-slate-800 dark:text-slate-300'
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
                " @class([ 'bg-indigo-600 text-white shadow-md'=> $mediaVariant === 'cropped',

                'bg-slate-100 text-slate-600 hover:bg-slate-200
                dark:bg-slate-800 dark:text-slate-300'
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

            <button type="button" wire:click="setMediaVariant('original')" class="
                    rounded-xl px-4 py-3 text-sm font-bold transition
                " @class([ 'bg-indigo-600 text-white shadow-md'=> $mediaVariant === 'original',

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
            </button>

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
                    px-4 py-3 text-sm outline-none transition
                    focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20
                    dark:border-slate-700 dark:bg-slate-950
                    dark:text-white
                ">

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- Upload --}}
    {{-- ========================================================= --}}

    <div class="
            mb-8 rounded-2xl border-2 border-dashed
            border-slate-300 bg-slate-50 p-6
            transition hover:border-indigo-400
            dark:border-slate-700 dark:bg-slate-950
        ">

        <label for="media-upload" class="flex cursor-pointer flex-col items-center justify-center text-center">

            <div class="
                    mb-4 flex h-16 w-16 items-center justify-center
                    rounded-2xl bg-indigo-100 text-indigo-600
                    dark:bg-indigo-950 dark:text-indigo-400
                ">

                <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 16V4" />
                    <path d="m7 9 5-5 5 5" />
                    <path d="M20 15v4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-4" />
                </svg>

            </div>

            <div class="font-black text-slate-800 dark:text-white">
                آپلود تصویر جدید
            </div>

            <div class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                تصویر را انتخاب کنید؛ سپس مرحله کراپ و واترمارک انجام می‌شود.
            </div>

            <input id="media-upload" type="file" wire:model="uploads" multiple accept="image/*" class="hidden">

        </label>


        {{-- Upload loading --}}

        <div wire:loading wire:target="uploads" class="mt-4 text-center text-sm font-bold text-indigo-600">
            در حال بارگذاری تصویر...
        </div>


        @error('uploads.*')

        <div class="mt-4 rounded-xl bg-red-50 p-3 text-sm font-bold text-red-700">
            {{ $message }}
        </div>

        @enderror

    </div>


    {{-- ========================================================= --}}
    {{-- Media Grid --}}
    {{-- ========================================================= --}}

    <div wire:loading.remove wire:target="search"
        class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">

        @forelse($this->media as $item)

        @php

        $isSelected = $this->isSelected($item->id);

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


        <div wire:key="media-card-{{ $item->id }}" class="
                    group relative overflow-hidden rounded-2xl
                    border bg-white p-2 shadow-sm transition
                    hover:-translate-y-1 hover:shadow-xl
                    dark:bg-slate-900
                " @class([ 'border-indigo-500 ring-4 ring-indigo-500/10'=> $isSelected,

            'border-slate-200 dark:border-slate-800'
            => ! $isSelected,
            ])
            >

            {{-- ================================================= --}}
            {{-- Image --}}
            {{-- ================================================= --}}

            <button type="button" wire:click="toggleSelection({{ $item->id }})" class="block w-full text-right">

                <div class="
                            relative aspect-video overflow-hidden
                            rounded-xl bg-slate-100
                            dark:bg-slate-800
                        ">

                    <img src="{{ $preview }}" alt="{{ $title }}" loading="lazy" class="
                                h-full w-full object-cover
                                transition duration-500
                                group-hover:scale-105
                            ">


                    {{-- Selection overlay --}}

                    @if($isSelected)

                    <div class="
                                    absolute inset-0 flex items-center
                                    justify-center bg-indigo-600/25
                                ">

                        <div class="
                                        flex h-12 w-12 items-center
                                        justify-center rounded-full
                                        bg-indigo-600 text-white
                                        shadow-xl
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
                                text-[10px] font-bold text-white
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
                                text-[10px] font-bold text-white
                            ">
                        {{ $extension }}
                    </div>

                </div>


                {{-- Title --}}

                <div class="mt-3 px-1">

                    <div class="
                                truncate text-sm font-bold
                                text-slate-800
                                dark:text-slate-100
                            " title="{{ $title }}">
                        {{ $title }}
                    </div>

                    <div class="
                                mt-1 text-xs text-slate-400
                            ">
                        {{ $item->width }} × {{ $item->height }}
                    </div>

                </div>

            </button>


            {{-- ================================================= --}}
            {{-- Select Button --}}
            {{-- ================================================= --}}

            <button type="button" wire:click="toggleSelection({{ $item->id }})" class="
                        mt-3 flex w-full items-center
                        justify-center gap-2 rounded-xl
                        px-3 py-2.5 text-xs font-black
                        transition
                    " @class([ 'bg-indigo-600 text-white hover:bg-indigo-700'=> $isSelected,

                'bg-slate-100 text-slate-700 hover:bg-indigo-50
                hover:text-indigo-700
                dark:bg-slate-800 dark:text-slate-200'
                => ! $isSelected,
                ])
                >

                @if($isSelected)

                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
                        dark:bg-slate-800
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
        class="sticky bottom-0 z-20 mt-4 flex items-center justify-between gap-3 rounded-2xl border border-slate-300 bg-slate-100 p-4 shadow-lg dark:border-slate-700 dark:bg-slate-800 bg-gray-100">

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
            class="rounded-xl bg-indigo-600 px-6 py-3 font-black text-black transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-40">
            <span wire:loading.remove wire:target="choose">
                تأیید انتخاب
            </span>

            <span wire:loading wire:target="choose">
                در حال انتخاب...
            </span>
        </button>

    </div>

    @endif


    {{-- ========================================================= --}}
    {{-- Crop Modal --}}
    {{-- ========================================================= --}}

    @if($showCropModal)

    <div class="
                fixed inset-0 z-[99999]
                flex items-center justify-center
                bg-black/80 p-4
            ">

        <div wire:key="crop-modal-{{ $cropMediaId }}" wire:ignore x-data="{
                    cropper: null,

                    init() {

                        this.$nextTick(() => {

                            const image =
                                document.getElementById(
                                    'cropper-image'
                                );

                            if (!image) {
                                return;
                            }

                            const createCropper = () => {

                                if (this.cropper) {

                                    this.cropper.destroy();

                                    this.cropper = null;
                                }

                                this.cropper =
                                    new Cropper(
                                        image,
                                        {
                                            aspectRatio: 16 / 9,
                                            viewMode: 1,
                                            dragMode: 'move',
                                            autoCropArea: 1,
                                            responsive: true,
                                            background: false,
                                            movable: true,
                                            zoomable: true,
                                            cropBoxMovable: true,
                                            cropBoxResizable: true,
                                        }
                                    );

                            };

                            if (image.complete) {

                                createCropper();

                            } else {

                                image.onload =
                                    createCropper;
                            }

                        });

                    },

                    save() {

                        if (!this.cropper) {
                            return;
                        }

                        const data =
                            this.cropper.getData();

                        Livewire.dispatch(
                            'saveCrop',
                            {
                                crop: data
                            }
                        );

                    },

                    rotateLeft() {

                        if (this.cropper) {
                            this.cropper.rotate(-90);
                        }

                    },

                    rotateRight() {

                        if (this.cropper) {
                            this.cropper.rotate(90);
                        }

                    },

                    zoomIn() {

                        if (this.cropper) {
                            this.cropper.zoom(0.1);
                        }

                    },

                    zoomOut() {

                        if (this.cropper) {
                            this.cropper.zoom(-0.1);
                        }

                    },

                    reset() {

                        if (this.cropper) {
                            this.cropper.reset();
                        }

                    },

                    destroy() {

                        if (this.cropper) {

                            this.cropper.destroy();

                            this.cropper = null;
                        }

                    }
                }" x-init="init()" x-on:close-cropper.window="destroy()" class="
                    w-full max-w-6xl
                    overflow-hidden rounded-3xl
                    bg-white shadow-2xl
                    dark:bg-slate-900
                ">

            {{-- Header --}}

            <div class="
                        flex items-center justify-between
                        border-b border-slate-200
                        px-6 py-4
                        dark:border-slate-800
                    ">

                <div>

                    <h2 class="
                                text-xl font-black
                                text-slate-900
                                dark:text-white
                            ">
                        برش تصویر
                    </h2>

                    @if($processingQueue)

                    <div class="
                                    mt-1 text-xs font-bold
                                    text-slate-500
                                ">
                        تصویر
                        {{ $queueIndex + 1 }}
                        از
                        {{ count($queue) }}
                    </div>

                    @endif

                </div>

            </div>


            {{-- Image --}}

            <div class="
                        flex max-h-[70vh]
                        min-h-[400px]
                        items-center justify-center
                        overflow-hidden bg-black
                    ">

                <img id="cropper-image" src="{{ $cropImage }}" alt="Crop image" class="
                            block max-h-[70vh]
                            max-w-full
                        ">

            </div>


            {{-- Tools --}}

            <div class="
                        flex flex-wrap items-center
                        justify-center gap-2
                        border-t border-slate-200
                        px-6 py-4
                        dark:border-slate-800
                    ">

                <button type="button" x-on:click="rotateRight()"
                    class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-bold dark:bg-slate-800">
                    چرخش راست
                </button>

                <button type="button" x-on:click="rotateLeft()"
                    class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-bold dark:bg-slate-800">
                    چرخش چپ
                </button>

                <button type="button" x-on:click="zoomIn()"
                    class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-bold dark:bg-slate-800">
                    +
                </button>

                <button type="button" x-on:click="zoomOut()"
                    class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-bold dark:bg-slate-800">
                    −
                </button>

                <button type="button" x-on:click="reset()"
                    class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-bold dark:bg-slate-800">
                    بازنشانی
                </button>

            </div>


            {{-- Footer --}}

            <div class="
                        flex items-center justify-between
                        border-t border-slate-200
                        px-6 py-4
                        dark:border-slate-800
                    ">

                <button type="button" wire:click="$set('showCropModal', false)" class="
                            rounded-xl bg-slate-100
                            px-5 py-2.5 font-bold
                            dark:bg-slate-800
                        ">
                    لغو
                </button>


                <button type="button" x-on:click="save()" class="
                            rounded-xl bg-indigo-600
                            px-7 py-2.5 font-black
                            text-white shadow-lg
                            hover:bg-indigo-700
                        ">
                    تایید و ادامه
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
            bg-black/80 p-4
        ">

        <div class="
                w-full max-w-xl
                rounded-3xl bg-white
                p-6 shadow-2xl
                dark:bg-slate-900
            ">

            {{-- Header --}}

            <div class="mb-6">

                <h2 class="
                        text-xl font-black
                        text-slate-900
                        dark:text-white
                    ">
                    انتخاب واترمارک
                </h2>

                <p class="
                        mt-2 text-sm
                        text-slate-500
                        dark:text-slate-400
                    ">
                    مشخص کنید نسخه نهایی تصاویر با چه واترمارکی ذخیره شود.
                </p>

            </div>


            {{-- ================================================= --}}
            {{-- Main Options --}}
            {{-- ================================================= --}}

            <div class="space-y-3">

                {{-- بدون واترمارک --}}

                <button type="button" wire:click="setWatermarkType('none')" class="
                        flex w-full items-center gap-3
                        rounded-2xl border p-4
                        text-right transition
                        hover:border-indigo-400
                    " @class([ 'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-500/10'=> $watermarkType === 'none',

                    'border-slate-200 dark:border-slate-700'
                    => $watermarkType !== 'none',
                    ])
                    >

                    <div class="
                            flex h-5 w-5 shrink-0
                            items-center justify-center
                            rounded-full border-2
                        " @class([ 'border-indigo-600'=> $watermarkType === 'none',

                        'border-slate-300 dark:border-slate-600'
                        => $watermarkType !== 'none',
                        ])
                        >

                        @if($watermarkType === 'none')

                        <div class="
                                    h-2.5 w-2.5
                                    rounded-full
                                    bg-indigo-600
                                "></div>

                        @endif

                    </div>


                    <div>

                        <div class="
                                font-black
                                text-slate-900
                                dark:text-white
                            ">
                            بدون واترمارک
                        </div>

                        <div class="
                                mt-1 text-xs
                                text-slate-500
                                dark:text-slate-400
                            ">
                            فقط نسخه کراپ‌شده ذخیره می‌شود.
                        </div>

                    </div>

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
                        flex w-full items-center gap-3
                        rounded-2xl border p-4
                        text-right transition
                        hover:border-indigo-400
                    " @class([ 'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-500/10'=> $watermarkType ===
                    'system',

                    'border-slate-200 dark:border-slate-700'
                    => $watermarkType !== 'system',
                    ])
                    >

                    <div class="
                            flex h-5 w-5 shrink-0
                            items-center justify-center
                            rounded-full border-2
                        " @class([ 'border-indigo-600'=> $watermarkType === 'system',

                        'border-slate-300 dark:border-slate-600'
                        => $watermarkType !== 'system',
                        ])
                        >

                        @if($watermarkType === 'system')

                        <div class="
                                    h-2.5 w-2.5
                                    rounded-full bg-indigo-600
                                "></div>

                        @endif

                    </div>


                    <div>

                        <div class="
                                font-black
                                text-slate-900
                                dark:text-white
                            ">
                            واترمارک عمومی
                        </div>

                        <div class="
                                mt-1 text-xs
                                text-slate-500
                                dark:text-slate-400
                            ">
                            واترمارک عمومی سامانه
                        </div>

                    </div>

                </button>


                {{-- ================================================= --}}
                {{-- ADMIN: انتخاب خبرنگار --}}
                {{-- ================================================= --}}

                @if(auth()->user()?->hasRole('Admin'))

                    <button
                        type="button"
                        wire:click="setWatermarkType('reporter')"
                        class="
                            flex w-full items-center gap-3
                            rounded-2xl border p-4
                            text-right transition
                            hover:border-indigo-400
                        "
                        @class([
                            'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-500/10'
                                => $watermarkType === 'reporter',

                            'border-slate-200 dark:border-slate-700'
                                => $watermarkType !== 'reporter',
                        ])
                    >

                        <div
                            class="
                                flex h-5 w-5 shrink-0
                                items-center justify-center
                                rounded-full border-2
                            "
                            @class([
                                'border-indigo-600'
                                    => $watermarkType === 'reporter',

                                'border-slate-300 dark:border-slate-600'
                                    => $watermarkType !== 'reporter',
                            ])
                        >

                            @if($watermarkType === 'reporter')

                                <div
                                    class="
                                        h-2.5 w-2.5
                                        rounded-full
                                        bg-indigo-600
                                    "
                                ></div>

                            @endif

                        </div>

                        <div class="flex-1">

                            <div
                                class="
                                    font-black
                                    text-slate-900
                                    dark:text-white
                                "
                            >
                                انتخاب واترمارک خبرنگار
                            </div>

                            <div
                                class="
                                    mt-1 text-xs
                                    text-slate-500
                                    dark:text-slate-400
                                "
                            >

                                @if($selectedReporterId)

                                    @php
                                        $selectedReporter = collect($reporterWatermarks)
                                            ->firstWhere('user_id', $selectedReporterId);
                                    @endphp

                                    {{ $selectedReporter['user_name'] ?? 'خبرنگار انتخاب‌شده' }}

                                @else

                                    برای انتخاب خبرنگار کلیک کنید

                                @endif

                            </div>

                        </div>

                        <svg
                            class="h-5 w-5 text-slate-400"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="m9 18 6-6-6-6" />
                        </svg>

                    </button>


                    {{-- Reporter List --}}

                    @if($showReporterWatermarks)

                        <div
                            class="
                                mt-3 rounded-2xl
                                border border-indigo-200
                                bg-indigo-50/50 p-3
                                dark:border-indigo-900
                                dark:bg-indigo-950/20
                            "
                        >

                            <div
                                class="
                                    mb-3 px-2 text-xs
                                    font-black text-slate-500
                                    dark:text-slate-400
                                "
                            >
                                انتخاب خبرنگار
                            </div>

                            <div class="max-h-64 space-y-2 overflow-y-auto">

                                @forelse($reporterWatermarks as $watermark)

                                    <button
                                        type="button"
                                        wire:click="selectReporterWatermark({{ $watermark['id'] }})"
                                        class="
                                            flex w-full items-center
                                            gap-3 rounded-xl
                                            bg-white p-3 text-right
                                            transition hover:bg-indigo-100
                                            dark:bg-slate-800
                                            dark:hover:bg-slate-700
                                        "
                                    >

                                        <div
                                            class="
                                                flex h-10 w-10
                                                shrink-0 items-center
                                                justify-center rounded-full
                                                bg-indigo-100
                                                font-black text-indigo-700
                                                dark:bg-indigo-950
                                                dark:text-indigo-300
                                            "
                                        >
                                            {{ mb_substr($watermark['user_name'], 0, 1) }}
                                        </div>

                                        <div class="flex-1">

                                            <div
                                                class="
                                                    font-black
                                                    text-slate-900
                                                    dark:text-white
                                                "
                                            >
                                                {{ $watermark['user_name'] }}
                                            </div>

                                            <div
                                                class="
                                                    mt-1 text-xs
                                                    text-slate-500
                                                    dark:text-slate-400
                                                "
                                            >
                                                {{ $watermark['title'] }}
                                            </div>

                                        </div>

                                        @if($watermarkId == $watermark['id'])

                                            <svg
                                                class="h-5 w-5 text-emerald-600"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="3"
                                            >
                                                <path d="m5 12 4 4L19 6" />
                                            </svg>

                                        @endif

                                    </button>

                                @empty

                                    <div
                                        class="
                                            rounded-xl
                                            bg-white p-4 text-center
                                            text-sm font-bold
                                            text-slate-500
                                            dark:bg-slate-800
                                            dark:text-slate-400
                                        "
                                    >
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

                <button
                    type="button"
                    wire:click="setWatermarkType('personal')"
                    class="
                        flex w-full items-center gap-3
                        rounded-2xl border p-4
                        text-right transition
                        hover:border-indigo-400
                    "
                    @class([
                        'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-500/10'
                            => $watermarkType === 'personal',

                        'border-slate-200 dark:border-slate-700'
                            => $watermarkType !== 'personal',
                    ])
                >

                    <div
                        class="
                            flex h-5 w-5 shrink-0
                            items-center justify-center
                            rounded-full border-2
                        "
                        @class([
                            'border-indigo-600'
                                => $watermarkType === 'personal',

                            'border-slate-300 dark:border-slate-600'
                                => $watermarkType !== 'personal',
                        ])
                    >

                        @if($watermarkType === 'personal')

                            <div
                                class="
                                    h-2.5 w-2.5
                                    rounded-full bg-indigo-600
                                "
                            ></div>

                        @endif

                    </div>

                    <div>

                        <div
                            class="
                                font-black
                                text-slate-900
                                dark:text-white
                            "
                        >
                            واترمارک اختصاصی خودم
                        </div>

                        <div
                            class="
                                mt-1 text-xs
                                text-slate-500
                                dark:text-slate-400
                            "
                        >
                            واترمارک اختصاصی حساب کاربری شما
                        </div>

                    </div>

                </button>

            @endif

            </div>


            {{-- Error --}}

            @error('watermarkId')

            <div class="
                        mt-4 rounded-xl
                        bg-red-50 p-3
                        text-sm font-bold
                        text-red-700
                    ">
                {{ $message }}
            </div>

            @enderror


            {{-- ================================================= --}}
            {{-- Footer --}}
            {{-- ================================================= --}}

            <div class="mt-8 flex justify-end gap-3">

                <button type="button" wire:click="$set('showWatermarkModal', false)" class="
                        rounded-xl
                        bg-slate-100
                        px-5 py-2.5
                        font-bold
                        text-slate-900
                        dark:bg-slate-800
                        dark:text-white
                    ">
                    انصراف
                </button>


                <button type="button" wire:click="applyWatermark" wire:loading.attr="disabled" class="
                        rounded-xl
                        bg-indigo-600
                        px-6 py-2.5
                        font-black
                        text-white
                        shadow-lg
                        hover:bg-indigo-700
                        disabled:opacity-50
                    ">

                    <span wire:loading.remove wire:target="applyWatermark">
                        اعمال و ذخیره
                    </span>

                    <span wire:loading wire:target="applyWatermark">
                        در حال پردازش...
                    </span>

                </button>

            </div>

        </div>

    </div>

    @endif

</div>
