@extends('layouts.auth')
@section('title', 'Login Petugas')

@section('content')
<div class="text-center mb-3"><i class="bi bi-qr-code-scan fs-1 text-dark"></i></div>
<h2 class="h4 mb-1 text-center">Portal Petugas</h2>
<p class="text-muted small mb-4 text-center">{{ $invitation->title }}</p>

@if (session('error')) <div class="alert alert-danger small">{{ session('error') }}</div> @endif

<form method="POST" action="{{ route('staff.login.submit', $staffLink->token) }}">
    @csrf
    <div class="mb-3">
        <label for="pin" class="form-label small fw-semibold">PIN 6 digit</label>
        <input id="pin" type="password" inputmode="numeric" maxlength="6" name="pin" class="form-control text-center fs-4 @error('pin') is-invalid @enderror" placeholder="000000" autofocus required>
        @error('pin') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <button class="btn btn-dark w-100 py-2">Buka scanner <i class="bi bi-arrow-right ms-1"></i></button>
</form>
<p class="text-center text-muted small mt-3 mb-0">Link ini bersifat pribadi untuk petugas yang ditunjuk.</p>
@endsection
