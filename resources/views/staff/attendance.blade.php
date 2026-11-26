@extends('layouts.auth')
@section('title', 'Rekap Check-in')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="h5 mb-1">Rekap Check-in</h2>
        <p class="text-muted small mb-0">{{ $invitation->title }}</p>
    </div>
    <a href="{{ route('staff.scanner', $staffLink->token) }}" class="btn btn-sm btn-outline-dark"><i class="bi bi-arrow-left"></i> Scanner</a>
</div>

<div class="row g-2 mb-4">
    <div class="col-4"><div class="card stat-card p-3 text-center"><span class="text-muted small">Check-in</span><div class="stat-value">{{ $checked_in }}</div></div></div>
    <div class="col-4"><div class="card stat-card p-3 text-center"><span class="text-muted small">Total tamu</span><div class="stat-value">{{ $total_guests }}</div></div></div>
    <div class="col-4"><div class="card stat-card p-3 text-center"><span class="text-muted small">Total pax hadir</span><div class="stat-value">{{ $total_pax }}</div></div></div>
</div>

<div class="table-responsive" style="max-height: 55vh; overflow-y: auto;">
    <table class="table table-sm mb-0">
        <thead class="sticky-top bg-white"><tr><th>Nama</th><th>Grup</th><th>Pax</th><th>Metode</th><th>Waktu</th><th></th></tr></thead>
        <tbody>
            @forelse ($checkins as $checkin)
                <tr>
                    <td class="small fw-semibold">{{ $checkin->name }}</td>
                    <td class="small">{{ $checkin->group ?: '-' }}</td>
                    <td>
                        <input type="number" min="1" max="50" value="{{ $checkin->pax_actual }}" class="form-control form-control-sm pax-input" data-id="{{ $checkin->id }}" style="width:64px">
                    </td>
                    <td><span class="badge text-bg-light small">{{ $checkin->method->label() }}</span></td>
                    <td class="small text-muted">{{ $checkin->checked_in_at->format('H:i') }}</td>
                    <td><button class="btn btn-sm btn-light save-pax" data-id="{{ $checkin->id }}"><i class="bi bi-check"></i></button></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted small py-4">Belum ada tamu yang check-in.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
    const updateUrl = '{{ route('staff.checkin.update', [$staffLink->token, '__ID__']) }}';
    document.querySelectorAll('.save-pax').forEach(function (button) {
        button.addEventListener('click', function () {
            const id = button.dataset.id;
            const input = document.querySelector('.pax-input[data-id="' + id + '"]');
            fetch(updateUrl.replace('__ID__', id), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ pax_actual: input.value }),
            }).then(function (response) { return response.json(); }).then(function (data) {
                button.innerHTML = '<i class="bi bi-check text-success"></i>';
                setTimeout(function () { button.innerHTML = '<i class="bi bi-check"></i>'; }, 1500);
            });
        });
    });
</script>
@endpush
