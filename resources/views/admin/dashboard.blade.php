@extends('layouts.dashboard')
@section('title', 'Panel Admin')
@section('breadcrumb', 'Admin / Ringkasan')

@section('content')
<div class="mb-4">
    <h1 class="mb-1">Panel Admin</h1>
    <p class="text-muted small mb-0">Pantau seluruh aktivitas platform UndanganKu.</p>
</div>

<div class="row g-3 mb-4">
    @foreach ([
        ['Undangan', $stats['invitations'], 'bi-mailbox2', 'primary'],
        ['Dipublikasikan', $stats['published'], 'bi-send', 'success'],
        ['Tamu', $stats['guests'], 'bi-people', 'info'],
        ['RSVP', $stats['rsvps'], 'bi-check2-square', 'warning'],
        ['Check-in', $stats['checkins'], 'bi-qr-code-scan', 'dark'],
        ['Akun', $stats['users'], 'bi-person-badge', 'secondary'],
    ] as $card)
        <div class="col-6 col-lg-3 col-xl-2">
            <div class="card stat-card p-3">
                <span class="stat-icon bg-{{ $card[3] }} bg-opacity-10 text-{{ $card[3] }}"><i class="bi {{ $card[2] }}"></i></span>
                <div class="stat-value mt-3">{{ $card[1] }}</div>
                <div class="text-muted small">{{ $card[0] }}</div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card card-soft">
            <div class="card-body">
                <h2 class="h6 mb-3">Undangan terbaru</h2>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Judul</th><th>Owner</th><th>Status</th><th>Tamu</th></tr></thead>
                        <tbody>
                            @foreach ($invitations as $invitation)
                                <tr>
                                    <td class="small"><a href="{{ route('admin.invitations') }}" class="text-decoration-none text-dark">{{ $invitation->title }}</a></td>
                                    <td class="small text-muted">{{ $invitation->user?->name }}</td>
                                    <td><span class="badge {{ $invitation->status->badgeClass() }}">{{ $invitation->status->label() }}</span></td>
                                    <td class="small">{{ $invitation->guests_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card card-soft">
            <div class="card-body">
                <h2 class="h6 mb-3">Aktivitas terbaru</h2>
                @foreach ($activities as $log)
                    <div class="border-bottom py-2">
                        <div class="small">{{ $log->description }}</div>
                        <div class="text-muted" style="font-size:.72rem">{{ $log->user?->name ?? 'Sistem' }} · {{ $log->created_at->diffForHumans() }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
