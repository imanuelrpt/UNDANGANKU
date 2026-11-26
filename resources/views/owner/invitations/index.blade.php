@extends('layouts.dashboard')
@section('title', 'Undangan Saya')
@section('breadcrumb', 'Undangan / Semua')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="h3 mb-1">Undangan Saya</h1>
        <p class="text-muted small mb-0">Kelola semua undangan digital Anda.</p>
    </div>
    <a href="{{ route('invitations.create') }}" class="btn btn-dark"><i class="bi bi-plus-lg me-1"></i> Buat undangan</a>
</div>

<div class="card card-soft mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari judul atau slug..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua status</option>
                    @foreach (App\Enums\InvitationStatus::options() as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto"><button class="btn btn-sm btn-outline-dark">Filter</button></div>
        </form>
    </div>
</div>

@if ($invitations->isEmpty())
    <div class="card card-soft">
        <div class="empty-state">
            <i class="bi bi-envelope-paper fs-1 d-block mb-3"></i>
            <h2 class="h6">Belum ada undangan</h2>
            <p class="small">Mulai dengan membuat undangan pertama Anda.</p>
            <a href="{{ route('invitations.create') }}" class="btn btn-dark btn-sm">Buat undangan</a>
        </div>
    </div>
@else
    <div class="row g-3">
        @foreach ($invitations as $invitation)
            <div class="col-md-6 col-xl-4">
                <div class="card card-soft h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                            <span class="badge {{ $invitation->status->badgeClass() }}">{{ $invitation->status->label() }}</span>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('invitations.edit', $invitation) }}"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                    <li><a class="dropdown-item" href="{{ route('invitations.preview', $invitation) }}" target="_blank"><i class="bi bi-eye me-2"></i>Preview</a></li>
                                    <li><a class="dropdown-item" href="{{ route('guests.index', $invitation) }}"><i class="bi bi-people me-2"></i>Tamu</a></li>
                                    <li><a class="dropdown-item" href="{{ route('wishes.index', $invitation) }}"><i class="bi bi-chat-quote me-2"></i>Ucapan</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('invitations.duplicate', $invitation) }}">
                                            @csrf <button class="dropdown-item"><i class="bi bi-files me-2"></i>Duplikat</button>
                                        </form>
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ $invitation->status->value === 'published' ? route('invitations.unpublish', $invitation) : route('invitations.publish', $invitation) }}">
                                            @csrf
                                            <button class="dropdown-item"><i class="bi bi-{{ $invitation->status->value === 'published' ? 'eye-slash' : 'send' }} me-2"></i>{{ $invitation->status->value === 'published' ? 'Jadikan draft' : 'Publikasikan' }}</button>
                                        </form>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('invitations.destroy', $invitation) }}" data-confirm="Undangan dan semua datanya akan dihapus. Yakin?">
                                            @csrf @method('DELETE') <button class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Hapus</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <h2 class="h6 mb-1">{{ $invitation->title }}</h2>
                        <a href="{{ $invitation->path() }}" target="_blank" class="small text-decoration-none text-muted d-block mb-3">/u/{{ $invitation->slug }}</a>
                        <div class="d-flex gap-3 small text-muted">
                            <span><i class="bi bi-people me-1"></i>{{ $invitation->guests_count }}</span>
                            <span><i class="bi bi-check2 me-1"></i>{{ $invitation->rsvps_count }}</span>
                            <span><i class="bi bi-chat me-1"></i>{{ $invitation->wishes_count }}</span>
                            <span><i class="bi bi-qr-code me-1"></i>{{ $invitation->checkins_count }}</span>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 pt-0">
                        <a href="{{ route('invitations.edit', $invitation) }}" class="btn btn-sm btn-outline-dark w-100">Kelola undangan</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-3">{{ $invitations->links() }}</div>
@endif
@endsection
