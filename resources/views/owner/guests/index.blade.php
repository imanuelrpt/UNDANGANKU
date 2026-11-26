@extends('layouts.dashboard')
@section('title', 'Tamu: '.$invitation->title)
@section('breadcrumb', 'Undangan / Tamu')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="h3 mb-1">Daftar Tamu</h1>
        <p class="text-muted small mb-0">{{ $invitation->title }} · total {{ $stats['total'] }} tamu / {{ $stats['pax'] }} pax</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('guests.create', $invitation) }}" class="btn btn-dark btn-sm"><i class="bi bi-person-plus me-1"></i>Tambah tamu</a>
        <a href="{{ route('guests.template', $invitation) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-file-earmark-excel me-1"></i>Template</a>
        <a href="{{ route('guests.export', $invitation) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-download me-1"></i>Excel</a>
        <a href="{{ route('guests.export-pdf', $invitation) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-file-pdf me-1"></i>PDF</a>
    </div>
</div>

<div class="row g-2 mb-3">
    <div class="col-6 col-md-3"><div class="card stat-card p-3"><span class="text-muted small">Total tamu</span><div class="stat-value">{{ $stats['total'] }}</div></div></div>
    <div class="col-6 col-md-3"><div class="card stat-card p-3"><span class="text-muted small">Total kuota pax</span><div class="stat-value">{{ $stats['pax'] }}</div></div></div>
    <div class="col-6 col-md-3"><div class="card stat-card p-3"><span class="text-muted small">Sudah jawab RSVP</span><div class="stat-value">{{ $stats['responded'] }}</div></div></div>
    <div class="col-6 col-md-3"><div class="card stat-card p-3"><span class="text-muted small">Check-in</span><div class="stat-value">{{ $stats['checked_in'] }}</div></div></div>
</div>

