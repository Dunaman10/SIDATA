@extends('layouts.auth-simple', ['title' => 'Verifikasi OTP'])

@section('content')
<div class="card">
    <div class="steps">
        <div class="step done"></div>
        <div class="step active"></div>
        <div class="step"></div>
    </div>

    <h2 class="card-title">Verifikasi Kode OTP</h2>
    <p class="card-desc">Masukkan kode 6 digit yang telah dikirim ke WhatsApp Anda.</p>

    <div style="text-align:center">
        <div class="phone-badge">📱 {{ $maskedPhone }}</div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->has('otp'))
        <div class="alert alert-error">{{ $errors->first('otp') }}</div>
    @endif

    <form method="POST" action="{{ route('password.verify-otp') }}">
        @csrf
        <div class="form-group">
            <label class="form-label" for="otp">Kode OTP</label>
            <input
                type="text"
                id="otp"
                name="otp"
                class="form-input otp"
                placeholder="······"
                maxlength="6"
                required
                autofocus
                autocomplete="one-time-code"
                inputmode="numeric"
                pattern="[0-9]{6}"
            >
        </div>

        <button type="submit" class="btn btn-primary">Verifikasi</button>
    </form>

    <div class="resend">
        <p style="margin-bottom:.5rem">Tidak menerima kode?</p>
        <form method="POST" action="{{ route('password.resend-otp') }}" id="resendForm">
            @csrf
            <button type="submit" class="btn btn-link" id="resendBtn" {{ $cooldown > 0 ? 'disabled' : '' }}>
                <span id="resendText">{{ $cooldown > 0 ? "Kirim ulang ({$cooldown}s)" : 'Kirim Ulang OTP' }}</span>
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-focus and auto-submit OTP
    const otpInput = document.getElementById('otp');
    otpInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Resend cooldown timer
    let cooldown = {{ $cooldown }};
    const resendBtn = document.getElementById('resendBtn');
    const resendText = document.getElementById('resendText');

    if (cooldown > 0) {
        const timer = setInterval(() => {
            cooldown--;
            if (cooldown <= 0) {
                clearInterval(timer);
                resendBtn.disabled = false;
                resendText.textContent = 'Kirim Ulang OTP';
            } else {
                resendText.textContent = `Kirim ulang (${cooldown}s)`;
            }
        }, 1000);
    }
</script>
@endpush
