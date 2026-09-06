<?php namespace App\Filament\Resources\NewsResource\Pages;
use App\Filament\Resources\NewsResource;
use App\Models\Media;
use App\Models\ReportType;
use Filament\Resources\Pages\CreateRecord;
use Livewire\Attributes\On;


class CreateNews extends CreateRecord {
    protected static string $resource=NewsResource::class;
    protected array $galleryMedia=[];

    #[On('featured-image-selected')]
    public function setFeaturedImage(
        int $id,
        string $variant
    ): void {
        $this->data['featured_media_id'] = $id;
        $this->data['featured_media_variant'] = $variant;
    }


    protected function mutateFormDataBeforeCreate(array $data): array {
        /* |-------------------------------------------------------------------------- | تصویر شاخص |-------------------------------------------------------------------------- */
        $featuredMediaId=$data['featured_media_id'] ?? null;

        if ( ! filled($featuredMediaId)) {
            $this->addError('featured_media_id', 'انتخاب تصویر شاخص الزامی است.');
            $this->halt();
        }

        $featuredMedia=Media::query() ->active() ->images() ->find($featuredMediaId);

        if ( ! $featuredMedia) {
            $this->addError('featured_media_id', 'تصویر شاخص انتخاب‌شده معتبر نیست.');
            $this->halt();
        }

        /* |-------------------------------------------------------------------------- | Variant تصویر شاخص |-------------------------------------------------------------------------- */
        $featuredVariant=$data['featured_media_variant'] ?? null;

        if ( ! in_array($featuredVariant, [ 'original', 'cropped', 'watermarked', ], true)) {
            $this->addError('featured_media_variant', 'نسخه تصویر شاخص مشخص نشده است.');
            $this->halt();
        }

        /* |-------------------------------------------------------------------------- | Gallery |-------------------------------------------------------------------------- */
        $gallery=$data['gallery_media'] ?? [];

        if (is_string($gallery)) {
            $gallery=json_decode($gallery, true) ?: [];
        }

        if ( ! is_array($gallery)) {
            $gallery=[];
        }

        $this->galleryMedia=$gallery;
        /* |-------------------------------------------------------------------------- | گزارش تصویری |-------------------------------------------------------------------------- */
        $reportTypeId=$data['report_type'] ?? null;
        $isGalleryReport=filled($reportTypeId) && ReportType::query() ->whereKey($reportTypeId) ->where('name', 'گزارش تصویری') ->exists();

        if ($isGalleryReport && count($gallery)===0) {
            $this->addError('gallery_media', 'برای گزارش تصویری باید حداقل یک تصویر انتخاب کنید.');
            $this->halt();
        }

        /* |-------------------------------------------------------------------------- | Reporter |-------------------------------------------------------------------------- */
        if ( ! auth()->user()?->hasRole('Admin')) {
            $data['reporter_id']=auth()->id();
        }

        /* |-------------------------------------------------------------------------- | Temporary field |-------------------------------------------------------------------------- */
        unset($data['gallery_media']);
        return $data;
    }

    protected function afterCreate(): void {
        if (empty($this->galleryMedia)) {
            return;
        }

        $syncData=[];

        foreach ($this->galleryMedia as $index=> $item) {
            $mediaId=isset($item['id']) ? (int) $item['id']: null;

            if ( ! $mediaId) {
                continue;
            }

            $syncData[$mediaId]=[ 'sort_order'=>$index,
            'is_featured'=>false,
            ];
        }

        if ( ! empty($syncData)) {
            $this->record ->media() ->syncWithoutDetaching($syncData);
        }
    }
}
