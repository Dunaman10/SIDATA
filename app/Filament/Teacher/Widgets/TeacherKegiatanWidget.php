<?php

namespace App\Filament\Teacher\Widgets;

use App\Models\Activity;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TeacherKegiatanWidget extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = '📋 Kegiatan Pondok Pesantren Mendatang';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Activity::query()
                    ->whereDate('activity_date', '>=', now())
                    ->orderBy('activity_date', 'asc')
            )
            ->paginated(false)
            ->emptyStateHeading('Tidak Ada Kegiatan Mendatang')
            ->emptyStateDescription('Belum ada kegiatan pondok yang dijadwalkan')
            ->emptyStateIcon('heroicon-o-calendar-days')
            ->columns([
                TextColumn::make('activity_name')
                    ->label('Nama Kegiatan')
                    ->icon('heroicon-m-bookmark')
                    ->weight('bold'),

                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(60)
                    ->tooltip(fn($record) => $record->description)
                    ->color('gray'),

                TextColumn::make('activity_date')
                    ->label('Tanggal')
                    ->formatStateUsing(function ($state) {
                        $date = Carbon::parse($state);
                        if ($date->isToday()) return 'Hari Ini';
                        if ($date->isTomorrow()) return 'Besok';
                        return $date->translatedFormat('d F Y');
                    })
                    ->icon('heroicon-m-calendar')
                    ->badge()
                    ->color(function ($state) {
                        $date = Carbon::parse($state);
                        if ($date->isToday()) return 'danger';
                        if ($date->isTomorrow()) return 'warning';
                        if ($date->diffInDays(now()) <= 7) return 'info';
                        return 'gray';
                    }),

                TextColumn::make('countdown')
                    ->label('Sisa Waktu')
                    ->getStateUsing(function ($record) {
                        $date = Carbon::parse($record->activity_date);
                        if ($date->isToday()) return '🔴 Hari Ini!';
                        if ($date->isTomorrow()) return '🟡 Besok';
                        return $date->diffForHumans();
                    })
                    ->alignCenter(),
            ]);
    }
}
