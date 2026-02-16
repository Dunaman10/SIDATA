<?php

namespace App\Filament\Teacher\Resources\TeachingJournalResource\Pages;

use App\Filament\Teacher\Resources\TeachingJournalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTeachingJournals extends ListRecords
{
  protected static string $resource = TeachingJournalResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\CreateAction::make()
        ->label('Buat Jurnal Baru'),
    ];
  }
}
