<?php

namespace App\Filament\Resources;

use App\Filament\Exports\UserExporter;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\UserResource\Widgets\UserOverview;
use App\Filament\Resources\UserResource\Widgets\UsersOverview;
use App\Models\Role;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use PhpParser\Node\Stmt\Static_;

class UserResource extends Resource
{
  protected static ?string $model = User::class;
  protected static ?string $navigationIcon = 'heroicon-o-user-group';
  protected static ?string $navigationLabel = 'Pengguna';
  protected static ?string $pluralModelLabel = 'Manajemen Data Pengguna';
  protected static ?int $navigationSort = 7;

  public static function getEloquentQuery(): Builder
  {
    return parent::getEloquentQuery()
      ->with(['role']);
  }

  public static function form(Form $form): Form
  {
    return $form
      ->columns(1)
      ->schema([
        TextInput::make('name')
          ->required()
          ->placeholder('masukkan nama orang tua'),

        TextInput::make('email')
          ->required()
          ->email()
          ->unique(ignoreRecord: true)
          ->placeholder('masukkan email'),

        TextInput::make('phone')
          ->label('Nomor Telepon')
          ->numeric()
          ->required()
          ->tel()
          ->rule('regex:/^[0-9]+$/')
          ->placeholder('masukkan nomor whatsapp'),

        TextInput::make('password')
          ->password()
          ->revealable()
          ->required(fn(string $operation): bool => $operation === 'create')
          ->dehydrateStateUsing(fn($state) => filled($state) ? bcrypt($state) : null)
          ->dehydrated(fn($state) => filled($state)),

        Select::make('role_id')
          ->label('Role')
          ->options(
            Role::whereIn('role_name', ['orang_tua', 'guru', 'keamanan', 'admin'])
              ->get()
              ->pluck(function ($role) {
                return match ($role->role_name) {
                  'orang_tua' => 'Orang Tua',
                  'guru' => 'Guru',
                  'keamanan' => 'Keamanan',
                  'admin' => 'Admin',
                  default => ucfirst($role->role_name),
                };
              }, 'id')
          )
          ->required()

      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->query(User::with('role'))
      ->defaultSort('created_at', 'desc')
      ->columns([
        TextColumn::make('name')
          ->searchable()
          ->sortable(),
        TextColumn::make('email')
          ->searchable(),
        TextColumn::make('phone')
          ->label('Nomor Telepon')
          ->searchable(),

        TextColumn::make('role.role_name')
          ->label('Role')
          ->searchable(),
      ])
      ->filters([
        SelectFilter::make('role_id')
          ->label('Filter by Role')
          ->options(
            Role::all()
              ->pluck(function ($role) {
                return match ($role->role_name) {
                  'orang_tua' => 'Orang Tua',
                  default => ucfirst($role->role_name),
                };
              }, 'id')
          ),
      ])
      ->headerActions([
        ExportAction::make()
          ->exporter(UserExporter::class),
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\ViewAction::make(),
        Tables\Actions\DeleteAction::make(),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
        ]),
        ExportBulkAction::make()
          ->exporter(UserExporter::class),
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
      'index' => Pages\ListUsers::route('/'),
      // 'create' => Pages\CreateUser::route('/create'),
      // 'edit' => Pages\EditUser::route('/{record}/edit'),
    ];
  }

  public static function getWidgets(): array
  {
    return [
      UserOverview::class
    ];
  }
}
