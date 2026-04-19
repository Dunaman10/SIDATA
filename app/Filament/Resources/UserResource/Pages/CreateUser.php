<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Teacher;
use App\Models\User;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateUser extends CreateRecord
{
  protected static string $resource = UserResource::class;

  public function getTitle(): string
  {
    return 'Buat User Baru'; // Gunakan metode ini untuk mengatur heading
  }

  protected function getRedirectUrl(): string
  {
    return UserResource::getUrl('index'); // Redirect ke halaman daftar student
  }

  protected function handleRecordCreation(array $data): Teacher
  {
    $user = User::create([
      'name' => $data['name'],
      'email' => $data['email'],
      'phone' => $data['phone'],
      'password' => Hash::make($data['password']),
      'role_id' => 2
    ]);

    return Teacher::create([
      'id_users' => $user->id
    ]);
  }

  protected function afterCreate(): void
  {
    Notification::make()
      ->title('Berhasil')
      ->body('Berhasil menambahkan pengguna')
      ->success()
      ->send();
  }

  protected function getCreateFormAction(): Action
  {
    return Actions\CreateAction::make()
      ->label('Save')
      ->submit('create')
      ->color('primary');
  }

  // protected function getCreateAnotherFormAction(): Action
  // {
  //   return Action::make('createAnother')
  //     ->hidden();
  // }

  protected function getCreatedNotification(): ?Notification
  {
    return null;
  }
}
