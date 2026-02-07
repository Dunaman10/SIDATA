<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeacherResource\Pages;
use App\Filament\Resources\TeacherResource\RelationManagers;
use App\Filament\Resources\TeacherResource\Widgets\TeacherOverview;
use App\Models\Teacher;
use Filament\Infolists\Components\Section;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TeacherResource extends Resource
{
  protected static ?string $model = Teacher::class;

  protected static ?string $navigationIcon = 'heroicon-o-user-group';
  protected static ?string $navigationLabel = 'Asatidz';
  protected static ?string $pluralModelLabel = 'Manajemen Asatidz';
  protected static ?int $navigationSort = 2;

  public static function getEloquentQuery(): Builder
  {
    return parent::getEloquentQuery()
      ->with(['user']);
  }
  // public static function getEloquentQuery(): Builder
  // {
  //   return parent::getEloquentQuery()
  //     ->with(['user', 'classes']);
  // }

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        // Select::make('id_users')
        //   ->label('Nama Guru')
        //   ->relationship('user', 'name'),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->defaultSort('id', 'desc')
      ->columns([
        TextColumn::make('user.name')
          ->label('Nama Ustadz/Ustadzah')
          ->searchable()
          ->sortable(),
        TextColumn::make('binaan.student.student_name')
          ->label('Santri Bimbingan')
          ->limit(20)
          ->placeholder('Kosong'),
        TextColumn::make('user.email')
          ->label('Email Ustadz/Ustadzah')
          ->searchable()
      ])
      ->filters([
        //
      ])
      ->actions([
        // Tables\Actions\EditAction::make(),
        Tables\Actions\ViewAction::make(),
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
      'index' => Pages\ListTeachers::route('/'),
      // 'create' => Pages\CreateTeacher::route('/create'),
      // 'edit' => Pages\EditTeacher::route('/{record}/edit'),
    ];
  }

  public static function getWidgets(): array
  {
    return [
      TeacherOverview::class
    ];
  }

  public static function infolist(Infolist $infolist): Infolist
  {
    return $infolist
      ->schema([
        Section::make('Detail Guru')
          ->schema([
            TextEntry::make('user.name')
              ->label('Nama Ustadz/Ustadzah'),

            TextEntry::make('user.email')
              ->label('Email Ustadz/Ustadzah'),

            TextEntry::make('binaan')
              ->label('Santri Binaan')
              ->formatStateUsing(
                fn($record) =>
                $record->binaan
                  ->map(fn($m) => $m->student->student_name)
                  ->join(', ')
              )
              ->placeholder('Tidak ada santri binaan')
              ->columnSpanFull(),
          ]),
      ]);
  }
}
