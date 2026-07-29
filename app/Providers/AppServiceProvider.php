<?php

namespace App\Providers;

use App\Http\Responses\CustomLogoutResponse;
use App\Livewire\EditProfileForm;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Http\Responses\Auth\LogoutResponse;
use Filament\Tables\Columns\Layout\Panel;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    $this->app->singleton(LogoutResponse::class, CustomLogoutResponse::class);
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    if (
        request()->isSecure() ||
        strtolower(request()->header('x-forwarded-proto', '')) === 'https' ||
        request()->server('HTTPS') === 'on' ||
        str_starts_with(config('app.url'), 'https://') ||
        str_contains(request()->getHost(), 'santriqu.id') ||
        str_contains(request()->getHost(), 'herd.network') ||
        config('app.env') === 'production'
    ) {
        URL::forceScheme('https');
    }
    
    Livewire::component('edit_profile_form', EditProfileForm::class);
  }
}
