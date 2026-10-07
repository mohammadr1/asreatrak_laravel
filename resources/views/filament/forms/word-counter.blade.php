@php
    $id = $getId();
    $maxWords = $maxWords ?? 15;
@endphp

<div
    x-data="{
        count: 0,
        max: {{ $maxWords }},

        updateCount() {
            const input = document.getElementById('{{ $id }}');

            if (!input) {
                this.count = 0;
                return;
            }

            const text = input.value
                .replace(/<[^>]*>/g, ' ')
                .trim();

            if (!text) {
                this.count = 0;
                return;
            }

            this.count = text
                .split(/\s+/u)
                .filter(word => word.length > 0)
                .length;
        },

        get color() {
            if (this.count >= this.max) {
                return '#dc2626';
            }

            if (this.count >= this.max * 0.8) {
                return '#d97706';
            }

            return '#16a34a';
        }
    }"
    x-init="
        updateCount();

        const input = document.getElementById('{{ $id }}');

        if (input) {
            input.addEventListener('input', () => updateCount());
        }
    "
    wire:ignore
    class="mt-1 text-sm font-medium"
>
    <span
        x-text="count + ' / ' + max + ' کلمه'"
        :style="'color:' + color"
    ></span>
</div>