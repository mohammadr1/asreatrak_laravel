<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Assets\Js;
use Illuminate\Support\Facades\Vite;
use Filament\View\PanelsRenderHook;

class AdminPanelProvider extends PanelProvider
{


    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('backroom-entry')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->colors([
                'primary' => [
                    50  => '#eef8fc',
                    100 => '#d6f0f9',
                    200 => '#b0e1f3',
                    300 => '#78cbea',
                    400 => '#3baedd',
                    500 => '#0d5b87', // رنگ لایت مدنظر شما
                    600 => '#0a496f', // رنگ اصلی مدنظر شما
                    700 => '#083c5c',
                    800 => '#0a324b',
                    900 => '#0f2233', // سرمه‌ای تیره مدنظر شما
                    950 => '#08141f',
                ],
                'gray' => [
                    50  => '#f5f7f9',
                    100 => '#e8ecf0',
                    200 => '#d4dce3',
                    300 => '#b3c3d1',
                    400 => '#8ba3b9',
                    500 => '#6d869f',
                    600 => '#556d85',
                    700 => '#44566a',
                    800 => '#3a4756',
                    900 => '#0f2233',
                    950 => '#08141f',
                ],
            ])
            ->login()
            // ->colors([
            //     'primary' => Color::Amber,
            // ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            // راستچین کردن پنل 
            ->renderHook(
                'panels::body.start',
                fn () => '<script>document.documentElement.dir = "rtl";</script>'
            )
            ->authMiddleware([
                Authenticate::class,
            ])

            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => Vite::useHotFile(storage_path('vite.hot'))
                        ->withEntryPoints([
                            'resources/js/app.js',
                        ])
                        ->toHtml()
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => view('filament.partials.admin-assets')->render()
            );
    }
}
