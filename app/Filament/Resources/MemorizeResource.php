<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemorizeResource\Pages;
use App\Models\Memorize;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Infolist;
use Filament\Tables\Columns\TextColumn;

class MemorizeResource extends Resource
{
  protected static ?string $model = Memorize::class;

  protected static ?string $navigationIcon = 'heroicon-o-archive-box-arrow-down';
  protected static ?string $navigationLabel = 'Arsip Hafalan';
  protected static ?string $pluralLabel = 'Arsip Hafalan Santri';
  protected static ?string $modelLabel = 'Arsip Hafalan';
  protected static ?int $navigationSort = 3;

  public static function canCreate(): bool
  {
    return false;
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('student.student_name')
          ->label('Nama Santri')
          ->searchable()
          ->sortable(),

        TextColumn::make('surah.surah_name')
          ->label('Nama Surat')
          ->searchable(),

        TextColumn::make('juz')
          ->label('Juz')
          ->alignCenter()
          ->sortable(),

        TextColumn::make('from')
          ->label('Dari Ayat')
          ->alignCenter(),

        TextColumn::make('to')
          ->label('Sampai Ayat')
          ->alignCenter(),

        TextColumn::make('nilai_avg')
          ->label('Nilai')
          ->alignCenter()
          ->badge()
          ->color(fn(string $state): string => match ($state) {
            'A' => 'success',
            'B' => 'info',
            'C' => 'warning',
            'D' => 'danger',
            default => 'gray',
          }),

        TextColumn::make('teacher.user.name')
          ->label('Guru Penguji')
          ->searchable(),

        TextColumn::make('created_at')
          ->label('Tanggal Setoran')
          ->date('d M Y')
          ->sortable(),
      ])
      ->defaultSort('created_at', 'desc')
      ->filters([
        //
      ])
      ->actions([
        Tables\Actions\ViewAction::make()
          ->infolist(function (Infolist $infolist): Infolist {
            return $infolist
              ->schema([
                TextEntry::make('student.student_name')
                  ->label('Nama Santri')
                  ->inlineLabel(),

                TextEntry::make('juz')
                  ->label('Juz')
                  ->inlineLabel(),

                TextEntry::make('surah.surah_name')
                  ->label('Nama Surat')
                  ->inlineLabel(),

                ViewEntry::make('audio')
                  ->view('components.audio-player')
                  ->viewData(fn($record) => [
                    'record' => $record,
                  ]),

                ImageEntry::make('foto')
                  ->label('Foto Santri')
                  ->disk('public')
                  ->width(200)
                  ->height(200),

                TextEntry::make('from')
                  ->label('Dari Ayat')
                  ->inlineLabel(),

                TextEntry::make('to')
                  ->label('Sampai Ayat')
                  ->inlineLabel(),

                TextEntry::make('approved_by')
                  ->label('Diperiksa Oleh')
                  ->inlineLabel(),

                TextEntry::make('complete')
                  ->label('Status')
                  ->inlineLabel()
                  ->formatStateUsing(fn($state) => $state ? 'Selesai' : 'Belum Selesai'),

                \Filament\Infolists\Components\Section::make('Penilaian Hafalan')
                  ->description('Bagian ini berisi nilai dan kualitas bacaan santri')
                  ->schema([
                    TextEntry::make('makharijul_huruf')
                      ->label('Makharijul Huruf')
                      ->inlineLabel(),

                    TextEntry::make('shifatul_huruf')
                      ->label('Shifatul Huruf')
                      ->inlineLabel(),

                    TextEntry::make('ahkamul_qiroat')
                      ->label('Ahkamul Qiroat')
                      ->inlineLabel(),

                    TextEntry::make('ahkamul_waqfi')
                      ->label('Ahkamul Waqfi')
                      ->inlineLabel(),

                    TextEntry::make('qowaid_tafsir')
                      ->label('Qowaid Tafsir')
                      ->inlineLabel(),

                    TextEntry::make('tarjamatul_ayat')
                      ->label('Tarjamatul Ayat')
                      ->inlineLabel(),
                  ])
                  ->columns(2)
                  ->collapsible(),
              ]);
          }),
      ])
      ->bulkActions([
        //
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
      'index' => Pages\ListMemorizes::route('/'),
    ];
  }
}
