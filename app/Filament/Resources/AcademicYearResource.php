<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcademicYearResource\Pages;
use App\Models\AcademicYear;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AcademicYearResource extends Resource
{
  protected static ?string $model = AcademicYear::class;

  protected static ?string $navigationIcon = 'heroicon-o-clock';
  protected static ?string $navigationLabel = 'Tahun Akademik';
  protected static ?string $pluralModelLabel = 'Tahun Akademik';
  protected static ?string $label = 'Tahun Akademik';

  public static function form(Form $form): Form
  {
    $currentYear = (int) date('Y');
    $startYear = 2020;
    $endYear = $currentYear + 5;
    $yearOptions = [];
    for ($y = $startYear; $y <= $endYear; $y++) {
      $yearOptions[$y . '-' . ($y + 1)] = $y . '-' . ($y + 1);
    }

    return $form
      ->columns(1)
      ->schema([
        Select::make('years')
          ->label('Tahun Akademik')
          ->required()
          ->options($yearOptions)
          ->native(false)
          ->searchable()
          ->placeholder('Pilih tahun akademik'),
        Select::make('semester')
          ->label('Semester')
          ->required()
          ->options([
            'ganjil' => 'Ganjil',
            'genap' => 'Genap',
          ])
          ->native(false),
        Toggle::make('is_active')
          ->label('Status'),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('years')
          ->label('Tahun Akademik')
          ->searchable()
          ->sortable(),
        TextColumn::make('semester')
          ->label('Semester')
          ->searchable(),
        TextColumn::make('is_active')
          ->label('Status')
          ->badge()
          ->formatStateUsing(fn(bool $state): string => $state ? 'Aktif' : 'Tidak Aktif')
          ->color(fn(bool $state): string => $state ? 'success' : 'danger'),
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
      'index' => Pages\ListAcademicYears::route('/'),
      // 'create' => Pages\CreateAcademicYear::route('/create'),
      // 'edit' => Pages\EditAcademicYear::route('/{record}/edit'),
    ];
  }
}
