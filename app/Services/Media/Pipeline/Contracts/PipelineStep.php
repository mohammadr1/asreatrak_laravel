<?php

namespace App\Services\Media\Pipeline\Contracts;

use App\Data\MediaUploadData;
use App\Services\Media\Pipeline\MediaPipelineContext;

interface PipelineStep
{
    public function handle(
        MediaUploadData $data,
        MediaPipelineContext $context,
    ): void;
}