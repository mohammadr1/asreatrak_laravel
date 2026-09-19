<div
    dir="rtl"
        class="
        mt-4 overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm ring-1 ring-slate-900/[0.03]
        dark:border-slate-700
        dark:bg-slate-900
    "
>

    {{-- Header --}}

    <div
        class="
            flex
            items-center
            justify-between
            border-b
            border-slate-200
                px-4 py-4
            dark:border-slate-700
        "
    >

        <div>

            <div
                class="
                    text-sm
                    font-black
                    text-slate-800
                    dark:text-white
                "
            >
                تصاویر گزارش تصویری
            </div>

            <div
                class="
                    mt-1
                    text-xs
                    text-slate-500
                    dark:text-slate-400
                "
            >
                {{ count($items) }} تصویر انتخاب شده
            </div>

        </div>

    </div>


    {{-- Images --}}

            <div
                class="
                    grid grid-cols-2 gap-3 bg-slate-50/70 p-4 sm:grid-cols-3
                    lg:grid-cols-4 dark:bg-slate-950/30
        "
    >

        @foreach($items as $index => $item)

            @php

                $url =
                    $item['url'] ?? null;

                $variant =
                    $item['variant'] ?? null;

                $mediaId =
                    $item['id'] ?? null;

            @endphp


            @if($url)

                <div
                        class="
                        group relative overflow-hidden rounded-2xl
                        border
                        border-slate-200
                        bg-slate-100 shadow-sm transition duration-300 hover:-translate-y-0.5 hover:shadow-lg
                        dark:border-slate-700
                        dark:bg-slate-800
                    "
                >

                    <img
                        src="{{ $url }}"
                        alt="تصویر گزارش تصویری {{ $index + 1 }}"
                        loading="lazy"
                            class="
                            aspect-[4/3]
                            w-full
                            object-cover
                            transition duration-500 group-hover:scale-105
                        "
                    >

                    {{-- Number --}}

                    <div
                        class="
                            absolute
                            right-2
                            top-2
                            flex
                            h-7
                            min-w-7
                            items-center
                            justify-center
                            rounded-full
                            bg-black/70
                            px-2
                            text-xs
                            font-black
                            text-white
                            backdrop-blur
                        "
                    >
                        {{ $index + 1 }}
                    </div>

                    {{-- Variant --}}

                    @if($variant)

                        <div
                            class="
                                absolute
                                bottom-0
                                left-0
                                right-0
                                bg-black/65
                                px-2
                                py-2
                                text-center
                                text-[10px]
                                font-bold
                                text-white
                                backdrop-blur
                            "
                        >
                            @switch($variant)

                                @case('watermarked')
                                    واترمارک‌دار
                                    @break

                                @case('cropped')
                                    کراپ‌شده
                                    @break

                                @case('original')
                                    اصلی
                                    @break

                                @default
                                    {{ $variant }}

                            @endswitch
                        </div>

                    @endif

                </div>

            @endif

        @endforeach

    </div>

</div>
