<?php

namespace App\Filament\Teacher\Widgets;

use App\Models\Activity;
use App\Models\Memorize;
use App\Models\MentorStudent;
use App\Models\Schedule;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class TeacherDashboard extends BaseWidget
{
    protected static ?int $sort = 1;

    // Refresh setiap 30 detik
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $user = Auth::user();
        $teacher = $user?->teacher;

        if (!$teacher) {
            return [
                Stat::make('Error', 'Akun tidak terhubung dengan data Guru')
                    ->color('danger')
                    ->icon('heroicon-o-exclamation-triangle'),
            ];
        }

        // === 1. Total Santri Binaan ===
        $totalSantri = MentorStudent::where('id_teacher', $teacher->id)->count();
        $santriNames = MentorStudent::where('id_teacher', $teacher->id)
            ->with('student')
            ->get()
            ->pluck('student.student_name')
            ->filter()
            ->implode(', ');

        // === 2. Total Setoran Bulan Ini (santri binaan saja) ===
        $studentIds = MentorStudent::where('id_teacher', $teacher->id)
            ->pluck('id_student');

        $setoranBulanIni = Memorize::whereIn('id_student', $studentIds)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Trend setoran 7 hari terakhir
        $setoranTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $setoranTrend[] = Memorize::whereIn('id_student', $studentIds)
                ->whereDate('created_at', $date)
                ->count();
        }

        // Perbandingan dengan bulan lalu
        $setoranBulanLalu = Memorize::whereIn('id_student', $studentIds)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        $setoranDiff = $setoranBulanIni - $setoranBulanLalu;
        $setoranDesc = $setoranDiff >= 0
            ? "+{$setoranDiff} dari bulan lalu"
            : "{$setoranDiff} dari bulan lalu";

        // === 3. Kegiatan Mendatang ===
        $upcomingActivity = Activity::whereDate('activity_date', '>=', now())
            ->orderBy('activity_date', 'asc')
            ->first();

        $activityFallback = $upcomingActivity ?? Activity::latest('activity_date')->first();

        // === 4. Jadwal Hari Ini ===
        $dayMap = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];
        $today = $dayMap[now()->dayOfWeek] ?? now()->translatedFormat('l');

        $jadwalHariIni = Schedule::where('teacher_id', $teacher->id)
            ->where('day_of_week', $today)
            ->with(['lesson', 'classes'])
            ->orderBy('start_time')
            ->get();

        $totalJadwalHariIni = $jadwalHariIni->count();

        $nextSchedule = $jadwalHariIni
            ->filter(fn($s) => Carbon::parse($s->start_time)->format('H:i') >= now()->format('H:i'))
            ->first();

        $jadwalDesc = $nextSchedule
            ? 'Berikutnya: ' . optional($nextSchedule->lesson)->name . ' (' . Carbon::parse($nextSchedule->start_time)->format('H:i') . ')'
            : ($totalJadwalHariIni > 0 ? 'Semua jadwal sudah selesai hari ini' : 'Tidak ada jadwal hari ini');

        return [
            Stat::make('Santri Binaan', $totalSantri . ' Santri')
                ->description($santriNames ?: 'Belum ada santri binaan')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary')
                ->icon('heroicon-o-user-group'),

            Stat::make('Setoran Bulan Ini', $setoranBulanIni . ' Setoran')
                ->description($setoranDesc)
                ->descriptionIcon($setoranDiff >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart($setoranTrend)
                ->color($setoranDiff >= 0 ? 'success' : 'warning')
                ->icon('heroicon-o-book-open'),

            Stat::make('Kegiatan Mendatang', $activityFallback ? $activityFallback->activity_name : 'Tidak Ada')
                ->description(
                    $activityFallback
                        ? Carbon::parse($activityFallback->activity_date)->translatedFormat('d F Y')
                        : 'Belum ada kegiatan'
                )
                ->descriptionIcon('heroicon-m-calendar')
                ->color($upcomingActivity ? 'success' : 'gray')
                ->icon('heroicon-o-calendar-days'),

            Stat::make('Jadwal Hari Ini (' . $today . ')', $totalJadwalHariIni . ' Sesi')
                ->description($jadwalDesc)
                ->descriptionIcon('heroicon-m-clock')
                ->color($totalJadwalHariIni > 0 ? 'info' : 'gray')
                ->icon('heroicon-o-clock'),
        ];
    }
}
