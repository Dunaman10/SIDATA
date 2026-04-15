<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Lupa Password' }} - Daruttafsir</title>
    <link rel="icon" href="{{ asset('img/logo-darutafsir.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Inter',sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#0f172a 0%,#1e293b 50%,#0f172a 100%);color:#e2e8f0;padding:1rem}
        .auth-wrap{width:100%;max-width:420px}
        .brand{text-align:center;margin-bottom:2rem}
        .brand img{width:60px;height:60px;border-radius:14px;margin-bottom:.5rem}
        .brand h1{font-size:1.375rem;font-weight:700;color:#f1f5f9}
        .card{background:rgba(30,41,59,.85);backdrop-filter:blur(20px);border:1px solid rgba(148,163,184,.1);border-radius:16px;padding:2rem;box-shadow:0 25px 50px -12px rgba(0,0,0,.5);animation:fadeUp .4s ease-out}
        .card-title{font-size:1.1rem;font-weight:600;color:#f1f5f9;margin-bottom:.375rem}
        .card-desc{font-size:.85rem;color:#94a3b8;margin-bottom:1.5rem;line-height:1.6}
        .steps{display:flex;justify-content:center;gap:.5rem;margin-bottom:1.5rem}
        .step{width:2.5rem;height:4px;border-radius:2px;background:rgba(148,163,184,.15);transition:background .3s}
        .step.active{background:#E5077C}
        .step.done{background:#22c55e}
        .form-group{margin-bottom:1.25rem}
        .form-label{display:block;font-size:.85rem;font-weight:500;color:#cbd5e1;margin-bottom:.5rem}
        .form-input{width:100%;padding:.75rem 1rem;background:rgba(15,23,42,.6);border:1px solid rgba(148,163,184,.2);border-radius:10px;color:#f1f5f9;font-size:.9375rem;font-family:'Inter',sans-serif;transition:border-color .2s,box-shadow .2s;outline:none}
        .form-input:focus{border-color:#E5077C;box-shadow:0 0 0 3px rgba(229,7,124,.15)}
        .form-input::placeholder{color:#475569}
        .form-input.otp{text-align:center;font-size:1.75rem;letter-spacing:.5em;font-weight:700}
        .form-error{color:#f87171;font-size:.8rem;margin-top:.375rem}
        .btn{width:100%;padding:.75rem 1.5rem;border:none;border-radius:10px;font-size:.9375rem;font-weight:600;cursor:pointer;transition:all .2s;font-family:'Inter',sans-serif}
        .btn-primary{background:linear-gradient(135deg,#E5077C,#c2185b);color:#fff;box-shadow:0 4px 15px rgba(229,7,124,.3)}
        .btn-primary:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(229,7,124,.4)}
        .btn-primary:active{transform:translateY(0)}
        .btn-link{background:0 0;color:#94a3b8;padding:.5rem;font-size:.85rem}
        .btn-link:hover{color:#E5077C}
        .btn-link:disabled{opacity:.5;cursor:not-allowed;color:#94a3b8}
        .alert{padding:.875rem 1rem;border-radius:10px;font-size:.85rem;margin-bottom:1.25rem;line-height:1.5}
        .alert-success{background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.2);color:#86efac}
        .alert-error{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);color:#fca5a5}
        .phone-badge{display:inline-flex;align-items:center;gap:.5rem;padding:.5rem 1rem;background:rgba(229,7,124,.08);border:1px solid rgba(229,7,124,.2);border-radius:8px;font-size:.875rem;color:#f472b6;margin-bottom:1rem;font-weight:500}
        .back-link{text-align:center;margin-top:1.5rem}
        .back-link a{color:#94a3b8;text-decoration:none;font-size:.85rem;display:inline-flex;align-items:center;gap:.5rem;transition:color .2s}
        .back-link a:hover{color:#e2e8f0}
        .resend{text-align:center;margin-top:1rem;font-size:.85rem;color:#94a3b8}
        .success-icon{width:80px;height:80px;border-radius:50%;background:rgba(34,197,94,.15);display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;font-size:2.5rem}
        @keyframes fadeUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
    </style>
    @stack('styles')
</head>
<body>
    <div class="auth-wrap">
        <div class="brand">
            <img src="{{ asset('img/logo-darutafsir.png') }}" alt="Logo Daruttafsir">
            <h1>Daruttafsir</h1>
        </div>

        @yield('content')

        @hasSection('hide-back')
        @else
            <div class="back-link">
                <a href="/auth/login">
                    <span>←</span>
                    <span>Kembali ke Login</span>
                </a>
            </div>
        @endif
    </div>

    @stack('scripts')
</body>
</html>