@if ($importResult && count($importResult['failed']))
    <div class="alert alert-warning small">
        <strong>{{ $importResult['imported'] }} tamu diimpor.</strong> {{ count($importResult['failed']) }} baris gagal:
        <ul class="mb-0 mt-1">
            @foreach ($importResult['failed'] as $failure)
                <li>Baris {{ $failure['row'] }}: {{ implode(', ', $failure['errors']) }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card card-soft mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-4"><input name="search" class="form-control form-control-sm" placeholder="Cari nama / WhatsApp" value="{{ request('search') }}"></div>
            <div class="col-md-2">
                <select name="group" class="form-select form-select-sm">
                    <option value="">Semua grup</option>
                    @foreach ($groups as $value => $label)<option value="{{ $value }}" @selected(request('group') === $value)>{{ $label }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="attendance" class="form-select form-select-sm">
                    <option value="">Semua RSVP</option>
                    @foreach ($attendances as $value => $label)<option value="{{ $value }}" @selected(request('attendance') === $value)>{{ $label }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-2">
                <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="checked_in" value="1" id="checkedIn" @checked(request('checked_in'))><label class="form-check-label small" for="checkedIn">Sudah check-in</label></div>
            </div>
            <div class="col-auto"><button class="btn btn-sm btn-outline-dark">Filter</button></div>
        </form>
    </div>
</div>

<div class="card card-soft mb-3">
    <div class="card-body py-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label small mb-1">Import Excel / CSV</label>
                <form method="POST" action="{{ route('guests.import', $invitation) }}" enctype="multipart/form-data" class="d-flex gap-2">
                    @csrf
                    <input type="file" name="file" class="form-control form-control-sm" accept=".xlsx,.xls,.csv" required>
                    <button class="btn btn-sm btn-dark">Import</button>
                </form>
            </div>
            <div class="col-md-7">
                <label class="form-label small mb-1">Template pesan WhatsApp (digunakan saat menyalin link)</label>
                <input class="form-control form-control-sm" value="{{ $whatsappTemplate }}" readonly>
            </div>
        </div>
    </div>
</div>

<div class="card card-soft">
    <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead>
                <tr>
                    <th><input type="checkbox" id="checkAll"></th>
                    <th>Nama</th><th>Grup</th><th>WhatsApp</th><th>Kuota</th><th>RSVP</th><th>Check-in</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($guests as $guest)
                    @php
                        $message = str_replace(
                            ['{nama}', '{link}', '{tanggal}'],
                            [$guest->name, $guest->personalPath(), optional($invitation->events->first())->dateLabel() ?? '-'],
                            $whatsappTemplate,
                        );
                        $waUrl = $guest->phone
                            ? 'https://wa.me/'.preg_replace('/\D/', '', $guest->phone).'?text='.rawurlencode($message)
                            : null;
                    @endphp
                    <tr>
                        <td><input type="checkbox" class="guest-check" value="{{ $guest->id }}"></td>
                        <td><strong class="small">{{ $guest->name }}</strong>@if ($guest->open_count)<br><span class="text-muted" style="font-size:.7rem">Dibuka {{ $guest->open_count }}x</span>@endif</td>
                        <td><span class="badge text-bg-light">{{ $guest->group->label() }}</span></td>
                        <td class="small">{{ $guest->phone ?: '-' }}</td>
                        <td>{{ $guest->pax_quota }}</td>
                        <td>
                            @if ($guest->rsvp)
                                <span class="badge {{ $guest->rsvp->attendance->badgeClass() }}">{{ $guest->rsvp->attendance->label() }} ({{ $guest->rsvp->pax }})</span>
                            @else
                                <span class="badge text-bg-secondary">Belum jawab</span>
                            @endif
                        </td>
                        <td>
                            @if ($guest->checkin)
                                <span class="badge text-bg-success"><i class="bi bi-check-circle"></i> {{ $guest->checkin->checked_in_at->format('H:i') }}</span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="text-nowrap">
                            @if ($waUrl)
                                <a href="{{ $waUrl }}" target="_blank" class="btn btn-sm btn-light" title="Kirim WhatsApp"><i class="bi bi-whatsapp text-success"></i></a>
                            @endif
                            <button type="button" class="btn btn-sm btn-light copy-link" data-link="{{ $guest->personalPath() }}" title="Salin link"><i class="bi bi-link-45deg"></i></button>
                            <a href="{{ route('invitation.public.guest', [$invitation->slug, $guest->token]) }}" target="_blank" class="btn btn-sm btn-light" title="Lihat undangan"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('guests.edit', $guest) }}" class="btn btn-sm btn-light" title="Edit"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="{{ route('guests.destroy', $guest) }}" class="d-inline" data-confirm="Hapus tamu {{ $guest->name }}?">
                                @csrf @method('DELETE') <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8"><div class="empty-state small">Belum ada tamu. Tambah manual atau import dari Excel.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
        <form method="POST" action="{{ route('guests.bulk', $invitation) }}" id="bulkForm" class="d-flex gap-2">
            @csrf
            <input type="hidden" name="action" id="bulkAction">
            <input type="hidden" name="ids[]" id="bulkIds">
            <button type="button" class="btn btn-sm btn-outline-secondary" id="bulkSent">Tandai sudah dikirim</button>
            <button type="button" class="btn btn-sm btn-outline-danger" id="bulkDelete">Hapus terpilih</button>
        </form>
        {{ $guests->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
    const checkAll = document.getElementById('checkAll');
    const checks = Array.from(document.querySelectorAll('.guest-check'));
    checkAll.addEventListener('change', function () { checks.forEach(function (box) { box.checked = checkAll.checked; }); });
    function submitBulk(action) {
        const ids = checks.filter(function (box) { return box.checked; }).map(function (box) { return box.value; });
        if (!ids.length) { alert('Pilih minimal satu tamu.'); return; }
        document.getElementById('bulkAction').value = action;
        document.getElementById('bulkIds').value = ids.join(',');
        const form = document.getElementById('bulkForm');
        const input = document.getElementById('bulkIds');
        input.remove();
        ids.forEach(function (id) {
            const hidden = document.createElement('input');
            hidden.type = 'hidden'; hidden.name = 'ids[]'; hidden.value = id; form.appendChild(hidden);
        });
        form.submit();
    }
    document.getElementById('bulkSent').addEventListener('click', function () { submitBulk('mark_sent'); });
    document.getElementById('bulkDelete').addEventListener('click', function () {
        if (confirm('Hapus tamu yang dipilih?')) submitBulk('delete');
    });
    document.querySelectorAll('.copy-link').forEach(function (button) {
        button.addEventListener('click', function () {
            navigator.clipboard.writeText(button.dataset.link).then(function () {
                button.innerHTML = '<i class="bi bi-check text-success"></i>';
                setTimeout(function () { button.innerHTML = '<i class="bi bi-link-45deg"></i>'; }, 1500);
            });
        });
    });
</script>
@endpush
