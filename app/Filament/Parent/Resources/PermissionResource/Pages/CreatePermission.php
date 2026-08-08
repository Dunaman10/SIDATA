<?php

namespace App\Filament\Parent\Resources\PermissionResource\Pages;

use App\Filament\Parent\Resources\PermissionResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePermission extends CreateRecord
{
  protected static string $resource = PermissionResource::class;

  public function getTitle(): string
  {
    return 'Buat Perizinan';
  }

  protected function mutateFormDataBeforeCreate(array $data): array
  {
    $user = auth()->user();

    $data['id_parent'] = $user->id;
    if (empty($data['id_student'])) {
      $student = $user->students->first();
      $data['id_student'] = $student ? $student->id : null;
    }

    return $data;
  }

  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }

  protected function afterCreate(): void
  {
    Notification::make()
      ->title('Berhasil')
      ->body('Berhasil meminta perizinan')
      ->success()
      ->send();
  }
}
