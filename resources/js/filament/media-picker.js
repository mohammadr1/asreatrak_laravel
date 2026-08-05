document.addEventListener(
    'livewire:init',
    () => {

        Livewire.on(
            'featured-image-selected',
            (event) => {

                const id = event.id;


                const input =
                    document.querySelector(
                        'input[name="featured_image_id"]'
                    );


                if (!input) {
                    return;
                }


                input.value = id;


                input.dispatchEvent(
                    new Event(
                        'input',
                        {
                            bubbles:true
                        }
                    )
                );


                input.dispatchEvent(
                    new Event(
                        'change',
                        {
                            bubbles:true
                        }
                    )
                );

            }
        );

    }
);