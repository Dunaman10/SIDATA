<?php

namespace App\Filament\Resources\StudentResource\Widgets;

use App\Models\Student;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class ViewSantri extends BaseWidget
{
  protected function getStats(): array
  {
    $countSantri = Student::count();
    return [
      Stat::make('Total santri', $countSantri)
    ];
  }
}
