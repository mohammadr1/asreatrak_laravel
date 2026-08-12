<?php

namespace App\Services\Media;

use App\Models\Watermark;
use Illuminate\Support\Facades\File;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class WatermarkService
{
    protected ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(
            new Driver()
        );
    }

    public function apply(
        string $imagePath,
        Watermark $watermark,
        string $outputPath,
        string $position = 'bottom-right',
        int $scale = 22,
        int $padding = 40,
    ): void {

        if (! File::exists($imagePath)) {
            throw new \Exception("Image not found: {$imagePath}");
        }

        $watermarkPath = storage_path(
            'app/public/' . ltrim($watermark->image, '/')
        );

        if (! File::exists($watermarkPath)) {
            throw new \Exception("Watermark not found: {$watermarkPath}");
        }

        $image = $this->manager->read($imagePath);

        $logo = $this->manager->read($watermarkPath);

        $targetWidth = (int) (
            $image->width() * ($scale / 100)
        );

        $logo->scale(width: $targetWidth);

        $place = match ($position) {

            'top-left' => 'top-left',

            'top-right' => 'top-right',

            'bottom-left' => 'bottom-left',

            default => 'bottom-right',
        };

        $image->place(
            $logo,
            $place,
            $padding,
            $padding,
        );

        $extension = strtolower(
            pathinfo($outputPath, PATHINFO_EXTENSION)
        );

        $encoded = match ($extension) {

            'jpg',
            'jpeg' => $image->toJpeg(90),

            'png' => $image->toPng(),

            'webp' => $image->toWebp(90),

            'gif' => $image->toGif(),

            'avif' => $image->toAvif(90),

            default => $image->toJpeg(90),

        };

        File::ensureDirectoryExists(
            dirname($outputPath)
        );

        file_put_contents(
            $outputPath,
            (string) $encoded
        );
    }
}