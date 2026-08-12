<?php

namespace App\Services\Media\DTO;

class ImageData
{
    public function __construct(

        public string $path,

        public int $width,

        public int $height,

        public string $mime,

        public int $size,

        public string $extension,

    ) {}

    public function aspectRatio(): float
    {
        return round(
            $this->width / $this->height,
            4
        );
    }

    public function isLandscape(): bool
    {
        return $this->width >= $this->height;
    }

    public function isPortrait(): bool
    {
        return $this->height > $this->width;
    }
}