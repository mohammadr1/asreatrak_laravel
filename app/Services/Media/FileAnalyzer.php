<?php

namespace App\Services\Media;

use Illuminate\Support\Facades\File;

class FileAnalyzer
{
    public function analyze(string $fullPath): array
    {
        $mime = File::mimeType($fullPath);

        $type = match (true) {
            str_starts_with($mime, 'image/') => 'image',
            str_starts_with($mime, 'video/') => 'video',
            str_starts_with($mime, 'audio/') => 'audio',
            default => 'document',
        };

        $data = [
            'extension' => File::extension($fullPath),
            'mime_type' => $mime,
            'size' => File::size($fullPath),
            'hash' => md5_file($fullPath),
            'type' => $type,
            'width' => null,
            'height' => null,
        ];

        if ($type === 'image') {

            [$width, $height] = getimagesize($fullPath);

            $data['width'] = $width;
            $data['height'] = $height;
        }

        return $data;
    }
}