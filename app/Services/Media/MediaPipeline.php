<?php

namespace App\Services\Media;

use App\Services\Media\Validation\ImageValidator;
use Illuminate\Http\UploadedFile;

class MediaPipeline
{
    public function __construct(
        protected ImageValidator $validator,
        protected ImageProcessor $processor,
    ) {}

    public function upload(
        UploadedFile $file,
        array $options = [],
    ): string {

        /*
        |--------------------------------------------------------------------------
        | 1) Validate
        |--------------------------------------------------------------------------
        */

        $this->validator->validate($file);

        /*
        |--------------------------------------------------------------------------
        | 2) Build destination
        |--------------------------------------------------------------------------
        */

        $directory = $options['directory']
            ?? now()->format('Y/m');

        $filename = str()->uuid().'.'.$file->extension();

        $path = storage_path(
            "app/public/media/{$directory}/{$filename}"
        );

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path),0755,true);
        }

        /*
        |--------------------------------------------------------------------------
        | 3) Move original
        |--------------------------------------------------------------------------
        */

        copy(
            $file->getRealPath(),
            $path
        );

        /*
        |--------------------------------------------------------------------------
        | 4) Compress
        |--------------------------------------------------------------------------
        */

        $this->processor->compress(
            $path,
            $path,
            config('media.image.quality')
        );

        /*
        |--------------------------------------------------------------------------
        | 5) Resize (Optional)
        |--------------------------------------------------------------------------
        */

        if (! empty($options['resize'])) {

            $this->processor->resize(
                $path,
                $path,
                $options['resize']['width'],
                $options['resize']['height'],
            );

        }

        /*
        |--------------------------------------------------------------------------
        | 6) Watermark
        |--------------------------------------------------------------------------
        */

        if (! empty($options['watermark'])) {

            $this->processor->applyWatermark(
                inputPath: $path,
                outputPath: $path,
                watermarkType: $options['watermark'],
                reporterId: $options['reporter_id'] ?? null,
            );

        }

        return "media/{$directory}/{$filename}";
    }
}