<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduleResource\Pages;
use App\Models\Schedule;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

use function Laravel\Prompts\text;

class ScheduleResource extends Resource
{
  protected static ?string $model = Schedule::class;

  protected static ?string $navigationIcon = 'heroicon-o-calendar';
  protected static ?string $navigationLabel = 'Penjadwalan';
  protected static ?string $pluralModelLabel = 'Penjadwalan';
  protected static ?string $label = 'Penjadwalan';

  public static function form(Form $form): Form
  {
    return $form
      ->columns(1)
      ->schema([
        Select::make('academic_year_id')
          ->label('Tahun Akademik')
          ->relationship('academicYear', 'years')
          ->placeholder('Pilih Tahun Akademik')
          ->required(),

        Select::make('classes_id')
          ->label('Kelas')
          ->relationship('classes', 'class_name')
          ->placeholder('Pilih Kelas')
          ->required(),

        Select::make('lesson_id')
          ->label('Pelajaran')
          ->relationship('lesson', 'name')
          ->placeholder('Pilih Mata Pelajaran')
          ->required(),

        Select::make('teacher_id')
          ->label('Guru')
          ->relationship('teacher', 'id_users', modifyQueryUsing: fn($query) =>
          $query
            ->with('user')
            ->whereHas('user', fn($q) => $q->where('role_id', 2)))
          ->getOptionLabelFromRecordUsing(fn($record) => $record->user?->name ?? $record->id_users)
          ->searchable()
          ->preload()
          ->placeholder('Pilih Guru')
          ->required(),

        Select::make('day_of_week')
          ->label('Hari')
          ->required()
          ->placeholder('Pilih Hari')
          ->options([
            'Senin' => 'Senin',
            'Selasa' => 'Selasa',
            'Rabu' => 'Rabu',
            'Kamis' => 'Kamis',
            "Jum'at" => "Jum'at",
            'Sabtu' => 'Sabtu',
          ]),

        TimePicker::make('start_time')
          ->label('Jam Mulai')
          ->required()
          ->placeholder('HH:MM'),

        TimePicker::make('end_time')
          ->label('Jam Selesai')
          ->required()
          ->placeholder('HH:MM'),

      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('academicYear.years')
          ->label('Tahun Akademik')
          ->searchable(),
        TextColumn::make('classes.class_name')
          ->label('Kelas')
          ->searchable(),
        TextColumn::make('lesson.name')
          ->label('Pelajaran')
          ->searchable(),
        TextColumn::make('teacher.user.name')
          ->label('Guru')
          ->searchable(),
        TextColumn::make('day_of_week')
          ->label('Hari')
          ->searchable(),
        TextColumn::make('start_time')
          ->label('Jam Mulai')
          ->formatStateUsing(fn($state) => date('H:i', strtotime($state))),
        TextColumn::make('end_time')
          ->label('Jam Selesai')
          ->formatStateUsing(fn($state) => date('H:i', strtotime($state))),
      ])
      ->filters([
        //
      ])
      ->actions([
        Tables\Actions\ViewAction::make(),
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
      'index' => Pages\ListSchedules::route('/'),
      // 'create' => Pages\CreateSchedule::route('/create'),
      // 'edit' => Pages\EditSchedule::route('/{record}/edit'),
    ];
  }
}
