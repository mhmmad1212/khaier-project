<?php

namespace App\Providers\Filament;

use App\Filament\Central\Pages\AssociationActivities;

use App\Filament\Central\Auth\Login;
use App\Filament\Central\Pages\AssociationsMonitoring;
use App\Filament\Central\Pages\Dashboard;
use App\Filament\Resources\AssociationResource;
use App\Filament\Resources\UserResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class CentralPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('central')
            ->path('khaier')
            ->login(Login::class)
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => Blade::render("@vite(['resources/css/app.css', 'resources/js/app.js'])"),
            )
            ->authGuard('web')
            ->brandName('لوحة المشرف')
            ->resources([
                AssociationResource::class,
                UserResource::class,
            ])
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(
                in: app_path('Filament/Central/Resources'),
                for: 'App\\Filament\\Central\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/Central/Pages'),
                for: 'App\\Filament\\Central\\Pages'
            )
            ->pages([
                Dashboard::class,
                AssociationsMonitoring::class,
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
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
