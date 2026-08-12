<?php

namespace App\Services\Media\Pipeline\Steps;

use App\Data\MediaUploadData;
use App\Services\Media\Pipeline\Contracts\PipelineStep;
use App\Services\Media\Pipeline\MediaPipelineContext;
use App\Services\Media\Validation\ImageValidator;

class ValidateStep implements PipelineStep
{
    public function __construct(
        protected ImageValidator $validator,
    ) {}

    public function handle(
        MediaUploadData $data,
        MediaPipelineContext $context,
    ): void {

        $this->validator->validate(
            $data->file
        );

    }
}