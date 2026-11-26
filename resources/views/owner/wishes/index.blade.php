@extends('layouts.dashboard')
@section('title', 'Buku Tamu')
@section('breadcrumb', 'Undangan / Ucapan')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="h3 mb-1">Buku Tamu</h1>
        <p class="text-muted small mb-0">{{ $invitation->title }} · {{ $stats['total'] }} ucapan, {{ $stats['visible'] }} tampil, {{ $stats['replied'] }} dibalas</p>
    </div>
    <a href="{{ route('invitations.edit', [$invitation, 'step' => 8]) }}" class="btn btn-sm btn-outline-secondary">Pengaturan ucapan</a>
</div>

<div class="card card-soft mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2">
            <div class="col-md-6"><input name="search" class="form-control form-control-sm" placeholder="Cari nama atau ucapan" value="{{ request('search') }}"></div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua ucapan</option>
                    <option value="visible" @selected(request('status') === 'visible')>Tampil</option>
                    <option value="hidden" @selected(request('status') === 'hidden')>Disembunyikan</option>
                </select>
            </div>
            <div class="col-auto"><button class="btn btn-sm btn-outline-dark">Filter</button></div>
        </form>
    </div>
</div>

@forelse ($wishes as $wish)
    <div class="card card-soft mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start gap-3">
                <div>
                    <h2 class="h6 mb-1">{{ $wish->name }} @if ($wish->is_hidden)<span class="badge text-bg-secondary">Disembunyikan</span>@endif</h2>
                    <p class="mb-2">{{ $wish->message }}</p>
                    <span class="text-muted small">{{ $wish->created_at->translatedFormat('d F Y H:i') }} @if ($wish->guest_id)· Tamu terdaftar@endif</span>
                    @if ($wish->reply_message)
                        <div class="alert alert-light small mt-2 mb-0"><strong>Balasan Anda:</strong> {{ $wish->reply_message }}</div>
                    @endif
                </div>
                <div class="d-flex gap-1">
                    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#reply{{ $wish->id }}"><i class="bi bi-reply"></i></button>
                    <form method="POST" action="{{ route('wishes.destroy', $wish) }}" data-confirm="Hapus ucapan ini?">
                        @csrf @method('DELETE') <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
            <div class="collapse mt-3" id="reply{{ $wish->id }}">
                <form method="POST" action="{{ route('wishes.update', $wish) }}" class="row g-2">
                    @csrf @method('PUT')
                    <input type="hidden" name="is_hidden" value="{{ $wish->is_hidden ? 0 : 1 }}">
                    <div class="col-md-8"><input name="reply_message" class="form-control form-control-sm" value="{{ $wish->reply_message }}" placeholder="Tulis balasan"></div>
                    <div class="col-md-4 d-flex gap-1">
                        <button class="btn btn-sm btn-dark">Simpan balasan</button>
                        <button class="btn btn-sm btn-outline-secondary">{{ $wish->is_hidden ? 'Tampilkan' : 'Sembunyikan' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@empty
    <div class="card card-soft"><div class="empty-state"><i class="bi bi-chat-quote fs-1 d-block mb-2"></i>Belum ada ucapan.</div></div>
@endforelse

<div>{{ $wishes->links() }}</div>
@endsection
