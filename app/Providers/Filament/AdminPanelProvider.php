<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Auth\Login;
use App\Filament\Admin\Pages\ChangePassword;
use App\Filament\Admin\Pages\Dashboard;
use App\Filament\Admin\Pages\ExecutiveDirectorSettings;
use App\Filament\Admin\Pages\MenuBuilderPro;
use App\Filament\Admin\Pages\TemplateVariables;
use App\Filament\Admin\Pages\VisitorStatistics;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->authGuard('tenant')
            ->brandName('لوحة الجمعية')
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => Blade::render("@vite(['resources/css/app.css', 'resources/js/app.js'])"),
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => new HtmlString('
                    <style>
                        .fi-sidebar { background-color: #ffffff !important; border-left: 1px solid #cbd5e1 !important; }
                        .fi-sidebar-group-label { color: #1e293b !important; font-weight: 800 !important; font-size: .95rem !important; padding-bottom: 8px !important; margin-top: 25px !important; margin-bottom: 8px !important; border-bottom: 2px solid #b48600 !important; display: block; width: 90%; }
                        .fi-sidebar-item { border-bottom: 1px solid #f1f5f9 !important; }
                        .fi-sidebar-item-button { border-radius: 4px !important; padding: 10px 12px !important; margin: 2px 8px !important; }
                        .fi-sidebar-item-active .fi-sidebar-item-button { background-color: #385833 !important; color: #ffffff !important; border-right: 4px solid #b48600 !important; }
                    </style>
                '),
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => view('filament.admin.partials.sidebar-icon-colors'),
            )
            ->navigationGroups([
                NavigationGroup::make()->label('لوحة التحكم'),
                NavigationGroup::make()->label('لوحة التحكم والمتابعة'),
                NavigationGroup::make()->label('إدارة المحتوى'),
                NavigationGroup::make()->label('الحوكمة والوثائق'),
                NavigationGroup::make()->label('خدمات المستفيدين'),
                NavigationGroup::make()->label('إعدادات الموقع'),
            ])
            ->discoverResources(
                in: app_path('Filament/Admin/Resources'),
                for: 'App\\Filament\\Admin\\Resources'
            )
            ->pages([
                Dashboard::class,
                VisitorStatistics::class,
                ChangePassword::class,
                MenuBuilderPro::class,
                TemplateVariables::class,
                ExecutiveDirectorSettings::class,
            ])
            ->discoverWidgets(
                in: app_path('Filament/Admin/Widgets'),
                for: 'App\\Filament\\Admin\\Widgets'
            )
            ->plugins([
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
