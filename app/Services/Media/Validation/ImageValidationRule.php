<?php

namespace App\Services\Media\Validation;

class ImageValidationRule
{
    public int $minWidth;

    public int $minHeight;

    public int $maxWidth;

    public int $maxHeight;

    public int $maxSize;

    public array $extensions;

    public function __construct()
    {
        $this->minWidth = config('media.image.min_width', 1280);

        $this->minHeight = config('media.image.min_height', 720);

        $this->maxWidth = config('media.image.max_width', 8000);

        $this->maxHeight = config('media.image.max_height', 8000);

        $this->maxSize = config('media.image.max_size', 15 * 1024 * 1024);

        $this->extensions = config(
            'media.image.extensions',
            [
                'jpg',
                'jpeg',
                'png',
                'webp',
                'avif',
            ]
        );
    }
}