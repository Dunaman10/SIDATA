<?php

namespace App\Filament\Parent\Resources;

use App\Filament\Parent\Resources\CertificateResource\Pages;
use App\Models\Certificate;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class CertificateResource extends Resource
{
  protected static ?string $model = Certificate::class;

  protected static ?string $navigationIcon = 'heroicon-o-document-text';
  protected static ?string $navigationLabel = 'Sertifikat Anak';
  protected static ?string $pluralModelLabel = 'Sertifikat Anak';
  protected static ?string $label = 'Sertifikat';

  public static function getEloquentQuery(): Builder
  {
    $parent = auth()->user();

    return parent::getEloquentQuery()
      ->whereHas('student', function ($query) use ($parent) {
        $query->where('parent', $parent->id);
      });
  }

  public static function canCreate(): bool
  {
    return false;
  }

  public static function form(Form $form): Form
  {
    return $form
      ->columns(1)
      ->schema([
        TextInput::make('student_name')
          ->label('Nama Santri')
          ->formatStateUsing(fn($record) => $record?->student?->student_name)
          ->disabled(),

        TextInput::make('title')
          ->label('Judul Sertifikat')
          ->disabled(),

        Textarea::make('description')
          ->label('Deskripsi')
          ->disabled(),

        FileUpload::make('file_path')
          ->label('File Sertifikat')
          ->disabled()
          ->previewable(true)
          ->downloadable(),

        DatePicker::make('issued_date')
          ->label('Tanggal Terbit')
          ->disabled(),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('student.student_name')
          ->label('Nama Santri')
          ->searchable()
          ->sortable(),

        TextColumn::make('title')
          ->label('Judul Sertifikat')
          ->searchable()
          ->sortable(),

        TextColumn::make('issued_date')
          ->label('Tanggal Terbit')
          ->date('d M Y')
          ->sortable(),
      ])
      ->filters([
        //
      ])
      ->actions([
        Tables\Actions\ViewAction::make()
          ->label('Lihat'),

        Action::make('download')
          ->label('Download')
          ->icon('heroicon-o-arrow-down-tray')
          ->color('success')
          ->url(fn(Certificate $record): string => Storage::url($record->file_path))
          ->openUrlInNewTab(),
      ])
      ->bulkActions([]);
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
      'index' => Pages\ListCertificates::route('/'),
    ];
  }
}
