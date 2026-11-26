<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Tiket — {{ $guest->name }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #26262c; margin: 0; }
        .ticket { border: 2px solid #c9a227; border-radius: 16px; padding: 34px; }
        .brand { color: #c9a227; font-weight: bold; letter-spacing: .16em; text-transform: uppercase; font-size: 11px; }
        h1 { margin: 8px 0 4px; font-size: 24px; }
        .muted { color: #777; font-size: 11px; }
        .qr { text-align: center; margin: 22px 0; }
        .qr img { width: 220px; height: 220px; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        td { border-top: 1px solid #eee; padding: 8px 4px; }
        .note { font-size: 10px; color: #777; margin-top: 18px; }
    </style>
</head>
<body>
<div class="ticket">
    <div class="brand">UndanganKu · Tiket Tamu</div>
    <h1>{{ $invitation->title }}</h1>
    <p class="muted">{{ $invitation->event_type->label() }} · {{ $invitation->setting('dress_code') ?: '-' }}</p>

    <div class="qr"><img src="data:image/svg+xml;base64,{{ $qr }}" alt="QR tiket"></div>

    <table>
        <tr><td><strong>Nama tamu</strong></td><td>{{ $guest->name }}</td></tr>
        <tr><td><strong>Grup</strong></td><td>{{ $guest->group->label() }}</td></tr>
        <tr><td><strong>Kuota</strong></td><td>{{ $guest->pax_quota }} orang</td></tr>
        @foreach ($events as $event)
            <tr><td><strong>{{ $event->name }}</strong></td><td>{{ $event->dateLabel() }} · {{ $event->timeLabel() }}<br>{{ $event->venue }}</td></tr>
        @endforeach
    </table>

    <p class="note">Tunjukkan QR ini kepada petugas saat check-in. QR bersifat personal dan tidak dapat dipindahtangankan.</p>
</div>
</body>
</html>
