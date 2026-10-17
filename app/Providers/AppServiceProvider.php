<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\ServiceProvider;
use App\Models\News;
use App\Observers\NewsObserver;
use App\Models\Media;
use App\Observers\MediaObserver;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;

use App\Support\ViteAsset;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

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
        // Keep Livewire's temporary upload ceiling large enough for the configured image limit.
        // The image picker and media service enforce the actual per-image limit.
        if (config('livewire.temporary_file_upload.rules') === null) {
            config(['livewire.temporary_file_upload.rules' => [
                'required', 'file', 'max:'.max(12288, (int) ceil(config('media.image.max_size') / 1024)),
            ]]);
        }
        
        News::observe(NewsObserver::class);
        Media::observe(MediaObserver::class);


        // اسم فایل را با فایل واقعی نوار بالای خودتان یکی کنید
        View::composer('*', function ($view) {
            $view->with('navCategories', Cache::remember(
                'nav.categories',
                now()->addHour(),
                fn () => Category::orderBy('id')->get(['id', 'name', 'slug'])
            ));
        });
    }
}
