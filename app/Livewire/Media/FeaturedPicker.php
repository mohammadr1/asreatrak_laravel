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

    /*
    |--------------------------------------------------------------------------
    | Picker Context
    |--------------------------------------------------------------------------
    */

    public string $selectionMode = 'single';

    public string $selectionContext = 'featured';

    public string $mediaVariant = 'watermarked';

    public ?int $selected = null;

    public ?string $selectedVariant = null;

    public array $selectedMediaIds = [];

    public array $selectedMediaVariants = [];

    public int $maxSelection = 15;

    /*
    |--------------------------------------------------------------------------
    | Delete Selection
    |--------------------------------------------------------------------------
    */

    public bool $deleteMode = false;

    public array $deleteSelectedMediaIds = [];

    /*
    |--------------------------------------------------------------------------
    | Permissions
    |--------------------------------------------------------------------------
    */

    public bool $isAdmin = false;

    public bool $isReporter = false;

    /*
    |--------------------------------------------------------------------------
    | Watermarks
    |--------------------------------------------------------------------------
    */

    public array $watermarks = [];

    public ?int $watermarkId = null;

    public string $watermarkType = 'none';

    public array $reporterWatermarks = [];

    public bool $showReporterWatermarks = false;

    public ?int $selectedReporterId = null;

    /*
    |--------------------------------------------------------------------------
    | Upload
    |--------------------------------------------------------------------------
    */

    public array $uploads = [];

    public string $uploadTitle = '';

    /*
    |--------------------------------------------------------------------------
    | Current Upload Session
    |--------------------------------------------------------------------------
    */

    public array $queue = [];

    public array $uploadSessionMediaIds = [];

    public int $queueIndex = 0;

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
    | Watermark Modal
    |--------------------------------------------------------------------------
    */

    public bool $showWatermarkModal = false;

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
        MediaServiceInterface $mediaService
    ): void {
        $this->mediaService = $mediaService;
    }

    /*
    |--------------------------------------------------------------------------
    | Mount
    |--------------------------------------------------------------------------
    */

    public function mount(
        string $context = 'featured'
    ): void {
        $this->configure($context);

        $user = Auth::user();

        $this->isAdmin = $user?->hasRole('Admin') ?? false;

        $this->isReporter = $user?->hasRole('Reporter') ?? false;

        if (! $this->isAdmin && $user) {
            $this->isAdmin = $user->hasRole('admin');
        }

        if (! $this->isReporter && $user) {
            $this->isReporter = $user->hasRole('reporter');
        }

        $this->loadWatermarks();
    }

    /*
    |--------------------------------------------------------------------------
    | Watermarks
    |--------------------------------------------------------------------------
    */

    protected function loadWatermarks(): void
    {
        $user = Auth::user();

        $this->watermarks = [];

        $this->reporterWatermarks = [];

        $this->watermarkId = null;

        $this->selectedReporterId = null;

        $this->showReporterWatermarks = false;

        if (! $user) {
            return;
        }

        $systemWatermark = Watermark::query()
            ->where('is_active', true)
            ->where('type', 'system')
            ->orderBy('id')
            ->first();

        if ($systemWatermark) {
            $this->watermarks[] = [
                'id' => $systemWatermark->id,
                'title' => $systemWatermark->title,
                'type' => 'system',
            ];
        }

        if ($this->isAdmin) {
            $this->reporterWatermarks = Watermark::query()
                ->where('is_active', true)
                ->where('type', 'user')
                ->whereNotNull('user_id')
                ->with('user:id,first_name,last_name')
                ->orderBy('title')
                ->get()
                ->map(function (Watermark $watermark) {
                    $userName = 'خبرنگار نامشخص';

                    if ($watermark->user) {
                        $userName = trim(
                            ($watermark->user->first_name ?? '')
                            . ' '
                            . ($watermark->user->last_name ?? '')
                        );

                        if ($userName === '') {
                            $userName = 'خبرنگار نامشخص';
                        }
                    }

                    return [
                        'id' => $watermark->id,
                        'title' => $watermark->title,
                        'type' => 'user',
                        'user_id' => $watermark->user_id,
                        'user_name' => $userName,
                        'user' => [
                            'id' => $watermark->user?->id,
                            'first_name' => $watermark->user?->first_name,
                            'last_name' => $watermark->user?->last_name,
                            'name' => $userName,
                        ],
                    ];
                })
                ->values()
                ->toArray();

            return;
        }

        if ($this->isReporter) {
            $personalWatermark = Watermark::query()
                ->where('is_active', true)
                ->where('type', 'user')
                ->where('user_id', $user->id)
                ->orderBy('id')
                ->first();

            if ($personalWatermark) {
                $this->watermarks[] = [
                    'id' => $personalWatermark->id,
                    'title' => $personalWatermark->title,
                    'type' => 'personal',
                ];
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Context
    |--------------------------------------------------------------------------
    */

    public function configure(
        string $context = 'featured'
    ): void {
        $this->selectionContext = $context;

        switch ($context) {
            case 'gallery':
                $this->selectionMode = 'multiple';
                $this->maxSelection = 15;
                break;

            case 'editor':
                $this->selectionMode = 'single';
                $this->maxSelection = 1;
                break;

            case 'featured':
            default:
                $this->selectionMode = 'single';
                $this->maxSelection = 1;
                break;
        }

        $this->clearSelection();
    }

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | Variant
    |--------------------------------------------------------------------------
    */

    public function setMediaVariant(
        string $variant
    ): void {
        if (! in_array(
            $variant,
            [
                'watermarked',
                'cropped',
                'original',
            ],
            true
        )) {
            return;
        }

        $this->mediaVariant = $variant;
    }

    /*
    |--------------------------------------------------------------------------
    | Watermark Type
    |--------------------------------------------------------------------------
    */

    public function setWatermarkType(
        string $type
    ): void {
        $this->watermarkId = null;

        $this->selectedReporterId = null;

        $this->showReporterWatermarks = false;

        $this->resetErrorBag('watermarkId');

        if ($type === 'none') {
            $this->watermarkType = 'none';

            return;
        }

        if ($type === 'system') {
            $this->watermarkType = 'system';

            $watermark = Watermark::query()
                ->where('is_active', true)
                ->where('type', 'system')
                ->orderBy('id')
                ->first();

            if ($watermark) {
                $this->watermarkId = $watermark->id;
            }

            return;
        }

        if ($type === 'reporter') {
            if (! $this->isAdmin) {
                $this->addError(
                    'watermarkId',
                    'شما اجازه انتخاب واترمارک خبرنگار را ندارید.'
                );

                return;
            }

            $this->watermarkType = 'reporter';

            $this->showReporterWatermarks = true;

            return;
        }

        if ($type === 'personal') {
            if (! $this->isReporter) {
                return;
            }

            $this->watermarkType = 'personal';

            $user = Auth::user();

            if (! $user) {
                return;
            }

            $watermark = Watermark::query()
                ->where('is_active', true)
                ->where('type', 'user')
                ->where('user_id', $user->id)
                ->orderBy('id')
                ->first();

            if ($watermark) {
                $this->watermarkId = $watermark->id;
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Select Reporter Watermark
    |--------------------------------------------------------------------------
    */

    public function selectReporterWatermark(
        int $watermarkId
    ): void {
        if (! $this->isAdmin) {
            $this->addError(
                'watermarkId',
                'شما اجازه انتخاب واترمارک خبرنگار دیگر را ندارید.'
            );

            return;
        }

        $watermark = Watermark::query()
            ->where('id', $watermarkId)
            ->where('is_active', true)
            ->where('type', 'user')
            ->whereNotNull('user_id')
            ->with('user:id,first_name,last_name')
            ->first();

        if (! $watermark) {
            $this->addError(
                'watermarkId',
                'واترمارک خبرنگار معتبر نیست.'
            );

            return;
        }

        $this->watermarkType = 'reporter';

        $this->watermarkId = $watermark->id;

        $this->selectedReporterId = $watermark->user_id;

        $this->showReporterWatermarks = false;

        $this->resetErrorBag('watermarkId');
    }

    /*
    |--------------------------------------------------------------------------
    | Media Selection
    |--------------------------------------------------------------------------
    */

    public function toggleSelection(
        int $mediaId
    ): void {
        if ($this->deleteMode) {
            $this->toggleDeleteSelection($mediaId);

            return;
        }

        $media = Media::find($mediaId);

        if (! $media) {
            return;
        }

        if ($this->selectionMode === 'single') {
            $this->selected = $mediaId;

            $this->selectedVariant = $this->mediaVariant;

            $this->selectedMediaIds = [
                $mediaId,
            ];

            $this->selectedMediaVariants = [
                $mediaId => $this->mediaVariant,
            ];

            return;
        }

        $isSelected = in_array(
            $mediaId,
            $this->selectedMediaIds,
            true
        );

        if ($isSelected) {
            $this->selectedMediaIds = array_values(
                array_filter(
                    $this->selectedMediaIds,
                    fn ($id) => $id !== $mediaId
                )
            );

            unset(
                $this->selectedMediaVariants[$mediaId]
            );

            return;
        }

        if (
            count($this->selectedMediaIds)
            >= $this->maxSelection
        ) {
            return;
        }

        $this->selectedMediaIds[] = $mediaId;

        $this->selectedMediaVariants[$mediaId] =
            $this->mediaVariant;
    }

    /*
    |--------------------------------------------------------------------------
    | Clear Selection
    |--------------------------------------------------------------------------
    */

    public function clearSelection(): void
    {
        $this->selected = null;

        $this->selectedVariant = null;

        $this->selectedMediaIds = [];

        $this->selectedMediaVariants = [];
    }

    /*
    |--------------------------------------------------------------------------
    | Selected Count
    |--------------------------------------------------------------------------
    */

    public function getSelectedCountProperty(): int
    {
        return count($this->selectedMediaIds);
    }

    public function getDeleteSelectedCountProperty(): int
    {
        return count($this->deleteSelectedMediaIds);
    }

    /*
    |--------------------------------------------------------------------------
    | Is Selected
    |--------------------------------------------------------------------------
    */

    public function isSelected(
        int $mediaId
    ): bool {
        return in_array(
            $mediaId,
            $this->selectedMediaIds,
            true
        );
    }

    public function isDeleteSelected(
        int $mediaId
    ): bool {
        return in_array(
            $mediaId,
            $this->deleteSelectedMediaIds,
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Mode
    |--------------------------------------------------------------------------
    */

    public function toggleDeleteMode(): void
    {
        $this->deleteMode = ! $this->deleteMode;

        $this->deleteSelectedMediaIds = [];

        if ($this->deleteMode) {
            $this->clearSelection();
        }
    }

    public function toggleDeleteSelection(
        int $mediaId
    ): void {
        $mediaId = (int) $mediaId;

        if (in_array(
            $mediaId,
            $this->deleteSelectedMediaIds,
            true
        )) {
            $this->deleteSelectedMediaIds = array_values(
                array_filter(
                    $this->deleteSelectedMediaIds,
                    fn ($id) => $id !== $mediaId
                )
            );

            return;
        }

        $this->deleteSelectedMediaIds[] = $mediaId;
    }

    public function clearDeleteSelection(): void
    {
        $this->deleteSelectedMediaIds = [];
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Media
    |--------------------------------------------------------------------------
    */

    public function deleteMedia(
        int $mediaId
    ): void {
        $media = Media::find($mediaId);

        if (! $media) {
            return;
        }

        $this->deleteMediaRecord($media);

        $this->removeDeletedMediaFromState($mediaId);

        $this->resetPage();
    }

    public function deleteSelectedMedia(): void
    {
        if (empty($this->deleteSelectedMediaIds)) {
            return;
        }

        $mediaItems = Media::query()
            ->whereIn(
                'id',
                $this->deleteSelectedMediaIds
            )
            ->get();

        foreach ($mediaItems as $media) {
            $this->deleteMediaRecord($media);
        }

        $this->deleteSelectedMediaIds = [];

        $this->deleteMode = false;

        $this->resetPage();
    }

    protected function deleteMediaRecord(
        Media $media
    ): void {
        $disk = Storage::disk(
            $media->disk ?: 'public'
        );

        $paths = array_unique(
            array_filter([
                $media->original_path,
                $media->cropped_path,
                $media->watermarked_path,
            ])
        );

        foreach ($paths as $path) {
            if ($disk->exists($path)) {
                $disk->delete($path);
            }
        }

        $media->delete();
    }

    protected function removeDeletedMediaFromState(
        int $mediaId
    ): void {
        $this->selectedMediaIds = array_values(
            array_filter(
                $this->selectedMediaIds,
                fn ($id) => $id !== $mediaId
            )
        );

        unset(
            $this->selectedMediaVariants[$mediaId]
        );

        $this->deleteSelectedMediaIds = array_values(
            array_filter(
                $this->deleteSelectedMediaIds,
                fn ($id) => $id !== $mediaId
            )
        );

        $this->queue = array_values(
            array_filter(
                $this->queue,
                fn ($id) => $id !== $mediaId
            )
        );

        $this->uploadSessionMediaIds = array_values(
            array_filter(
                $this->uploadSessionMediaIds,
                fn ($id) => $id !== $mediaId
            )
        );

        if ($this->selected === $mediaId) {
            $this->selected = null;

            $this->selectedVariant = null;
        }

        if ($this->cropMediaId === $mediaId) {
            $this->cropMediaId = null;

            $this->cropImage = null;

            $this->cropData = null;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Cancel Current Upload Session
    |--------------------------------------------------------------------------
    */

    public function cancelCurrentUpload(): void
    {
        $this->cleanupCurrentUploadSession();

        $this->dispatch('closeCropper');
    }

    protected function cleanupCurrentUploadSession(): void
    {
        $ids = array_unique(
            array_merge(
                $this->queue,
                $this->uploadSessionMediaIds
            )
        );

        if (! empty($ids)) {
            $mediaItems = Media::query()
                ->whereIn('id', $ids)
                ->get();

            foreach ($mediaItems as $media) {
                $this->deleteMediaRecord($media);
            }
        }

        $this->uploads = [];

        $this->uploadTitle = '';

        $this->queue = [];

        $this->uploadSessionMediaIds = [];

        $this->queueIndex = 0;

        $this->processingQueue = false;

        $this->showCropModal = false;

        $this->cropMediaId = null;

        $this->cropImage = null;

        $this->cropData = null;

        $this->showWatermarkModal = false;

        $this->watermarkType = 'none';

        $this->watermarkId = null;

        $this->selectedReporterId = null;

        $this->showReporterWatermarks = false;

        $this->resetErrorBag();
    }

    /*
    |--------------------------------------------------------------------------
    | Confirm Selection
    |--------------------------------------------------------------------------
    */

    public function confirmSelection(): void
    {
        if ($this->deleteMode) {
            return;
        }

        if ($this->selectionMode === 'single') {
            if (! $this->selected) {
                return;
            }

            $media = Media::find($this->selected);

            if (! $media) {
                return;
            }

            $variant = $this->selectedVariant
                ?: $this->mediaVariant;

            if ($this->selectionContext === 'featured') {
                $this->dispatch(
                    'featured-image-selected',
                    id: $media->id,
                    variant: $variant,
                    url: $media->variantUrl($variant),
                );

                return;
            }

            $this->dispatch(
                'media-selected',
                mediaId: $media->id,
                variant: $variant,
                url: $media->variantUrl($variant),
                context: $this->selectionContext,
            );

            return;
        }

        if (empty($this->selectedMediaIds)) {
            return;
        }

        $media = Media::query()
            ->whereIn(
                'id',
                $this->selectedMediaIds
            )
            ->get()
            ->keyBy('id');

        $items = [];

        foreach ($this->selectedMediaIds as $mediaId) {
            if (! isset($media[$mediaId])) {
                continue;
            }

            $item = $media[$mediaId];

            $variant =
                $this->selectedMediaVariants[$mediaId]
                ?? $this->mediaVariant;

            $items[] = [
                'id' => $item->id,
                'variant' => $variant,
                'url' => $item->variantUrl($variant),
            ];
        }

        $this->dispatch(
            'media-multiple-selected',
            items: $items,
            context: $this->selectionContext,
        );
    }

    public function choose(): void
    {
        $this->confirmSelection();
    }

    /*
    |--------------------------------------------------------------------------
    | Upload
    |--------------------------------------------------------------------------
    */

    public function updatedUploads(): void
    {
        // فقط فایل انتخاب شده است.
        // آپلود و پردازش با دکمه جداگانه انجام می‌شود.
    }

    public function startUpload(): void
    {
        if (empty($this->uploads)) {
            $this->addError(
                'uploads',
                'حداقل یک تصویر انتخاب کنید.'
            );

            return;
        }

        $this->validate();

        $this->uploadImages();
    }

    protected function uploadImages(): void
    {
        if ($this->processingQueue) {
            return;
        }

        $this->queue = [];

        $this->uploadSessionMediaIds = [];

        $this->queueIndex = 0;

        $this->resetErrorBag();

        $processor = app(ImageProcessor::class);

        foreach ($this->uploads as $file) {
            $media = $this->mediaService->upload(
                $file,
                Auth::id()
            );

            if (! $media) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Apply upload title
            |--------------------------------------------------------------------------
            */

            if (
                trim($this->uploadTitle) !== ''
            ) {
                $media->update([
                    'title' => trim($this->uploadTitle),
                ]);
            }

            $this->uploadSessionMediaIds[] = $media->id;

            $disk = Storage::disk(
                $media->disk ?: 'public'
            );

            if (! $media->original_path) {
                $this->deleteMediaRecord($media);

                continue;
            }

            $sourcePath = $disk->path(
                $media->original_path
            );

            if (! file_exists($sourcePath)) {
                $this->deleteMediaRecord($media);

                continue;
            }

            $temporaryDirectory = trim(
                ($media->directory ?: 'media')
                . '/crop-source',
                '/'
            );

            $disk->makeDirectory(
                $temporaryDirectory
            );

            /*
            |--------------------------------------------------------------------------
            | Crop source is always a lightweight JPEG
            |--------------------------------------------------------------------------
            */

            $temporaryFilename =
                pathinfo(
                    $media->filename,
                    PATHINFO_FILENAME
                )
                . '_crop-source.jpg';

            $temporaryRelativePath =
                $temporaryDirectory
                . '/'
                . $temporaryFilename;

            $temporaryAbsolutePath =
                $disk->path(
                    $temporaryRelativePath
                );

            try {
                $processor->prepareForCrop(
                    input: $sourcePath,
                    output: $temporaryAbsolutePath,
                    maxWidth: 1600,
                    maxHeight: 1200,
                    quality: 75,
                );
            } catch (\Throwable $e) {
                report($e);

                $this->deleteMediaRecord($media);

                continue;
            }

            if (! file_exists($temporaryAbsolutePath)) {
                $this->deleteMediaRecord($media);

                continue;
            }

            if ($disk->exists($media->original_path)) {
                $disk->delete($media->original_path);
            }

            $media->update([
                'original_path' => $temporaryRelativePath,
            ]);

            $this->queue[] = $media->id;
        }

        $this->uploads = [];

        if (empty($this->queue)) {
            $this->uploadSessionMediaIds = [];

            return;
        }

        $this->queueIndex = 0;

        $this->processingQueue = true;

        $this->loadCurrentCrop();
    }

    /*
    |--------------------------------------------------------------------------
    | Load Current Crop
    |--------------------------------------------------------------------------
    */

    protected function loadCurrentCrop(): void
    {
        if (! isset(
            $this->queue[$this->queueIndex]
        )) {
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

        if (! $media->original_path) {
            $this->queueIndex++;

            $this->loadCurrentCrop();

            return;
        }

        $sourcePath = Storage::disk(
            $media->disk ?: 'public'
        )->path(
            $media->original_path
        );

        if (! file_exists($sourcePath)) {
            $this->queueIndex++;

            $this->loadCurrentCrop();

            return;
        }

        $this->cropMediaId = $media->id;

        /*
        |--------------------------------------------------------------------------
        | Crop preview URL
        |--------------------------------------------------------------------------
        |
        | از localhost استفاده نمی‌کنیم.
        | URL نسبی روی همان آدرسی که کاربر با آن
        | سایت را باز کرده resolve می‌شود.
        |
        */

        $this->cropImage =
            '/storage/' .
            ltrim(
                $media->original_path,
                '/'
            );

        $this->cropData = null;

        $this->showCropModal = true;

        $this->dispatch(
            'openCropper',
            imageUrl: $this->cropImage,
            mediaId: $media->id,
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Next Image
    |--------------------------------------------------------------------------
    */

    public function nextImage(): void
    {
        $this->dispatch('closeCropper');

        $this->showCropModal = false;

        $this->cropData = null;

        $this->cropMediaId = null;

        $this->cropImage = null;

        $this->queueIndex++;

        $this->loadCurrentCrop();
    }

    /*
    |--------------------------------------------------------------------------
    | Finish Queue
    |--------------------------------------------------------------------------
    */

    protected function finishQueue(): void
    {
        $this->dispatch('closeCropper');

        $this->processingQueue = false;

        $this->showCropModal = false;

        $this->cropData = null;

        $this->cropMediaId = null;

        $this->cropImage = null;

        $this->watermarkType = 'none';

        $this->watermarkId = null;

        $this->selectedReporterId = null;

        $this->showReporterWatermarks = false;

        $this->resetErrorBag();

        $this->showWatermarkModal = true;
    }

    /*
    |--------------------------------------------------------------------------
    | Save Crop
    |--------------------------------------------------------------------------
    */

    public function saveCrop(
        array $crop
    ): void {
        if (! $this->cropMediaId) {
            return;
        }

        $x = isset($crop['x'])
            ? (float) $crop['x']
            : 0;

        $y = isset($crop['y'])
            ? (float) $crop['y']
            : 0;

        $width = isset($crop['width'])
            ? (float) $crop['width']
            : 0;

        $height = isset($crop['height'])
            ? (float) $crop['height']
            : 0;

        if ($width <= 0 || $height <= 0) {
            $this->addError(
                'crop',
                'محدوده برش معتبر نیست. لطفاً دوباره تصویر را انتخاب کنید.'
            );

            $this->dispatch('cropError');

            return;
        }

        $media = Media::find(
            $this->cropMediaId
        );

        if (! $media) {
            return;
        }

        if (! $media->original_path) {
            $this->addError(
                'crop',
                'تصویر اصلی پیدا نشد.'
            );

            return;
        }

        $processor = app(
            ImageProcessor::class
        );

        $disk = Storage::disk(
            $media->disk ?: 'public'
        );

        $source = $disk->path(
            $media->original_path
        );

        if (! file_exists($source)) {
            $this->addError(
                'crop',
                'فایل تصویر اصلی روی دیسک پیدا نشد.'
            );

            return;
        }

        $sourceInfo = @getimagesize($source);

        if (! $sourceInfo) {
            $this->addError(
                'crop',
                'ابعاد تصویر قابل تشخیص نیست.'
            );

            return;
        }

        $sourceWidth = (int) $sourceInfo[0];

        $sourceHeight = (int) $sourceInfo[1];

        if (
            $sourceWidth <= 0 ||
            $sourceHeight <= 0
        ) {
            $this->addError(
                'crop',
                'ابعاد تصویر نامعتبر است.'
            );

            return;
        }

        $x = max(
            0,
            min(
                $x,
                $sourceWidth - 1
            )
        );

        $y = max(
            0,
            min(
                $y,
                $sourceHeight - 1
            )
        );

        $width = min(
            $width,
            $sourceWidth - $x
        );

        $height = min(
            $height,
            $sourceHeight - $y
        );

        $width = (int) round($width);

        $height = (int) round($height);

        $x = (int) round($x);

        $y = (int) round($y);

        if (
            $width < 1 ||
            $height < 1
        ) {
            $this->addError(
                'crop',
                'اندازه ناحیه برش باید حداقل یک پیکسل باشد.'
            );

            return;
        }

        $croppedDirectory = trim(
            ($media->directory ?: 'media')
            . '/cropped',
            '/'
        );

        $disk->makeDirectory(
            $croppedDirectory
        );

        $extension = strtolower(
            $media->extension ?: 'jpg'
        );

        if ($extension === 'jpeg') {
            $extension = 'jpg';
        }

        $croppedFilename =
            pathinfo(
                $media->filename,
                PATHINFO_FILENAME
            )
            . '_cropped.'
            . $extension;

        $croppedPath =
            $croppedDirectory
            . '/'
            . $croppedFilename;

        $destination = $disk->path(
            $croppedPath
        );

        try {
            $processor->crop(
                $source,
                $destination,
                $x,
                $y,
                $width,
                $height
            );
        } catch (\Throwable $e) {
            report($e);

            $this->addError(
                'crop',
                'پردازش تصویر انجام نشد. لطفاً دوباره امتحان کنید.'
            );

            return;
        }

        if (! file_exists($destination)) {
            $this->addError(
                'crop',
                'فایل برش‌خورده ایجاد نشد.'
            );

            return;
        }

        $outputInfo = @getimagesize(
            $destination
        );

        if (! $outputInfo) {
            $this->addError(
                'crop',
                'تصویر برش‌خورده معتبر نیست.'
            );

            return;
        }

        $temporarySourcePath = $media->original_path;

        if (
            $temporarySourcePath
            &&
            $temporarySourcePath !== $croppedPath
            &&
            $disk->exists($temporarySourcePath)
        ) {
            $disk->delete(
                $temporarySourcePath
            );
        }

        $outputWidth = (int) $outputInfo[0];

        $outputHeight = (int) $outputInfo[1];

        if (
            $outputWidth <= 0 ||
            $outputHeight <= 0
        ) {
            $this->addError(
                'crop',
                'ابعاد تصویر خروجی نامعتبر است.'
            );

            return;
        }

        $media->update([
            'original_path' => null,

            'cropped_path' => $croppedPath,

            'crop_data' => [
                'x' => $x,
                'y' => $y,
                'width' => $width,
                'height' => $height,
            ],

            'width' => $outputWidth,

            'height' => $outputHeight,

            'size' => filesize($destination),
        ]);

        $this->cropData = [
            'x' => $x,
            'y' => $y,
            'width' => $width,
            'height' => $height,
        ];

        if (
            $this->processingQueue
            &&
            $this->queueIndex
            <
            count($this->queue) - 1
        ) {
            $this->nextImage();

            return;
        }

        $this->dispatch('closeCropper');

        $this->showCropModal = false;

        $this->processingQueue = false;

        $this->showWatermarkModal = true;
    }

    /*
    |--------------------------------------------------------------------------
    | Media
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Apply Watermark
    |--------------------------------------------------------------------------
    */

    public function applyWatermark(): void
    {
        if ($this->watermarkType === 'none') {
            $this->finishProcessedUpload(
                'cropped'
            );

            return;
        }

        if (! $this->watermarkId) {
            $this->addError(
                'watermarkId',
                'لطفاً یک واترمارک انتخاب کنید.'
            );

            return;
        }

        $watermarkQuery = Watermark::query()
            ->where('id', $this->watermarkId)
            ->where('is_active', true);

        if ($this->watermarkType === 'system') {
            $watermarkQuery->where(
                'type',
                'system'
            );
        }

        if ($this->watermarkType === 'reporter') {
            if (! $this->isAdmin) {
                $this->addError(
                    'watermarkId',
                    'شما اجازه استفاده از واترمارک خبرنگار دیگر را ندارید.'
                );

                return;
            }

            $watermarkQuery
                ->where('type', 'user')
                ->whereNotNull('user_id');
        }

        if ($this->watermarkType === 'personal') {
            $user = Auth::user();

            if (! $user) {
                $this->addError(
                    'watermarkId',
                    'کاربر معتبر نیست.'
                );

                return;
            }

            $watermarkQuery
                ->where('type', 'user')
                ->where(
                    'user_id',
                    $user->id
                );
        }

        $watermark = $watermarkQuery->first();

        if (! $watermark) {
            $this->addError(
                'watermarkId',
                'واترمارک انتخاب‌شده معتبر نیست.'
            );

            return;
        }

        if (empty($this->queue)) {
            $this->addError(
                'watermarkId',
                'تصویری برای پردازش وجود ندارد.'
            );

            return;
        }

        $watermarkService = app(
            WatermarkService::class
        );

        foreach ($this->queue as $mediaId) {
            $media = Media::find($mediaId);

            if (! $media) {
                continue;
            }

            if (! $media->cropped_path) {
                continue;
            }

            $sourceRelativePath =
                $media->cropped_path;

            $disk = Storage::disk(
                $media->disk ?: 'public'
            );

            $sourcePath = $disk->path(
                $sourceRelativePath
            );

            if (! file_exists($sourcePath)) {
                continue;
            }

            $directory = trim(
                ($media->directory ?: 'media')
                . '/watermarked',
                '/'
            );

            $disk->makeDirectory(
                $directory
            );

            $extension = strtolower(
                $media->extension ?: 'jpg'
            );

            if ($extension === 'jpeg') {
                $extension = 'jpg';
            }

            $filename =
                pathinfo(
                    $media->filename,
                    PATHINFO_FILENAME
                )
                . '_wm.'
                . $extension;

            $watermarkedRelativePath =
                $directory
                . '/'
                . $filename;

            $watermarkedAbsolutePath =
                $disk->path(
                    $watermarkedRelativePath
                );

            try {
                $watermarkService->apply(
                    imagePath: $sourcePath,
                    watermark: $watermark,
                    outputPath: $watermarkedAbsolutePath,
                    position: 'bottom-left',
                    scale: 17,
                    padding: 2,
                );
            } catch (\Throwable $e) {
                report($e);

                $this->addError(
                    'watermarkId',
                    'اعمال واترمارک روی یکی از تصاویر انجام نشد.'
                );

                return;
            }

            if (! file_exists(
                $watermarkedAbsolutePath
            )) {
                $this->addError(
                    'watermarkId',
                    'فایل واترمارک‌شده ایجاد نشد.'
                );

                return;
            }

            $media->update([
                'watermarked_path' =>
                    $watermarkedRelativePath,

                'watermark_id' =>
                    $watermark->id,

                'watermark_type' =>
                    $this->watermarkType,

                'has_watermark' =>
                    true,
            ]);
        }

        $this->finishProcessedUpload(
            'watermarked'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Finish Processed Upload
    |--------------------------------------------------------------------------
    */

    protected function finishProcessedUpload(
        string $variant
    ): void {
        $this->showWatermarkModal = false;

        $this->resetErrorBag();

        $items = Media::query()
            ->whereIn(
                'id',
                $this->queue
            )
            ->get()
            ->keyBy('id');

        if ($this->selectionMode === 'single') {
            $mediaId = $this->queue[0] ?? null;

            $media = $mediaId
                ? ($items[$mediaId] ?? null)
                : null;

            if ($media) {
                $this->selected = $media->id;

                $this->selectedVariant = $variant;

                $this->selectedMediaIds = [
                    $media->id,
                ];

                $this->selectedMediaVariants = [
                    $media->id => $variant,
                ];
            }

            $this->resetCurrentUploadState();

            return;
        }

        $this->selectedMediaIds = [];

        $this->selectedMediaVariants = [];

        foreach ($this->queue as $mediaId) {
            if (! isset($items[$mediaId])) {
                continue;
            }

            $this->selectedMediaIds[] = $mediaId;

            $this->selectedMediaVariants[$mediaId] =
                $variant;
        }

        $this->resetCurrentUploadState();
    }

    protected function resetCurrentUploadState(): void
    {
        $this->uploads = [];

        $this->uploadTitle = '';

        $this->queue = [];

        $this->uploadSessionMediaIds = [];

        $this->queueIndex = 0;

        $this->processingQueue = false;

        $this->cropMediaId = null;

        $this->cropImage = null;

        $this->cropData = null;

        $this->showCropModal = false;

        $this->showWatermarkModal = false;

        $this->watermarkType = 'none';

        $this->watermarkId = null;

        $this->selectedReporterId = null;

        $this->showReporterWatermarks = false;
    }

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        return view(
            'livewire.media.featured-picker'
        );
    }
}