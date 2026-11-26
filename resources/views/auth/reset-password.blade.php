@extends('layouts.auth')
@section('title', 'Reset Password')

@section('content')
<h2 class="h4 mb-1">Atur ulang password</h2>
<p class="text-muted small mb-4">Buat password baru yang aman untuk akun Anda.</p>

<form method="POST" action="{{ route('password.store') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">
    <div class="mb-3">
        <label for="email" class="form-label small fw-semibold">Email</label>
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $request->email) }}" required autofocus>
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="mb-3">
        <label for="password" class="form-label small fw-semibold">Password baru</label>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="mb-4">
        <label for="password_confirmation" class="form-label small fw-semibold">Konfirmasi password</label>
        <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
    </div>
    <button type="submit" class="btn btn-dark w-100 py-2">Simpan password baru</button>
</form>
@endsection
