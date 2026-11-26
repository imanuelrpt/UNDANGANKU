<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Daftar Tamu — {{ $invitation->title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #222; }
        h1 { font-size: 18px; margin-bottom: 2px; }
        .muted { color: #777; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background: #f3f3f3; }
    </style>
</head>
<body>
<h1>Daftar Tamu</h1>
<p class="muted">{{ $invitation->title }} — /u/{{ $invitation->slug }} — dicetak {{ now()->translatedFormat('d F Y H:i') }} WIB</p>
<table>
    <thead>
        <tr><th>No</th><th>Nama</th><th>Grup</th><th>WhatsApp</th><th>Kuota</th><th>RSVP</th><th>Check-in</th></tr>
    </thead>
    <tbody>
        @foreach ($guests as $index => $guest)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $guest->name }}</td>
                <td>{{ $guest->group->label() }}</td>
                <td>{{ $guest->phone ?: '-' }}</td>
                <td>{{ $guest->pax_quota }}</td>
                <td>{{ $guest->rsvp?->attendance->label() ?? 'Belum jawab' }}</td>
                <td>{{ $guest->checkin ? $guest->checkin->checked_in_at->format('d/m/Y H:i') : '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<p class="muted" style="margin-top:12px">Total: {{ $guests->count() }} tamu</p>
</body>
</html>
