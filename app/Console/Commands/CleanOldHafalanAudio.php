<?php

namespace App\Console\Commands;

use App\Models\Memorize;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanOldHafalanAudio extends Command
{

  protected $signature = 'app:clean-old-hafalan-audio';
  protected $description = 'Delete all memorize records (including audio & photo files) from the previous semester';

  public function handle()
  {
    $now = Carbon::now();
    $month = $now->month;
    $year = $now->year;

    // Tentukan awal semester saat ini
    // Semester 1: Jan-Jun → awal = 1 Januari
    // Semester 2: Jul-Des → awal = 1 Juli
    if ($month >= 1 && $month <= 6) {
      $semesterStart = Carbon::create($year, 1, 1, 0, 0, 0);
      $currentSemester = "Semester 1 (Januari – Juni {$year})";
    } else {
      $semesterStart = Carbon::create($year, 7, 1, 0, 0, 0);
      $currentSemester = "Semester 2 (Juli – Desember {$year})";
    }

    $this->info("Semester aktif: {$currentSemester}");
    $this->info("Menghapus data hafalan yang dibuat SEBELUM: {$semesterStart->format('d M Y')}");

    // Hapus semua record yang dibuat sebelum awal semester saat ini
    $records = Memorize::where('created_at', '<', $semesterStart)->get();

    if ($records->isEmpty()) {
      $this->info('Tidak ada data hafalan dari semester sebelumnya yang perlu dibersihkan.');
      return;
    }

    $deletedAudio = 0;
    $deletedFoto = 0;
    $deletedRecords = 0;

    foreach ($records as $row) {
      // Hapus file audio dari storage
      if ($row->audio && Storage::disk('public')->exists($row->audio)) {
        Storage::disk('public')->delete($row->audio);
        $deletedAudio++;
      }

      // Hapus file foto dari storage
      if ($row->foto && Storage::disk('public')->exists($row->foto)) {
        Storage::disk('public')->delete($row->foto);
        $deletedFoto++;
      }

      // Hapus record dari database
      $row->delete();
      $deletedRecords++;
    }

    $this->info("Pembersihan data hafalan semester lalu selesai:");
    $this->info("  - {$deletedRecords} record dihapus dari database");
    $this->info("  - {$deletedAudio} file audio dihapus dari storage");
    $this->info("  - {$deletedFoto} file foto dihapus dari storage");
  }
}
