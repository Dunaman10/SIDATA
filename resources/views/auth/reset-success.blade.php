@extends('layouts.auth-simple', ['title' => 'Password Berhasil Direset'])

@section('hide-back')@endsection

@section('content')
<div class="card" style="text-align:center">
    <div class="success-icon">✅</div>

    <h2 class="card-title">Password Berhasil Direset!</h2>
    <p class="card-desc">Password Anda telah berhasil diperbarui. Silakan login dengan password baru Anda.</p>

    <a href="/auth/login" class="btn btn-primary" style="display:inline-block;text-decoration:none;text-align:center">
        Masuk ke Akun
    </a>
</div>
@endsection
