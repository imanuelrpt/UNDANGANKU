@extends('layouts.dashboard')
@section('title', 'Buat Undangan')
@section('breadcrumb', 'Undangan / Buat')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('invitations.index') }}" class="btn btn-light btn-sm"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h1 class="h3 mb-1">Buat undangan baru</h1>
                <p class="text-muted small mb-0">Isi informasi dasar, lalu lanjutkan ke wizard 9 langkah.</p>
            </div>
        </div>

        <div class="card card-soft">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('invitations.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="title" class="form-label small fw-semibold">Judul undangan</label>
                        <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="contoh: Pernikahan Rina & Dimas" required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="event_type" class="form-label small fw-semibold">Jenis acara</label>
                            <select id="event_type" name="event_type" class="form-select @error('event_type') is-invalid @enderror" required>
                                @foreach (App\Enums\EventType::options() as $value => $label)
                                    <option value="{{ $value }}" @selected(old('event_type') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('event_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="slug" class="form-label small fw-semibold">Alamat undangan (slug)</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ url('/u/') }}/</span>
                                <input type="text" id="slug" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}" placeholder="rina-dan-dimas" required>
                            </div>
                            @error('slug') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label for="theme" class="form-label small fw-semibold">Tema</label>
                            <select id="theme" name="theme" class="form-select @error('theme') is-invalid @enderror" required>
                                @foreach ($themes as $key => $name)
                                    <option value="{{ $key }}" @selected(old('theme') === $key)>{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('theme') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="expires_at" class="form-label small fw-semibold">Tanggal kedaluwarsa <span class="text-muted">(opsional)</span></label>
                            <input type="datetime-local" id="expires_at" name="expires_at" class="form-control @error('expires_at') is-invalid @enderror" value="{{ old('expires_at') }}">
                            @error('expires_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('invitations.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-dark">Buat & lanjutkan <i class="bi bi-arrow-right ms-1"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
