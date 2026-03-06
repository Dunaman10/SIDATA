<?php

namespace App\Filament\Resources\MemorizeResource\Pages;

use App\Filament\Resources\MemorizeResource;
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
        ->label('Download Arsip Hafalan')
        ->icon('heroicon-o-arrow-down-tray')
        ->color('success')
        ->requiresConfirmation()
        ->modalHeading('Download Arsip Hafalan')
        ->modalDescription('Sistem akan mengumpulkan SEMUA data setoran hafalan (termasuk file Audio & Foto) ke dalam satu file ZIP untuk Anda download. Proses ini mungkin membutuhkan waktu beberapa saat.')
        ->modalSubmitActionLabel('Ya, Download Sekarang')
        ->action(function () {
          $records = Memorize::with(['student', 'surah', 'teacher.user'])->get();

          if ($records->isEmpty()) {
            Notification::make()
              ->title('Tidak ada data hafalan untuk diarsipkan.')
              ->warning()
              ->send();
            return;
          }

          // Buat nama file ZIP
          $timestamp = now()->format('Y-m-d_His');
          $zipFileName = "arsip_hafalan_{$timestamp}.zip";
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
          // 1. Buat CSV data setoran (sebagai pengganti Excel, tanpa library tambahan)
          // ============================================
          $csvContent = implode(';', [
            'No',
            'Nama Santri',
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

          // Tambahkan BOM untuk UTF-8 agar Excel bisa membaca karakter Indonesia
          $csvContent = "\xEF\xBB\xBF" . $csvContent;
          $zip->addFromString('data_setoran_hafalan.csv', $csvContent);

          // ============================================
          // 2. Tambahkan file Audio ke dalam ZIP
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
          // 3. Tambahkan file Foto ke dalam ZIP
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

          // Kirim notifikasi berhasil
          Notification::make()
            ->title('Arsip hafalan berhasil dibuat!')
            ->success()
            ->send();

          // Download file ZIP kemudian hapus file sementara setelah dikirim
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
      MemorizeResource\Widgets\MemorizeWarningWidget::class,
    ];
  }
}
