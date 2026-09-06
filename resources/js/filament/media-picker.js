document.addEventListener('livewire:init', () => {

    console.log('LIVEWIRE MEDIA PICKER READY');

    Livewire.on('featured-image-selected', (event) => {

        console.log('FEATURED IMAGE EVENT:', event);

        const id = event?.id;
        const variant = event?.variant || 'cropped';
        const url = event?.url || '';

        if (!id) {
            console.error(
                'FEATURED IMAGE ID NOT FOUND',
                event
            );

            return;
        }

        const mediaInput =
            document.querySelector(
                'input[name="featured_media_id"]'
            );

        const variantInput =
            document.querySelector(
                'input[name="featured_media_variant"]'
            );

        if (!mediaInput) {
            console.error(
                'FEATURED MEDIA ID INPUT NOT FOUND'
            );

            return;
        }

        if (!variantInput) {
            console.error(
                'FEATURED MEDIA VARIANT INPUT NOT FOUND'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Set DOM values
        |--------------------------------------------------------------------------
        */

        mediaInput.value = String(id);

        variantInput.value = variant;

        /*
        |--------------------------------------------------------------------------
        | Sync directly with Livewire / Filament
        |--------------------------------------------------------------------------
        */

        const componentElement =
            mediaInput.closest('[wire\\:id]');

        if (componentElement) {

            const componentId =
                componentElement.getAttribute('wire:id');

            const component =
                Livewire.find(componentId);

            if (component) {

                component.set(
                    'data.featured_media_id',
                    String(id)
                );

                component.set(
                    'data.featured_media_variant',
                    variant
                );

                console.log(
                    'FILAMENT STATE UPDATED:',
                    {
                        featured_media_id: id,
                        featured_media_variant: variant,
                    }
                );

            } else {

                console.error(
                    'LIVEWIRE COMPONENT NOT FOUND'
                );

            }

        } else {

            console.error(
                'FILAMENT COMPONENT ELEMENT NOT FOUND'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Keep DOM events too
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
        | Preview
        |--------------------------------------------------------------------------
        */

        window.dispatchEvent(
            new CustomEvent(
                'featured-media-preview',
                {
                    detail: {
                        id: Number(id),
                        variant: variant,
                        url: url,
                    },
                }
            )
        );

        console.log(
            'FEATURED MEDIA SAVED TO FORM STATE:',
            {
                id,
                variant,
                url,
            }
        );
    });


    Livewire.on('media-multiple-selected', (event) => {

        console.log(
            'MEDIA MULTIPLE SELECTED:',
            event
        );

        if (
            event?.context !== 'gallery'
        ) {
            return;
        }

        const items =
            Array.isArray(event?.items)
                ? event.items
                : [];

        if (!items.length) {
            console.warn(
                'NO GALLERY MEDIA SELECTED'
            );

            return;
        }

        const formElement =
            document.querySelector(
                '[wire\\:id]'
            );

        if (!formElement) {
            console.error(
                'FILAMENT LIVEWIRE COMPONENT NOT FOUND'
            );

            return;
        }

        const componentId =
            formElement.getAttribute(
                'wire:id'
            );

        const component =
            Livewire.find(componentId);

        if (!component) {
            console.error(
                'LIVEWIRE COMPONENT INSTANCE NOT FOUND'
            );

            return;
        }

        component.set(
            'data.gallery_media',
            items
        );

        console.log(
            'GALLERY MEDIA STATE SAVED:',
            items
        );
    });

});