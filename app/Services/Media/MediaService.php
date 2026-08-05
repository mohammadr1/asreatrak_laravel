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

        $filename =
            Str::uuid().
            '.'.
            $file->getClientOriginalExtension();

        $path = $file->storeAs(
            "media/{$directory}",
            $filename,
            'public'
        );

        [$width, $height] = getimagesize(
            $file->getRealPath()
        );

        return Media::create([

            'uuid' => (string) Str::uuid(),

            'disk' => 'public',

            'directory' => "media/{$directory}",

            'filename' => $filename,

            'original_path' => $path,

            'extension' => $file->getClientOriginalExtension(),

            'mime_type' => $file->getMimeType(),

            'size' => $file->getSize(),

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

            'hash' => md5_file(
                Storage::disk('public')->path($path)
            ),

            'metadata' => json_encode([]),

            'has_watermark' => false,

            'watermark_type' => null,

            'watermark_id' => null,
        ]);
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