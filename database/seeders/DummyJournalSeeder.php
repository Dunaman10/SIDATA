<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TeachingJournal;
use App\Models\Schedule;
use App\Models\Lesson;
use App\Models\Classes;
use App\Models\Teacher;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\StudentAttendance;
use Carbon\Carbon;

class DummyJournalSeeder extends Seeder
{
    public function run()
    {
        // Identify teacher (Alif Fauzan, User AF ID 179 -> Teacher ID 4)
        $teacher = Teacher::where('id', 4)->first();
        if (!$teacher) {
            $teacher = Teacher::first();
        }

        // Identify Academic Year (Semester Ganjil)
        $ay = AcademicYear::where('semester', 'Ganjil')->where('is_active', 1)->first();
        if (!$ay) {
            $ay = AcademicYear::find(1);
        }

        // 7. Define lessons and their schedules helper
        $teachingSchedules = [
            [
                'lesson_name' => 'Akhlaq',
                'class_name' => 'Kelas 1',
                'day' => 'Senin',
                'start' => '07:10:00',
                'end' => '08:50:00',
                'start_date' => Carbon::create(2025, 7, 7),
            ],
            [
                'lesson_name' => 'Mathematic',
                'class_name' => 'Kelas 4',
                'day' => 'Selasa',
                'start' => '08:50:00',
                'end' => '10:30:00',
                'start_date' => Carbon::create(2025, 7, 8),
            ],
            [
                'lesson_name' => 'Matan Bina',
                'class_name' => 'Kelas 2',
                'day' => 'Rabu',
                'start' => '10:30:00',
                'end' => '12:00:00',
                'start_date' => Carbon::create(2025, 7, 9),
            ]
        ];

        foreach ($teachingSchedules as $ts) {
            $l = Lesson::where('name', 'like', "%{$ts['lesson_name']}%")->first();
            $c = Classes::where('class_name', 'like', "%{$ts['class_name']}%")->first();

            if (!$l || !$c) continue;

            // Cleanup OLD dummy data for this teacher and lesson
            TeachingJournal::where('lesson_id', $l->id)->where('teacher_id', $teacher->id)->delete();

            // Schedule
            $sched = Schedule::updateOrCreate(
                [
                    'lesson_id' => $l->id,
                    'classes_id' => $c->id,
                    'teacher_id' => $teacher->id,
                ],
                [
                    'academic_year_id' => $ay->id,
                    'day_of_week' => $ts['day'],
                    'start_time' => $ts['start'],
                    'end_time' => $ts['end'],
                ]
            );

            // Generate 18 meetings
            $students = Student::where('class_id', $c->id)->get();

            for ($i = 1; $i <= 18; $i++) {
                $journal = TeachingJournal::create([
                    'schedule_id' => $sched->id,
                    'teacher_id' => $teacher->id,
                    'classes_id' => $c->id,
                    'lesson_id' => $l->id,
                    'date' => $ts['start_date']->copy()->addWeeks($i - 1),
                    'start_time' => $ts['start'],
                    'end_time' => $ts['end'],
                    'topic' => "Materi {$ts['lesson_name']} Pertemuan ke-{$i}",
                    'notes' => "Catatan untuk {$ts['lesson_name']} pertemuan ke-{$i}",
                    'photo' => null,
                ]);

                // Create random attendance
                foreach ($students as $student) {
                    StudentAttendance::create([
                        'teaching_journal_id' => $journal->id,
                        'student_id' => $student->id,
                        'status' => (rand(1, 10) > 1) ? 'Hadir' : 'Alfa', // Value must match options in Resource: Hadir, Sakit, Izin, Alfa
                    ]);
                }
            }
            echo "Successfully created 18 journals and attendance for {$ts['lesson_name']} for Teacher ID {$teacher->id}.\n";
        }
    }
}
