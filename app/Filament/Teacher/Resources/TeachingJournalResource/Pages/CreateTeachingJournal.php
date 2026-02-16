<?php

namespace App\Filament\Teacher\Resources\TeachingJournalResource\Pages;

use App\Filament\Teacher\Resources\TeachingJournalResource;
use App\Models\Teacher;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateTeachingJournal extends CreateRecord
{
  protected static string $resource = TeachingJournalResource::class;

  /**
   * Mutate form data sebelum disimpan ke teaching_journals.
   * Auto-set teacher_id dari guru yang sedang login.
   */
  protected function mutateFormDataBeforeCreate(array $data): array
  {
    $teacher = Teacher::where('id_users', Auth::id())->first();
    $data['teacher_id'] = $teacher?->id;
    $data['date'] = $data['date'] ?? now()->toDateString();

    return $data;
  }

  /**
   * Setelah record TeachingJournal dibuat,
   * simpan data presensi (attendance) ke tabel student_attendances.
   */
  protected function afterCreate(): void
  {
    $attendanceData = $this->data['attendance'] ?? [];

    foreach ($attendanceData as $item) {
      $this->record->studentAttendances()->create([
        'student_id' => $item['student_id'],
        'status' => $item['status'],
      ]);
    }
  }
}
