<?php

namespace App\Filament\Resources\NewsResource\Pages;

class CreateVideoNews extends CreateNews
{
    protected static ?string $title = 'افزودن خبر ویدیویی';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = parent::mutateFormDataBeforeCreate($data);
        $data['post_type'] = 'video';

        return $data;
    }
}
