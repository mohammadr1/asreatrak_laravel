<?php

namespace App\Services\Media;

use App\Models\User;
use App\Models\Watermark;
use App\Services\Media\DTO\ImageData;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use RuntimeException;

class ImageProcessor
{
    protected ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(
            new Driver()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Read
    |--------------------------------------------------------------------------
    */

    public function read(string $path): ImageInterface
    {
        if (! is_file($path)) {
            throw new RuntimeException(
                "Image file does not exist: {$path}"
            );
        }

        return $this->manager->read($path);
    }

    /*
    |--------------------------------------------------------------------------
    | Save
    |--------------------------------------------------------------------------
    */

    public function save(
        ImageInterface $image,
        string $path,
        int $quality = 90
    ): void {
        $directory = dirname($path);

        if (! is_dir($directory)) {
            mkdir(
                $directory,
                0755,
                true
            );
        }

        $extension = strtolower(
            pathinfo(
                $path,
                PATHINFO_EXTENSION
            )
        );

        $quality = max(
            1,
            min(
                100,
                $quality
            )
        );

        $encoded = match ($extension) {

            'jpg',
            'jpeg' => $image->toJpeg(
                $quality
            ),

            'png' => $image->toPng(),

            'webp' => $image->toWebp(
                $quality
            ),

            'gif' => $image->toGif(),

            'avif' => $image->toAvif(
                $quality
            ),

            default => $image->toJpeg(
                $quality
            ),
        };

        file_put_contents(
            $path,
            (string) $encoded
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Resize
    |--------------------------------------------------------------------------
    */

    public function resize(
        string $input,
        string $output,
        int $width,
        int $height,
        int $quality = 90
    ): void {
        if ($width <= 0 || $height <= 0) {
            throw new RuntimeException(
                'Resize dimensions must be greater than zero.'
            );
        }

        $image = $this->read(
            $input
        );

        $image->cover(
            $width,
            $height
        );

        $this->save(
            $image,
            $output,
            $quality
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Crop
    |--------------------------------------------------------------------------
    */

    public function crop(
        string $input,
        string $output,
        int $x,
        int $y,
        int $width,
        int $height,
        int $quality = 90
    ): void {
        $image = $this->read(
            $input
        );

        $imageWidth = $image->width();
        $imageHeight = $image->height();

        /*
        |--------------------------------------------------------------------------
        | Validate original image
        |--------------------------------------------------------------------------
        */

        if (
            $imageWidth <= 0 ||
            $imageHeight <= 0
        ) {
            throw new RuntimeException(
                'Source image has invalid dimensions.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Validate crop dimensions
        |--------------------------------------------------------------------------
        */

        if (
            $width <= 0 ||
            $height <= 0
        ) {
            throw new RuntimeException(
                "Invalid crop dimensions: {$width}x{$height}."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Normalize coordinates
        |--------------------------------------------------------------------------
        */

        $x = max(
            0,
            $x
        );

        $y = max(
            0,
            $y
        );

        /*
        |--------------------------------------------------------------------------
        | Keep crop inside image
        |--------------------------------------------------------------------------
        */

        if ($x >= $imageWidth) {
            $x = 0;
        }

        if ($y >= $imageHeight) {
            $y = 0;
        }

        /*
        |--------------------------------------------------------------------------
        | Prevent crop from exceeding image bounds
        |--------------------------------------------------------------------------
        */

        $width = min(
            $width,
            $imageWidth - $x
        );

        $height = min(
            $height,
            $imageHeight - $y
        );

        /*
        |--------------------------------------------------------------------------
        | Final safety check
        |--------------------------------------------------------------------------
        */

        if (
            $width <= 0 ||
            $height <= 0
        ) {
            throw new RuntimeException(
                'Crop area is outside the source image.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Crop
        |--------------------------------------------------------------------------
        */

        $image->crop(
            $width,
            $height,
            $x,
            $y
        );

        /*
        |--------------------------------------------------------------------------
        | Save
        |--------------------------------------------------------------------------
        */

        $this->save(
            $image,
            $output,
            $quality
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Rotate
    |--------------------------------------------------------------------------
    */

    public function rotate(
        string $input,
        string $output,
        float $angle,
        int $quality = 90
    ): void {
        $image = $this->read(
            $input
        );

        $image->rotate(
            $angle
        );

        $this->save(
            $image,
            $output,
            $quality
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Flip Horizontal
    |--------------------------------------------------------------------------
    */

    public function flipHorizontal(
        string $input,
        string $output,
        int $quality = 90
    ): void {
        $image = $this->read(
            $input
        );

        $image->flip();

        $this->save(
            $image,
            $output,
            $quality
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Flip Vertical
    |--------------------------------------------------------------------------
    */

    public function flipVertical(
        string $input,
        string $output,
        int $quality = 90
    ): void {
        $image = $this->read(
            $input
        );

        $image->flop();

        $this->save(
            $image,
            $output,
            $quality
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Compress
    |--------------------------------------------------------------------------
    */

    public function compress(
        string $input,
        string $output,
        int $quality = 80
    ): void {
        $image = $this->read(
            $input
        );

        $this->save(
            $image,
            $output,
            $quality
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Watermark
    |--------------------------------------------------------------------------
    */

    public function applyWatermark(
        string $inputPath,
        string $outputPath,
        string $watermarkType,
        ?int $reporterId = null
    ): void {
        $image = $this->read(
            $inputPath
        );

        /*
        |--------------------------------------------------------------------------
        | No watermark
        |--------------------------------------------------------------------------
        */

        if ($watermarkType === 'none') {

            $this->save(
                $image,
                $outputPath
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Find watermark
        |--------------------------------------------------------------------------
        */

        $watermarkPath = $this->getWatermarkPath(
            $watermarkType,
            $reporterId
        );

        /*
        |--------------------------------------------------------------------------
        | Watermark does not exist
        |--------------------------------------------------------------------------
        */

        if (
            ! $watermarkPath ||
            ! is_file($watermarkPath)
        ) {
            throw new RuntimeException(
                'Watermark file could not be found.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Read watermark
        |--------------------------------------------------------------------------
        */

        $watermark = $this->read(
            $watermarkPath
        );

        $imageWidth = $image->width();
        $imageHeight = $image->height();

        $watermarkWidth = $watermark->width();
        $watermarkHeight = $watermark->height();

        /*
        |--------------------------------------------------------------------------
        | Invalid dimensions
        |--------------------------------------------------------------------------
        */

        if (
            $imageWidth <= 0 ||
            $imageHeight <= 0 ||
            $watermarkWidth <= 0 ||
            $watermarkHeight <= 0
        ) {
            throw new RuntimeException(
                'Invalid image or watermark dimensions.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Scale watermark if it is too large
        |--------------------------------------------------------------------------
        */

        $maxWatermarkWidth =
            max(
                1,
                (int) round(
                    $imageWidth * 0.28
                )
            );

        $maxWatermarkHeight =
            max(
                1,
                (int) round(
                    $imageHeight * 0.28
                )
            );

        if (
            $watermarkWidth > $maxWatermarkWidth ||
            $watermarkHeight > $maxWatermarkHeight
        ) {

            $ratio = min(
                $maxWatermarkWidth / $watermarkWidth,
                $maxWatermarkHeight / $watermarkHeight
            );

            $newWidth = max(
                1,
                (int) round(
                    $watermarkWidth * $ratio
                )
            );

            $newHeight = max(
                1,
                (int) round(
                    $watermarkHeight * $ratio
                )
            );

            $watermark->resize(
                $newWidth,
                $newHeight
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Calculate safe margin
        |--------------------------------------------------------------------------
        */

        $margin = max(
            10,
            min(
                40,
                (int) round(
                    min(
                        $imageWidth,
                        $imageHeight
                    ) * 0.02
                )
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Place watermark
        |--------------------------------------------------------------------------
        */

        $image->place(
            $watermark,
            'bottom-left',
            $margin,
            $margin
        );

        /*
        |--------------------------------------------------------------------------
        | Save
        |--------------------------------------------------------------------------
        */

        $this->save(
            $image,
            $outputPath
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Get Watermark Path
    |--------------------------------------------------------------------------
    */

    private function getWatermarkPath(
        string $type,
        ?int $reporterId
    ): ?string {

        /*
        |--------------------------------------------------------------------------
        | Reporter watermark
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $type,
                [
                    'personal',
                    'reporter',
                ],
                true
            )
        ) {

            if (! $reporterId) {
                return null;
            }

            /*
            |--------------------------------------------------------------------------
            | First try Watermark model
            |--------------------------------------------------------------------------
            */

            $watermark = Watermark::query()
                ->where(
                    'is_active',
                    true
                )
                ->where(
                    'type',
                    'user'
                )
                ->where(
                    'user_id',
                    $reporterId
                )
                ->orderBy(
                    'id'
                )
                ->first();

            if ($watermark) {

                /*
                |--------------------------------------------------------------------------
                | Try common watermark path fields
                |--------------------------------------------------------------------------
                */

                foreach (
                    [
                        'path',
                        'file_path',
                        'watermark_path',
                        'image_path',
                    ] as $field
                ) {

                    if (
                        isset(
                            $watermark->{$field}
                        ) &&
                        filled(
                            $watermark->{$field}
                        )
                    ) {

                        $path =
                            storage_path(
                                'app/public/' .
                                ltrim(
                                    $watermark->{$field},
                                    '/'
                                )
                            );

                        if (
                            is_file($path)
                        ) {
                            return $path;
                        }
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Fallback: User watermark_path
            |--------------------------------------------------------------------------
            */

            $user = User::find(
                $reporterId
            );

            if (
                $user &&
                filled(
                    $user->watermark_path
                )
            ) {

                $path =
                    storage_path(
                        'app/public/' .
                        ltrim(
                            $user->watermark_path,
                            '/'
                        )
                    );

                if (
                    is_file($path)
                ) {
                    return $path;
                }
            }

            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | System watermark
        |--------------------------------------------------------------------------
        */

        if ($type === 'system') {

            $watermark =
                Watermark::query()
                    ->where(
                        'is_active',
                        true
                    )
                    ->where(
                        'type',
                        'system'
                    )
                    ->orderBy(
                        'id'
                    )
                    ->first();

            if ($watermark) {

                foreach (
                    [
                        'path',
                        'file_path',
                        'watermark_path',
                        'image_path',
                    ] as $field
                ) {

                    if (
                        isset(
                            $watermark->{$field}
                        ) &&
                        filled(
                            $watermark->{$field}
                        )
                    ) {

                        $path =
                            storage_path(
                                'app/public/' .
                                ltrim(
                                    $watermark->{$field},
                                    '/'
                                )
                            );

                        if (
                            is_file($path)
                        ) {
                            return $path;
                        }
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Fallback system watermark
            |--------------------------------------------------------------------------
            */

            $fallback =
                public_path(
                    'images/general-watermark.png'
                );

            return is_file($fallback)
                ? $fallback
                : null;
        }

        /*
        |--------------------------------------------------------------------------
        | Unknown type
        |--------------------------------------------------------------------------
        */

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | Inspect
    |--------------------------------------------------------------------------
    */

    public function inspect(
        string $path
    ): ImageData {

        $image = $this->read(
            $path
        );

        $mime =
            mime_content_type(
                $path
            );

        $size =
            filesize(
                $path
            );

        return new ImageData(

            path: $path,

            width: $image->width(),

            height: $image->height(),

            mime: $mime ?: 'application/octet-stream',

            size: $size ?: 0,

            extension: strtolower(
                pathinfo(
                    $path,
                    PATHINFO_EXTENSION
                )
            ),
        );
    }



    public function prepareForCrop(
        string $input,
        string $output,
        int $maxWidth = 1600,
        int $maxHeight = 1200,
        int $quality = 75,
    ): array {
        if (! is_file($input)) {
            throw new RuntimeException(
                "Image file does not exist: {$input}"
            );
        }

        $image = $this->manager->read($input);

        /*
        |--------------------------------------------------------------------------
        | Reduce large images before sending them to Cropper
        |--------------------------------------------------------------------------
        |
        | تصویر اصلی ممکن است مثلاً:
        |
        | 6000 × 4000
        | 8000 × 6000
        | 5000 × 3333
        |
        | باشد.
        |
        | برای Cropper نیازی به چنین ابعادی نداریم.
        |
        | حداکثر خروجی:
        |
        | 1600 × 1200
        |
        | اما اگر تصویر کوچک‌تر باشد، بزرگ نمی‌شود.
        |
        */

        $image->scaleDown(
            width: $maxWidth,
            height: $maxHeight,
        );

        /*
        |--------------------------------------------------------------------------
        | Always create a lightweight JPEG for Cropper
        |--------------------------------------------------------------------------
        |
        | این قسمت مهم است.
        |
        | اگر عکس PNG/WEBP/HEIC یا فرمت سنگین باشد،
        | همان فرمت را برای Cropper نگه نمی‌داریم.
        |
        | Cropper فقط یک نسخه موقت سبک می‌گیرد.
        |
        */

        $directory = dirname($output);

        if (! is_dir($directory)) {
            mkdir(
                $directory,
                0755,
                true
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Convert temporary crop source to JPEG
        |--------------------------------------------------------------------------
        */

        $encoded = $image->toJpeg(
            max(
                1,
                min(
                    100,
                    $quality
                )
            )
        );

        file_put_contents(
            $output,
            (string) $encoded
        );

        return [
            'width' => $image->width(),
            'height' => $image->height(),
            'size' => filesize($output) ?: 0,
        ];
    }


}