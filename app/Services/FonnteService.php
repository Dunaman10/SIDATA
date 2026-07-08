<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
  /**
   * Kirim pesan WhatsApp via Fonnte API.
   *
   * @param  string  $number   Nomor tujuan format 62xxx
   * @param  string  $message  Isi pesan
   * @param  bool    $delay    Aktifkan random delay sebelum mengirim (default: true)
   * @return bool
   */
  public function send(string $number, string $message, bool $delay = true): bool
  {
    // ── Random Delay ─────────────────────────────────────────────────────────
    // Jeda acak 10–25 detik agar pola pengiriman tampak natural seperti manusia,
    // bukan bot yang menembak request secara instan.
    if ($delay) {
      $sleepSeconds = rand(10, 25);
      Log::info("Fonnte: menunggu {$sleepSeconds} detik sebelum kirim ke {$number}");
      sleep($sleepSeconds);
    }
    // ─────────────────────────────────────────────────────────────────────────

    try {
      $response = Http::withHeaders([
        'Authorization' => config('services.fonnte.token'),
      ])->asForm()->post(config('services.fonnte.url'), [
        'target'  => $number,
        'message' => $message,
      ]);

      return $response->successful();
    } catch (\Throwable $e) {

      Log::error('Fonnte Error', [
        'msg' => $e->getMessage()
      ]);

      return false;
    }
  }

  /**
   * Buat suffix acak agar setiap pesan tidak 100% identik.
   * Contoh hasil: " ." atau "  " atau " ·" (karakter zero-width)
   *
   * Suffix ini tidak terlihat oleh penerima namun membuat hash teks berbeda
   * di setiap pengiriman, sehingga WhatsApp tidak mendeteksinya sebagai
   * pesan duplikat massal.
   *
   * @return string
   */
  public static function randomSuffix(): string
  {
    $pool = [
      "\u{200B}",          // Zero-width space
      "\u{200C}",          // Zero-width non-joiner
      "\u{200D}",          // Zero-width joiner
      "\u{FEFF}",          // Zero-width no-break space
      " \u{200B}",
      "\u{200B} ",
    ];

    // Ambil 1–3 karakter acak dari pool
    $count  = rand(1, 3);
    $suffix = '';
    for ($i = 0; $i < $count; $i++) {
      $suffix .= $pool[array_rand($pool)];
    }

    return $suffix;
  }
}
