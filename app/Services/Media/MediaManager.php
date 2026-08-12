<?php

namespace App\Services\Media;

use Illuminate\Http\UploadedFile;
use App\Models\Media;


class MediaManager
{
    public function __construct(

        protected MediaPipeline $pipeline,

        protected ThumbnailGenerator $thumbnailGenerator,

        protected MetadataExtractor $metadata,

        protected \App\Repositories\MediaRepository $repository,

    ) {}

    /**
     * Upload image
     */
    public function upload(
        UploadedFile $file,
        array $options = [],
    ): Media {

        $path = $this->pipeline->upload(
            $file,
            $options,
        );

        $absolutePath = storage_path(
            'app/public/'.$path
        );

        $directory = dirname($path);

        $thumbs = $this->thumbnailGenerator->generate(
            $absolutePath,
            $directory.'/thumbs'
        );

        $meta = $this->metadata->image($absolutePath);

        $media = $this->repository->create([

            'disk' => 'public',

            'filename' => basename($path),

            'original_path' => $path,

            'mime_type' => $meta['mime'],

            'extension' => $meta['extension'],

            'size' => $meta['size'],

            'width' => $meta['width'],

            'height' => $meta['height'],

        ]);

        return $media;
    }
}