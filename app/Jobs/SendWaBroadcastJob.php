<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\FonnteService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWaBroadcastJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Jumlah percobaan ulang jika job gagal.
     * Set 1 agar tidak kirim pesan duplikat jika terjadi error di tengah jalan.
     */
    public int $tries = 1;

    /**
     * Timeout maksimal job ini boleh berjalan (dalam detik).
     * Hitung: 25 dtk delay × max wali (misal 50) = 1250 dtk → set 1800 (30 menit) aman.
     */
    public int $timeout = 1800;

    public function __construct(
        public readonly string $activityName,
        public readonly string $activityDate,
        public readonly string $description,
        public readonly string $keterangan,
    ) {}

    public function handle(FonnteService $fonnte): void
    {
        // Ambil semua wali yang punya nomor WA
        $wali = User::where('role_id', 3)
            ->whereNotNull('phone')
            ->get();

        if ($wali->isEmpty()) {
            Log::warning('SendWaBroadcastJob: Tidak ada wali dengan nomor WA.');
            return;
        }

        $tanggal  = Carbon::parse($this->activityDate)->translatedFormat('d F Y');
        $jamKirim = Carbon::now()->setTimezone('Asia/Jakarta')->format('H:i');
        $sent     = 0;
        $failed   = 0;

        Log::info("SendWaBroadcastJob: Mulai broadcast ke {$wali->count()} wali.");

        foreach ($wali as $w) {
            // Suffix acak per penerima agar setiap pesan unik (anti-spam flag)
            $suffix  = FonnteService::randomSuffix();

            $message = "السَّلَامُ عَلَيكُمْ وَرَحمَةُ اللهِ وَبَرَكَاتُهُ

Yth. Bapak/Ibu Wali Santri {$w->name},

Dengan hormat, kami informasikan bahwa Pondok Pesantren akan menyelenggarakan kegiatan sebagai berikut:

📌 *Kegiatan* : {$this->activityName}
📅 *Tanggal*  : {$tanggal}
📝 *Deskripsi*: {$this->description}
🔔 *Keterangan*: Wali Santri {$this->keterangan}

Kami memohon kesediaan Bapak/Ibu untuk memperhatikan informasi tersebut dan mendukung kelancaran kegiatan santri.

Demikian pemberitahuan ini kami sampaikan. Atas perhatian dan kerja sama Bapak/Ibu, kami ucapkan terima kasih.

📎 Dikirim pukul {$jamKirim} WIB

وَالسَّلَامُ عَلَيكُمْ وَرَحمَةُ اللهِ وَبَرَكَاتُهُ" . $suffix;

            // FonnteService::send() sudah handle random delay 10-25 dtk di dalamnya
            if ($fonnte->send($w->phone, $message)) {
                $sent++;
                Log::info("SendWaBroadcastJob: Terkirim ke {$w->name} ({$w->phone})");
            } else {
                $failed++;
                Log::warning("SendWaBroadcastJob: Gagal kirim ke {$w->name} ({$w->phone})");
            }
        }

        Log::info("SendWaBroadcastJob: Selesai. Berhasil: {$sent}, Gagal: {$failed}.");
    }
}
