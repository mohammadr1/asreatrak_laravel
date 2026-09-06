<?php namespace App\Filament\Resources\NewsResource\Pages;

use App\Filament\Resources\NewsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Livewire\Attributes\On;


class EditNews extends EditRecord {
    
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

    
    /* |-------------------------------------------------------------------------- | Load Form Data |-------------------------------------------------------------------------- */
    protected function mutateFormDataBeforeFill(array $data): array {
        /* |-------------------------------------------------------------------------- | Gallery |-------------------------------------------------------------------------- | | تصاویر گالری را از جدول news_media می‌خوانیم | و برای Hidden field فرم آماده می‌کنیم. | */
        $gallery=$this->record ->media() ->orderByPivot('sort_order') ->get();
        $this->galleryMedia=[];

        foreach ($gallery as $media) {
            $variant='cropped';

            /* |-------------------------------------------------------------------------- | انتخاب Variant |-------------------------------------------------------------------------- */
            if ($media->watermarked_path) {
                $variant='watermarked';
            }

            elseif ($media->cropped_path) {
                $variant='cropped';
            }

            elseif ($media->original_path) {
                $variant='original';
            }

            $url=$media->variantUrl($variant);

            if ( ! $url) {
                continue;
            }

            $this->galleryMedia[]=[ 'id'=>$media->id,
            'variant'=>$variant,
            'url'=>$url,
            ];
        }

        /* |-------------------------------------------------------------------------- | Put gallery into form |-------------------------------------------------------------------------- */
        $data['gallery_media']=$this->galleryMedia;
        return $data;
    }

    /* |-------------------------------------------------------------------------- | Save Gallery |-------------------------------------------------------------------------- */
    protected function afterSave(): void {
        /* |-------------------------------------------------------------------------- | Read gallery from form |-------------------------------------------------------------------------- */
        $gallery=$this->data['gallery_media'] ?? [];

        if (is_string($gallery)) {
            $gallery=json_decode($gallery, true) ?: [];
        }

        if ( ! is_array($gallery)) {
            $gallery=[];
        }

        $this->galleryMedia=$gallery;
        /* |-------------------------------------------------------------------------- | Prepare Pivot Data |-------------------------------------------------------------------------- */
        $syncData=[];

        foreach ($gallery as $index=> $item) {
            $mediaId=isset($item['id']) ? (int) $item['id']: null;

            if ( ! $mediaId) {
                continue;
            }

            $syncData[$mediaId]=[ 'sort_order'=>$index,
            'is_featured'=>false,
            ];
        }

        /* |-------------------------------------------------------------------------- | Sync |-------------------------------------------------------------------------- | | sync() عمداً استفاده شده تا: | | - تصاویر جدید اضافه شوند | - تصاویر حذف‌شده از گالری حذف شوند | - ترتیب تصاویر اصلاح شود | */
        $this->record ->media() ->sync($syncData);
    }
}
