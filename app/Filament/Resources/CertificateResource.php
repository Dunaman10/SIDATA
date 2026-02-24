<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CertificateResource\Pages;
use App\Models\Certificate;
use App\Models\Classes;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CertificateResource extends Resource
{
  protected static ?string $model = Certificate::class;

  protected static ?string $navigationIcon = 'heroicon-o-document-text';
  protected static ?int $navigationSort = 6;
  protected static ?string $navigationLabel = 'Sertifikat';
  protected static ?string $pluralModelLabel = 'Manajemen Sertifikat';
  protected static ?string $label = 'Manajemen Sertifikat';

  public static function form(Form $form): Form
  {
    return $form
      ->columns(1)
      ->schema([
        Select::make('class_id')
          ->label('Filter Kelas')
          ->options(Classes::pluck('class_name', 'id'))
          ->placeholder('Semua Kelas')
          ->searchable()
          ->preload()
          ->live()
          ->afterStateUpdated(fn(Set $set) => $set('student_id', null))
          ->dehydrated(false),

        Select::make('student_id')
          ->label('Santri')
          ->options(function (Get $get) {
            $query = Student::query();
            if ($get('class_id')) {
              $query->where('class_id', $get('class_id'));
            }
            return $query->pluck('student_name', 'id');
          })
          ->searchable()
          ->preload()
          ->placeholder('Pilih Santri')
          ->required(),

        TextInput::make('title')
          ->label('Judul Sertifikat')
          ->required()
          ->placeholder('Masukkan judul sertifikat'),

        Textarea::make('description')
          ->label('Deskripsi')
          ->placeholder('Masukkan deskripsi sertifikat')
          ->rows(3),

        FileUpload::make('file_path')
          ->label('File Sertifikat')
          ->directory('certificates')
          ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
          ->maxSize(2048)
          ->required(),

        DatePicker::make('issued_date')
          ->label('Tanggal Terbit')
          ->required()
          ->native(false)
          ->placeholder('Pilih tanggal terbit'),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('student.student_name')
          ->label('Santri')
          ->searchable()
          ->sortable(),

        TextColumn::make('title')
          ->label('Judul Sertifikat')
          ->searchable()
          ->sortable()
          ->limit(30),

        TextColumn::make('issued_date')
          ->label('Tanggal Terbit')
          ->date('d M Y')
          ->sortable(),
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
      'index' => Pages\ListCertificates::route('/'),
      // 'create' => Pages\CreateCertificate::route('/create'),
      // 'edit' => Pages\EditCertificate::route('/{record}/edit'),
    ];
  }
}
