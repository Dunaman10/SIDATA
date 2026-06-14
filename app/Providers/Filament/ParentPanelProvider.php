<?php

namespace App\Providers\Filament;

use App\Filament\Parent\Widgets\ChildMemorizationChart;
use App\Filament\Parent\Widgets\ParentDashboard;
use App\Http\Middleware\OnlyParent;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;
use Illuminate\Support\HtmlString;

class ParentPanelProvider extends PanelProvider
{
  public function panel(Panel $panel): Panel
  {
    return $panel
      ->brandLogo(new HtmlString('
          <div class="flex items-center gap-2">
              <img src="' . asset('img/logo-darutafsir.png') . '" alt="Logo" class="h-9 w-auto">
              <span class="text-xl font-bold tracking-tight text-gray-950 dark:text-white">Daruttafsir</span>
          </div>
      '))
      ->brandName('Daruttafsir')
      ->favicon(asset('img/logo-darutafsir.png'))
      ->id('parent')
      ->path('parent')
      ->login()
      ->colors([
        'primary' => Color::hex('#E5077C'),
      ])
      ->userMenuItems([
        'profile' => \Filament\Navigation\MenuItem::make()
          ->label('Edit Profile')
          ->url(fn(): string => EditProfilePage::getUrl())
          ->icon('heroicon-m-user-circle'),
      ])
      ->plugins(
        [
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
            ]),
        ]
      )
      ->discoverResources(in: app_path('Filament/Parent/Resources'), for: 'App\\Filament\\Parent\\Resources')
      ->discoverPages(in: app_path('Filament/Parent/Pages'), for: 'App\\Filament\\Parent\\Pages')
      ->pages([
        Pages\Dashboard::class,
      ])
      ->discoverWidgets(in: app_path('Filament/Parent/Widgets'), for: 'App\\Filament\\Parent\\Widgets')
      ->widgets([
        ParentDashboard::class,
        ChildMemorizationChart::class,
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
        OnlyParent::class,
      ])
      ->authMiddleware([
        Authenticate::class,
      ]);
  }
}
