@extends('layouts.auth')
@section('title', 'Konfirmasi Password')

@section('content')
<h2 class="h4 mb-1">Konfirmasi password</h2>
<p class="text-muted small mb-4">Ini adalah area aman. Silakan masukkan password Anda untuk melanjutkan.</p>

<form method="POST" action="{{ route('password.confirm') }}">
    @csrf
    <div class="mb-4">
        <label for="password" class="form-label small fw-semibold">Password</label>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" autofocus>
        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <button type="submit" class="btn btn-dark w-100 py-2">Konfirmasi</button>
</form>
@endsection
