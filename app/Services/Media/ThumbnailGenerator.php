<?php

namespace App\Services\Media;

class ThumbnailGenerator
{
    public function __construct(
        protected ImageProcessor $processor,
    ) {}

    public function generate(
        string $source,
        string $directory,
    ): array {

        $sizes = config('media.thumbnails');

        $generated = [];

        foreach ($sizes as $name => [$width,$height]) {

            $file = "{$directory}/{$name}.jpg";

            $this->processor->resize(
                $source,
                storage_path("app/public/".$file),
                $width,
                $height,
            );

            $generated[$name] = $file;
        }

        return $generated;
    }
}