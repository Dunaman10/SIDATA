<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\RekapPresensiResource\Pages;
use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\Teacher;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RekapPresensiResource extends Resource
{
  // Model: Classes — satu baris = satu kelas yang diajar guru
  protected static ?string $model = Classes::class;

  protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
  protected static ?string $navigationLabel = 'Rekapitulasi Presensi';
  protected static ?string $pluralLabel = 'Rekapitulasi Presensi Santri';
  protected static ?int $navigationSort = 6;
  protected static ?string $slug = 'rekap-presensis';

  /**
   * Ambil teacher model yang sedang login.
   */
  protected static function getTeacher(): ?Teacher
  {
    return Teacher::where('id_users', Auth::id())->first();
  }

  public static function form(Form $form): Form
  {
    return $form->schema([]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->query(function (): Builder {
        $teacher = self::getTeacher();
        if (!$teacher) {
          return Classes::query()->whereRaw('1 = 0');
        }

        // Hanya tampilkan kelas-kelas yang diajar guru ini (dari tabel schedules)
        $classIds = Schedule::where('teacher_id', $teacher->id)
          ->distinct()
          ->pluck('classes_id');

        return Classes::query()
          ->whereIn('id', $classIds)
          ->withCount(['students']) // total santri di kelas
          ->orderBy('class_name');
      })
      ->columns([
        TextColumn::make('class_name')
          ->label('Nama Kelas')
          ->sortable()
          ->searchable()
          ->weight('bold')
          ->icon('heroicon-o-academic-cap'),

        TextColumn::make('students_count')
          ->label('Total Santri')
          ->alignCenter()
          ->badge()
          ->color('info')
          ->suffix(' santri'),

        TextColumn::make('mata_pelajaran')
          ->label('Mata Pelajaran')
          ->getStateUsing(function (Classes $record): string {
            $teacher = self::getTeacher();
            if (!$teacher) return '-';

            $pelajaran = Schedule::where('teacher_id', $teacher->id)
              ->where('classes_id', $record->id)
              ->with('lesson')
              ->get()
              ->pluck('lesson.name')
              ->filter()
              ->unique()
              ->implode(', ');

            return $pelajaran ?: '-';
          })
          ->wrap(),
      ])
      ->filters([
        //
      ])
      ->actions([
        Tables\Actions\Action::make('downloadRekap')
          ->label('Download')
          ->icon('heroicon-o-arrow-down-tray')
          ->color('success')
          ->modalHeading('Download Rekap Presensi Kelas')
          ->modalDescription(fn(Classes $record) => 'Download rekap presensi (tahun akademik aktif) seluruh santri di ' . $record->class_name . '?')
          ->modalSubmitActionLabel('Download PDF')
          ->action(function (Classes $record, $livewire) {
            $teacher = self::getTeacher();
            $activeYearId = AcademicYear::where('is_active', true)->value('id');

            if (!$activeYearId) {
                \Filament\Notifications\Notification::make()
                    ->title('Gagal')
                    ->body('Tidak ada Tahun Akademik yang berstatus Aktif. Silakan hubungi Admin.')
                    ->danger()
                    ->send();
                return;
            }

            $params = [
              'academic_year_id' => $activeYearId,
              'teacher_id'       => $teacher?->id,
            ];

            $url = route('rekap-presensi-kelas.pdf', ['class' => $record->id]) . '?' . http_build_query($params);
            $livewire->js("window.open('{$url}', '_blank')");
          }),
      ])
      ->bulkActions([])
      ->emptyStateHeading('Belum ada kelas yang diajarkan')
      ->emptyStateDescription('Kelas akan muncul setelah jadwal mengajar Anda ditambahkan.');
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
