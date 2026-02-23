<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\KegiatanResource\Pages;
use App\Models\Activity;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KegiatanResource extends Resource
{
  protected static ?string $model = Activity::class;

  protected static ?string $navigationLabel = 'Kegiatan';
  protected static ?string $pluralModelLabel = 'Daftar Kegiatan Pondok Pesantren';
  protected static ?string $label = "Kegiatan";
  protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
  protected static ?int $navigationSort = 4;


  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Section::make('Informasi Kegiatan')
          ->schema([
            TextInput::make('activity_name')
              ->label('Nama Kegiatan')
              ->required()
              ->placeholder('Masukkan nama kegiatan'),
            Textarea::make('description')
              ->label('Deskripsi')
              ->required()
              ->placeholder('Masukkan deskripsi kegiatan'),
            DatePicker::make('activity_date')
              ->label('Tanggal Kegiatan')
              ->required()
              ->placeholder('Masukkan tanggal kegiatan'),
          ])
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('activity_name')
          ->label('Nama Kegiatan')
          ->searchable()
          ->copyable()
          ->sortable('activity_name'),
        TextColumn::make('description')
          ->label('Deskripsi')
          ->searchable()
          ->copyable()
          ->limit(50)
          ->sortable('description'),
        TextColumn::make('activity_date')
          ->label('Tanggal Kegiatan')
          ->date()
          ->sortable(),
      ])
      ->filters([
        //
      ])
      ->actions([
        // Tables\Actions\EditAction::make(),
        ViewAction::make()
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          // Tables\Actions\DeleteBulkAction::make(),
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
      'index' => Pages\ListKegiatans::route('/'),
      // 'create' => Pages\CreateKegiatan::route('/create'),
      // 'edit' => Pages\EditKegiatan::route('/{record}/edit'),
    ];
  }
}
