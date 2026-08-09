<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Widgets\UserOverview;
use App\Models\Role;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
  protected static string $resource = UserResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\CreateAction::make('create')
        ->label('Tambah Pengguna'),
    ];
  }

  protected function getHeaderWidgets(): array
  {
    return [
      UserOverview::class
    ];
  }

  public function getTabs(): array
  {
    $roles = Role::withCount('users')->get();

    $tabs = [
      'semua' => Tab::make('Semua')
        ->badge(User::count())
        ->icon('heroicon-o-users'),
    ];

    foreach ($roles as $role) {
      $label = match ($role->role_name) {
        'orang_tua' => 'Orang Tua',
        'guru' => 'Guru',
        'admin' => 'Admin',
        'keamanan' => 'Keamanan',
        default => ucfirst($role->role_name),
      };

      $icon = match ($role->role_name) {
        'orang_tua' => 'heroicon-o-heart',
        'guru' => 'heroicon-o-academic-cap',
        'admin' => 'heroicon-o-shield-check',
        'keamanan' => 'heroicon-o-lock-closed',
        default => 'heroicon-o-user',
      };

      $tabs[$role->role_name] = Tab::make($label)
        ->badge($role->users_count)
        ->icon($icon)
        ->modifyQueryUsing(fn (Builder $query) => $query->where('role_id', $role->id));
    }

    return $tabs;
  }
}
