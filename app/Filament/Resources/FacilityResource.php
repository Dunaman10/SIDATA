<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FacilityResource\Pages;
use App\Filament\Resources\FacilityResource\RelationManagers;
use App\Models\Facility;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FacilityResource extends Resource
{
  protected static ?string $model = Facility::class;

  protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
  protected static ?string $navigationLabel = 'Fasilitas Pondok';
  protected static ?string $pluralModelLabel = 'Fasilitas Pondok';
  protected static ?string $label = 'Fasilitas Pondok';
  protected static ?int $navigationSort = 1;

  public static function form(Form $form): Form
  {
    return $form
      ->columns(1)
      ->schema([
        TextInput::make('title')
          ->label('Nama Fasilitas')
          ->required(),
        TextInput::make('description')
          ->label('Deskripsi')
          ->required(),
        Forms\Components\Select::make('icon')
          ->label('Icon')
          ->options(\App\Support\FontAwesome::getIconsWithPreview())
          ->searchable()
          ->allowHtml()
          ->required(),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
    ->paginated(false)
      ->columns([
        TextColumn::make('title')
          ->label('Nama Fasilitas'),
        TextColumn::make('description')
          ->label('Deskripsi')
          ->words(5),
        TextColumn::make('icon')
          ->label('Icon')
          ->formatStateUsing(fn (string $state): string => '<i class="' . $state . '" style="font-size: 1.5rem;"></i>')
          ->html(),
      ])
      ->filters([
        //
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
        // Tables\Actions\DeleteAction::make(),
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
      'index' => Pages\ListFacilities::route('/'),
      // 'create' => Pages\CreateFacility::route('/create'),
      // 'edit' => Pages\EditFacility::route('/{record}/edit'),
    ];
  }
}
