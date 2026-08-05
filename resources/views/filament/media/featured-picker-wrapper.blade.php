<div
    x-data
    x-on:featured-image-selected.window="
        $wire.set(
            'data.featured_media_id',
            $event.detail.id
        )
    "
>

    <livewire:media.featured-picker />

</div>