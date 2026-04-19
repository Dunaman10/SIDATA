<?php

namespace App\Filament\Teacher\Resources\TeachingJournalResource\Pages;

use App\Filament\Teacher\Resources\TeachingJournalResource;
use App\Models\Schedule;
use App\Models\Teacher;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateTeachingJournal extends CreateRecord
{
  protected static string $resource = TeachingJournalResource::class;

  /**
   * Mutate form data sebelum disimpan ke teaching_journals.
   * Auto-set teacher_id dan schedule_id dari guru yang sedang login.
   */
  protected function mutateFormDataBeforeCreate(array $data): array
  {
    $teacher = Teacher::where('id_users', Auth::id())->first();
    $data['teacher_id'] = $teacher?->id;
    $data['date'] = $data['date'] ?? now()->toDateString();

    // Auto-set schedule_id berdasarkan teacher + kelas + mata pelajaran
    // agar rekap presensi per tahun akademik bisa bekerja dengan benar
    if ($teacher && isset($data['classes_id']) && isset($data['lesson_id'])) {
      $schedule = Schedule::where('teacher_id', $teacher->id)
        ->where('classes_id', $data['classes_id'])
        ->where('lesson_id', $data['lesson_id'])
        ->first();

      $data['schedule_id'] = $schedule?->id;
    }

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
        'status'     => $item['status'],
      ]);
    }
  }
}

