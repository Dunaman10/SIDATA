<?php

namespace App\Filament\Teacher\Widgets;

use App\Models\Memorize;
use App\Models\MentorStudent;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class TeacherSantriProgress extends BaseWidget
{
    protected static ?int $sort = 6;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = '📖 Progres Hafalan Terbaru Santri Binaan';

    public function table(Table $table): Table
    {
        $teacher = Auth::user()?->teacher;

        $studentIds = $teacher
            ? MentorStudent::where('id_teacher', $teacher->id)->pluck('id_student')
            : collect();

        return $table
            ->query(
                Memorize::query()
                    ->whereIn('id_student', $studentIds)
                    ->with(['student', 'surah'])
                    ->latest('created_at')
            )
            ->defaultPaginationPageOption(10)
            ->emptyStateHeading('Belum Ada Setoran')
            ->emptyStateDescription('Santri binaan Anda belum melakukan setoran hafalan')
            ->emptyStateIcon('heroicon-o-book-open')
            ->columns([
                TextColumn::make('student.student_name')
                    ->label('Nama Santri')
                    ->icon('heroicon-m-user')
                    ->weight('bold'),

                TextColumn::make('surah.surah_name')
                    ->label('Surah')
                    ->icon('heroicon-m-book-open'),

                TextColumn::make('juz')
                    ->label('Juz')
                    ->alignCenter()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('from')
                    ->label('Ayat')
                    ->formatStateUsing(fn($record) => 'Ayat ' . $record->from . ' - ' . $record->to)
                    ->alignCenter(),

                TextColumn::make('nilai_avg')
                    ->label('Nilai')
                    ->alignCenter()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'A' => 'success',
                        'B' => 'info',
                        'C' => 'warning',
                        'D' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'A' => 'heroicon-m-star',
                        'B' => 'heroicon-m-check-circle',
                        'C' => 'heroicon-m-exclamation-circle',
                        'D' => 'heroicon-m-x-circle',
                        default => 'heroicon-m-minus-circle',
                    }),

                TextColumn::make('complete')
                    ->label('Status')
                    ->alignCenter()
                    ->formatStateUsing(fn($state) => $state == 1 ? 'Selesai' : 'Belum Selesai')
                    ->badge()
                    ->color(fn($state) => $state == 1 ? 'success' : 'warning'),

                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->since()
                    ->tooltip(fn($record) => Carbon::parse($record->created_at)->translatedFormat('d F Y, H:i'))
                    ->color('gray'),
            ]);
    }
}
