<?php

namespace App\Services\Media;

class MetadataExtractor
{
    public function image(string $path): array
    {
        [$width, $height] = getimagesize($path);

        return [

            'width' => $width,

            'height' => $height,

            'mime' => mime_content_type($path),

            'size' => filesize($path),

            'extension' => pathinfo(
                $path,
                PATHINFO_EXTENSION
            ),

        ];
    }
}