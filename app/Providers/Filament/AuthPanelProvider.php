<?php

namespace App\Providers\Filament;

use App\Http\Middleware\RedirectByRole;
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
use Illuminate\Support\HtmlString;
use Swis\Filament\Backgrounds\FilamentBackgroundsPlugin;
use Swis\Filament\Backgrounds\ImageProviders\MyImages;

class AuthPanelProvider extends PanelProvider
{
  public function panel(Panel $panel): Panel
  {
    return $panel
      ->id('auth')
      ->path('auth')
      ->brandLogo(new HtmlString('
          <div class="flex items-center justify-center gap-4 mb-10">
              <img src="' . asset('img/logo-darutafsir.png') . '" alt="Logo" style="height: 56px;" class="w-auto mb-5">
          </div>
      '))
      ->brandName('Daruttafsir')
      ->favicon(asset('img/logo-darutafsir.png'))
      ->login(\App\Filament\Pages\Auth\Login::class)
      ->colors([
        'primary' => Color::hex('#E5077C'),
      ])
      ->plugins(
        [
          ChangePasswordPlugin::make(),
          FilamentBackgroundsPlugin::make()
            ->imageProvider(MyImages::make()->directory('assets/admin')),
        ]
      )
      ->discoverResources(in: app_path('Filament/Auth/Resources'), for: 'App\\Filament\\Auth\\Resources')
      ->discoverPages(in: app_path('Filament/Auth/Pages'), for: 'App\\Filament\\Auth\\Pages')
      ->pages([
        Pages\Dashboard::class,
      ])
      ->discoverWidgets(in: app_path('Filament/Auth/Widgets'), for: 'App\\Filament\\Auth\\Widgets')
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
        RedirectByRole::class
      ])
      ->renderHook(
        'panels::head.end',
        fn() => new HtmlString('
          <style>
            .fi-simple-main.fi-simple-main.fi-simple-main {
              background-color: rgba(255, 255, 255, 0.45) !important;
              border: 1px solid rgba(255, 255, 255, 0.25) !important;
              box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.08) !important;
            }
            .dark .fi-simple-main.fi-simple-main.fi-simple-main {
              background-color: rgba(24, 24, 27, 0.55) !important;
              border: 1px solid rgba(255, 255, 255, 0.1) !important;
              box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37) !important;
            }
            .fi-simple-main.fi-simple-main.fi-simple-main:before {
              backdrop-filter: blur(20px) !important;
              -webkit-backdrop-filter: blur(20px) !important;
            }
          </style>
        ')
      )
      ->renderHook(
        'panels::auth.login.form.after',
        fn() => new HtmlString('
          <div class="flex flex-col items-center w-full mt-4 gap-3">
            <a href="/lupa-password" class="text-sm font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300 transition-colors duration-200">
              Lupa Password?
            </a>
            <a href="/" class="flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 transition-colors duration-200">
              <span>&larr;</span>
              <span>Kembali ke Beranda</span>
            </a>
          </div>
        ')
      )
      ->authMiddleware([
        Authenticate::class,
      ]);
  }
}
