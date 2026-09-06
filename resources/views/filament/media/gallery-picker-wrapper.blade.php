<div
    x-data="{
        syncGallery(event) {

            console.log('GALLERY EVENT RECEIVED:', event);

            const detail = event.detail || {};

            console.log('GALLERY EVENT DETAIL:', detail);

            if (detail.context !== 'gallery') {
                console.log(
                    'IGNORED EVENT - CONTEXT:',
                    detail.context
                );

                return;
            }

            const items = Array.isArray(detail.items)
                ? detail.items
                : [];

            console.log('GALLERY ITEMS:', items);

            if (!items.length) {

                console.warn(
                    'GALLERY EVENT HAS NO ITEMS'
                );

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Find Filament hidden input
            |--------------------------------------------------------------------------
            */

            let input =
                document.querySelector(
                    '#data\\.gallery_media'
                );

            /*
            |--------------------------------------------------------------------------
            | Fallback
            |--------------------------------------------------------------------------
            */

            if (!input) {

                input =
                    document.querySelector(
                        'input[name=\"data[gallery_media]\"]'
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | Input not found
            |--------------------------------------------------------------------------
            */

            if (!input) {

                console.error(
                    'GALLERY MEDIA INPUT NOT FOUND'
                );

                console.log(
                    'AVAILABLE INPUTS:',
                    document.querySelectorAll('input')
                );

                return;
            }

            console.log(
                'GALLERY MEDIA INPUT FOUND:',
                input
            );

            /*
            |--------------------------------------------------------------------------
            | Save selected images
            |--------------------------------------------------------------------------
            */

            const value =
                JSON.stringify(items);

            input.value = value;

            /*
            |--------------------------------------------------------------------------
            | Notify Filament
            |--------------------------------------------------------------------------
            */

            input.dispatchEvent(
                new Event('input', {
                    bubbles: true,
                })
            );

            input.dispatchEvent(
                new Event('change', {
                    bubbles: true,
                })
            );

            input.dispatchEvent(
                new Event('blur', {
                    bubbles: true,
                })
            );

            console.log(
                'GALLERY MEDIA SYNCED:',
                items
            );

            /*
            |--------------------------------------------------------------------------
            | Close modal
            |--------------------------------------------------------------------------
            */

            setTimeout(() => {

                const closeButton =
                    document.querySelector(
                        '[x-on\\:click*=\"close\"]'
                    );

                if (closeButton) {
                    closeButton.click();
                }

            }, 300);
        }
    }"

    x-on:media-multiple-selected.window="syncGallery($event)"

    class="w-full"
>

    <livewire:media.featured-picker
        context="gallery"
    />

</div>