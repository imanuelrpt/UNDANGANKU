<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>UndanganKu — Undangan Digital, RSVP & Check-in</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/welcome.css') }}" rel="stylesheet">
</head>
<body>
<header class="site-header">
    <div class="container-xl">
        <nav class="nav-top" aria-label="Navigasi utama">
            <a href="{{ route('welcome') }}" class="wordmark"><span class="wordmark-mark">UK</span>UndanganKu</a>
            <div class="nav-actions">
                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-dark">Masuk</a>
                <a href="{{ route('register') }}" class="btn btn-sm btn-dark">Daftar</a>
            </div>
        </nav>
    </div>
</header>

<main>
    <section class="hero">
        <div class="container-xl">
            <div class="row align-items-center g-5">
                <div class="col-lg-7">
                    <p class="kicker">Platform undangan digital</p>
                    <h1>Undangan yang terasa personal, <em>prosesnya tetap sederhana.</em></h1>
                    <p class="hero-lead">Buat undangan pernikahan, ulang tahun, atau acara lain dalam sembilan langkah. Bagikan link personal ke tiap tamu, pantau kehadiran, dan check-in memakai QR.</p>
                    <div class="hero-actions">
                        <a href="{{ route('register') }}" class="btn btn-dark">Mulai buat undangan</a>
                        <a href="{{ route('invitation.public', 'rina-dan-dimas') }}" class="btn btn-outline-dark">Lihat demo undangan</a>
                    </div>
                    <p class="hero-note">9 langkah · 3 tema · tanpa coding</p>
                </div>
                <div class="col-lg-5">
                    <div class="invite-preview" aria-label="Contoh tampilan undangan">
                        <p class="preview-eyebrow">Kepada Yth.</p>
                        <h2 class="preview-title">Rina &amp; Dimas</h2>
                        <div class="preview-rule"></div>
                        <p class="preview-meta">Sabtu, 12 Desember 2026 · 10.00 WIB<br>Gedung Graha Krida, Jakarta</p>
                        <div class="preview-badges">
                            <span class="preview-badge"><i class="bi bi-check2-circle"></i> RSVP</span>
                            <span class="preview-badge"><i class="bi bi-qr-code"></i> Tiket QR</span>
                            <span class="preview-badge"><i class="bi bi-chat-quote"></i> Ucapan</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section container-xl">
        <div class="section-heading">
            <p class="kicker">Alur kerja</p>
            <h2>Dari draft sampai tamu check-in</h2>
        </div>
        <div class="feature-list">
            @foreach ([
                ['Wizard 9 langkah', 'Dari informasi dasar sampai publikasi, setiap bagian undangan dipandu dengan jelas.'],
                ['Tiga tema responsif', 'Elegant Gold, Rustic Green, dan Minimal Modern. Tema baru bisa ditambahkan lewat konfigurasi.'],
                ['Tamu dan link personal', 'Impor dari Excel, atur kuota pax, dan berikan token unik untuk setiap tamu.'],
                ['Check-in QR', 'Scanner kamera, check-in manual, walk-in, dan rekap kehadiran langsung.'],
                ['Laporan yang rapi', 'Tingkat respons RSVP, statistik kehadiran, serta ekspor Excel dan PDF.'],
                ['Akses peran jelas', 'Admin, owner, dan petugas dengan PIN serta pembatasan akses yang sesuai.'],
            ] as $feature)
                <div class="feature-item">
                    <span class="feature-num">{{ sprintf('%02d', $loop->iteration) }}</span>
                    <div>
                        <h3>{{ $feature[0] }}</h3>
                        <p>{{ $feature[1] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="section section-tinted">
        <div class="container-xl">
            <div class="section-heading">
                <p class="kicker">Tema</p>
                <h2>Pilih gaya yang sesuai acara</h2>
            </div>
            <div class="row g-4">
                @foreach ($themes as $theme)
                    <div class="col-md-4">
                        <div class="theme-card h-100">
                            <img src="{{ $theme['preview'] }}" alt="Pratinjau {{ $theme['name'] }}">
                            <div class="card-body">
                                <h3>{{ $theme['name'] }}</h3>
                                <p>{{ $theme['description'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section container-xl">
        <div class="demo-panel">
            <div>
                <p class="kicker">Demo</p>
                <h2>Coba semua peran</h2>
                <p>Data contoh sudah siap. Gunakan akun berikut untuk menjelajah dashboard owner, panel admin, dan halaman tamu.</p>
                <ul class="demo-list">
                    <li><strong>Admin</strong><code>admin@undangan.test / password</code></li>
                    <li><strong>Owner</strong><code>owner@undangan.test / password</code></li>
                    <li><strong>Tamu</strong><code>/u/rina-dan-dimas</code></li>
                </ul>
            </div>
            <div class="demo-actions">
                <a href="{{ route('login') }}" class="btn btn-dark">Masuk ke dashboard</a>
                <a href="{{ route('invitation.public', 'rina-dan-dimas') }}" class="btn btn-outline-dark">Lihat undangan publik</a>
            </div>
        </div>
    </section>
</main>

<footer class="site-footer">&copy; {{ date('Y') }} UndanganKu · Dibangun dengan Laravel 10, Bootstrap 5, dan MySQL.</footer>
</body>
</html>
