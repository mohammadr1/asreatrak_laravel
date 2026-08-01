<?php

namespace App\Services\Media;

use App\Models\Watermark;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\File;


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
        int $padding = 40
    ): void {

        if (! File::exists($imagePath)) {
            throw new \Exception('Image not found.');
        }

        $watermarkPath = storage_path(
            'app/public/' . $watermark->image
        );

        if (! File::exists($watermarkPath)) {
            throw new \Exception('Watermark not found.');
        }

        $image = $this->manager->read($imagePath);

        $logo = $this->manager->read($watermarkPath);

        $targetWidth = intval(
            $image->width() * ($scale / 100)
        );

        $logo->scale(width: $targetWidth);

        $place = match ($position) {

            'top-left' => [
                'top-left',
                $padding,
                $padding,
            ],

            'top-right' => [
                'top-right',
                $padding,
                $padding,
            ],

            'bottom-left' => [
                'bottom-left',
                $padding,
                $padding,
            ],

            default => [
                'bottom-right',
                $padding,
                $padding,
            ],
        };

        $image->place(
            $logo,
            $place[0],
            $place[1],
            $place[2]
        );

        $image->save(
            $outputPath,
            quality: 90
        );
    }
}