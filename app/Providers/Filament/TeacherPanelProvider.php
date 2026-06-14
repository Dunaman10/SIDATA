<?php


namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use App\Http\Middleware\OnlyTeacher;
use Filament\Http\Middleware\Authenticate;
use App\Filament\Teacher\Pages\ClassDetail;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Support\HtmlString;

class TeacherPanelProvider extends PanelProvider
{
  public function panel(Panel $panel): Panel
  {
    return $panel
      // ->darkMode(false)
      ->brandLogo(new HtmlString('
          <div class="flex items-center gap-2">
              <img src="' . asset('img/logo-darutafsir.png') . '" alt="Logo" class="h-9 w-auto">
              <span class="text-xl font-bold tracking-tight text-gray-950 dark:text-white">Daruttafsir</span>
          </div>
      '))
      ->brandName('Daruttafsir')
      ->favicon(asset('img/logo-darutafsir.png'))
      ->id('teacher')
      ->path('teacher')
      ->login()
      ->databaseNotifications()
      ->colors([
        'primary' => Color::hex('#E5077C'),
      ])
      ->userMenuItems([
        'profile' => \Filament\Navigation\MenuItem::make()
          ->label('Edit Profile')
          ->url(fn(): string => EditProfilePage::getUrl())
          ->icon('heroicon-m-user-circle'),
      ])
      ->plugins([
        FilamentApexChartsPlugin::make(),
        FilamentEditProfilePlugin::make()
          ->shouldRegisterNavigation(false)
          ->shouldShowEditProfileForm(false)
          ->shouldShowDeleteAccountForm(false)
          ->shouldShowAvatarForm(
              value: true,
              directory: 'avatars',
              rules: 'mimes:jpeg,png,jpg|max:1024'
          )
          ->customProfileComponents([
              \App\Livewire\EditProfileForm::class,
          ])
      ])
      ->discoverResources(in: app_path('Filament/Teacher/Resources'), for: 'App\\Filament\\Teacher\\Resources')
      ->discoverPages(in: app_path('Filament/Teacher/Pages'), for: 'App\\Filament\\Teacher\\Pages')
      ->pages([
        Pages\Dashboard::class,
        ClassDetail::class,
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
