<?php

namespace App\Filament\Resources\MediaResource\Pages;

use App\Filament\Resources\MediaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;


class CreateMedia extends CreateRecord
{
    protected static string $resource = MediaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['uuid'] = (string) \Illuminate\Support\Str::uuid();

        $data['uploaded_by'] = auth()->id();

        $path = $data['filename'];

        $fullPath = Storage::disk('public')->path($path);

        $data['extension'] = File::extension($fullPath);

        $data['mime_type'] = File::mimeType($fullPath);

        $data['size'] = File::size($fullPath);

        $data['hash'] = md5_file($fullPath);

        $mime = $data['mime_type'];

        if (str_starts_with($mime, 'image/')) {

            $data['type'] = 'image';

        } elseif (str_starts_with($mime, 'video/')) {

            $data['type'] = 'video';

        } elseif (str_starts_with($mime, 'audio/')) {

            $data['type'] = 'audio';

        } else {

            $data['type'] = 'document';

        }
        if ($data['type'] === 'image') {

            [$width, $height] = getimagesize($fullPath);

            $data['width'] = $width;

            $data['height'] = $height;
        }

        return $data;
    }

    
}
