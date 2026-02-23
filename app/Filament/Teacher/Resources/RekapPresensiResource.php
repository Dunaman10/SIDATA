<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\RekapPresensiResource\Pages;
use App\Models\Classes;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Teacher;
use Carbon\Carbon;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;

class RekapPresensiResource extends Resource
{
  protected static ?string $model = Student::class;

  protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
  protected static ?string $navigationLabel = 'Rekapitulasi Presensi';
  protected static ?string $pluralLabel = 'Rekapitulasi Presensi Santri';
  protected static ?int $navigationSort = 6;
  protected static ?string $slug = 'rekap-presensis';

  /**
   * Generate semester options dynamically
   * Format: "semester|year" => "Label"
   */
  protected static function getSemesterOptions(): array
  {
    $currentYear = now()->year;

    return [
      "1|{$currentYear}" => "Semester 1 — Juli - Desember {$currentYear}",
      "2|{$currentYear}" => "Semester 2 — Januari - Juni {$currentYear}",
      "1|" . ($currentYear - 1) => "Semester 1 — Juli - Desember " . ($currentYear - 1),
      "2|" . ($currentYear - 1) => "Semester 2 — Januari - Juni " . ($currentYear - 1),
    ];
  }

  /**
   * Get date range from semester value
   * Semester 1: Juli (7) - Desember (12)
   * Semester 2: Januari (1) - Juni (6)
   */
  public static function getDateRangeFromSemester(string $semesterValue): array
  {
    [$semester, $year] = explode('|', $semesterValue);

    if ($semester == '1') {
      return [
        'from'  => Carbon::create($year, 7, 1)->startOfDay(),
        'until' => Carbon::create($year, 12, 31)->endOfDay(),
      ];
    }

    return [
      'from'  => Carbon::create($year, 1, 1)->startOfDay(),
      'until' => Carbon::create($year, 6, 30)->endOfDay(),
    ];
  }

  public static function form(Form $form): Form
  {
    return $form->schema([]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->query(function () {
        $teacher = Teacher::where('id_users', Auth::id())->first();

        return Student::query()
          ->whereHas('attendances.teachingJournal', function (Builder $query) use ($teacher) {
            $query->where('teacher_id', $teacher?->id);
          });
      })
      ->columns([
        TextColumn::make('student_name')
          ->label('Nama Santri')
          ->searchable()
          ->sortable(),

        TextColumn::make('class.class_name')
          ->label('Kelas')
          ->sortable(),

        TextColumn::make('total_hadir')
          ->label('Hadir')
          ->alignCenter()
          ->getStateUsing(function (Student $record) {
            return self::getAttendanceCount($record, 'Hadir');
          })
          ->badge()
          ->color('success'),

        TextColumn::make('total_izin')
          ->label('Izin')
          ->alignCenter()
          ->getStateUsing(function (Student $record) {
            return self::getAttendanceCount($record, 'Izin');
          })
          ->badge()
          ->color('warning'),

        TextColumn::make('total_sakit')
          ->label('Sakit')
          ->alignCenter()
          ->getStateUsing(function (Student $record) {
            return self::getAttendanceCount($record, 'Sakit');
          })
          ->badge()
          ->color('info'),

        TextColumn::make('total_alfa')
          ->label('Alfa')
          ->alignCenter()
          ->getStateUsing(function (Student $record) {
            return self::getAttendanceCount($record, 'Alfa');
          })
          ->badge()
          ->color('danger'),
      ])
      ->filters([
        SelectFilter::make('class_id')
          ->label('Kelas')
          ->options(function () {
            $teacher = Teacher::where('id_users', Auth::id())->first();
            if (!$teacher) return [];

            $classIds = \App\Models\Schedule::where('teacher_id', $teacher->id)
              ->distinct()
              ->pluck('classes_id');

            return Classes::whereIn('id', $classIds)
              ->orderBy('class_name')
              ->pluck('class_name', 'id');
          })
          ->query(function (Builder $query, array $data): Builder {
            if (!$data['value']) return $query;
            return $query->where('class_id', $data['value']);
          }),

        SelectFilter::make('semester')
          ->label('Semester')
          ->options(self::getSemesterOptions())
          ->query(function (Builder $query, array $data): Builder {
            if (!$data['value']) return $query;

            $range = self::getDateRangeFromSemester($data['value']);

            return $query->whereHas('attendances.teachingJournal', function (Builder $q) use ($range) {
              $q->whereDate('date', '>=', $range['from'])
                ->whereDate('date', '<=', $range['until']);
            });
          })
          ->indicateUsing(function (array $data): ?string {
            if (!$data['value']) return null;
            $options = self::getSemesterOptions();
            return $options[$data['value']] ?? null;
          }),
      ])
      ->actions([
        Tables\Actions\Action::make('exportPdf')
          ->label('Download PDF')
          ->icon('heroicon-o-arrow-down-tray')
          ->form([
            Select::make('semester')
              ->label('Semester')
              ->options(self::getSemesterOptions())
              ->required()
              ->helperText('Semester 1: Juli - Desember | Semester 2: Januari - Juni'),
          ])
          ->action(function (Student $record, array $data, $livewire) {
            $params = [
              'student'  => $record->id,
              'semester' => $data['semester'],
            ];

            $url = route('rekap-presensi.pdf', $params);
            $livewire->js("window.open('{$url}', '_blank')");
          })
          ->modalHeading('Export PDF Presensi')
          ->modalDescription('Pilih semester untuk laporan presensi')
          ->modalSubmitActionLabel('Download PDF')
          ->color('success'),
      ])
      ->bulkActions([])
      ->defaultSort('student_name');
  }

  /**
   * Count attendance by status for a student
   */
  protected static function getAttendanceCount(Student $record, string $status): int
  {
    $teacher = Teacher::where('id_users', Auth::id())->first();

    $query = StudentAttendance::where('student_id', $record->id)
      ->where('status', $status)
      ->whereHas('teachingJournal', function (Builder $q) use ($teacher) {
        $q->where('teacher_id', $teacher?->id);
      });

    return $query->count();
  }

  public static function getRelations(): array
  {
    return [];
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListRekapPresensis::route('/'),
    ];
  }

  public static function canCreate(): bool
  {
    return false;
  }
}
