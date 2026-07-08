<?php

namespace App\Filament\Resources\ActivityResource\Pages;

use App\Filament\Resources\ActivityResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Filament\Actions;
use App\Services\FonnteService;
use App\Jobs\SendWaBroadcastJob;
use App\Models\User;
use Carbon\Carbon;

class CreateActivity extends CreateRecord
{
  protected static string $resource = ActivityResource::class;

  public function getTitle(): string
  {
    return 'Tambah Kegiatan';
  }

  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }

  // Kalau mau, afterCreate cukup buat notif saja
  protected function afterCreate(): void
  {
    Notification::make()
      ->title('Berhasil')
      ->body('Berhasil menambahkan kegiatan baru')
      ->success()
      ->send();
  }

  // 🔥 Tombol di bawah form (Create, Create & Kirim WA, Cancel)
  protected function getFormActions(): array
  {
    return [
      $this->getCreateFormAction(),                // tombol Create biasa
      $this->getCreateAnotherFormAction(),         // tombol Create & create another (kalau mau)
      $this->getCancelFormAction(),                // tombol Cancel

      Actions\Action::make('createAndSendWa')
        ->label('Create & Kirim WA')
        ->icon('heroicon-o-paper-airplane')
        ->color('success')
        ->requiresConfirmation()
        ->action(function (FonnteService $fonnte) {

          // 1. Simpan kegiatan dulu
          $this->create();          // ini method bawaan CreateRecord
          $activity = $this->record;

          // 2. Cek ada wali yang punya nomor WA
          $waliCount = User::where('role_id', 3)
            ->whereNotNull('phone')
            ->count();

          if ($waliCount === 0) {
            Notification::make()
              ->title('Kegiatan dibuat, tapi tidak ada wali yang memiliki nomor WhatsApp.')
              ->warning()
              ->send();

            return;
          }

          // 3. Dispatch Job ke queue — browser TIDAK perlu menunggu
          //    Random delay antar kiriman (10–25 dtk) dihandle di dalam Job
          SendWaBroadcastJob::dispatch(
            activityName: $activity->activity_name,
            activityDate: $activity->activity_date,
            description:  $activity->description,
            keterangan:   $activity->keterangan,
          );

          Notification::make()
            ->title("Kegiatan dibuat! WA sedang dikirim ke {$waliCount} wali santri di background.")
            ->body('Proses pengiriman berjalan otomatis. Cek log jika ingin memantau.')
            ->success()
            ->send();

          // 4. Balik ke index
          $this->redirect(ActivityResource::getUrl('index'));
        }),
    ];
  }
}
