<?php

namespace App\Repositories;

use App\Models\Media;

class MediaRepository
{
    public function create(array $data): Media
    {
        return Media::create($data);
    }

    public function update(Media $media, array $data): Media
    {
        $media->update($data);

        return $media->refresh();
    }

    public function delete(Media $media): void
    {
        $media->delete();
    }

    public function find(int $id): ?Media
    {
        return Media::find($id);
    }
}