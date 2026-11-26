<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Masuk') — UndanganKu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
</head>
<body class="auth-body">
<div class="auth-shell">
    <aside class="auth-aside">
        <a href="{{ route('welcome') }}" class="auth-mark">
            <span>UK</span>
            UndanganKu
        </a>
        <div class="auth-pitch">
            <p class="kicker">Undangan digital</p>
            <h1>Setiap undangan terasa dibuat untuk orang yang menerimanya.</h1>
            <p>Buat, bagikan, dan pantau acara dari satu ruang kerja. Tamu cukup membuka satu link, tanpa perlu akun.</p>
        </div>
        <ul class="auth-points">
            <li><i class="bi bi-check2"></i>Wizard 9 langkah, tanpa coding</li>
            <li><i class="bi bi-check2"></i>RSVP, ucapan, dan amplop digital</li>
            <li><i class="bi bi-check2"></i>Tiket QR dan check-in petugas</li>
        </ul>
    </aside>

    <main class="auth-main">
        <div class="auth-card">
            @yield('content')
        </div>
        <p class="auth-foot">&copy; {{ date('Y') }} UndanganKu · Dibuat dengan Laravel.</p>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
