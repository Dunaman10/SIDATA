<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MentorStudentResource\Pages;
use App\Filament\Resources\MentorStudentResource\RelationManagers;
use App\Models\MentorStudent;
use App\Models\Teacher;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MentorStudentResource extends Resource
{
  protected static ?string $model = MentorStudent::class;

  protected static ?string $navigationIcon = 'heroicon-o-users';
  protected static ?int $navigationSort = 5;
  protected static ?string $navigationLabel = 'Mentor Santri';
  protected static ?string $pluralModelLabel = 'Manajemen Mentor Santri';

  public static function form(Form $form): Form
  {
    return $form
    ->columns(1)
      ->schema([

        Select::make('id_student')
          ->label('Nama Santri')
          ->relationship(
            'student',
            'student_name',
            fn($query) =>
            $query->whereNotIn('id', MentorStudent::pluck('id_student'))
          )
          ->preload()
          ->searchable()
          ->required(),

        Select::make('id_teacher')
          ->label('Nama Pembimbing')
          ->relationship(
            name: 'teacher',
            titleAttribute: 'id_users',
            modifyQueryUsing: fn($query) =>
            $query
              ->whereHas('user', fn($q) => $q->where('role_id', 2))
              ->withCount('binaan')
              ->having('binaan_count', '<', 3)
          )
          ->getOptionLabelFromRecordUsing(fn($record) => $record->user->name)
          ->preload()
          ->searchable()
          ->required()
          ->live(),

      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('student.student_name')
          ->label('Nama Santri')
          ->searchable(),
        TextColumn::make('teacher.user.name')
          ->label('Nama Pembimbing')
          ->searchable()
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
      'index' => Pages\ListMentorStudents::route('/'),
      // 'create' => Pages\CreateMentorStudent::route('/create'),
      // 'edit' => Pages\EditMentorStudent::route('/{record}/edit'),
    ];
  }
}
