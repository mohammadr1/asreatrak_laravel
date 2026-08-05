<?php

namespace App\Livewire\Media;

use App\Models\Media;
use App\Models\Watermark;
use App\Services\Media\Contracts\MediaServiceInterface;
use App\Services\Media\ImageProcessor;
use App\Services\Media\WatermarkService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class FeaturedPicker extends Component
{
    use WithPagination;
    use WithFileUploads;

    protected MediaServiceInterface $mediaService;

    protected $paginationTheme = 'tailwind';

    protected $queryString = [];

    /*
    |--------------------------------------------------------------------------
    | Library
    |--------------------------------------------------------------------------
    */

    public string $search = '';

    public string $type = 'image';

    public int $perPage = 24;

    public ?int $selected = null;

    /*
    |--------------------------------------------------------------------------
    | Upload
    |--------------------------------------------------------------------------
    */

    /**
     * فایل‌های انتخاب شده توسط کاربر
     */
    public array $uploads = [];

    /**
     * شناسه تمام فایل‌هایی که بعد از آپلود ساخته می‌شوند
     */
    public array $queue = [];

    /**
     * شماره تصویر فعلی داخل صف
     */
    public int $queueIndex = 0;

    /**
     * آیا در حال پردازش صف هستیم؟
     */
    public bool $processingQueue = false;

    /*
    |--------------------------------------------------------------------------
    | Crop
    |--------------------------------------------------------------------------
    */

    public bool $showCropModal = false;

    public ?int $cropMediaId = null;

    public ?string $cropImage = null;

    public ?array $cropData = null;

    /*
    |--------------------------------------------------------------------------
    | Watermark
    |--------------------------------------------------------------------------
    */

    public bool $showWatermarkModal = false;

    public string $watermarkType = 'none';

    public ?int $watermarkId = null;

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    protected $rules = [

        'uploads.*' => [
            'image',
            'max:10240',
        ],

    ];

    /*
    |--------------------------------------------------------------------------
    | Listeners
    |--------------------------------------------------------------------------
    */

    protected $listeners = [

        'saveCrop',

    ];

    /*
    |--------------------------------------------------------------------------
    | Boot
    |--------------------------------------------------------------------------
    */

    public function boot(
        MediaServiceInterface $mediaService,
    ): void {

        $this->mediaService = $mediaService;

    }

    /*
    |--------------------------------------------------------------------------
    | Mount
    |--------------------------------------------------------------------------
    */

    public function mount(): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | Selection
    |--------------------------------------------------------------------------
    */

    public function select(int $id): void
    {
        $this->selected = $id;
    }
    /*
    |--------------------------------------------------------------------------
    | Upload
    |--------------------------------------------------------------------------
    */

    public function updatedUploads(): void
    {
        if (empty($this->uploads)) {
            return;
        }

        $this->validate();

        $this->uploadImages();
    }

    protected function uploadImages(): void
    {
        $this->queue = [];

        foreach ($this->uploads as $file) {

            $media = $this->mediaService->upload(
                $file,
                Auth::id(),
            );

            $this->queue[] = $media->id;
        }

        $this->uploads = [];

        if (empty($this->queue)) {
            return;
        }

        $this->queueIndex = 0;

        $this->processingQueue = true;

        $this->loadCurrentCrop();
    }

    /*
    |--------------------------------------------------------------------------
    | Crop Queue
    |--------------------------------------------------------------------------
    */

    protected function loadCurrentCrop(): void
    {
        if (! isset($this->queue[$this->queueIndex])) {

            $this->finishQueue();

            return;
        }

        $media = Media::find(
            $this->queue[$this->queueIndex]
        );

        if (! $media) {

            $this->queueIndex++;

            $this->loadCurrentCrop();

            return;
        }

        $this->cropMediaId = $media->id;

        $this->cropImage = Storage::url(
            $media->original_path
        );

        $this->showCropModal = true;

        $this->dispatch('openCropper');
    }

    public function nextImage(): void
    {
        $this->dispatch('closeCropper');

        $this->showCropModal = false;

        $this->queueIndex++;

        $this->loadCurrentCrop();
    }

    protected function finishQueue(): void
    {
        $this->dispatch('closeCropper');

        $this->processingQueue = false;

        $this->showCropModal = false;

        $this->queue = [];

        $this->queueIndex = 0;

        $this->cropMediaId = null;

        $this->cropImage = null;

        $this->showWatermarkModal = true;
    }
    public function saveCrop(array $crop): void
{
    $this->cropData = $crop;

    $media = Media::findOrFail(
        $this->cropMediaId
    );

    $processor = app(
        ImageProcessor::class
    );

    $source = Storage::disk('public')->path(
        $media->original_path
    );

    $croppedDirectory = $media->directory . '/cropped';

    Storage::disk('public')->makeDirectory(
        $croppedDirectory
    );

    $croppedFilename =
        pathinfo(
            $media->filename,
            PATHINFO_FILENAME
        )
        . '_cropped.'
        . $media->extension;

    $croppedPath =
        $croppedDirectory
        . '/'
        . $croppedFilename;

    $destination = Storage::disk('public')->path(
        $croppedPath
    );

    $processor->crop(
        $source,
        $destination,
        intval($crop['x']),
        intval($crop['y']),
        intval($crop['width']),
        intval($crop['height'])
    );

    $media->update([

        'cropped_path' => $croppedPath,

        'crop_data' => json_encode($crop),

    ]);

    /*
    |--------------------------------------------------------------------------
    | تصویر بعدی
    |--------------------------------------------------------------------------
    */

    if ($this->processingQueue) {

        $this->nextImage();

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | حالت تک تصویر
    |--------------------------------------------------------------------------
    */

    $this->dispatch('closeCropper');

    $this->showCropModal = false;

    $this->showWatermarkModal = true;
}

public function applyWatermark(): void
{
    /*
    |--------------------------------------------------------------------------
    | همه تصاویر صف
    |--------------------------------------------------------------------------
    */

    $mediaIds = empty($this->queue)
        ? [$this->cropMediaId]
        : $this->queue;

    foreach ($mediaIds as $mediaId) {

        $media = Media::find($mediaId);

        if (! $media) {
            continue;
        }

        /*
        |--------------------------------------------------------------------------
        | بدون واترمارک
        |--------------------------------------------------------------------------
        */

        if ($this->watermarkType === 'none') {

            $this->selected = $media->id;

            continue;
        }

        $watermark = Watermark::find($this->watermarkId);

        if (! $watermark) {
            continue;
        }

        $service = app(
            WatermarkService::class
        );

        $source = Storage::disk('public')->path(
            $media->cropped_path ?: $media->original_path
        );

        $directory =
            $media->directory .
            '/watermarked';

        Storage::disk('public')
            ->makeDirectory($directory);

        $filename =
            pathinfo(
                $media->filename,
                PATHINFO_FILENAME
            )
            . '_wm.'
            . $media->extension;

        $watermarkedPath =
            $directory .
            '/' .
            $filename;

        $destination = Storage::disk('public')->path(
            $watermarkedPath
        );

        $service->apply(
            $source,
            $watermark,
            $destination
        );

        $media->update([

            'watermarked_path' => $watermarkedPath,

            'has_watermark' => true,

            'watermark_type' => $this->watermarkType,

            'watermark_id' => $watermark->id,

        ]);

        $this->selected = $media->id;
    }

    $this->showWatermarkModal = false;

    $this->dispatch(
        'featured-image-selected',
        id: $media->id
    );

    /*
    |--------------------------------------------------------------------------
    | Reset Queue
    |--------------------------------------------------------------------------
    */

    $this->queue = [];

    $this->queueIndex = 0;

    $this->processingQueue = false;

    $this->cropMediaId = null;

    $this->cropImage = null;
}

public function choose(): void
{
    if (! $this->selected) {
        return;
    }

    $this->dispatch(
        'featured-image-selected',
        id: $this->selected
    );
}


public function getMediaProperty()
{
    return Media::query()

        ->active()

        ->images()

        ->when(
            $this->search,
            function ($query) {

                $query->where(
                    function ($q) {

                        $q->where(
                            'title',
                            'like',
                            "%{$this->search}%"
                        )
                        ->orWhere(
                            'filename',
                            'like',
                            "%{$this->search}%"
                        );

                    }
                );

            }
        )

        ->latest()

        ->paginate(
            $this->perPage
        );
}
}