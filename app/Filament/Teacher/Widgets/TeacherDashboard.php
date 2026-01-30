<?php

namespace App\Filament\Teacher\Widgets;

use App\Models\Activity;
use App\Models\Classes;
use App\Models\Student;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class TeacherDashboard extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = Auth::user();
        $teacher = $user?->teacher;

        if (!$teacher) {
            return [
                Stat::make('Error', 'Akun tidak terhubung dengan data Guru')
                    ->color('danger'),
            ];
        }

        // Ambil maksimal 3 santri binaan
        $mentoredStudents = $teacher->binaan()
            ->with(['student.memorizes' => function ($query) {
                // Optimasi: ambil data hanya yang dibutuhkan untuk trend
                $query->select('id_student', 'created_at');
            }])
            ->has('student') // Pastikan ada datanya
            ->take(3)
            ->get();

        $stats = [];

        foreach ($mentoredStudents as $mentor) {
            $student = $mentor->student;
            
            // Hitung total hafalan (per baris/setoran)
            $totalSetoran = $student->memorizes->count();

            // Hitung trend 7 hari terakhir
            $trendData = $student->memorizes
                ->where('created_at', '>=', now()->subDays(7))
                ->groupBy(fn ($item) => $item->created_at->format('Y-m-d'))
                ->map(fn ($items) => $items->count())
                ->toArray();
            
            // Urutkan berdasarkan tanggal (array keys)
            ksort($trendData);

            // Normalisasi ke array sederhana untuk chart, isi 0 jika kosong
            // Jika ingin benar-benar 7 hari fix:
            $chartValues = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $chartValues[] = $trendData[$date] ?? 0;
            }

            // Ambil setoran terakhir
            $lastMemorize = $student->memorizes()->latest()->first();
            $desc = $lastMemorize 
                ? 'Terakhir: ' . Carbon::parse($lastMemorize->created_at)->diffForHumans()
                : 'Belum ada setoran';

            $stats[] = Stat::make($student->student_name, $totalSetoran . ' Setoran')
                ->description($desc)
                ->descriptionIcon('heroicon-m-clock')
                ->chart($chartValues)
                ->color('primary')
                ->icon('heroicon-o-user');
        }

        // Jika kurang dari 3 santri, bisa tambahkan informasi Kegiatan atau Slot kosong
        // Sesuai request "maksimal 3 santri", jika hanya ada 1 atau 2, kita tampilkan itu saja.
        // Namun jika user ingin tetap 3 kolom terisi, kita bisa tambahkan placeholder.
        // Tapi "percantik dashboard" biasanya berarti "tampilkan data yang relevan".
        
        // Selalu tampilkan Widget Kegiatan
        $upcomingActivity = Activity::whereDate('activity_date', '>=', now())
            ->orderBy('activity_date', 'asc')
            ->first();
            
        // Fallback ke activity terakhir jika tidak ada yang akan datang, agar tidak kosong
        $activity = $upcomingActivity ?? Activity::latest('activity_date')->first();

        $stats[] = Stat::make('Kegiatan Pondok', $activity ? $activity->activity_name : 'Tidak Ada Kegiatan')
            ->description($activity ? Carbon::parse($activity->activity_date)->format('d M Y') : '-')
            ->descriptionIcon('heroicon-m-calendar')
            ->color($upcomingActivity ? 'success' : 'gray') // Hijau jika akan datang, abu-abu jika lewat
            ->icon('heroicon-o-calendar-days');
        
        return $stats;
    }
}
