<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\TeachingJournalResource\Pages;
use App\Models\Classes;
use App\Models\Lesson;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TeachingJournal;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;


class TeachingJournalResource extends Resource
{
  protected static ?string $model = TeachingJournal::class;

  protected static ?string $navigationIcon = 'heroicon-o-clipboard';
  protected static ?string $navigationLabel = 'Jurnal Mengajar';
  protected static ?string $pluralModelLabel = 'Jurnal Mengajar Saya';
  protected static ?string $label = "Jurnal Mengajar";

  public static function getEloquentQuery(): Builder
  {
    $teacher = Teacher::where('id_users', Auth::id())->first();

    return parent::getEloquentQuery()
      ->where('teacher_id', $teacher?->id);
  }

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        // Header Jurnal Mengajar
        Section::make('Informasi Kelas')
          ->schema([
            DatePicker::make('date')
              ->label('Tanggal')
              ->default(now())
              ->disabled()
              ->required(),

            Toggle::make('is_substitute')
              ->label('Saya Guru Pengganti')
              ->helperText('Aktifkan jika Anda menggantikan guru lain. Semua kelas akan ditampilkan.')
              ->default(false)
              ->dehydrated(false)
              ->live()
              ->afterStateUpdated(function (Set $set) {
                // Reset pilihan kelas, pelajaran & attendance saat toggle berubah
                $set('classes_id', null);
                $set('lesson_id', null);
                $set('attendance', []);
              }),

            Select::make('classes_id')
              ->label('Kelas')
              ->required()
              ->live()
              ->options(function (Get $get) {
                $isSubstitute = $get('is_substitute');

                if ($isSubstitute) {
                  // Guru Pengganti: tampilkan SEMUA kelas
                  return Classes::orderBy('class_name')
                    ->pluck('class_name', 'id');
                }

                // Default: hanya kelas yang diampu guru ini (dari jadwal/schedules)
                $teacher = Teacher::where('id_users', Auth::id())->first();

                if (!$teacher) {
                  return [];
                }

                // Ambil class IDs unik dari tabel schedules milik guru ini
                $classIds = Schedule::where('teacher_id', $teacher->id)
                  ->distinct()
                  ->pluck('classes_id');

                return Classes::whereIn('id', $classIds)
                  ->orderBy('class_name')
                  ->pluck('class_name', 'id');
              })
              ->afterStateUpdated(function (?string $state, Set $set, Get $get) {
                if (!$state) {
                  $set('lesson_id', null);
                  $set('attendance', []);
                  return;
                }

                // Auto-set mata pelajaran dari jadwal (hanya jika bukan guru pengganti)
                $isSubstitute = $get('is_substitute');
                if (!$isSubstitute) {
                  $teacher = Teacher::where('id_users', Auth::id())->first();
                  $schedule = Schedule::where('teacher_id', $teacher?->id)
                    ->where('classes_id', $state)
                    ->with('lesson')
                    ->first();

                  $set('lesson_id', $schedule?->lesson_id);
                }

                // Isi daftar santri untuk presensi
                $students = Student::where('class_id', $state)
                  ->orderBy('student_name')
                  ->get();

                $attendanceData = [];
                foreach ($students as $student) {
                  $attendanceData[] = [
                    'student_id'   => (string) $student->id,
                    'student_name' => $student->student_name,
                    'status'       => 'Hadir',
                  ];
                }

                $set('attendance', $attendanceData);
              }),

            Select::make('lesson_id')
              ->label('Mata Pelajaran')
              ->required()
              ->live()
              ->disabled(fn(Get $get): bool => !$get('is_substitute'))
              ->dehydrated(true)
              ->helperText(fn(Get $get): string => !$get('is_substitute')
                ? 'Mata pelajaran otomatis diisi dari jadwal mengajar Anda.'
                : 'Silakan pilih mata pelajaran.')
              // Selalu return semua lessons sebagai options agar label nilai
              // yang di-set otomatis via $set() bisa tampil untuk kelas apapun.
              // Nilai yang benar sudah dikontrol oleh afterStateUpdated di classes_id.
              ->options(fn() => Lesson::orderBy('name')->pluck('name', 'id')),
          ]),

        // Presensi Siswa
        Section::make('Presensi Siswa')
          ->description('Tandai status kehadiran setiap santri. Default semua santri adalah Hadir — ubah status bagi yang tidak hadir.')
          ->schema([
            Repeater::make('attendance')
              ->label('')
              ->addable(false)
              ->deletable(false)
              ->reorderable(false)
              ->columns(1)
              ->schema([
                Hidden::make('student_id'),
                TextInput::make('student_name')
                  ->label('')
                  ->disabled()
                  ->dehydrated(false)
                  ->extraInputAttributes([
                    'style' => 'color: #ffffff; font-weight: 600; font-size: 0.95rem; border: none; background: transparent;',
                  ]),
                Radio::make('status')
                  ->label('')
                  ->options([
                    'Hadir' => 'Hadir',
                    'Izin' => 'Izin',
                    'Sakit' => 'Sakit',
                    'Alfa' => 'Alfa',
                  ])
                  ->default('Hadir')
                  ->inline()
                  ->required()
                  ->extraInputAttributes([
                    'style' => ' text-align: center;',
                  ]),
              ]),
          ]),

        // Detail Pembelajaran
        Section::make('Detail Pembelajaran')
          ->schema([
            Textarea::make('topic')
              ->label('Topik Pembelajaran'),
          ]),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('date')
          ->label('Tanggal')
          ->date('d M Y')
          ->sortable(),
        TextColumn::make('classes.class_name')
          ->label('Kelas')
          ->searchable(),
        TextColumn::make('lesson.name')
          ->label('Mata Pelajaran')
          ->searchable(),
        TextColumn::make('topic')
          ->label('Topik')
          ->limit(40),

        TextColumn::make('jumlah_hadir')
          ->label('Santri Hadir')
          ->alignCenter()
          ->getStateUsing(function ($record) {
            return $record->studentAttendances()->where('status', 'Hadir')->count()
              . ' / '
              . $record->studentAttendances()->count();
          })
          ->badge()
          ->color('success'),
      ])
      ->filters([
        //
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make(),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ]);
  }

  public static function getRelations(): array
  {
    return [
      //
    ];
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListTeachingJournals::route('/'),
      'create' => Pages\CreateTeachingJournal::route('/create'),
      'edit' => Pages\EditTeachingJournal::route('/{record}/edit'),
    ];
  }
}
