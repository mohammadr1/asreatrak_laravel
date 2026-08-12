<?php

namespace App\Data;

use App\Enums\WatermarkType;
use Illuminate\Http\UploadedFile;

class MediaUploadData
{
    public function __construct(

        public UploadedFile $file,

        public string $directory = 'media',

        public bool $crop = true,

        public bool $compress = true,

        public bool $generateThumbnails = true,

        public bool $optimize = true,

        public ?WatermarkType $watermarkType = null,

        public ?int $reporterId = null,

        public ?int $resizeWidth = null,

        public ?int $resizeHeight = null,

        public int $quality = 88,

    ) {}
}