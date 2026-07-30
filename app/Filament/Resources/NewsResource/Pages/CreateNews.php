<?php

namespace App\Filament\Resources\NewsResource\Pages;

use App\Enums\NewsStatus;
use App\Filament\Resources\NewsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateNews extends CreateRecord
{
    protected static string $resource = NewsResource::class;


    protected function mutateFormDataBeforeCreate(array $data): array
    {

        $data['reporter_id'] = auth()->id();

        $data['created_by'] = auth()->id();

        $data['status'] = NewsStatus::Draft;
        
        return $data;
    }
}
