<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
  $profile = \App\Models\Profile::first();
  return view('index', compact('profile'));
})->name('index');


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
