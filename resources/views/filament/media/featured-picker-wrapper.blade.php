
<div
    x-data
    x-on:featured-image-selected.window="
        $wire.set('data.featured_media_id', $event.detail.id);
        $wire.set('data.featured_media_variant', $event.detail.variant);
    "
>
    @livewire('media.featured-picker', [
        'context' => 'featured',
    ])
</div>