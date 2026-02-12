<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LessonResource\Pages;
use App\Filament\Resources\LessonResource\RelationManagers;
use App\Models\Lesson;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LessonResource extends Resource
{
  protected static ?string $model = Lesson::class;

  protected static ?string $navigationIcon = 'heroicon-o-book-open';

  protected static ?string $navigationLabel = 'Mata Pelajaran';
  protected static ?string $pluralModelLabel = 'Mata Pelajaran';
  protected static ?string $label = 'Mata Pelajaran';

  public static function form(Form $form): Form
  {
    return $form
      ->columns(1)
      ->schema([
        TextInput::make('name')
          ->label('Mata Pelajaran')
          ->placeholder('masukkan nama mata pelajaran')
          ->required(),
        TextInput::make('code_lesson')
          ->label('Kode Mata Pelajaran')
          ->placeholder('masukkan kode mata pelajaran')
          ->required(),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('name')
          ->label('Mata Pelajaran')
          ->searchable(),
        TextColumn::make('code_lesson')
          ->label('Kode Mata Pelajaran')
          ->searchable(),
      ])
      ->filters([
        //
      ])
      ->actions([
        Tables\Actions\viewAction::make(),
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
      'index' => Pages\ListLessons::route('/'),
      // 'create' => Pages\CreateLesson::route('/create'),
      // 'edit' => Pages\EditLesson::route('/{record}/edit'),
    ];
  }
}
