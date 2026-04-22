<?php

namespace App\Filament\Exports;

use App\Models\Activity;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ActivityExporter extends Exporter
{
  protected static ?string $model = Activity::class;

  public static function getColumns(): array
  {
    return [
      ExportColumn::make('activity_name')
        ->label('Nama Kegiatan'),
      ExportColumn::make('description')
        ->label('Deskripsi'),
      ExportColumn::make('activity_date')
        ->label('Tanggal Kegiatan'),
      ExportColumn::make('keterangan')
        ->label('Keterangan'),
    ];
  }

  public static function getCompletedNotificationBody(Export $export): string
  {
    $body = 'Your activity export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

    if ($failedRowsCount = $export->getFailedRowsCount()) {
      $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
    }

    return $body;
  }
}
