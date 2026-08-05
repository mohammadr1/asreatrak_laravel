<?php

namespace App\Providers\Filament;

use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\ServiceProvider;

class MediaAssetProvider extends ServiceProvider
{
    public function boot(): void
    {
        // FilamentAsset::register([
        //     Js::make(
        //         'media-editor',
        //         resource_path('js/media/media-editor.js')
        //     ),
        // ]);
    }
}