<?php

namespace App\Filament\Widgets;

use App\Models\Activity;
use App\Models\Classes;
use App\Models\Memorize;
use App\Models\Student;
use App\Models\Surah;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminDashboard extends BaseWidget
{
  protected static ?int $sort = 1;

  protected function getStats(): array
  {
    $countGuru = Teacher::count();
    $countSiswa = Student::count();
    $countKelas = Classes::count();
    $countPengguna = User::count();
    $countHafalan = Memorize::count();
    $activity = Activity::latest('activity_date')->first();

    // Helper to get 7-day trend
    $getTrend = function ($model) {
      $data = $model::selectRaw('DATE(created_at) as date, count(*) as count')
        ->where('created_at', '>=', now()->subDays(7))
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->pluck('count')
        ->toArray();

      return count($data) < 2 ? array_merge([0, 0, 0, 0, 0], $data) : $data;
    };

    $memorizeTrend = $getTrend(Memorize::class);
    $studentTrend = $getTrend(Student::class);
    $userTrend = $getTrend(User::class);

    return [
      Stat::make('Total Pengajar', $countGuru)
        ->description('Guru aktif mengajar')
        ->descriptionIcon('heroicon-m-briefcase')
        ->color('success')
        ->icon('heroicon-o-briefcase'),

      Stat::make('Total Santri', $countSiswa)
        ->description('Total santri terdaftar')
        ->descriptionIcon('heroicon-m-academic-cap')
        ->color('primary')
        ->chart($studentTrend)
        ->icon('heroicon-o-academic-cap'),

      Stat::make('Total Kelas', $countKelas)
        ->description('Ruang kelas tersedia')
        ->descriptionIcon('heroicon-m-building-office-2')
        ->color('warning')
        ->icon('heroicon-o-building-library'),

      Stat::make('Total Hafalan Masuk', $countHafalan)
        ->description('Setoran hafalan santri')
        ->descriptionIcon('heroicon-m-microphone')
        ->chart($memorizeTrend)
        ->color('info')
        ->icon('heroicon-o-microphone'),

      Stat::make('Total Pengguna', $countPengguna)
        ->description('User terdaftar')
        ->color('secondary')
        ->chart($userTrend)
        ->icon('heroicon-o-users'),

      Stat::make('Kegiatan Terbaru', $activity ? $activity->activity_name : 'Tidak Ada')
        ->description($activity ? Carbon::parse($activity->activity_date)->format('d M Y') : 'Belum ada kegiatan')
        ->descriptionIcon('heroicon-m-calendar')
        ->color('danger')
        ->icon('heroicon-o-calendar-days'),
    ];
  }
}
