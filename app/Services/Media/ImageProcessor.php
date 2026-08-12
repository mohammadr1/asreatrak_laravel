<?php

namespace App\Services\Media;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\EncodedImage;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use App\Services\Media\DTO\ImageData;


class ImageProcessor
{
    protected ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(
            new Driver()
        );
    }

    /**
     * Read image
     */
    public function read(
        string $path
    ): ImageInterface {

        return $this->manager->read($path);

    }

    /**
     * Save image
     */
    public function save(
        ImageInterface $image,
        string $path,
        int $quality = 90
    ): void {

        $extension = strtolower(
            pathinfo($path, PATHINFO_EXTENSION)
        );

        $encoded = match ($extension) {

            'jpg',
            'jpeg'
                => $image->toJpeg($quality),

            'png'
                => $image->toPng(),

            'webp'
                => $image->toWebp($quality),

            'gif'
                => $image->toGif(),

            'avif'
                => $image->toAvif($quality),

            default
                => $image->toJpeg($quality),

        };

        file_put_contents(
            $path,
            (string) $encoded
        );
    }

    /**
     * Resize
     */
    public function resize(
        string $input,
        string $output,
        int $width,
        int $height,
        int $quality = 90
    ): void {

        $image = $this->read($input);

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

    /**
     * Crop
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

        $image = $this->read($input);

        $image->crop(
            $width,
            $height,
            $x,
            $y
        );

        $this->save(
            $image,
            $output,
            $quality
        );
    }

    /**
     * Rotate
     */
    public function rotate(
        string $input,
        string $output,
        float $angle,
        int $quality = 90
    ): void {

        $image = $this->read($input);

        $image->rotate($angle);

        $this->save(
            $image,
            $output,
            $quality
        );
    }

    /**
     * Flip Horizontal
     */
    public function flipHorizontal(
        string $input,
        string $output,
        int $quality = 90
    ): void {

        $image = $this->read($input);

        $image->flip();

        $this->save(
            $image,
            $output,
            $quality
        );
    }

    /**
     * Flip Vertical
     */
    public function flipVertical(
        string $input,
        string $output,
        int $quality = 90
    ): void {

        $image = $this->read($input);

        $image->flop();

        $this->save(
            $image,
            $output,
            $quality
        );
    }

    /**
     * Compress
     */
    public function compress(
        string $input,
        string $output,
        int $quality = 80
    ): void {

        $image = $this->read($input);

        $this->save(
            $image,
            $output,
            $quality
        );
    }




/**
     * اعمال واترمارک
     */
    public function applyWatermark(
        string $inputPath, 
        string $outputPath, 
        string $watermarkType, 
        ?int $reporterId = null
    ): void {
        $image = $this->read($inputPath);
        
        $watermarkPath = $this->getWatermarkPath($watermarkType, $reporterId);
        
        if (file_exists($watermarkPath)) {
            $watermark = $this->read($watermarkPath);
            // ثبت واترمارک پایین سمت چپ با فاصله 20 پیکسل
            $image->place($watermark, 'bottom-left', 20, 20); 
        }

        $this->save($image, $outputPath);
    }

    /**
     * پیدا کردن مسیر دقیق واترمارک
     */
    private function getWatermarkPath(string $type, ?int $reporterId): string
    {
        // واترمارک اختصاصی خبرنگار
        if ($type === 'personal' && $reporterId) {
            $user = \App\Models\User::find($reporterId);
            if ($user && $user->watermark_path) {
                return storage_path('app/public/' . $user->watermark_path);
            }
        }
        
        // مسیر واترمارک عمومی سایت (این عکس باید در مسیر public/images قرار داشته باشد)
        return public_path('images/general-watermark.png'); 
    }

    public function inspect(
        string $path
    ): ImageData {

        $image = $this->read($path);

        return new ImageData(

            path: $path,

            width: $image->width(),

            height: $image->height(),

            mime: mime_content_type($path),

            size: filesize($path),

            extension: strtolower(
                pathinfo($path, PATHINFO_EXTENSION)
            ),

        );

    }


}