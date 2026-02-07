<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Exports\AttendanceExporter;
use App\Filament\Teacher\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttendanceResource extends Resource
{
  protected static ?string $model = Attendance::class;

  protected static ?string $navigationLabel = 'Kehadiran';
  protected static ?string $pluralModelLabel = 'Kehadiran Santri';
  protected static ?string $label = "Kehadiran";
  protected static ?string $navigationIcon = 'heroicon-o-clipboard';
  protected static ?int $navigationSort = 1;

  // Sembunyikan Resource dari sidebar
  // public static function shouldRegisterNavigation(): bool
  // {
  //   return false;
  // }

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        //
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->defaultSort('date', 'desc')
      ->columns([
        TextColumn::make('student.student_name')
          ->label('Nama Santri')
          ->searchable(),

        TextColumn::make('classes.class_name')
          ->label('Kelas'),

        TextColumn::make('date')
          ->label('Tanggal')
          ->date(),

        // Edit status langsung di table
        SelectColumn::make('status')
          ->label('Ubah Status')
          ->options([
            'hadir' => 'Hadir',
            'sakit' => 'Sakit',
            'tidak_hadir' => 'Tidak Hadir',
            'izin_pulang' => 'Izin Pulang',
          ]),
      ])
      ->filters([
        Filter::make('Tanggal')
          ->form([
            DatePicker::make('from')->label('Dari'),
            DatePicker::make('to')->label('Sampai'),
          ])
          ->query(function ($query, array $data) {
            return $query
              ->when($data['from'], fn($q) => $q->whereDate('date', '>=', $data['from']))
              ->when($data['to'], fn($q) => $q->whereDate('date', '<=', $data['to']));
          }),

        SelectFilter::make('status')
          ->options([
            'belum_absen' => 'Belum Absen',
            'hadir'       => 'Hadir',
            'sakit'       => 'Sakit',
            'tidak_hadir' => 'Tidak Hadir',
            'izin'        => 'Izin Pulang',
          ]),

        Filter::make('kelas')
          ->form([
            Forms\Components\Select::make('class_id')
              ->label('Kelas')
              ->relationship('classes', 'class_name'),
          ])
          ->query(function (Builder $query, array $data) {
            return $query
              ->when($data['class_id'], fn($q) => $q->where('id_class', $data['class_id']));
          }),
      ])
      ->actions([
        //
      ])
      ->headerActions([
        ExportAction::make()
          ->exporter(AttendanceExporter::class),

      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
        ]),
        ExportBulkAction::make()
          ->exporter(AttendanceExporter::class),
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
      'index' => Pages\ListAttendances::route('/'),
      'create' => Pages\CreateAttendance::route('/create'),
      // 'edit' => Pages\EditAttendance::route('/{record}/edit'),
    ];
  }
}
