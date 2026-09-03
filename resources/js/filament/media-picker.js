console.log('MEDIA PICKER JS LOADED');

document.addEventListener('livewire:init', () => {

    console.log('LIVEWIRE MEDIA PICKER READY');

    Livewire.on('featured-image-selected', (event) => {

        console.log('FEATURED IMAGE EVENT:', event);

        /*
        |--------------------------------------------------------------------------
        | Selected media data
        |--------------------------------------------------------------------------
        */

        const id = event?.id;
        const variant = event?.variant;
        const url = event?.url;

        if (!id) {
            console.error(
                'FEATURED IMAGE ID NOT FOUND',
                event
            );

            return;
        }

        console.log(
            'SELECTED FEATURED MEDIA:',
            {
                id,
                variant,
                url,
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Find featured_media_id field
        |--------------------------------------------------------------------------
        */

        let mediaInput =
            document.querySelector(
                'input[name="featured_media_id"]'
            );

        if (!mediaInput) {

            mediaInput =
                document.getElementById(
                    'data.featured_media_id'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Find featured_media_variant field
        |--------------------------------------------------------------------------
        */

        let variantInput =
            document.querySelector(
                'input[name="featured_media_variant"]'
            );

        if (!variantInput) {

            variantInput =
                document.getElementById(
                    'data.featured_media_variant'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Media ID field missing
        |--------------------------------------------------------------------------
        */

        if (!mediaInput) {

            console.error(
                'FEATURED MEDIA ID INPUT NOT FOUND',
                {
                    id,
                    variant,
                }
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Variant field missing
        |--------------------------------------------------------------------------
        */

        if (!variantInput) {

            console.error(
                'FEATURED MEDIA VARIANT INPUT NOT FOUND',
                {
                    id,
                    variant,
                }
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Set Media ID
        |--------------------------------------------------------------------------
        */

        mediaInput.value = id;

        /*
        |--------------------------------------------------------------------------
        | Set EXACT selected variant
        |--------------------------------------------------------------------------
        */

        variantInput.value =
            variant || 'cropped';

        /*
        |--------------------------------------------------------------------------
        | Notify Filament / Livewire
        |--------------------------------------------------------------------------
        */

        mediaInput.dispatchEvent(
            new Event('input', {
                bubbles: true,
            })
        );

        mediaInput.dispatchEvent(
            new Event('change', {
                bubbles: true,
            })
        );

        variantInput.dispatchEvent(
            new Event('input', {
                bubbles: true,
            })
        );

        variantInput.dispatchEvent(
            new Event('change', {
                bubbles: true,
            })
        );

        /*
        |--------------------------------------------------------------------------
        | Store selected data
        |--------------------------------------------------------------------------
        */

        mediaInput.dataset.mediaVariant =
            variant || 'cropped';

        mediaInput.dataset.mediaUrl =
            url || '';

        variantInput.dataset.mediaVariant =
            variant || 'cropped';

        variantInput.dataset.mediaUrl =
            url || '';

        /*
        |--------------------------------------------------------------------------
        | Dispatch preview event
        |--------------------------------------------------------------------------
        */

        window.dispatchEvent(
            new CustomEvent(
                'featured-media-preview',
                {
                    detail: {
                        id: id,
                        variant:
                            variant || 'cropped',
                        url: url || '',
                    },
                }
            )
        );

        console.log(
            'FEATURED MEDIA SAVED:',
            {
                id,
                variant:
                    variant || 'cropped',
                url,
            }
        );
    });

});