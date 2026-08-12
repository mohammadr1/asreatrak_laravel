<?php

namespace App\Services\Media\Validation;

use Exception;
use App\Services\Media\DTO\ImageData;
use App\Services\Media\Exceptions\MediaValidationException;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageValidator
{
        public function validate(
        UploadedFile $file,
        ?ImageValidationRule $rule = null
    ): void {

        $rule ??= new ImageValidationRule();

        if ($file->getSize() > $rule->maxSize) {
            throw new Exception('حجم تصویر بیش از حد مجاز است.');
        }

        $extension = strtolower($file->getClientOriginalExtension());

        if (! in_array($extension, $rule->extensions)) {
            throw new Exception('فرمت تصویر مجاز نیست.');
        }

        $manager = new ImageManager(
            new Driver()
        );

        $image = $manager->read(
            $file->getRealPath()
        );

        if ($image->width() < $rule->minWidth) {
            throw new Exception(
                "حداقل عرض تصویر {$rule->minWidth}px است."
            );
        }

        if ($image->height() < $rule->minHeight) {
            throw new Exception(
                "حداقل ارتفاع تصویر {$rule->minHeight}px است."
            );
        }

        if ($image->width() > $rule->maxWidth) {
            throw new Exception(
                "عرض تصویر بیش از حد مجاز است."
            );
        }

        if ($image->height() > $rule->maxHeight) {
            throw new Exception(
                "ارتفاع تصویر بیش از حد مجاز است."
            );
        }
    }
}