<div x-data x-on:media-selected.window="
    if ($event.detail.context !== 'editor') return;
    window.dispatchEvent(new CustomEvent('news-editor-image', { detail: $event.detail }));
    $nextTick(() => $wire.unmountFormComponentAction());
">
    <livewire:media.featured-picker context="editor" />
</div>
