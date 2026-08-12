console.log('MEDIA PICKER JS LOADED');

document.addEventListener('livewire:init', () => {

    console.log('LIVEWIRE MEDIA PICKER READY');

    Livewire.on('featured-image-selected', (event) => {

        console.log('FEATURED IMAGE EVENT:', event);

        const id = event?.id;

        if (!id) {
            console.error('FEATURED IMAGE ID NOT FOUND');
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Filament / Livewire field
        |--------------------------------------------------------------------------
        */

        let input =
            document.querySelector(
                'input[name="featured_media_id"]'
            );

        /*
        |--------------------------------------------------------------------------
        | Backward compatibility
        |--------------------------------------------------------------------------
        */

        if (!input) {

            input =
                document.querySelector(
                    'input[name="featured_image_id"]'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | اگر input مستقیم پیدا نشد
        |--------------------------------------------------------------------------
        */

        if (!input) {

            console.error(
                'FEATURED MEDIA INPUT NOT FOUND',
                id
            );

            return;
        }

        console.log(
            'FEATURED MEDIA INPUT FOUND:',
            input
        );

        /*
        |--------------------------------------------------------------------------
        | مقداردهی
        |--------------------------------------------------------------------------
        */

        input.value = id;

        /*
        |--------------------------------------------------------------------------
        | اطلاع به Livewire
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

        /*
        |--------------------------------------------------------------------------
        | اطمینان از به‌روزرسانی Alpine / Filament
        |--------------------------------------------------------------------------
        */

        input.dispatchEvent(
            new Event('blur', {
                bubbles: true,
            })
        );

        console.log(
            'FEATURED MEDIA SELECTED:',
            id
        );
    });

});