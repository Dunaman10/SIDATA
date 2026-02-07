<?php

namespace App\Filament\Teacher\Resources;

use Carbon\Carbon;
use Filament\Tables;
use App\Models\Student;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;

use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Infolists\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use App\Filament\Teacher\Resources\DataKelasResource\Pages;
use App\Models\Classes;
use App\Models\MentorStudent;
use Illuminate\Support\Facades\Auth;

class DataKelasResource extends Resource
{
  protected static ?string $model = Student::class;
  protected static ?string $navigationIcon = 'heroicon-o-user-group';
  protected static ?string $navigationLabel = 'Data Santri';
  protected static ?string $pluralModelLabel = 'Data Santri Anda';
  protected static ?int $navigationSort = 0;

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
      ->paginated(false)
      ->columns([
        ImageColumn::make('profile')
          ->label('Foto Santri')
          ->circular()
          ->size(50)
          ->disk('public'),

        TextColumn::make('student_name')
          ->label('Nama Santri')
          ->searchable()
          ->sortable(),

        TextColumn::make('tanggal_lahir')
          ->label('Tanggal Lahir')
          ->formatStateUsing(fn($state) => Carbon::parse($state)->translatedFormat('d F Y')),

        TextColumn::make('latestMemorize.surah.surah_name')
          ->label('Progress Terakhir')
          ->searchable(),

        TextColumn::make('class.class_name')
          ->label('Kelas')
          ->searchable()

      ])
      ->filters([
        // Tables\Filters\SelectFilter::make('class_id')
        //   ->label('Pilih Kelas')
        //   ->options(Classes::all()->pluck('class_name', 'id'))
      ])
      ->actions([
        Tables\Actions\ViewAction::make()
          ->modalHeading(fn(Model $record) => 'Biodata ' . $record->student_name),
      ]);
    // ->bulkActions([
    //     Tables\Actions\BulkActionGroup::make([
    //     Tables\Actions\DeleteBulkAction::make(),
    //     ]),
    // ]);
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
      'index' => Pages\ListDataKelas::route('/'),
      // 'create' => Pages\CreateDataKelas::route('/create'),
      // 'edit' => Pages\EditDataKelas::route('/{record}/edit'),

    ];
  }

  public static function getEloquentQuery(): Builder
  {
    $user = Auth::user();
    $teacher = $user->teacher;

    if (!$teacher) {
      return parent::getEloquentQuery()->whereRaw('1=0');
    }

    // Ambil santri binaan dari tabel mentor_students
    $studentIds = MentorStudent::where('id_teacher', $teacher->id)
      ->limit(3) // jaga-jaga kalau lebih dari 3
      ->pluck('id_student');

    return parent::getEloquentQuery()->whereIn('id', $studentIds);
  }



  public static function infolist(Infolist $infolist): Infolist
  {
    return $infolist
      ->schema([
        Grid::make(['default' => 1, 'sm' => 1, 'md' => 1])

          ->schema([
            ImageEntry::make('profile')
              ->disk('public')
              ->size(150)
              ->circular()
              ->extraAttributes(['class' => 'flex justify-center']),

            Section::make()
              ->schema([
                TextEntry::make('student_name')
                  ->label('Nama Santri')
                  ->inlineLabel()
                  ->weight(FontWeight::Bold),

                TextEntry::make('tanggal_lahir')
                  ->label('Tanggal Lahir')
                  ->inlineLabel()
                  ->weight(FontWeight::Bold)
                  ->formatStateUsing(fn($state) => Carbon::parse($state)->translatedFormat('d F Y')),

                TextEntry::make('latestMemorize.surah.surah_name')
                  ->label('Progress Terakhir')
                  ->inlineLabel()
                  ->weight(FontWeight::Bold),

                TextEntry::make('latestMemorize.juz')
                  ->label('Juz')
                  ->inlineLabel()
                  ->weight(FontWeight::Bold),

                TextEntry::make('class.class_name')
                  ->label('Kelas')
                  ->inlineLabel()
                  ->weight(FontWeight::Bold),
              ]),
          ]),
      ]);
  }
}
