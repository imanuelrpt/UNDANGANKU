<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Scanner — {{ $invitation->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    <style>
        body { background: #111a17; color: #eef2ef; }
        #reader { width: 100%; max-width: 480px; margin: 0 auto; }
        #reader video { border-radius: 8px; }
        .panel {
            background: #18241f; border: 1px solid rgba(255,255,255,.08);
            border-radius: 10px; box-shadow: 0 1px 2px rgba(0,0,0,.2);
        }
        .result-name { font-size: 1.35rem; font-weight: 650; letter-spacing: -.02em; }
        .form-control, .form-select {
            background: #1f2d27; border-color: rgba(255,255,255,.12); color: #eef2ef;
            border-radius: 8px;
        }
        .form-control:focus, .form-select:focus {
            background: #1f2d27; border-color: #d7a98f; color: #fff;
            box-shadow: 0 0 0 3px rgba(215,169,143,.15);
        }
        .btn-light { --bs-btn-bg: #f2ede4; --bs-btn-border-color: #f2ede4; border-radius: 8px; font-weight: 600; }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h1 class="h4 mb-1"><i class="bi bi-qr-code-scan me-2"></i>Check-in Tamu</h1>
            <span class="text-white-50 small">{{ $invitation->title }} · {{ $staffLink->label }}</span>
        </div>
        <div class="d-flex gap-2">
            <span class="badge text-bg-light">{{ $checkedIn }}/{{ $totalGuests }} check-in</span>
            <a href="{{ route('staff.attendance', $staffLink->token) }}" class="btn btn-sm btn-outline-light">Rekap <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>

    @if (session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="panel p-3">
                <div id="reader"></div>
                <div class="d-grid gap-2 mt-3">
                    <button type="button" id="startScanner" class="btn btn-light"><i class="bi bi-camera me-1"></i>Nyalakan kamera</button>
                    <button type="button" id="stopScanner" class="btn btn-outline-light" disabled>Matikan kamera</button>
                </div>
                <p class="text-white-50 small mt-3 mb-0">Arahkan kamera ke QR tiket tamu. Pastikan izin kamera diberikan.</p>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="panel p-4 h-100 d-flex flex-column justify-content-center">
                <div id="scanResult" class="text-center">
                    <i class="bi bi-qr-code fs-1 text-white-50"></i>
                    <p class="text-white-50 small mt-2">Hasil pemindaian akan muncul di sini.</p>
                </div>
                <div id="confirmBox" class="mt-4 d-none">
                    <div class="text-center">
                        <div class="result-name" id="guestName"></div>
                        <div class="text-white-50 small" id="guestMeta"></div>
                    </div>
                    <div class="row g-2 mt-3">
                        <div class="col-6">
                            <label class="form-label small text-white-50">Jumlah hadir</label>
                            <input type="number" id="paxActual" class="form-control" min="1" value="1">
                        </div>
                        <div class="col-6 d-flex align-items-end">
                            <button type="button" id="confirmCheckin" class="btn btn-success w-100"><i class="bi bi-check-circle me-1"></i>Konfirmasi check-in</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel p-4 mt-3">
        <h2 class="h6 mb-3">Check-in manual / walk-in</h2>
        <form id="manualForm" class="row g-2">
            <div class="col-md-5">
                <select id="guestSelect" class="form-select form-select-sm">
                    <option value="">— Tamu terdaftar —</option>
                    @foreach ($invitation->guests as $guest)
                        <option value="{{ $guest->id }}" data-pax="{{ $guest->pax_quota }}">{{ $guest->name }} ({{ $guest->group->label() }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3"><input id="walkInName" class="form-control form-control-sm" placeholder="Atau nama tamu walk-in"></div>
            <div class="col-md-1"><input id="manualPax" type="number" min="1" value="1" class="form-control form-control-sm"></div>
            <div class="col-md-2"><button class="btn btn-sm btn-light w-100">Proses</button></div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script src="{{ asset('js/scanner.js') }}"></script>
<script>
    window.scannerConfig = {
        scanUrl: '{{ route('staff.scan', $staffLink->token) }}',
        manualUrl: '{{ route('staff.manual', $staffLink->token) }}',
        csrf: '{{ csrf_token() }}',
    };
</script>
