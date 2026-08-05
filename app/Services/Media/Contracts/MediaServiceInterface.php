<?php

namespace App\Services\Media\Contracts;

use App\Models\Media;
use Illuminate\Http\UploadedFile;

interface MediaServiceInterface
{
    public function upload(
        UploadedFile $file,
        ?int $userId = null,
    ): Media;

    public function delete(
        Media $media,
    ): void;
}