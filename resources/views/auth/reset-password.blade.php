@extends('layouts.auth-simple', ['title' => 'Reset Password'])

@section('content')
<div class="card">
    <div class="steps">
        <div class="step done"></div>
        <div class="step done"></div>
        <div class="step active"></div>
    </div>

    <h2 class="card-title">Buat Password Baru</h2>
    <p class="card-desc">Masukkan password baru untuk akun Anda. Minimal 8 karakter.</p>

    @if ($errors->any())
        <div class="alert alert-error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <div class="form-group">
            <label class="form-label" for="password">Password Baru</label>
            <div class="input-wrapper">
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-input"
                    placeholder="Minimal 8 karakter"
                    required
                    autofocus
                    minlength="8"
                >
                <button type="button" class="eye-btn" onclick="togglePassword('password', this)" tabindex="-1" aria-label="Tampilkan password">
                    <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
            <div class="input-wrapper">
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="form-input"
                    placeholder="Ulangi password baru"
                    required
                    minlength="8"
                >
                <button type="button" class="eye-btn" onclick="togglePassword('password_confirmation', this)" tabindex="-1" aria-label="Tampilkan konfirmasi password">
                    <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Password</button>
    </form>
</div>
@endsection

@push('styles')
<style>
    .input-wrapper {
        position: relative;
    }
    .input-wrapper .form-input {
        padding-right: 2.75rem;
    }
    .eye-btn {
        position: absolute;
        right: .75rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        padding: .25rem;
        color: #64748b;
        display: flex;
        align-items: center;
        transition: color .2s;
    }
    .eye-btn:hover {
        color: #E5077C;
    }
    .eye-icon {
        width: 1.125rem;
        height: 1.125rem;
    }
</style>
@endpush

@push('scripts')
<script>
    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';

        // Swap icon: eye ↔ eye-off
        btn.querySelector('svg').innerHTML = isHidden
            ? `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
               <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
               <line x1="1" y1="1" x2="23" y2="23"/>`
            : `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
               <circle cx="12" cy="12" r="3"/>`;

        btn.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Tampilkan password');
    }
</script>
@endpush
