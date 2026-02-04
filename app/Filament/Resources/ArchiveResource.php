<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArchiveResource\Pages;
use App\Filament\Resources\ArchiveResource\RelationManagers;
use App\Models\Archive;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class ArchiveResource extends Resource
{
    protected static ?string $model = Archive::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';

    protected static ?string $navigationLabel = 'Arsip';
    protected static ?string $pluralModelLabel = 'Manajemen Arsip';
    protected static ?string $label = 'Manajemen Arsip';

    public static function form(Form $form): Form
    {
        return $form
        ->columns(1)
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Judul Arsip')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('file_path')
                    ->label('File')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Arsip')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Archive $record) {
                        return Storage::download(
                            $record->file_path,
                            $record->title . '.' . pathinfo($record->file_path, PATHINFO_EXTENSION)
                        );
                    }),
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
            'index' => Pages\ListArchives::route('/'),
            // 'create' => Pages\CreateArchive::route('/create'),
            // 'edit' => Pages\EditArchive::route('/{record}/edit'),
        ];
    }
}
