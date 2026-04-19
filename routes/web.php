<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
  $profile = \App\Models\Profile::first();
  return view('index', compact('profile'));
})->name('index');

// Forgot Password via WhatsApp OTP
Route::prefix('lupa-password')->group(function () {
  Route::get('/', [ForgotPasswordController::class, 'showRequestForm'])->name('password.forgot');
  Route::post('/', [ForgotPasswordController::class, 'sendOtp'])->name('password.send-otp');
  Route::get('/verifikasi', [ForgotPasswordController::class, 'showVerifyForm'])->name('password.verify-otp');
  Route::post('/verifikasi', [ForgotPasswordController::class, 'verifyOtp']);
  Route::post('/kirim-ulang', [ForgotPasswordController::class, 'resendOtp'])->name('password.resend-otp');
  Route::get('/reset', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset-form');
  Route::post('/reset', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');
});


// Route::get('/auth', function () {
//   return redirect('/auth');
// });

// Route::get('/phpinfo', function () {
//   phpinfo();
// });

Route::get('/rekap/{student}/pdf', [\App\Http\Controllers\RekapPdfController::class, 'export'])
  ->name('rekap.pdf');


Route::get('/rekap-presensi/{student}/pdf', [\App\Http\Controllers\RekapPresensiPdfController::class, 'export'])
  ->name('rekap-presensi.pdf');

// Rekap presensi seluruh santri per kelas (PDF)
Route::get('/rekap-presensi-kelas/{class}/pdf', [\App\Http\Controllers\RekapPresensiKelasPdfController::class, 'export'])
  ->name('rekap-presensi-kelas.pdf');

