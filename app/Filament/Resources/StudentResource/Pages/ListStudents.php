<?php

namespace App\Filament\Resources\StudentResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\StudentResource;
use App\Filament\Resources\StudentResource\Widgets\ViewSantri;

class ListStudents extends ListRecords
{
  protected static string $resource = StudentResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\CreateAction::make('create')
        ->label('Tambah Santri')
        ->outlined()
        ->color('gray'),
    ];
  }
  protected function getHeaderWidgets(): array
  {
    return [
      ViewSantri::class, ///widget di halaman data santri
    ];
  }


  protected function getDeletedNotification(): ?Notification
  {
    return null; // Menonaktifkan notifikasi 'Deleted' bawaan di level Page
  }

  protected function getBulkDeletedNotification(): ?Notification
  {
    return null; // Menonaktifkan notifikasi 'Bulk Deleted' bawaan di level Page
  }
}
