<?php

namespace Database\Seeders;

use App\Models\Archive;
use App\Models\Certificate;
use App\Models\Classes;
use App\Models\Memorize;
use App\Models\Student;
use App\Models\Surah;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Activity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Storage;

class DummyCompleteDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Pastikan ada class dan ortu (parent)
        $class = Classes::first() ?? Classes::create(['class_name' => 'Kelas Dummy']);
        
        $parent = User::where('role_id', '3')->first(); 
        if (!$parent) {
            $parent = User::create([
                'name' => 'Ortu Dummy',
                'email' => 'ortu@dummy.com',
                'password' => bcrypt('password'),
                'role_id' => '3',
            ]);
        }
        
        $teacher = Teacher::first();
        if (!$teacher) {
             $teacherUser = User::firstOrCreate(['email' => 'guru@dummy.com'], [
                 'name' => 'Guru Dummy',
                 'password' => bcrypt('password'),
                 'role_id' => '2',
             ]);
             $teacher = Teacher::create(['id_users' => $teacherUser->id, 'nip' => '123456789']);
        }
        
        $surahs = Surah::take(5)->get();
        if ($surahs->isEmpty()) {
            $surahs = collect([
                Surah::create(['surah_name' => 'Al-Fatihah', 'total_ayat' => 7]),
                Surah::create(['surah_name' => 'Al-Baqarah', 'total_ayat' => 286]),
                Surah::create(['surah_name' => 'Ali \'Imran', 'total_ayat' => 200]),
                Surah::create(['surah_name' => 'An-Nisa\'', 'total_ayat' => 176]),
                Surah::create(['surah_name' => 'Al-Ma\'idah', 'total_ayat' => 120]),
            ]);
        }
        
        // Setup foto profil dummy (kopi dari default_pp.png)
        $profilePath = 'profile/dummy_profile.png';
        if (!Storage::disk('public')->exists($profilePath)) {
            Storage::disk('public')->makeDirectory('profile');
            $source = public_path('assets/default_pp.png');
            if (file_exists($source)) {
                Storage::disk('public')->put($profilePath, file_get_contents($source));
            }
        }
        
        // Setup file dummy arsip/sertifikat
        $dummyPdfPath = 'arsip/dummy_file.pdf';
        if (!Storage::disk('public')->exists($dummyPdfPath)) {
            Storage::disk('public')->makeDirectory('arsip');
            Storage::disk('public')->put($dummyPdfPath, 'Dummy PDF content');
        }
        $dummyCertPath = 'certificates/dummy_cert.pdf';
        if (!Storage::disk('public')->exists($dummyCertPath)) {
            Storage::disk('public')->makeDirectory('certificates');
            Storage::disk('public')->put($dummyCertPath, 'Dummy Cert content');
        }

        // 1. Buat 10 Santri
        $students = [];
        for ($i = 0; $i < 10; $i++) {
            $student = Student::create([
                'student_name' => $faker->name,
                'nisn' => $faker->unique()->numerify('##########'),
                'parent' => $parent->id,
                'class_id' => $class->id,
                'profile' => $profilePath,
                'tanggal_lahir' => $faker->date('Y-m-d', '-10 years'),
            ]);
            $students[] = $student;
            
            // 2. Masing-masing santri hafalan 5 kali
            for ($j = 0; $j < 5; $j++) {
                $surah = $surahs->random();
                Memorize::create([
                    'id_student' => $student->id,
                    'id_teacher' => $teacher->id,
                    'id_surah' => $surah->id,
                    'from' => 1,
                    'to' => rand(2, 5),
                    'makharijul_huruf' => $faker->randomElement(['A', 'B', 'C']),
                    'shifatul_huruf' => $faker->randomElement(['A', 'B', 'C']),
                    'ahkamul_qiroat' => $faker->randomElement(['A', 'B', 'C']),
                    'ahkamul_waqfi' => $faker->randomElement(['A', 'B', 'C']),
                    'qowaid_tafsir' => $faker->randomElement(['A', 'B', 'C']),
                    'tarjamatul_ayat' => $faker->randomElement(['A', 'B', 'C']),
                    'juz' => 'Juz 30',
                    'approved_by' => $teacher->id,
                    'complete' => 1,
                    'created_at' => now()->subDays(rand(1, 30)), // beri selisih tanggal
                ]);
            }
            
            // 3. Sertifikat per santri
            Certificate::create([
                'student_id' => $student->id,
                'title' => 'Sertifikat Hafalan - ' . $student->student_name,
                'description' => 'Sertifikat pencapaian hafalan Al-Qur\'an',
                'file_path' => $dummyCertPath,
                'issued_date' => now()->subDays(rand(1, 10)),
            ]);
        }
        
        // 4. Data Dummy Arsip
        for ($k = 0; $k < 5; $k++) {
            Archive::create([
                'title' => 'Arsip Dokumen Laporan ' . ($k + 1),
                'file_path' => $dummyPdfPath,
            ]);
        }
        
        // 5. Data Dummy Kegiatan
        for ($l = 0; $l < 5; $l++) {
            Activity::create([
                'activity_name' => 'Kegiatan Pondok ' . ($l + 1),
                'description' => $faker->paragraph(),
                'keterangan' => $faker->randomElement(['Wajib Hadir', 'Tidak Wajib Hadir']),
                'activity_date' => now()->addDays(rand(1, 14))->format('Y-m-d'),
            ]);
        }
        
        $this->command->info('Dummy data santri, hafalan (5x), sertifikat, arsip, dan kegiatan berhasil dibuat!');
    }
}
