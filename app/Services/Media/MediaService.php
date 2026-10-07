<?php

namespace App\Services\Media;

use App\Models\Media;
use App\Services\Media\Contracts\MediaServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaService implements MediaServiceInterface
{
    public function upload(
        UploadedFile $file,
        ?int $userId = null,
    ): Media {

        $directory = now()->format('Y/m');

        \Illuminate\Support\Facades\Validator::make(['image' => $file], [
            'image' => ['required', 'image', 'max:'.(int) floor(config('media.image.max_size', 2097152) / 1024)],
        ])->validate();

        $image = app(ImageProcessor::class)->read($file->getRealPath());
        $encoded = (string) $image->toWebp(max(1, min(100, (int) config('media.quality', 90))));

        $filename =
            Str::uuid().
            '.'.
            'webp';

        $path = "media/{$directory}/{$filename}";
        if (! Storage::disk('public')->put($path, $encoded)) {
            throw new \RuntimeException('ذخیره تصویر WebP انجام نشد.');
        }

        $width = $image->width();
        $height = $image->height();

        try {
            return Media::create([

            'uuid' => (string) Str::uuid(),

            'disk' => 'public',

            'directory' => "media/{$directory}",

            'filename' => $filename,

            'original_path' => $path,

            'extension' => 'webp',

            'mime_type' => 'image/webp',

            'size' => strlen($encoded),

            'width' => $width,

            'height' => $height,

            'duration' => null,

            'type' => 'image',

            'title' => pathinfo(
                $file->getClientOriginalName(),
                PATHINFO_FILENAME
            ),

            'alt' => null,

            'caption' => null,

            'copyright' => null,

            'uploaded_by' => $userId,

            'visibility' => 'public',

            'is_active' => true,

            'hash' => md5($encoded),

            'metadata' => json_encode([]),

            'has_watermark' => false,

            'watermark_type' => null,

            'watermark_id' => null,
            ]);
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($path);
            throw $exception;
        }
    }

    public function delete(
        Media $media,
    ): void {

        Storage::disk($media->disk)
            ->delete($media->original_path);

        if ($media->cropped_path) {

            Storage::disk($media->disk)
                ->delete($media->cropped_path);

        }

        if ($media->watermarked_path) {

            Storage::disk($media->disk)
                ->delete($media->watermarked_path);

        }

        $media->delete();
    }
}
