@extends('layouts.dashboard')
@section('title', 'Ringkasan')
@section('breadcrumb', 'Ringkasan')

@section('content')
@php
    $first = $invitations->first();
    $cards = $isAdminView ? [
        ['Undangan', $stats['invitations'], 'bi-mailbox2', 'primary'],
        ['Tamu', $stats['guests'], 'bi-people', 'success'],
        ['RSVP', $stats['rsvps'], 'bi-check2-square', 'warning'],
        ['Ucapan', $stats['wishes'], 'bi-chat-quote', 'info'],
        ['Check-in', $stats['checkins'], 'bi-qr-code-scan', 'dark'],
        ['Akun', $stats['users'], 'bi-person-badge', 'secondary'],
    ] : [
        ['Undangan', $invitations->count(), 'bi-mailbox2', 'primary'],
        ['Tamu', $invitations->sum('guests_count'), 'bi-people', 'success'],
        ['RSVP', $invitations->sum('rsvps_count'), 'bi-check2-square', 'warning'],
        ['Ucapan', $invitations->sum('wishes_count'), 'bi-chat-quote', 'info'],
        ['Check-in', $invitations->sum('checkins_count'), 'bi-qr-code-scan', 'dark'],
    ];
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="mb-1">Halo, {{ auth()->user()->name }}</h1>
        <p class="text-muted mb-0 small">Ringkasan aktivitas undangan digital Anda.</p>
    </div>
    @unless ($isAdminView)
        <a href="{{ route('invitations.create') }}" class="btn btn-dark"><i class="bi bi-plus-lg me-1"></i> Buat undangan</a>
    @endunless
</div>

<div class="row g-3 mb-4">
    @foreach ($cards as $card)
        <div class="col-6 col-lg-3 col-xl-2">
            <div class="card stat-card h-100 p-3">
                <span class="stat-icon bg-{{ $card[3] }} bg-opacity-10 text-{{ $card[3] }}"><i class="bi {{ $card[2] }}"></i></span>
                <div class="stat-value mt-3">{{ $card[1] }}</div>
                <div class="text-muted small">{{ $card[0] }}</div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card card-soft h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h6 mb-0">Statistik kehadiran</h2>
                    @if ($first && ! $isAdminView)
                        <a href="{{ route('invitations.edit', $first) }}" class="small text-decoration-none">Kelola undangan <i class="bi bi-arrow-right"></i></a>
                    @endif
                </div>
                @if ($first && ! $isAdminView)
                    <canvas id="attendanceChart" height="110"></canvas>
                    <div id="chartCaption" class="text-muted small mt-3">Memuat data...</div>
                @else
                    <div class="empty-state">
                        <i class="bi bi-bar-chart fs-1 d-block mb-2"></i>
                        <span>Buat undangan untuk melihat statistik kehadiran.</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card card-soft h-100">
            <div class="card-body">
                <h2 class="h6 mb-3">Undangan terbaru</h2>
                @forelse ($invitations as $item)
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                        <div class="min-w-0">
                            <a href="{{ route('invitations.edit', $item) }}" class="text-decoration-none text-dark small fw-semibold d-block text-truncate">{{ $item->title }}</a>
                            <span class="text-muted" style="font-size:.75rem">/u/{{ $item->slug }}</span>
                        </div>
                        <span class="badge {{ $item->status->badgeClass() }}">{{ $item->status->label() }}</span>
                    </div>
                @empty
                    <div class="empty-state small">Belum ada undangan.</div>
                @endforelse
                <a href="{{ $isAdminView ? route('admin.invitations') : route('invitations.index') }}" class="btn btn-sm btn-outline-secondary w-100 mt-3">Lihat semua</a>
            </div>
        </div>
    </div>
</div>

@if ($isAdminView && $activities->isNotEmpty())
    <div class="card card-soft mt-3">
        <div class="card-body">
            <h2 class="h6 mb-3">Aktivitas terbaru</h2>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    @foreach ($activities as $log)
                        <tr>
                            <td class="small">{{ $log->description }}</td>
                            <td class="small text-muted">{{ $log->user?->name ?? 'Sistem' }}</td>
                            <td class="small text-muted text-nowrap">{{ $log->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
@endif
@endsection

@if ($first && ! $isAdminView)
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        fetch('{{ route('invitations.stats', $first) }}', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                new Chart(document.getElementById('attendanceChart'), {
                    type: 'bar',
                    data: {
                        labels: ['Hadir', 'Tidak Hadir', 'Masih Ragu', 'Belum Jawab'],
                        datasets: [{
                            label: 'Jumlah tamu',
                            data: [data.hadir, data.tidak_hadir, data.ragu, data.belum_jawab],
                            backgroundColor: ['#198754', '#dc3545', '#ffc107', '#adb5bd'],
                            borderRadius: 8,
                        }],
                    },
                    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } },
                });
                document.getElementById('chartCaption').textContent =
                    'Tingkat respons ' + data.response_rate + '% · ' + data.checkins + ' tamu sudah check-in · ' + data.wishes + ' ucapan tampil.';
            })
            .catch(function () { document.getElementById('chartCaption').textContent = 'Statistik belum tersedia.'; });
    </script>
    @endpush
@endif
