@extends('layouts.auth-simple', ['title' => 'Lupa Password'])

@section('content')
<div class="card">
    <div class="steps">
        <div class="step active"></div>
        <div class="step"></div>
        <div class="step"></div>
    </div>

    <h2 class="card-title">Lupa Password?</h2>
    <p class="card-desc">Masukkan nomor WhatsApp yang terdaftar pada akun Anda. Kami akan mengirimkan kode OTP untuk verifikasi.</p>

    @if ($errors->has('phone'))
        <div class="alert alert-error">{{ $errors->first('phone') }}</div>
    @endif

    <form method="POST" action="{{ route('password.send-otp') }}">
        @csrf
        <div class="form-group">
            <label class="form-label" for="phone">Nomor WhatsApp</label>
            <input
                type="text"
                id="phone"
                name="phone"
                class="form-input"
                placeholder="Contoh: 081234567890"
                value="{{ old('phone') }}"
                required
                autofocus
                inputmode="tel"
            >
        </div>

        <button type="submit" class="btn btn-primary">Kirim Kode OTP</button>
    </form>
</div>
@endsection
