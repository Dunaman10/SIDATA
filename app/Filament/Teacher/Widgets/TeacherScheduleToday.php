<?php

namespace App\Filament\Teacher\Widgets;

use App\Models\Schedule;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class TeacherScheduleToday extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = '📅 Jadwal Mengajar Hari Ini';

    public function table(Table $table): Table
    {
        $dayMap = [
            0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu',
            4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu',
        ];
        $today = $dayMap[now()->dayOfWeek] ?? now()->translatedFormat('l');

        $teacher = Auth::user()?->teacher;

        return $table
            ->query(
                Schedule::query()
                    ->where('teacher_id', $teacher?->id)
                    ->where('day_of_week', $today)
                    ->orderBy('start_time')
            )
            ->paginated(false)
            ->emptyStateHeading('Tidak Ada Jadwal Hari Ini')
            ->emptyStateDescription('Anda tidak memiliki jadwal mengajar pada hari ' . $today)
            ->emptyStateIcon('heroicon-o-calendar')
            ->columns([
                TextColumn::make('start_time')
                    ->label('Jam Mulai')
                    ->formatStateUsing(fn($state) => Carbon::parse($state)->format('H:i'))
                    ->alignCenter()
                    ->icon('heroicon-m-clock')
                    ->color('primary'),

                TextColumn::make('end_time')
                    ->label('Jam Selesai')
                    ->formatStateUsing(fn($state) => Carbon::parse($state)->format('H:i'))
                    ->alignCenter(),

                TextColumn::make('lesson.name')
                    ->label('Mata Pelajaran')
                    ->icon('heroicon-m-book-open')
                    ->weight('bold'),

                TextColumn::make('classes.class_name')
                    ->label('Kelas')
                    ->icon('heroicon-m-building-office-2')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('status')
                    ->label('Status')
                    ->alignCenter()
                    ->getStateUsing(function ($record) {
                        $now = now()->format('H:i');
                        $start = Carbon::parse($record->start_time)->format('H:i');
                        $end = Carbon::parse($record->end_time)->format('H:i');

                        if ($now < $start) return 'Belum Mulai';
                        if ($now >= $start && $now <= $end) return 'Sedang Berlangsung';
                        return 'Selesai';
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Sedang Berlangsung' => 'success',
                        'Belum Mulai' => 'warning',
                        'Selesai' => 'gray',
                        default => 'gray',
                    }),
            ]);
    }
}
