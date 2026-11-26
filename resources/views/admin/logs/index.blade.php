@extends('layouts.dashboard')
@section('title', 'Log Aktivitas')
@section('breadcrumb', 'Admin / Log')

@section('content')
<div class="mb-4"><h1 class="h3 mb-1">Log Aktivitas</h1><p class="text-muted small mb-0">Riwayat seluruh aksi penting di platform.</p></div>

<div class="card card-soft mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2">
            <div class="col-md-4"><input name="search" class="form-control form-control-sm" placeholder="Cari deskripsi" value="{{ request('search') }}"></div>
            <div class="col-md-3">
                <select name="action" class="form-select form-select-sm">
                    <option value="">Semua aksi</option>
                    @foreach ($actions as $action)<option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>@endforeach
                </select>
            </div>
            <div class="col-auto"><button class="btn btn-sm btn-outline-dark">Filter</button></div>
        </form>
    </div>
</div>

<div class="card card-soft">
    <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead><tr><th>Waktu</th><th>Aksi</th><th>Deskripsi</th><th>Pengguna</th><th>Undangan</th></tr></thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td class="small text-muted text-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td><code class="small">{{ $log->action }}</code></td>
                        <td class="small">{{ $log->description }}</td>
                        <td class="small">{{ $log->user?->name ?? 'Sistem' }}</td>
                        <td class="small">{{ $log->invitation?->title ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada aktivitas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body border-top">{{ $logs->links() }}</div>
</div>
@endsection
