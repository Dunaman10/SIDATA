<?php

namespace App\Filament\Parent\Resources\MemorizeResource\Pages;

use App\Filament\Parent\Resources\MemorizeResource;
use App\Models\Memorize;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;

class ListMemorizes extends ListRecords
{
  protected static string $resource = MemorizeResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\Action::make('downloadArsip')
        ->label('Download Arsip Hafalan Anak')
        ->icon('heroicon-o-arrow-down-tray')
        ->color('success')
        ->requiresConfirmation()
        ->modalHeading('Download Arsip Hafalan Anak')
        ->modalDescription('Sistem akan mengumpulkan data setoran hafalan anak Anda (termasuk file Audio & Foto) ke dalam satu file ZIP untuk Anda download. Proses ini mungkin membutuhkan waktu beberapa saat.')
        ->modalSubmitActionLabel('Ya, Download Sekarang')
        ->action(function () {
          $parent = auth()->user();

          // Hanya ambil data anak milik orang tua yang sedang login
          $studentIds = $parent->students()->pluck('id');

          $records = Memorize::with(['student', 'surah', 'teacher.user'])
            ->whereIn('id_student', $studentIds)
            ->get();

          if ($records->isEmpty()) {
            Notification::make()
              ->title('Tidak ada data hafalan anak untuk diarsipkan.')
              ->warning()
              ->send();
            return;
          }

          // Buat nama file ZIP
          $timestamp = now()->format('Y-m-d_His');
          $zipFileName = "arsip_hafalan_anak_{$timestamp}.zip";
          $zipPath = storage_path("app/public/{$zipFileName}");

          $zip = new \ZipArchive();

          if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            Notification::make()
              ->title('Gagal membuat file ZIP.')
              ->danger()
              ->send();
            return;
          }

          // ============================================
          // 1. Buat CSV data setoran
          // ============================================
          $csvContent = implode(';', [
            'No',
            'Nama Anak',
            'Surat',
            'Juz',
            'Dari Ayat',
            'Sampai Ayat',
            'Makharijul Huruf',
            'Shifatul Huruf',
            'Ahkamul Qiroat',
            'Ahkamul Waqfi',
            'Qowaid Tafsir',
            'Tarjamatul Ayat',
            'Nilai Rata-Rata',
            'Diperiksa Oleh',
            'Status',
            'Guru Penguji',
            'Tanggal Setoran',
          ]) . "\n";

          $no = 1;
          foreach ($records as $record) {
            $csvContent .= implode(';', [
              $no++,
              $record->student?->student_name ?? '-',
              $record->surah?->surah_name ?? '-',
              $record->juz ?? '-',
              $record->from ?? '-',
              $record->to ?? '-',
              $record->makharijul_huruf ?? '-',
              $record->shifatul_huruf ?? '-',
              $record->ahkamul_qiroat ?? '-',
              $record->ahkamul_waqfi ?? '-',
              $record->qowaid_tafsir ?? '-',
              $record->tarjamatul_ayat ?? '-',
              $record->nilai_avg ?? '-',
              $record->approved_by ?? '-',
              $record->complete ? 'Selesai' : 'Belum Selesai',
              $record->teacher?->user?->name ?? '-',
              $record->created_at?->format('d-m-Y H:i') ?? '-',
            ]) . "\n";
          }

          // BOM untuk UTF-8 agar Excel membaca karakter Indonesia
          $csvContent = "\xEF\xBB\xBF" . $csvContent;
          $zip->addFromString('data_setoran_hafalan_anak.csv', $csvContent);

          // ============================================
          // 2. Tambahkan file Audio
          // ============================================
          foreach ($records as $record) {
            if ($record->audio && Storage::disk('public')->exists($record->audio)) {
              $audioPath = Storage::disk('public')->path($record->audio);
              $studentName = str_replace(['/', '\\', ' '], '_', $record->student?->student_name ?? 'unknown');
              $surahName = str_replace(['/', '\\', ' '], '_', $record->surah?->surah_name ?? 'unknown');
              $date = $record->created_at?->format('Y-m-d') ?? 'unknown';
              $ext = pathinfo($record->audio, PATHINFO_EXTENSION);
              $zip->addFile($audioPath, "audio/{$studentName}_{$surahName}_{$date}.{$ext}");
            }
          }

          // ============================================
          // 3. Tambahkan file Foto
          // ============================================
          foreach ($records as $record) {
            if ($record->foto && Storage::disk('public')->exists($record->foto)) {
              $fotoPath = Storage::disk('public')->path($record->foto);
              $studentName = str_replace(['/', '\\', ' '], '_', $record->student?->student_name ?? 'unknown');
              $surahName = str_replace(['/', '\\', ' '], '_', $record->surah?->surah_name ?? 'unknown');
              $date = $record->created_at?->format('Y-m-d') ?? 'unknown';
              $ext = pathinfo($record->foto, PATHINFO_EXTENSION);
              $zip->addFile($fotoPath, "foto/{$studentName}_{$surahName}_{$date}.{$ext}");
            }
          }

          $zip->close();

          Notification::make()
            ->title('Arsip hafalan anak berhasil dibuat!')
            ->success()
            ->send();

          return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
        }),
    ];
  }

  public function getHeaderWidgetsColumns(): int|string|array
  {
    return 1;
  }

  protected function getHeaderWidgets(): array
  {
    return [
      MemorizeResource\Widgets\ParentMemorizeWarningWidget::class,
    ];
  }
}
