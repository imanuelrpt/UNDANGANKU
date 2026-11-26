@extends('layouts.auth')
@section('title', 'Verifikasi Email')

@section('content')
<h2 class="h4 mb-1">Verifikasi email</h2>
<p class="text-muted small mb-4">Terima kasih telah mendaftar! Silakan klik tautan verifikasi yang kami kirim ke email Anda.</p>

@if (session('status') === 'verification-link-sent')
    <div class="alert alert-success small">Tautan verifikasi baru telah dikirim.</div>
@endif

<form method="POST" action="{{ route('verification.send') }}">
    @csrf
    <button type="submit" class="btn btn-dark w-100 py-2">Kirim ulang tautan verifikasi</button>
</form>
<form method="POST" action="{{ route('logout') }}" class="mt-3">
    @csrf
    <button type="submit" class="btn btn-link w-100 small">Keluar</button>
</form>
@endsection
