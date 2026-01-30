<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProfileResource\Pages;
use App\Models\Profile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProfileResource extends Resource
{
  protected static ?string $model = Profile::class;

  protected static ?string $navigationIcon = 'heroicon-o-building-library';
  protected static ?string $navigationLabel = 'Profil Pondok';
  protected static ?string $pluralModelLabel = 'Profil Pondok';
  protected static ?string $label = 'Profil Pondok';
  protected static ?int $navigationSort = 0;

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Section::make('Informasi Pondok')
          ->description('Data identitas Pondok pendidikan')
          ->schema([
            TextInput::make('title')
              ->label('Title Banner')
              ->required()
              ->maxLength(255)
              ->placeholder('masukkan title banner'),
            Textarea::make('subtitle')
              ->label('Subtitle Banner')
              ->required()
              ->rows(3)
              ->placeholder('masukkan subtitle banner'),
            Textarea::make('about')
              ->label('Tentang Pondok')
              ->required()
              ->rows(3)
              ->placeholder('masukkan tentang pondok'),
            FileUpload::make('banner_image')
              ->label('Gambar Banner')
              ->image()
              ->required()
              ->disk('public')
              ->directory('banner')
              ->visibility('public')
              ->previewable(true),
            TextInput::make('email')
              ->label('Email')
              ->required()
              ->email()
              ->maxLength(255)
              ->placeholder('masukkan email pondok'),
            TextInput::make('phone')
              ->label('No Telepon')
              ->required()
              ->numeric()
              ->placeholder('masukkan nomor pondok'),
            Textarea::make('address')
              ->label('Alamat Pondok')
              ->required()
              ->rows(3)
              ->placeholder('masukkan alamat pondok'),
          ])
          ->columns(1),

        Section::make('Visi & Misi')
          ->description('Visi dan misi Pondok pendidikan')
          ->schema([
            Textarea::make('vision')
              ->label('Visi')
              ->required()
              ->rows(3)
              ->placeholder('Masukkan visi Pondok'),
            Textarea::make('mission')
              ->label('Misi')
              ->required()
              ->rows(3)
              ->placeholder('Masukkan misi Pondok'),
          ])
          ->columns(1),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->defaultSort('id', 'desc')
      ->paginated(false)
      ->columns([
        TextColumn::make('title')
          ->label('Title')
          ->weight('bold'),

        TextColumn::make('address')
          ->label('Alamat Pondok')
          ->limit(50),

        TextColumn::make('phone')
          ->label('Nomor Pondok')
          ->formatStateUsing(function (string $state): string {
            $nomor = str_starts_with($state, '62')
              ? '0' . substr($state, 2)
              : $state;
            if (!str_starts_with($nomor, '0')) {
              $nomor = '0' . $nomor;
            }
            return trim(chunk_split($nomor, 4, ' '));
          })
          ->copyable()
          ->copyMessage('Nomor HP disalin'),

        TextColumn::make('email')
          ->label('Email Pondok')
          ->copyable()
          ->copyMessage('Email disalin'),
      ])
      ->filters([
        //
      ])
      ->actions([
        Tables\Actions\ViewAction::make(),
        Tables\Actions\EditAction::make(),
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
      'index' => Pages\ListProfiles::route('/'),
      // 'create' => Pages\CreateProfile::route('/create'),
      // 'view' => Pages\ViewProfile::route('/{record}'),
      // 'edit' => Pages\EditProfile::route('/{record}/edit'),
    ];
  }
}
