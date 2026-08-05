<div
    x-data="imageEditor"
    x-show="open"
    x-cloak

    class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/70"
>

    <div
        class="absolute inset-0 bg-black/80"
        @click="close()"
    ></div>

    <div
        class="absolute inset-5 rounded-2xl bg-white overflow-hidden shadow-2xl flex flex-col"
    >

        {{-- Header --}}
        <div class="h-16 border-b flex items-center justify-between px-6">

            <h2 class="font-bold text-lg">

                ویرایش تصویر

            </h2>

            <button

                @click="close()"

                class="text-red-500"

            >

                ✕

            </button>

        </div>

        {{-- Toolbar --}}
        <div

            class="border-b p-3 flex flex-wrap gap-2"

        >

            <button @click="rotate(-90)" class="fi-btn">
                ↺
            </button>

            <button @click="rotate(90)" class="fi-btn">
                ↻
            </button>

            <button @click="zoom(0.1)" class="fi-btn">
                +
            </button>

            <button @click="zoom(-0.1)" class="fi-btn">
                -
            </button>

            <button @click="flipX()" class="fi-btn">
                ↔
            </button>

            <button @click="flipY()" class="fi-btn">
                ↕
            </button>

            <button @click="reset()" class="fi-btn">

                Reset

            </button>

        </div>

        {{-- Canvas --}}
        <div

            class="flex-1 bg-gray-900 flex items-center justify-center overflow-hidden"

        >

            <img

                id="editor-image"

                class="max-h-full"

            >

        </div>

        {{-- Footer --}}
        <div

            class="border-t h-16 flex items-center justify-end gap-3 px-5"

        >

            <button

                @click="close()"

                class="fi-btn"

            >

                انصراف

            </button>

            <button

                @click="save()"

                class="fi-btn fi-btn-color-primary"

            >

                ذخیره

            </button>

        </div>

    </div>

</div>