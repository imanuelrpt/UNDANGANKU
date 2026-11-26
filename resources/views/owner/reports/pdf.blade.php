<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan — {{ $invitation->title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #222; }
        h1 { font-size: 18px; margin: 0; }
        h2 { font-size: 12px; margin: 18px 0 6px; border-bottom: 1px solid #ddd; padding-bottom: 4px; }
        .muted { color: #777; }
        .grid { width: 100%; margin-top: 14px; }
        .grid td { width: 25%; border: 1px solid #ddd; padding: 8px; }
        .value { font-size: 16px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 5px 7px; text-align: left; }
        th { background: #f3f3f3; }
    </style>
</head>
<body>
<h1>Laporan Undangan: {{ $invitation->title }}</h1>
<p class="muted">/u/{{ $invitation->slug }} · dicetak {{ now()->translatedFormat('d F Y H:i') }} WIB</p>

<table class="grid">
    <tr>
        <td>Total tamu<div class="value">{{ $stats['total_guests'] }}</div></td>
        <td>Total pax<div class="value">{{ $stats['total_pax'] }}</div></td>
        <td>Tingkat respons<div class="value">{{ $stats['response_rate'] }}%</div></td>
        <td>Check-in<div class="value">{{ $stats['checkins'] }}</div></td>
    </tr>
</table>

<h2>Ringkasan kehadiran</h2>
<table>
    <tr><th>Status</th><th>Jumlah tamu</th><th>Jumlah pax</th></tr>
    <tr><td>Hadir</td><td>{{ $stats['hadir'] }}</td><td>{{ $stats['pax_hadir'] }}</td></tr>
    <tr><td>Tidak hadir</td><td>{{ $stats['tidak_hadir'] }}</td><td>0</td></tr>
    <tr><td>Masih ragu</td><td>{{ $stats['ragu'] }}</td><td>0</td></tr>
    <tr><td>Belum jawab</td><td>{{ $stats['belum_jawab'] }}</td><td>0</td></tr>
</table>

<h2>Sesi acara</h2>
<table>
    <tr><th>Sesi</th><th>Waktu</th><th>Tempat</th></tr>
    @forelse ($events as $event)
        <tr><td>{{ $event->name }}</td><td>{{ $event->dateLabel() }} · {{ $event->timeLabel() }}</td><td>{{ $event->venue ?: '-' }}</td></tr>
    @empty
        <tr><td colspan="3">Belum ada sesi.</td></tr>
    @endforelse
</table>

<h2>Detail RSVP</h2>
<table>
    <tr><th>Nama</th><th>Status</th><th>Pax</th><th>Catatan</th></tr>
    @forelse ($summary as $rsvp)
        <tr>
            <td>{{ $rsvp->name }}</td>
            <td>{{ $rsvp->attendance->label() }}</td>
            <td>{{ $rsvp->pax }}</td>
            <td>{{ $rsvp->note ?: '-' }}</td>
        </tr>
    @empty
        <tr><td colspan="4">Belum ada RSVP.</td></tr>
    @endforelse
</table>
</body>
</html>
