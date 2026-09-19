<div
    x-data='{
        isSyncing: false,

        async syncGallery(event, wire, element) {
            const detail = event.detail ?? {};

            if (detail.context !== "gallery" || this.isSyncing) {
                return;
            }

            const items = Array.isArray(detail.items)
                ? detail.items
                : [];

            if (items.length === 0) {
                return;
            }

            const modal = element.closest("[data-fi-modal-id]");
            const modalId = modal?.getAttribute("data-fi-modal-id");

            this.isSyncing = true;

            try {
                await wire.set("data.gallery_media", items);

                if (modalId) {
                    window.dispatchEvent(
                        new CustomEvent("close-modal", {
                            detail: { id: modalId },
                        }),
                    );

                    return;
                }

                modal
                    ?.querySelector(".fi-modal-close-btn")
                    ?.click();
            } finally {
                this.isSyncing = false;
            }
        },
    }'
    x-on:media-multiple-selected.window="syncGallery($event, $wire, $el)"
    class="w-full"
>
    <livewire:media.featured-picker context="gallery" />
</div>
