<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Patch semua teaching_journals yang schedule_id-nya masih NULL
        // dengan mencari schedule yang cocok berdasarkan teacher_id + classes_id + lesson_id
        $journals = DB::table('teaching_journals')
            ->whereNull('schedule_id')
            ->get();

        foreach ($journals as $journal) {
            $schedule = DB::table('schedules')
                ->where('teacher_id', $journal->teacher_id)
                ->where('classes_id', $journal->classes_id)
                ->where('lesson_id', $journal->lesson_id)
                ->first();

            if ($schedule) {
                DB::table('teaching_journals')
                    ->where('id', $journal->id)
                    ->update(['schedule_id' => $schedule->id]);
            }
        }
    }

    public function down(): void
    {
        // Tidak perlu rollback — ini patch data
    }
};
