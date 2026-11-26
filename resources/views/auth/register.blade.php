@extends('layouts.auth')
@section('title', 'Daftar')

@section('content')
<h2 class="h4 mb-1">Buat akun owner</h2>
<p class="text-muted small mb-4">Mulai buat undangan digital pertama Anda secara gratis.</p>

<form method="POST" action="{{ route('register') }}">
    @csrf
    <div class="mb-3">
        <label for="name" class="form-label small fw-semibold">Nama lengkap</label>
        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autofocus>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="mb-3">
        <label for="email" class="form-label small fw-semibold">Email</label>
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="username">
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="mb-3">
        <label for="password" class="form-label small fw-semibold">Password</label>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="mb-4">
        <label for="password_confirmation" class="form-label small fw-semibold">Konfirmasi password</label>
        <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
    </div>
    <button type="submit" class="btn btn-dark w-100 py-2">Daftar sekarang</button>
</form>
<p class="text-center small mt-3 mb-0">Sudah punya akun? <a href="{{ route('login') }}" class="text-decoration-none">Masuk</a></p>
@endsection
