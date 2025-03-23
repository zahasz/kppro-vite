<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
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
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\Settings;
use App\Filament\Pages\Logs;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\RoleResource;
use App\Filament\Resources\PermissionResource;
use App\Filament\Resources\ContractorResource;
use App\Filament\Resources\InvoiceResource;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Pages\UserProfile;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Indigo,
                'danger' => Color::Rose,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
                'info' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->resources([
                UserResource::class,
                RoleResource::class,
                PermissionResource::class,
                ContractorResource::class,
                InvoiceResource::class,
            ])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
                Settings::class,
                Logs::class,
                UserProfile::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                StatsOverview::class,
                Widgets\AccountWidget::class,
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
            ])
            ->navigation(function () {
                return [
                    // Możesz dostosować kolejność i grupowanie - elementy zostaną wygenerowane automatycznie
                ];
            })
            ->navigationGroups([
                'System',
                'Kontrahenci',
                'Faktury',
                'Magazyn',
                'Użytkownicy',
            ])
            ->databaseNotifications()
            ->brandName('KPPro Admin')
            ->favicon(asset('images/favicon.ico'))
            ->maxContentWidth('full')
            ->sidebarFullyCollapsibleOnDesktop();
    }
}
