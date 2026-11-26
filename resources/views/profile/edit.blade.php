@extends('layouts.dashboard')
@section('title', 'Profil')
@section('breadcrumb', 'Akun / Profil')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="mb-4"><h1 class="h3 mb-1">Pengaturan Profil</h1><p class="text-muted small mb-0">Perbarui informasi akun dan password Anda.</p></div>

        @if (session('status') === 'profile-updated')
            <div class="alert alert-success small">Profil berhasil diperbarui.</div>
        @endif

        <div class="card card-soft mb-3">
            <div class="card-body p-4">
                <h2 class="h6 mb-3">Informasi pribadi</h2>
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf @method('PATCH')
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama</label>
                        <input name="name" class="form-control @error('name', 'updateProfileInformation') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name', 'updateProfileInformation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control @error('email', 'updateProfileInformation') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email', 'updateProfileInformation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div class="alert alert-warning small">Email Anda belum terverifikasi.</div>
                    @endif
                    <button class="btn btn-dark">Simpan profil</button>
                </form>
            </div>
        </div>

        <div class="card card-soft mb-3">
            <div class="card-body p-4">
                <h2 class="h6 mb-3">Ubah password</h2>
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Password saat ini</label>
                        <input type="password" name="current_password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" required>
                        @error('current_password', 'updatePassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Password baru</label>
                        <input type="password" name="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" required>
                        @error('password', 'updatePassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Konfirmasi password baru</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <button class="btn btn-dark">Perbarui password</button>
                    @if (session('status') === 'password-updated') <span class="small text-success ms-2">Password diperbarui.</span> @endif
                </form>
            </div>
        </div>

        <div class="card card-soft border-danger">
            <div class="card-body p-4">
                <h2 class="h6 mb-3 text-danger">Hapus akun</h2>
                <p class="small text-muted">Tindakan ini permanen. Semua undangan Anda akan ikut terhapus.</p>
                <button class="btn btn-outline-danger btn-sm" data-bs-toggle="collapse" data-bs-target="#deleteForm">Hapus akun saya</button>
                <div class="collapse mt-3" id="deleteForm">
                    <form method="POST" action="{{ route('profile.destroy') }}">
                        @csrf @method('DELETE')
                        <input type="password" name="password" class="form-control form-control-sm mb-2" placeholder="Masukkan password" required>
                        @error('password', 'userDeletion') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        <button class="btn btn-danger btn-sm">Ya, hapus permanen</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
