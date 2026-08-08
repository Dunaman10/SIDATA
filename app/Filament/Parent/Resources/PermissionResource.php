<?php

namespace App\Filament\Parent\Resources;

use App\Filament\Parent\Resources\PermissionResource\Pages;
use App\Filament\Parent\Resources\PermissionResource\RelationManagers;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Student;
use Dom\Text;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PermissionResource extends Resource
{
  protected static ?string $model = Permission::class;

  protected static ?string $navigationIcon = 'heroicon-o-shield-check';
  protected static ?string $navigationLabel = 'Perizinan';
  protected static ?string $pluralModelLabel = 'Perizinan Santri';
  protected static ?string $label = "Perizinan";

  public static function getEloquentQuery(): Builder
  {
    return parent::getEloquentQuery()
      ->where('id_parent', auth()->id())
      ->with(['student', 'parent']);
  }

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Select::make('id_student')
          ->label('Nama Santri')
          ->options(function () {
            $user = auth()->user();
            $students = Student::where('parent', $user->id)->get();
            return $students->pluck('student_name', 'id');
          })
          ->required()
          ->placeholder('Pilih santri'),
        TextInput::make('reason')
          ->label('Alasan')
          ->required()
          ->placeholder('Masukkan alasan perizinan'),
        TextInput::make('date_out')
          ->label('Tanggal Izin')
          ->required()
          ->type('date'),
        TextInput::make('date_in')
          ->label('Tanggal Kembali')
          ->required()
          ->type('date'),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('student.student_name')
          ->label('Nama Santri')
          ->searchable(),
        TextColumn::make('reason')
          ->label('Alasan')
          ->searchable(),
        TextColumn::make('date_out')
          ->label('Tanggal Keluar')
          ->date(),
        TextColumn::make('date_in')
          ->label('Tanggal Kembali')
          ->date(),
        TextColumn::make('status')
          ->label('Status')
          ->badge()
          ->color(
            fn(TextColumn $column) =>
            match ($column->getState()) {
              'approved' => 'success',
              'rejected' => 'danger',
              'pending' => 'warning',
              default => null,
            }
          ),
        TextColumn::make('approved_by')
          ->label('Diterima/Ditolak Oleh')
          ->placeholder('Keamanan')
          ->searchable(),
      ])
      ->filters([
        //
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          //
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
      'index' => Pages\ListPermissions::route('/'),
      'create' => Pages\CreatePermission::route('/create'),
      'edit' => Pages\EditPermission::route('/{record}/edit'),
    ];
  }
}
