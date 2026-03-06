<?php

namespace App\Filament\Parent\Resources\MemorizeResource\Widgets;

use App\Models\Memorize;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class ParentMemorizeWarningWidget extends Widget
{
  protected static string $view = 'filament.parent.resources.memorize-resource.widgets.parent-memorize-warning-widget';

  protected int | string | array $columnSpan = 'full';

  public int $totalRecords = 0;
  public string $semesterLabel = '';
  public string $deleteDate = '';
  public int $daysLeft = 0;

  public function mount(): void
  {
    $parent = auth()->user();
    $studentIds = $parent->students()->pluck('id');

    $this->totalRecords = Memorize::whereIn('id_student', $studentIds)->count();

    $now = Carbon::now();
    $month = $now->month;
    $year = $now->year;

    // Tentukan semester saat ini dan tanggal penghapusan
    if ($month >= 1 && $month <= 6) {
      // Semester 1 (Jan-Jun) → data dihapus 1 Juli
      $this->semesterLabel = "Januari – Juni {$year}";
      $deleteAt = Carbon::create($year, 7, 1, 0, 0, 0);
    } else {
      // Semester 2 (Jul-Des) → data dihapus 1 Januari tahun depan
      $this->semesterLabel = "Juli – Desember {$year}";
      $deleteAt = Carbon::create($year + 1, 1, 1, 0, 0, 0);
    }

    $this->deleteDate = $deleteAt->format('d M Y');
    $this->daysLeft = (int) $now->diffInDays($deleteAt, false);

    if ($this->daysLeft < 0) {
      $this->daysLeft = 0;
    }
  }
}
