<?php


namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use App\Http\Middleware\OnlyTeacher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Filament\Http\Middleware\Authenticate;
use App\Filament\Teacher\Pages\ClassDetail;
use App\Models\Classes;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Hardikkhorasiya09\ChangePassword\ChangePasswordPlugin;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class TeacherPanelProvider extends PanelProvider
{
  public function panel(Panel $panel): Panel
  {
    return $panel
      // ->darkMode(false)
      ->brandName('Darutafsir')
      ->favicon(asset('img/logo-darutafsir.png'))
      ->id('teacher')
      ->path('teacher')
      ->login()
      ->databaseNotifications()
      ->colors([
        'primary' => Color::hex('#E5077C'),
      ])
      ->plugins([
          ChangePasswordPlugin::make(),
          FilamentApexChartsPlugin::make(),
      ])
      ->discoverResources(in: app_path('Filament/Teacher/Resources'), for: 'App\\Filament\\Teacher\\Resources')
      ->discoverPages(in: app_path('Filament/Teacher/Pages'), for: 'App\\Filament\\Teacher\\Pages')
      ->pages([
        Pages\Dashboard::class,
        ClassDetail::class, // Pastikan ini sesuai dengan namespace dan nama kelas
      ])
      ->discoverWidgets(in: app_path('Filament/Teacher/Widgets'), for: 'App\\Filament\\Teacher\\Widgets')
      ->widgets([
        // Widgets\AccountWidget::class,
        // Widgets\FilamentInfoWidget::class,
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
        OnlyTeacher::class,
      ])
      ->authMiddleware([
        Authenticate::class,
      ]);
  }
}
