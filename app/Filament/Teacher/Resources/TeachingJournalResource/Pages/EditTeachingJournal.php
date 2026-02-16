<?php

namespace App\Filament\Teacher\Resources\TeachingJournalResource\Pages;

use App\Filament\Teacher\Resources\TeachingJournalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTeachingJournal extends EditRecord
{
  protected static string $resource = TeachingJournalResource::class;

  /**
   * Saat halaman edit dibuka, load data attendance
   * dari relasi studentAttendances ke dalam repeater.
   */
  protected function mutateFormDataBeforeFill(array $data): array
  {
    $data['attendance'] = $this->record->studentAttendances
      ->load('student')
      ->map(fn($att) => [
        'student_id' => (string) $att->student_id,
        'student_name' => $att->student->student_name ?? '',
        'status' => $att->status,
      ])
      ->toArray();

    return $data;
  }

  /**
   * Setelah record diupdate, sync data presensi.
   */
  protected function afterSave(): void
  {
    $attendanceData = $this->data['attendance'] ?? [];

    // Hapus data lama, lalu insert ulang
    $this->record->studentAttendances()->delete();

    foreach ($attendanceData as $item) {
      $this->record->studentAttendances()->create([
        'student_id' => $item['student_id'],
        'status' => $item['status'],
      ]);
    }
  }

  protected function getHeaderActions(): array
  {
    return [
      Actions\DeleteAction::make(),
    ];
  }
}
