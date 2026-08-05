<div class="mt-4">

    <img
        src="{{ Storage::url(
            $media->cropped_path
            ?: $media->original_path
        ) }}"
        class="h-40 w-full rounded-xl object-cover"
    >

</div>