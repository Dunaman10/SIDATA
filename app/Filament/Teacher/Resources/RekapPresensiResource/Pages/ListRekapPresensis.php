<?php

namespace App\Filament\Teacher\Resources\RekapPresensiResource\Pages;

use App\Filament\Teacher\Resources\RekapPresensiResource;
use Filament\Resources\Pages\ListRecords;

class ListRekapPresensis extends ListRecords
{
  protected static string $resource = RekapPresensiResource::class;

  protected function getHeaderActions(): array
  {
    return [];
  }
}
