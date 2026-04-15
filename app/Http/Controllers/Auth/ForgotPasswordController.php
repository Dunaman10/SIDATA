<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\User;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class ForgotPasswordController extends Controller
{
    // Step 1: Show form to input WhatsApp number
    public function showRequestForm()
    {
        return view('auth.forgot-password');
    }

    // Step 1: Process OTP request
    public function sendOtp(Request $request, FonnteService $fonnte)
    {
        $request->validate([
            'phone' => 'required|string|min:10|max:15',
        ], [
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
            'phone.min' => 'Nomor WhatsApp minimal 10 digit.',
        ]);

        $inputPhone = $request->phone;

        // Rate limiting
        $key = 'otp-request:' . preg_replace('/[^0-9]/', '', $inputPhone);
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = ceil($seconds / 60);
            return back()->withErrors([
                'phone' => "Terlalu banyak percobaan. Coba lagi dalam {$minutes} menit.",
            ])->withInput();
        }

        // Find user by phone (flexible matching)
        $user = $this->findUserByPhone($inputPhone);

        if (!$user) {
            return back()->withErrors([
                'phone' => 'Nomor WhatsApp tidak ditemukan dalam sistem.',
            ])->withInput();
        }

        // Normalize phone for Fonnte (62xxx format)
        $fonntePhone = $this->normalizeForFonnte($user->phone);

        // Generate 6-digit OTP
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Delete existing OTPs for this user
        OtpCode::where('user_id', $user->id)->delete();

        // Save hashed OTP
        OtpCode::create([
            'user_id' => $user->id,
            'code' => Hash::make($otpCode),
            'phone' => $fonntePhone,
            'expires_at' => now()->addMinutes(5),
            'created_at' => now(),
        ]);

        // Send OTP via WhatsApp
        $message = "🔐 *Kode OTP Daruttafsir*\n\n"
            . "Kode verifikasi Anda: *{$otpCode}*\n\n"
            . "Kode ini berlaku selama 5 menit.\n"
            . "Jangan bagikan kode ini kepada siapa pun.\n\n"
            . "_Jika Anda tidak merasa meminta kode ini, abaikan pesan ini._";

        $sent = $fonnte->send($fonntePhone, $message);

        if (!$sent) {
            return back()->withErrors([
                'phone' => 'Gagal mengirim OTP. Silakan coba lagi.',
            ])->withInput();
        }

        RateLimiter::hit($key, 900);

        // Store in session for next steps
        session([
            'otp_user_id' => $user->id,
            'otp_phone' => $fonntePhone,
            'otp_sent_at' => now()->timestamp,
        ]);

        return redirect()->route('password.verify-otp');
    }

    // Step 2: Show OTP verification form
    public function showVerifyForm()
    {
        if (!session('otp_user_id')) {
            return redirect()->route('password.forgot');
        }

        $maskedPhone = $this->maskPhone(session('otp_phone'));
        $sentAt = session('otp_sent_at', 0);
        $cooldown = max(0, 60 - (now()->timestamp - $sentAt));

        return view('auth.verify-otp', compact('maskedPhone', 'cooldown'));
    }

    // Step 2: Verify OTP code
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ], [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.size' => 'Kode OTP harus 6 digit.',
        ]);

        $userId = session('otp_user_id');
        if (!$userId) {
            return redirect()->route('password.forgot');
        }

        $otp = OtpCode::where('user_id', $userId)
            ->where('expires_at', '>', now())
            ->latest('created_at')
            ->first();

        if (!$otp) {
            session()->forget(['otp_user_id', 'otp_phone', 'otp_sent_at']);
            return redirect()->route('password.forgot')->withErrors([
                'phone' => 'Kode OTP sudah kedaluwarsa. Silakan minta kode baru.',
            ]);
        }

        if ($otp->isMaxAttempts()) {
            $otp->delete();
            session()->forget(['otp_user_id', 'otp_phone', 'otp_sent_at']);
            return redirect()->route('password.forgot')->withErrors([
                'phone' => 'Terlalu banyak percobaan salah. Silakan minta kode OTP baru.',
            ]);
        }

        if (!Hash::check($request->otp, $otp->code)) {
            $otp->increment('attempts');
            $remaining = 5 - $otp->attempts;
            return back()->withErrors([
                'otp' => "Kode OTP salah. Sisa percobaan: {$remaining}",
            ]);
        }

        // OTP verified
        session(['otp_verified' => true]);
        $otp->delete();

        return redirect()->route('password.reset-form');
    }

    // Resend OTP
    public function resendOtp(FonnteService $fonnte)
    {
        $userId = session('otp_user_id');
        $phone = session('otp_phone');

        if (!$userId || !$phone) {
            return redirect()->route('password.forgot');
        }

        // Cooldown 60 seconds
        $sentAt = session('otp_sent_at', 0);
        if ((now()->timestamp - $sentAt) < 60) {
            return back()->withErrors([
                'otp' => 'Tunggu 60 detik sebelum mengirim ulang.',
            ]);
        }

        // Rate limiting
        $key = 'otp-request:' . $phone;
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'otp' => "Terlalu banyak permintaan. Coba lagi dalam " . ceil($seconds / 60) . " menit.",
            ]);
        }

        // Generate new OTP
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        OtpCode::where('user_id', $userId)->delete();

        OtpCode::create([
            'user_id' => $userId,
            'code' => Hash::make($otpCode),
            'phone' => $phone,
            'expires_at' => now()->addMinutes(5),
            'created_at' => now(),
        ]);

        $message = "🔐 *Kode OTP Daruttafsir*\n\n"
            . "Kode verifikasi Anda: *{$otpCode}*\n\n"
            . "Kode ini berlaku selama 5 menit.\n"
            . "Jangan bagikan kode ini kepada siapa pun.\n\n"
            . "_Jika Anda tidak merasa meminta kode ini, abaikan pesan ini._";

        $fonnte->send($phone, $message);
        RateLimiter::hit($key, 900);
        session(['otp_sent_at' => now()->timestamp]);

        return back()->with('success', 'Kode OTP baru telah dikirim ke WhatsApp Anda.');
    }

    // Step 3: Show reset password form
    public function showResetForm()
    {
        if (!session('otp_verified') || !session('otp_user_id')) {
            return redirect()->route('password.forgot');
        }

        return view('auth.reset-password');
    }

    // Step 3: Process password reset
    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $userId = session('otp_user_id');
        if (!$userId || !session('otp_verified')) {
            return redirect()->route('password.forgot');
        }

        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('password.forgot');
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Cleanup
        OtpCode::where('user_id', $userId)->delete();
        session()->forget(['otp_user_id', 'otp_phone', 'otp_sent_at', 'otp_verified']);

        return view('auth.reset-success');
    }

    /**
     * Find user by phone with flexible matching (08xx, 628xx, 8xx, +628xx)
     */
    private function findUserByPhone(string $input): ?User
    {
        $clean = preg_replace('/[^0-9]/', '', $input);

        $variations = [$clean];

        if (str_starts_with($clean, '62')) {
            $local = substr($clean, 2);
            $variations = array_merge($variations, ['0' . $local, $local, '+62' . $local]);
        } elseif (str_starts_with($clean, '0')) {
            $local = substr($clean, 1);
            $variations = array_merge($variations, ['62' . $local, $local, '+62' . $local]);
        } else {
            $variations = array_merge($variations, ['0' . $clean, '62' . $clean, '+62' . $clean]);
        }

        return User::whereIn('phone', $variations)->first();
    }

    /**
     * Normalize phone number to 62xxx format for Fonnte API
     */
    private function normalizeForFonnte(string $phone): string
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($clean, '0')) {
            return '62' . substr($clean, 1);
        }
        if (str_starts_with($clean, '8')) {
            return '62' . $clean;
        }

        return $clean;
    }

    /**
     * Mask phone number: 6281234567890 → 6281****7890
     */
    private function maskPhone(string $phone): string
    {
        $len = strlen($phone);
        if ($len <= 6) return $phone;
        return substr($phone, 0, 4) . str_repeat('*', $len - 8) . substr($phone, -4);
    }
}
