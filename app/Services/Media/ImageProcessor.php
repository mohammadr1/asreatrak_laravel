<?php

namespace App\Services\Media;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

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
     * خواندن تصویر
     */
    public function read(string $path)
    {
        return $this->manager->read($path);
    }

    /**
     * ذخیره تصویر
     */
    public function save($image, string $path, int $quality = 90): void
    {
        $image->save($path, quality: $quality);
    }

    /**
     * تغییر اندازه
     */
    public function resize(
        string $input,
        string $output,
        int $width,
        int $height
    ): void {

        $image = $this->read($input);

        $image->cover($width, $height);

        $this->save(
            $image,
            $output
        );
    }

    /**
     * تبدیل به webp
     */
    public function convertToWebp(
        string $input,
        string $output,
        int $quality = 85
    ): void {

        $image = $this->read($input);

        $image->toWebp($quality)
            ->save($output);

    }

    /**
     * ذخیره jpg
     */
    public function convertToJpg(
        string $input,
        string $output,
        int $quality = 90
    ): void {

        $image = $this->read($input);

        $image->toJpeg($quality)
            ->save($output);

    }
}