<div
    x-data="{
        count: 0,
        words: 0,
        update(value) {
            this.count = Array.from(value || '').length;
            this.words = (value || '').trim().split(/\s+/u).filter(Boolean).length;
        }
    }"
    x-init="$nextTick(() => update(document.getElementById(@js($id))?.value || ''))"
    x-on:input.window="if ($event.target.id === @js($id)) update($event.target.value)"
    class="text-sm text-gray-600 dark:text-gray-400"
>
    <span x-text="count + ' / {{ $maximum }} کاراکتر'">0 / {{ $maximum }} کاراکتر</span>
    <span aria-hidden="true"> · </span>
    <span x-text="words + ' کلمه'">0 کلمه</span>
    <span class="block">{{ $guidance }}</span>
    <span x-show="count > {{ $recommended }}" x-cloak class="block font-medium">
        طول متن از پیشنهاد تحریریه بیشتر شده؛ در صورت امکان کوتاه‌ترش کنید.
    </span>
</div>
