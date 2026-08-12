<?php

namespace App\Services\Media\Pipeline;

class MediaPipelineContext
{
    public ?string $originalPath = null;

    public ?string $workingPath = null;

    public array $metadata = [];

    public array $thumbnails = [];

    public ?int $mediaId = null;
}