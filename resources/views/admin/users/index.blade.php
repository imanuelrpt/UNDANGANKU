@extends('layouts.dashboard')
@section('title', 'Kelola Akun')
@section('breadcrumb', 'Admin / Akun')

@section('content')
<div class="mb-4"><h1 class="h3 mb-1">Kelola Akun</h1><p class="text-muted small mb-0">Atur role dan status akun pengguna.</p></div>

<div class="card card-soft mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2">
            <div class="col-md-6"><input name="search" class="form-control form-control-sm" placeholder="Cari nama atau email" value="{{ request('search') }}"></div>
            <div class="col-md-3">
                <select name="role" class="form-select form-select-sm">
                    <option value="">Semua role</option>
                    <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                    <option value="owner" @selected(request('role') === 'owner')>Owner</option>
                </select>
            </div>
            <div class="col-auto"><button class="btn btn-sm btn-outline-dark">Filter</button></div>
        </form>
    </div>
</div>

<div class="card card-soft">
    <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead><tr><th>Nama</th><th>Email</th><th>Role</th><th>Status</th><th>Undangan</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td class="small fw-semibold">{{ $user->name }}</td>
                        <td class="small">{{ $user->email }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                                @csrf @method('PUT')
                                <input type="hidden" name="name" value="{{ $user->name }}">
                                <input type="hidden" name="email" value="{{ $user->email }}">
                                <select name="role" class="form-select form-select-sm" style="width:110px" onchange="this.form.submit()">
                                    <option value="owner" @selected($user->role->value === 'owner')>Owner</option>
                                    <option value="admin" @selected($user->role->value === 'admin')>Admin</option>
                                </select>
                            </form>
                        </td>
                        <td><span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td class="small">{{ $user->invitations_count }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                                @csrf <button class="btn btn-sm btn-light">{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada akun.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body border-top">{{ $users->links() }}</div>
</div>
@endsection
