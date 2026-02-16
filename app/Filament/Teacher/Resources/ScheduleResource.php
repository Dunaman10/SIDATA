<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\ScheduleResource\Pages;
use App\Models\Schedule;
use App\Models\Teacher;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ScheduleResource extends Resource
{
  protected static ?string $model = Schedule::class;

  protected static ?string $navigationIcon = 'heroicon-o-calendar';
  protected static ?string $navigationLabel = 'Jadwal Mengajar';
  protected static ?string $pluralModelLabel = 'Jadwal Mengajar Saya';
  protected static ?string $label = "Jadwal Mengajar";

  public static function getEloquentQuery(): Builder
  {
    $teacher = Teacher::where('id_users', Auth::id())->first();

    return parent::getEloquentQuery()
      ->where('teacher_id', $teacher?->id);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->paginated(false)
      ->columns([
        TextColumn::make('day_of_week')
          ->label('Hari'),

        TextColumn::make('classes.class_name')
          ->label('Kelas'),

        TextColumn::make('start_time')
          ->label('Jam Mulai')
          ->alignCenter()
          ->formatStateUsing(fn($state) => date('H:i', strtotime($state))),

        TextColumn::make('end_time')
          ->label('Jam Selesai')
          ->alignCenter()
          ->formatStateUsing(fn($state) => date('H:i', strtotime($state))),

        TextColumn::make('lesson.name')
          ->label('Mata Pelajaran'),
      ])
      ->filters([
        //
      ])
      ->actions([
        // Tables\Actions\EditAction::make(),
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
