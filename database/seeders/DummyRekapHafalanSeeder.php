<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Memorize;
use App\Models\Student;
use Carbon\Carbon;

class DummyRekapHafalanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $studentIds = [3, 13, 14]; // Fadlan, Alvin, Alfin (Mentored by Alif Fauzan)
        $teacherId = 4; // Alif Fauzan (ID in teachers table)
        
        $predicates = ['A', 'B', 'C', 'D'];

        foreach ($studentIds as $studentId) {
            $student = Student::find($studentId);
            if (!$student) continue;

            $numRecords = rand(10, 20);

            for ($i = 0; $i < $numRecords; $i++) {
                // Random date within the last 5 months
                $createdAt = Carbon::now()->subDays(rand(1, 150));

                $qowaid_tafsir = $predicates[array_rand($predicates)];
                $tarjamatul_ayat = $predicates[array_rand($predicates)];

                Memorize::create([
                    'id_surah' => rand(1, 114), // Random surah
                    'id_student' => $studentId,
                    'id_teacher' => $teacherId,
                    'from' => rand(1, 5),
                    'to' => rand(6, 20),
                    'makharijul_huruf' => $predicates[array_rand($predicates)],
                    'shifatul_huruf' => $predicates[array_rand($predicates)],
                    'ahkamul_qiroat' => $predicates[array_rand($predicates)],
                    'ahkamul_waqfi' => $predicates[array_rand($predicates)],
                    'qowaid_tafsir' => $qowaid_tafsir,
                    'tarjamatul_ayat' => $tarjamatul_ayat,
                    'juz' => rand(1, 30),
                    'complete' => rand(0, 1),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
        }
    }
}
