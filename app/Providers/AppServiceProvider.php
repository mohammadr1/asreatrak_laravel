<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\News;
use App\Observers\NewsObserver;
use App\Models\Media;
use App\Observers\MediaObserver;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;

use App\Support\ViteAsset;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(

            \App\Services\Media\Contracts\MediaServiceInterface::class,

            \App\Services\Media\MediaService::class

        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        
        News::observe(NewsObserver::class);
        Media::observe(MediaObserver::class);


    }
}
