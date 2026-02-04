<?php

namespace App\Providers\Filament;

use App\Filament\Keamanan\Resources\KeamananResource\Widgets\KeamananDashboard;
use App\Http\Middleware\OnlyKeamanan;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Hardikkhorasiya09\ChangePassword\ChangePasswordPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class KeamananPanelProvider extends PanelProvider
{
  public function panel(Panel $panel): Panel
  {
    return $panel
      ->id('keamanan')
      ->path('keamanan')
      ->favicon(asset('img/logo-darutafsir.png'))
      ->brandName('Darutafsir')
      ->login()
      ->databaseNotifications()
      ->colors([
        'primary' => Color::hex('#E5077C'),
      ])
      ->plugins(
        [
          ChangePasswordPlugin::make(),
        ]
      )
      ->discoverResources(in: app_path('Filament/Keamanan/Resources'), for: 'App\\Filament\\Keamanan\\Resources')
      ->discoverPages(in: app_path('Filament/Keamanan/Pages'), for: 'App\\Filament\\Keamanan\\Pages')
      ->pages([
        Pages\Dashboard::class,
      ])
      ->discoverWidgets(in: app_path('Filament/Keamanan/Widgets'), for: 'App\\Filament\\Keamanan\\Widgets')
      ->widgets([
        KeamananDashboard::class,
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
        OnlyKeamanan::class,
      ])
      ->authMiddleware([
        Authenticate::class,
      ]);
  }
}
