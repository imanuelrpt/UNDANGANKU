@extends('layouts.dashboard')
@section('title', $guest ? 'Edit Tamu' : 'Tambah Tamu')
@section('breadcrumb', 'Undangan / Tamu / '.($guest ? 'Edit' : 'Tambah'))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('guests.index', $invitation) }}" class="btn btn-light btn-sm"><i class="bi bi-arrow-left"></i></a>
            <h1 class="h4 mb-0">{{ $guest ? 'Edit data tamu' : 'Tambah tamu baru' }}</h1>
        </div>
        <div class="card card-soft">
            <div class="card-body p-4">
                <form method="POST" action="{{ $guest ? route('guests.update', $guest) : route('guests.store', $invitation) }}">
                    @csrf
                    @if ($guest) @method('PUT') @endif
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama lengkap</label>
                        <input name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $guest?->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Grup</label>
                            <select name="group" class="form-select @error('group') is-invalid @enderror" required>
                                @foreach ($groups as $value => $label)
                                    <option value="{{ $value }}" @selected(old('group', $guest?->group?->value) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('group') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Nomor WhatsApp</label>
                            <input name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $guest?->phone) }}" placeholder="08xxxxxxxxxx">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Kuota pax</label>
                            <input type="number" name="pax_quota" class="form-control @error('pax_quota') is-invalid @enderror" value="{{ old('pax_quota', $guest?->pax_quota ?? 1) }}" min="1" max="50" required>
                            @error('pax_quota') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Catatan</label>
                            <input name="notes" class="form-control" value="{{ old('notes', $guest?->notes) }}">
                        </div>
                    </div>
                    @if ($guest)
                        <div class="alert alert-light small mt-3 mb-0">
                            Link personal: <a href="{{ $guest->personalPath() }}" target="_blank" class="text-break">{{ $guest->personalPath() }}</a>
                        </div>
                    @endif
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('guests.index', $invitation) }}" class="btn btn-outline-secondary">Batal</a>
                        <button class="btn btn-dark">{{ $guest ? 'Simpan perubahan' : 'Tambah tamu' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
