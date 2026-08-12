<?php

namespace App\Livewire\Media;

use App\Models\Media;
use App\Models\Watermark;
use App\Services\Media\Contracts\MediaServiceInterface;
use App\Services\Media\ImageProcessor;
use App\Services\Media\WatermarkService;
use Illuminate\Database\Eloquent\Collection;
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

    /**
     * single | multiple
     */
    public string $selectionMode = 'single';

    /**
     * featured | gallery | editor
     */
    public string $selectionContext = 'featured';

    /**
     * watermarked | cropped | original
     */
    public string $mediaVariant = 'watermarked';

    /**
     * انتخاب تکی
     */
    public ?int $selected = null;

    /**
     * انتخاب چندتایی
     */
    public array $selectedMediaIds = [];

    /**
     * حداکثر انتخاب
     */
    public int $maxSelection = 15;


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

    /**
     * واترمارک خبرنگاران برای ادمین
     */
    public array $reporterWatermarks = [];

    /**
     * آیا لیست خبرنگاران برای ادمین باز است؟
     */
    public bool $showReporterWatermarks = false;

    /**
     * خبرنگار انتخاب‌شده
     */
    public ?int $selectedReporterId = null;

    /*
    |--------------------------------------------------------------------------
    | Upload
    |--------------------------------------------------------------------------
    */

    public array $uploads = [];

    public array $queue = [];

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

        $this->configure(
            $context
        );

        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | تشخیص دسترسی
        |--------------------------------------------------------------------------
        */

        $this->isAdmin = $user?->hasRole('Admin') ?? false;

        $this->isReporter = $user?->hasRole('Reporter') ?? false;

        /*
        |--------------------------------------------------------------------------
        | اگر سیستم نقش‌ها با حروف کوچک ذخیره شده
        |--------------------------------------------------------------------------
        */

        if (! $this->isAdmin && $user) {

            $this->isAdmin =
                $user->hasRole('admin');

        }

        if (! $this->isReporter && $user) {

            $this->isReporter =
                $user->hasRole('reporter');

        }

        /*
        |--------------------------------------------------------------------------
        | واترمارک‌های قابل مشاهده
        |--------------------------------------------------------------------------
        */

        $this->loadWatermarks();
    }


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

        /*
        |--------------------------------------------------------------------------
        | واترمارک عمومی
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | Admin
        |--------------------------------------------------------------------------
        |
        | ادمین لیست واترمارک خبرنگاران را فقط
        | بعد از انتخاب «انتخاب واترمارک خبرنگار» می‌بیند.
        |
        */

        if ($this->isAdmin) {

            $this->reporterWatermarks = Watermark::query()
                ->where('is_active', true)
                ->where('type', 'user')
                ->whereNotNull('user_id')
                ->with('user:id,first_name,last_name')
                ->orderBy('title')
                ->get()
                ->map(function (Watermark $watermark) {

                    return [
                        'id' => $watermark->id,

                        'title' => $watermark->title,

                        'type' => 'user',

                        'user_id' => $watermark->user_id,

                        'user_name' => $watermark->user
                            ? $watermark->user->full_name
                            : 'خبرنگار نامشخص',
                    ];

                })
                ->values()
                ->toArray();

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Reporter
        |--------------------------------------------------------------------------
        |
        | خبرنگار فقط واترمارک اختصاصی خودش را دارد.
        |
        */

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

        if (
            ! in_array(
                $variant,
                [
                    'watermarked',
                    'cropped',
                    'original',
                ],
                true
            )
        ) {
            return;
        }

        $this->mediaVariant = $variant;
    }


    public function setWatermarkType(string $type): void
    {
        /*
        |--------------------------------------------------------------------------
        | Reset
        |--------------------------------------------------------------------------
        */

        $this->watermarkId = null;

        $this->selectedReporterId = null;

        $this->showReporterWatermarks = false;

        $this->resetErrorBag('watermarkId');

        /*
        |--------------------------------------------------------------------------
        | بدون واترمارک
        |--------------------------------------------------------------------------
        */

        if ($type === 'none') {

            $this->watermarkType = 'none';

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | واترمارک عمومی
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | انتخاب واترمارک خبرنگار - فقط Admin
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | واترمارک اختصاصی خودم - Reporter
        |--------------------------------------------------------------------------
        */

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

            return;
        }
    }
    


    public function selectReporterWatermark(int $watermarkId): void
    {
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
    | Selection
    |--------------------------------------------------------------------------
    */

    public function toggleSelection(
        int $mediaId
    ): void {

        $media = Media::find(
            $mediaId
        );

        if (! $media) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Single
        |--------------------------------------------------------------------------
        */

        if (
            $this->selectionMode === 'single'
        ) {

            $this->selected = $mediaId;

            $this->selectedMediaIds = [
                $mediaId,
            ];

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Multiple
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $mediaId,
                $this->selectedMediaIds,
                true
            )
        ) {

            $this->selectedMediaIds =
                array_values(
                    array_filter(
                        $this->selectedMediaIds,
                        fn ($id) =>
                            $id !== $mediaId
                    )
                );

            return;
        }

        if (
            count(
                $this->selectedMediaIds
            ) >= $this->maxSelection
        ) {
            return;
        }

        $this->selectedMediaIds[] =
            $mediaId;
    }

    /*
    |--------------------------------------------------------------------------
    | Clear
    |--------------------------------------------------------------------------
    */

    public function clearSelection(): void
    {
        $this->selected = null;

        $this->selectedMediaIds = [];
    }

    /*
    |--------------------------------------------------------------------------
    | Selected Count
    |--------------------------------------------------------------------------
    */

    public function getSelectedCountProperty(): int
    {
        return count(
            $this->selectedMediaIds
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Selected
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

    /*
    |--------------------------------------------------------------------------
    | Confirm Selection
    |--------------------------------------------------------------------------
    */

    public function confirmSelection(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Single
        |--------------------------------------------------------------------------
        */

        if (
            $this->selectionMode === 'single'
        ) {

            if (! $this->selected) {
                return;
            }

            $media = Media::find(
                $this->selected
            );

            if (! $media) {
                return;
            }

            if ($this->selectionContext === 'featured') {

                $this->dispatch(
                    'featured-image-selected',
                    id: $media->id,
                    variant: $this->mediaVariant,
                    url: $media->variantUrl(
                        $this->mediaVariant
                    ),
                );

                return;
            }

            $this->dispatch(
                'media-selected',
                mediaId: $media->id,
                variant: $this->mediaVariant,
                url: $media->variantUrl(
                    $this->mediaVariant
                ),
                context: $this->selectionContext,
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Multiple
        |--------------------------------------------------------------------------
        */

        if (
            empty(
                $this->selectedMediaIds
            )
        ) {
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

        foreach (
            $this->selectedMediaIds
            as $mediaId
        ) {

            if (
                ! isset(
                    $media[$mediaId]
                )
            ) {
                continue;
            }

            $item =
                $media[$mediaId];

            $items[] = [

                'id' => $item->id,

                'variant' =>
                    $this->mediaVariant,

                'url' =>
                    $item->variantUrl(
                        $this->mediaVariant
                    ),
            ];
        }

        $this->dispatch(
            'media-multiple-selected',
            items: $items,
            context: $this->selectionContext,
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Choose
    |--------------------------------------------------------------------------
    */

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
        if (
            empty($this->uploads)
        ) {
            return;
        }

        $this->validate();

        $this->uploadImages();
    }

    protected function uploadImages(): void
    {
        $this->queue = [];

        foreach (
            $this->uploads as $file
        ) {

            $media =
                $this->mediaService->upload(
                    $file,
                    Auth::id()
                );

            $this->queue[] =
                $media->id;
        }

        $this->uploads = [];

        if (
            empty($this->queue)
        ) {
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
        if (
            ! isset(
                $this->queue[
                    $this->queueIndex
                ]
            )
        ) {

            $this->finishQueue();

            return;
        }

        $media = Media::find(
            $this->queue[
                $this->queueIndex
            ]
        );

        if (! $media) {

            $this->queueIndex++;

            $this->loadCurrentCrop();

            return;
        }

        $this->cropMediaId =
            $media->id;

        $this->cropImage =
            Storage::disk(
                'public'
            )->url(
                $media->original_path
            );

        $this->showCropModal = true;

        $this->dispatch(
            'openCropper'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Next Image
    |--------------------------------------------------------------------------
    */

    public function nextImage(): void
    {
        $this->dispatch(
            'closeCropper'
        );

        $this->showCropModal = false;

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
        $this->dispatch(
            'closeCropper'
        );

        $this->processingQueue = false;

        $this->showCropModal = false;

        /*
        | queue را فعلاً نگه می‌داریم
        | چون Watermark باید روی همین
        | تصاویر اعمال شود.
        */

        $this->showWatermarkModal = true;
    }

    /*
    |--------------------------------------------------------------------------
    | Crop
    |--------------------------------------------------------------------------
    */

    public function saveCrop(
        array $crop
    ): void {

        if (! $this->cropMediaId) {
            return;
        }

        $media = Media::find(
            $this->cropMediaId
        );

        if (! $media) {
            return;
        }

        $processor =
            app(
                ImageProcessor::class
            );

        $source =
            Storage::disk(
                'public'
            )->path(
                $media->original_path
            );

        $croppedDirectory =
            $media->directory .
            '/cropped';

        Storage::disk(
            'public'
        )->makeDirectory(
            $croppedDirectory
        );

        $croppedFilename =
            pathinfo(
                $media->filename,
                PATHINFO_FILENAME
            ) .
            '_cropped.' .
            $media->extension;

        $croppedPath =
            $croppedDirectory .
            '/' .
            $croppedFilename;

        $destination =
            Storage::disk(
                'public'
            )->path(
                $croppedPath
            );

        $processor->crop(
            $source,
            $destination,
            (int) round($crop['x']),
            (int) round($crop['y']),
            (int) round($crop['width']),
            (int) round($crop['height'])
        );

        if (
            ! file_exists(
                $destination
            )
        ) {
            throw new \RuntimeException(
                'Cropped image was not created.'
            );
        }

        [$width, $height] =
            getimagesize(
                $destination
            );

        $media->update([

            'cropped_path' =>
                $croppedPath,

            'crop_data' =>
                $crop,

            'width' =>
                $width,

            'height' =>
                $height,

            'size' =>
                filesize(
                    $destination
                ),

        ]);

        /*
        |--------------------------------------------------------------------------
        | Next
        |--------------------------------------------------------------------------
        */

        if (
            $this->processingQueue &&
            $this->queueIndex <
            count($this->queue) - 1
        ) {

            $this->nextImage();

            return;
        }

        $this->dispatch(
            'closeCropper'
        );

        $this->showCropModal = false;

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




    
    public function applyWatermark(): void
    {
        if (! $this->cropMediaId) {
            $this->showWatermarkModal = false;
            return;
        }

        $media = Media::find($this->cropMediaId);

        if (! $media) {
            $this->showWatermarkModal = false;
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | بدون واترمارک
        |--------------------------------------------------------------------------
        */

        if ($this->watermarkType === 'none') {

            $this->selected = $media->id;

            $this->showWatermarkModal = false;

            $this->dispatch(
                'featured-image-selected',
                id: $media->id,
                variant: 'cropped',
                url: $media->variantUrl('cropped'),
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | باید واترمارک انتخاب شده باشد
        |--------------------------------------------------------------------------
        */

        if (! $this->watermarkId) {

            $this->addError(
                'watermarkId',
                'لطفاً یک واترمارک انتخاب کنید.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Query واترمارک
        |--------------------------------------------------------------------------
        */

        $watermarkQuery = Watermark::query()
            ->where('id', $this->watermarkId)
            ->where('is_active', true);

        /*
        |--------------------------------------------------------------------------
        | عمومی
        |--------------------------------------------------------------------------
        */

        if ($this->watermarkType === 'system') {

            $watermarkQuery
                ->where('type', 'system');
        }

        /*
        |--------------------------------------------------------------------------
        | واترمارک خبرنگار
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | واترمارک اختصاصی خود خبرنگار
        |--------------------------------------------------------------------------
        */

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
                ->where('user_id', $user->id);
        }

        $watermark = $watermarkQuery->first();

        if (! $watermark) {

            $this->addError(
                'watermarkId',
                'واترمارک انتخاب‌شده معتبر نیست.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | تصویر ورودی
        |--------------------------------------------------------------------------
        */

        $sourceRelativePath =
            $media->cropped_path
            ?: $media->original_path;

        if (! $sourceRelativePath) {

            $this->addError(
                'watermarkId',
                'تصویر پردازش‌شده پیدا نشد.'
            );

            return;
        }

        $disk = Storage::disk($media->disk);

        $sourcePath = $disk->path(
            $sourceRelativePath
        );

        if (! file_exists($sourcePath)) {

            $this->addError(
                'watermarkId',
                'فایل تصویر روی دیسک پیدا نشد.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | مسیر خروجی
        |--------------------------------------------------------------------------
        */

        $directory =
            $media->directory . '/watermarked';

        $disk->makeDirectory($directory);

        $filename =
            pathinfo(
                $media->filename,
                PATHINFO_FILENAME
            )
            . '_wm.'
            . $media->extension;

        $watermarkedRelativePath =
            $directory . '/' . $filename;

        $watermarkedAbsolutePath =
            $disk->path(
                $watermarkedRelativePath
            );

        /*
        |--------------------------------------------------------------------------
        | اعمال واترمارک
        |--------------------------------------------------------------------------
        */

        $watermarkService = app(
            WatermarkService::class
        );

        $watermarkService->apply(
            imagePath: $sourcePath,
            watermark: $watermark,
            outputPath: $watermarkedAbsolutePath,
            position: 'bottom-left',
            scale: 22,
            padding: 30,
        );

        /*
        |--------------------------------------------------------------------------
        | بررسی خروجی
        |--------------------------------------------------------------------------
        */

        if (! file_exists($watermarkedAbsolutePath)) {

            $this->addError(
                'watermarkId',
                'فایل واترمارک‌شده ایجاد نشد.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | بروزرسانی Media
        |--------------------------------------------------------------------------
        */

        $media->update([
            'watermarked_path' => $watermarkedRelativePath,
            'watermark_id' => $watermark->id,
            'watermark_type' => $this->watermarkType,
            'has_watermark' => true,
        ]);

        /*
        |--------------------------------------------------------------------------
        | انتخاب نهایی
        |--------------------------------------------------------------------------
        */

        $this->selected = $media->id;

        $this->showWatermarkModal = false;

        $this->resetErrorBag('watermarkId');

        /*
        |--------------------------------------------------------------------------
        | ارسال تصویر نهایی به Filament
        |--------------------------------------------------------------------------
        */

        $this->dispatch(
            'featured-image-selected',
            id: $media->id,
            variant: 'watermarked',
            url: $media->variantUrl('watermarked'),
        );
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