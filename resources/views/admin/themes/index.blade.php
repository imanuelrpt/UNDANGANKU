@extends('layouts.dashboard')
@section('title', 'Kelola Tema')
@section('breadcrumb', 'Admin / Tema')

@section('content')
<div class="mb-4"><h1 class="h3 mb-1">Kelola Tema</h1><p class="text-muted small mb-0">Aktifkan atau nonaktifkan tema yang dapat dipilih owner.</p></div>

<div class="row g-3">
    @foreach ($themes as $key => $theme)
        @php($active = (bool) ($overrides[$key] ?? $theme['active']))
        <div class="col-md-6 col-xl-4">
            <div class="card card-soft h-100">
                <div class="card-body p-0">
                    <div style="height:130px;background:linear-gradient(135deg, {{ $theme['color'] }}, #171525);display:flex;align-items:center;justify-content:center">
                        <span class="text-white fw-semibold" style="text-shadow:0 1px 3px rgba(0,0,0,.5)">{{ $theme['name'] }}</span>
                    </div>
                    <div class="p-3">
                        <p class="small text-muted mb-2">{{ $theme['description'] }}</p>
                        <code class="small">{{ $key }}</code>
                        <span class="badge {{ $active ? 'bg-success' : 'bg-secondary' }} float-end">{{ $active ? 'Aktif' : 'Nonaktif' }}</span>
                        <form method="POST" action="{{ route('admin.themes.update', $key) }}" class="mt-3">
                            @csrf @method('PUT')
                            <input type="hidden" name="theme" value="{{ $key }}">
                            <input type="hidden" name="active" value="{{ $active ? 0 : 1 }}">
                            <button class="btn btn-sm {{ $active ? 'btn-outline-secondary' : 'btn-dark' }} w-100">{{ $active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
