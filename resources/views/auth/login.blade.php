@extends('layouts.auth')
@section('title', 'Masuk')

@section('content')
<h2 class="h4 mb-1">Selamat datang kembali</h2>
<p class="text-muted small mb-4">Masuk untuk mengelola undangan Anda.</p>

@if (session('status'))
    <div class="alert alert-success small">{{ session('status') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger small">{{ session('error') }}</div>
@endif

<form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="mb-3">
        <label for="email" class="form-label small fw-semibold">Email</label>
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="mb-3">
        <label for="password" class="form-label small fw-semibold">Password</label>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label small" for="remember">Ingat saya</label>
        </div>
        @if (Route::has('password.request'))
            <a class="small text-decoration-none" href="{{ route('password.request') }}">Lupa password?</a>
        @endif
    </div>
    <button type="submit" class="btn btn-dark w-100 py-2">Masuk <i class="bi bi-arrow-right ms-1"></i></button>
</form>

<div class="border-top mt-4 pt-4">
    <p class="small text-muted mb-2">Akun demo untuk mencoba aplikasi:</p>
    <div class="d-grid gap-2">
        <button type="button" class="btn btn-outline-secondary btn-sm text-start demo-login" data-email="admin@undangan.test">
            <i class="bi bi-shield-lock me-2"></i>Admin — admin@undangan.test / password
        </button>
        <button type="button" class="btn btn-outline-secondary btn-sm text-start demo-login" data-email="owner@undangan.test">
            <i class="bi bi-person-badge me-2"></i>Owner — owner@undangan.test / password
        </button>
    </div>
    <p class="text-center small mt-3 mb-0">Belum punya akun? <a href="{{ route('register') }}" class="text-decoration-none">Daftar sebagai owner</a></p>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.demo-login').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById('email').value = button.dataset.email;
            document.getElementById('password').value = 'password';
        });
    });
</script>
@endpush
