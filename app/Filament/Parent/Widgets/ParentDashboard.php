<?php

namespace App\Filament\Parent\Widgets;

use App\Models\Activity;
use App\Models\Student;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class ParentDashboard extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = Auth::user();
        
        // Asumsi relasi: User (Orang Tua) -> Children (Student) via 'parent' field di table students
        $children = Student::where('parent', $user->id)->get();
        $countChildren = $children->count();

        // Activity logic (same as others)
        $upcomingActivity = Activity::whereDate('activity_date', '>=', now())
            ->orderBy('activity_date', 'asc')
            ->first();
        $activity = $upcomingActivity ?? Activity::latest('activity_date')->first();

        $stats = [
             Stat::make('Jumlah Anak', $countChildren)
                ->description('Anak anda yang terdaftar')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary')
                ->icon('heroicon-o-users'),
                
             Stat::make('Kegiatan Pondok', $activity ? $activity->activity_name : 'Tidak Ada Kegiatan')
                ->description($activity ? Carbon::parse($activity->activity_date)->format('d M Y') : '-')
                ->descriptionIcon('heroicon-m-calendar')
                ->color($upcomingActivity ? 'success' : 'gray')
                ->icon('heroicon-o-calendar-days'),
        ];
        
        // Optional: Stats for each child (Simple Summary)
        foreach($children as $child) {
             $totalMemorize = $child->memorizes()->count();
             $lastMemorize = $child->memorizes()->latest()->first();
             
             $desc = $lastMemorize 
                ? 'Terakhir: ' . Carbon::parse($lastMemorize->created_at)->diffForHumans()
                : 'Belum ada setoran';
                
             $stats[] = Stat::make($child->student_name, $totalMemorize . ' Setoran Total')
                ->description($desc)
                ->descriptionIcon('heroicon-m-clock')
                ->color('info')
                ->icon('heroicon-o-user');
        }

        return $stats;
    }
}
