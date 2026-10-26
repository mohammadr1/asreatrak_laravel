<?php

namespace App\Providers;

use App\Enums\NewsStatus;
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
use Illuminate\Support\Str;
use Illuminate\Support\HtmlString;
use DOMDocument;

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
        Str::macro('sanitizeHtml', function (?string $html): string {
            $html = $html ?? '';
            if ($html === '') {
                return '';
            }

            $document = new DOMDocument('1.0', 'UTF-8');
            $previous = libxml_use_internal_errors(true);
            try {
                $document->loadHTML('<?xml encoding="UTF-8"><html><body>'.$html.'</body></html>', LIBXML_NONET);
            } finally {
                libxml_clear_errors();
                libxml_use_internal_errors($previous);
            }

            $allowedTags = ['a', 'p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'ul', 'ol', 'li', 'blockquote', 'h2', 'h3', 'h4', 'img', 'iframe', 'div', 'span'];
            $allowedAttributes = ['href', 'src', 'alt', 'title', 'width', 'height', 'class', 'loading', 'allow', 'allowfullscreen', 'referrerpolicy', 'sandbox'];
            $body = $document->getElementsByTagName('body')->item(0);

            $sanitize = function ($node) use (&$sanitize, $allowedTags, $allowedAttributes): void {
                foreach (iterator_to_array($node->childNodes) as $child) {
                    if ($child instanceof \DOMElement) {
                        if (! in_array(strtolower($child->tagName), $allowedTags, true)) {
                            if (in_array(strtolower($child->tagName), ['script', 'style', 'iframe', 'object', 'embed'], true)) {
                                $child->parentNode?->removeChild($child);
                                continue;
                            }
                            while ($child->firstChild) {
                                $child->parentNode?->insertBefore($child->firstChild, $child);
                            }
                            $child->parentNode?->removeChild($child);
                            continue;
                        }

                        if (strtolower($child->tagName) === 'iframe') {
                            $source = $child->getAttribute('src');
                            if (! preg_match('~^https://(?:www\\.)?aparat\\.com/iframe/[^/?#]+~i', $source)) {
                                $child->parentNode?->removeChild($child);
                                continue;
                            }
                        }

                        foreach (iterator_to_array($child->attributes) as $attribute) {
                            $name = strtolower($attribute->name);
                            $value = trim($attribute->value);
                            if (! in_array($name, $allowedAttributes, true) || str_starts_with($name, 'on') || preg_match('/^(?:javascript|vbscript|data):/i', $value)) {
                                $child->removeAttribute($attribute->name);
                            }
                        }
                        $sanitize($child);
                    }
                }
            };

            $sanitize($body);
            $output = '';
            foreach ($body->childNodes as $child) {
                $output .= $document->saveHTML($child);
            }

            return new HtmlString($output);
        });

        // Keep Livewire's temporary upload ceiling large enough for the configured image limit.
        // The image picker and media service enforce the actual per-image limit.
        if (config('livewire.temporary_file_upload.rules') === null) {
            config(['livewire.temporary_file_upload.rules' => [
                'required', 'file', 'max:'.max(12288, (int) ceil(config('media.image.max_size') / 1024)),
            ]]);
        }
        
        News::observe(NewsObserver::class);
        Media::observe(MediaObserver::class);


        View::composer('*', function ($view) {
            $view->with('navMenus', Cache::remember(
                'nav.menus',
                now()->addHour(),
                fn () => \App\Models\NavigationMenu::query()
                    ->whereNull('parent_id')
                    ->where('is_active', true)
                    ->with(['category', 'children' => fn ($query) => $query
                        ->where('is_active', true)
                        ->with(['category', 'children' => fn ($children) => $children
                            ->where('is_active', true)
                            ->with(['category', 'children' => fn ($grandchildren) => $grandchildren
                                ->where('is_active', true)
                                ->with('category')
                                ->orderBy('sort_order')->orderBy('id')])
                            ->orderBy('sort_order')->orderBy('id')])
                        ->orderBy('sort_order')->orderBy('id')])
                    ->orderBy('sort_order')->orderBy('id')
                    ->get()
            ));
        });

    
        View::composer('Customer.layouts.breaking_bar', function ($view) {
            $latestNews = News::query()
                ->where('status', NewsStatus::Published)
                ->where(function ($query) {
                    $query->whereNull('published_at')
                        ->orWhere('published_at', '<=', now());
                })
                ->latest('published_at')
                ->take(10)
                ->get();

            $view->with('news', $latestNews);
        });

    }
}
