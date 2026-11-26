@extends('layouts.auth')
@section('title', 'Lupa Password')

@section('content')
<h2 class="h4 mb-1">Lupa password?</h2>
<p class="text-muted small mb-4">Masukkan email Anda, kami akan mengirim tautan untuk mengatur ulang password.</p>

@if (session('status'))
    <div class="alert alert-success small">{{ session('status') }}</div>
@endif

<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <div class="mb-4">
        <label for="email" class="form-label small fw-semibold">Email</label>
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus>
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <button type="submit" class="btn btn-dark w-100 py-2">Kirim tautan reset</button>
</form>
<p class="text-center small mt-3 mb-0"><a href="{{ route('login') }}" class="text-decoration-none">Kembali ke masuk</a></p>
@endsection
