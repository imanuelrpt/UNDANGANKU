@extends('layouts.dashboard')
@section('title', 'Semua Undangan')
@section('breadcrumb', 'Admin / Undangan')

@section('content')
<div class="mb-4"><h1 class="h3 mb-1">Semua Undangan</h1><p class="text-muted small mb-0">Pantau dan tindak konten yang melanggar.</p></div>

<div class="card card-soft mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2">
            <div class="col-md-6"><input name="search" class="form-control form-control-sm" placeholder="Cari judul atau slug" value="{{ request('search') }}"></div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua status</option>
                    @foreach ($statuses as $value => $label)<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>@endforeach
                </select>
            </div>
            <div class="col-auto"><button class="btn btn-sm btn-outline-dark">Filter</button></div>
        </form>
    </div>
</div>

<div class="card card-soft">
    <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead><tr><th>Judul</th><th>Owner</th><th>Status</th><th>Tamu</th><th>RSVP</th><th>Check-in</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse ($invitations as $invitation)
                    <tr>
                        <td class="small">
                            <strong>{{ $invitation->title }}</strong><br>
                            <a href="{{ $invitation->path() }}" target="_blank" class="text-muted text-decoration-none">/u/{{ $invitation->slug }}</a>
                        </td>
                        <td class="small">{{ $invitation->user?->name ?? '-' }}</td>
                        <td><span class="badge {{ $invitation->status->badgeClass() }}">{{ $invitation->status->label() }}</span></td>
                        <td class="small">{{ $invitation->guests_count }}</td>
                        <td class="small">{{ $invitation->rsvps_count }}</td>
                        <td class="small">{{ $invitation->checkins_count }}</td>
                        <td>
                            @if ($invitation->status->value !== 'archived')
                                <form method="POST" action="{{ route('admin.invitations.takedown', $invitation) }}" data-confirm="Arsipkan undangan ini? Tautan publik akan berhenti berfungsi.">
                                    @csrf <button class="btn btn-sm btn-outline-danger">Takedown</button>
                                </form>
                            @else
                                <span class="text-muted small">Diarsipkan</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada undangan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body border-top">{{ $invitations->links() }}</div>
</div>
@endsection
