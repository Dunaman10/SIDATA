<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\RekapNilaiResource\Pages;
use App\Filament\Teacher\Resources\RekapNilaiResource\RelationManagers;
use App\Models\RekapNilai;
use App\Models\Student;
use Filament\Forms;
use Filament\Infolists\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RekapNilaiResource extends Resource
{
  protected static ?string $model = Student::class;

  protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
  protected static ?string $navigationLabel = 'Rekapitulasi Nilai';
  protected static ?string $pluralLabel = 'Rekapitulasi Nilai Hafalan Santri';
  protected static ?int $navigationSort = 5;


  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        //
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->query(fn() => Student::with('memorizes'))
      ->columns([
        TextColumn::make('student_name')
          ->label('Nama Santri')
          ->searchable(),

        TextColumn::make('class.class_name')
          ->label('Kelas')
          ->searchable(),

        TextColumn::make('pembimbing.user.name')
          ->label('Guru Pembimbing'),

        TextColumn::make('latestMemorize.juz')
          ->label('Juz')
          ->alignCenter(),

        TextColumn::make('latestMemorize.nilai_avg')
          ->label('Nilai Akhir')
          ->alignCenter(),

        TextColumn::make('total_setoran_6_bulan')
          ->label('Total Setoran')
          ->alignCenter(),

        TextColumn::make('last_setoran')
          ->label('Setoran Terakhir')
          ->date('d M Y'),
      ])
      ->filters([
        //
      ])
      ->actions([
        // Tables\Actions\EditAction::make(),
        Tables\Actions\ViewAction::make(),
        Tables\Actions\Action::make('exportPdf')
          ->label('Export PDF')
          ->icon('heroicon-o-arrow-down-tray')
          ->url(fn(Student $record) => route('rekap.pdf', $record->id))
          ->openUrlInNewTab(),

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
      'index' => Pages\ListRekapNilais::route('/'),
      // 'create' => Pages\CreateRekapNilai::route('/create'),
      // 'edit' => Pages\EditRekapNilai::route('/{record}/edit'),
    ];
  }

  public static function infolist(Infolist $infolist): Infolist
  {
    return $infolist
      ->schema([
        Section::make()
          ->description('Rekapitulasi Hafalan Santri')
          ->schema([
            TextEntry::make('student_name')
              ->label('Nama Santri')
              ->weight(FontWeight::Bold)
              ->inlineLabel(),

            TextEntry::make('class.class_name')
              ->label('Kelas')
              ->weight(FontWeight::Bold)
              ->inlineLabel(),

            TextEntry::make('pembimbing.user.name')
              ->label('Guru Pembimbing')
              ->weight(FontWeight::Bold)
              ->inlineLabel(),

            TextEntry::make('periode')
              ->label('Periode')
              ->inlineLabel(),

            Section::make()
              ->description('Penilaian Kualitas Hafalan')
              ->schema([
                Section::make()
                  ->description('Tahsinul Qiroat')
                  ->schema([
                    TextEntry::make('avg_makharijul_huruf')
                      ->label('Makharijul Huruf')
                      ->weight(FontWeight::Bold)
                      ->inlineLabel(),

                    TextEntry::make('avg_shifatul_huruf')
                      ->label('Shifatul Huruf')
                      ->weight(FontWeight::Bold)
                      ->inlineLabel(),

                    TextEntry::make('avg_ahkamul_qiroat')
                      ->label('Ahkamul Qiroat')
                      ->weight(FontWeight::Bold)
                      ->inlineLabel(),

                    TextEntry::make('avg_ahkamul_waqfi')
                      ->label('Ahkamul Waqfi')
                      ->weight(FontWeight::Bold)
                      ->inlineLabel(),
                  ]),
                Section::make()
                  ->description('Pemahaman Tafsir')
                  ->schema([
                    TextEntry::make('avg_qowaid_tafsir')
                      ->label('Qowaid Tafsir')
                      ->weight(FontWeight::Bold)
                      ->inlineLabel(),

                    TextEntry::make('avg_tarjamatul_ayat')
                      ->label('Tarjamatul Ayat')
                      ->weight(FontWeight::Bold)
                      ->inlineLabel(),
                  ]),
                Section::make()
                  ->schema([
                    TextEntry::make('avg_nilai')
                      ->label('Nilai Rata-Rata')
                      ->weight(FontWeight::Bold)
                      ->inlineLabel(),
                  ])
              ]),

            Section::make()
              ->description('Statistik')
              ->schema([
                TextEntry::make('total_setoran_6_bulan')
                  ->label('Total Setoran')
                  ->weight(FontWeight::Bold)
                  ->inlineLabel(),

                TextEntry::make('latestMemorize.juz')
                  ->label('Juz')
                  ->weight(FontWeight::Bold)
                  ->inlineLabel(),

                TextEntry::make('latestMemorize.surah.surah_name')
                  ->label('Surah Terakhir')
                  ->weight(FontWeight::Bold)
                  ->inlineLabel(),
              ])
          ])

      ]);
  }
}
