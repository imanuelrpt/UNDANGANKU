@extends('layouts.dashboard')
@section('title', 'Kelola: '.$invitation->title)
@section('breadcrumb', 'Undangan / '.$invitation->title)

@section('content')
@php
    $steps = [
        1 => 'Info Dasar', 2 => 'Mempelai', 3 => 'Cerita', 4 => 'Acara', 5 => 'Galeri',
        6 => 'Musik', 7 => 'RSVP', 8 => 'Amplop & Ucapan', 9 => 'Tamu & Publikasi',
    ];
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h1 class="h3 mb-1">{{ $invitation->title }}</h1>
        <p class="text-muted small mb-0"><a href="{{ $invitation->path() }}" target="_blank" class="text-decoration-none">/u/{{ $invitation->slug }}</a> · <span class="badge {{ $invitation->status->badgeClass() }}">{{ $invitation->status->label() }}</span></p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('invitations.preview', $invitation) }}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye me-1"></i>Preview</a>
        <a href="{{ route('guests.index', $invitation) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-people me-1"></i>Tamu</a>
    </div>
</div>

<div class="wizard-steps mb-4">
    @foreach ($steps as $number => $label)
        <a href="{{ route('invitations.edit', [$invitation, 'step' => $number]) }}" class="{{ $step === $number ? 'active' : '' }}">{{ $number }}. {{ $label }}</a>
    @endforeach
</div>

@if ($step === 1)
    <div class="card card-soft">
        <div class="card-body p-4">
            <h2 class="h6 mb-3">Informasi dasar</h2>
            <form method="POST" action="{{ route('invitations.update', $invitation) }}">
                @csrf @method('PUT')
                <input type="hidden" name="step" value="1">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label small fw-semibold">Judul undangan</label>
                        <input name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $invitation->title) }}" required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Jenis acara</label>
                        <select name="event_type" class="form-select @error('event_type') is-invalid @enderror">
                            @foreach ($eventTypes as $value => $label)
                                <option value="{{ $value }}" @selected(old('event_type', $invitation->event_type->value) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('event_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Slug</label>
                        <input name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $invitation->slug) }}" required>
                        @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Tema</label>
                        <select name="theme" class="form-select @error('theme') is-invalid @enderror">
                            @foreach ($themes as $key => $name)
                                <option value="{{ $key }}" @selected(old('theme', $invitation->theme) === $key)>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('theme') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Kedaluwarsa</label>
                        <input type="datetime-local" name="expires_at" class="form-control" value="{{ old('expires_at', $invitation->expires_at?->format('Y-m-d\TH:i')) }}">
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-4"><button class="btn btn-dark">Simpan & lanjut <i class="bi bi-arrow-right ms-1"></i></button></div>
            </form>
        </div>
    </div>
@elseif ($step === 2)
    <div class="card card-soft">
        <div class="card-body p-4">
            <h2 class="h6 mb-3">Mempelai / tuan rumah</h2>
            <p class="text-muted small">Maksimal 4 orang. Foto mempelai dapat ditambahkan lewat form terpisah di bawah.</p>
            <form method="POST" action="{{ route('invitations.update', $invitation) }}">
                @csrf @method('PUT')
                <input type="hidden" name="step" value="2">
                @for ($i = 0; $i < 2; $i++)
                    @php($host = $invitation->hosts->get($i))
                    <div class="border rounded-3 p-3 mb-3">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">Peran</label>
                                <select name="hosts[{{ $i }}][type]" class="form-select" required>
                                    @foreach (['bride' => 'Mempelai wanita', 'groom' => 'Mempelai pria', 'host' => 'Tuan rumah'] as $value => $label)
                                        <option value="{{ $value }}" @selected(old("hosts.$i.type", $host?->type ?? ($i === 0 ? 'bride' : 'groom')) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">Nama lengkap</label>
                                <input name="hosts[{{ $i }}][name]" class="form-control" value="{{ old("hosts.$i.name", $host?->name) }}" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold">Nama panggilan</label>
                                <input name="hosts[{{ $i }}][nickname]" class="form-control" value="{{ old("hosts.$i.nickname", $host?->nickname) }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold">Anak ke-</label>
                                <input name="hosts[{{ $i }}][birth_order]" class="form-control" value="{{ old("hosts.$i.birth_order", $host?->birth_order) }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold">Urutan</label>
                                <input type="number" name="hosts[{{ $i }}][sort_order]" class="form-control" value="{{ $host?->sort_order ?? $i }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Nama orang tua</label>
                                <input name="hosts[{{ $i }}][parent_name]" class="form-control" value="{{ old("hosts.$i.parent_name", $host?->parent_name) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Deskripsi singkat</label>
                                <input name="hosts[{{ $i }}][description]" class="form-control" value="{{ old("hosts.$i.description", $host?->description) }}">
                            </div>
                        </div>
                    </div>
                @endfor
                <div class="d-flex justify-content-end"><button class="btn btn-dark">Simpan & lanjut <i class="bi bi-arrow-right ms-1"></i></button></div>
            </form>
            @foreach ($invitation->hosts as $host)
                <div class="border-top pt-3 mt-4">
                    <div class="d-flex align-items-center gap-3">
                        @if ($host->photoUrl())
                            <img src="{{ $host->photoUrl() }}" alt="" class="rounded" style="width:54px;height:54px;object-fit:cover">
                        @else
                            <span class="rounded bg-light d-flex align-items-center justify-content-center" style="width:54px;height:54px"><i class="bi bi-person text-muted"></i></span>
                        @endif
                        <div class="small"><strong>{{ $host->name }}</strong><br><span class="text-muted">{{ $host->type }}</span></div>
                        <form method="POST" action="{{ route('hosts.photo', [$invitation, $host]) }}" enctype="multipart/form-data" class="ms-auto d-flex gap-2 align-items-center">
                            @csrf
                            <input type="file" name="photo" class="form-control form-control-sm" accept="image/*" required style="max-width:220px">
                            <button class="btn btn-sm btn-outline-dark">Unggah foto</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@elseif ($step === 3)
    <div class="card card-soft">
        <div class="card-body p-4">
            <h2 class="h6 mb-3">Cerita & sampul</h2>
            <form method="POST" action="{{ route('invitations.update', $invitation) }}">
                @csrf @method('PUT')
                <input type="hidden" name="step" value="3">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Cerita cinta</label>
                    <textarea name="settings[story]" rows="5" class="form-control @error('settings.story') is-invalid @enderror">{{ old('settings.story', $invitation->setting('story')) }}</textarea>
                    @error('settings.story') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Kutipan</label>
                        <input name="settings[quote]" class="form-control" value="{{ old('settings.quote', $invitation->setting('quote')) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Dress code</label>
                        <input name="settings[dress_code]" class="form-control" value="{{ old('settings.dress_code', $invitation->setting('dress_code')) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Sapaan sampul</label>
                        <input name="settings[cover_message]" class="form-control" value="{{ old('settings.cover_message', $invitation->setting('cover_message')) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Template WhatsApp</label>
                        <input name="settings[whatsapp_template]" class="form-control" value="{{ old('settings.whatsapp_template', $invitation->setting('whatsapp_template')) }}" placeholder="Hai {nama}... {link}...">
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-4"><button class="btn btn-dark">Simpan & lanjut <i class="bi bi-arrow-right ms-1"></i></button></div>
            </form>
        </div>
    </div>
@elseif ($step === 4)
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card card-soft">
                <div class="card-body p-4">
                    <h2 class="h6 mb-3">Sesi acara</h2>
                    @forelse ($invitation->events as $event)
                        <div class="border rounded-3 p-3 mb-3">
                            <div class="d-flex justify-content-between">
                                <div><strong class="small">{{ $event->name }}</strong><br><span class="text-muted small">{{ $event->dateLabel() }} · {{ $event->timeLabel() }}</span></div>
                                <form method="POST" action="{{ route('events.destroy', $event) }}" data-confirm="Hapus sesi ini?">
                                    @csrf @method('DELETE') <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                            <div class="small text-muted mt-1"><i class="bi bi-geo-alt me-1"></i>{{ $event->venue ?: 'Lokasi belum diisi' }}</div>
                            <details class="mt-2"><summary class="small text-primary">Edit sesi</summary>
                                <form method="POST" action="{{ route('events.update', $event) }}" class="mt-2">
                                    @csrf @method('PUT')
                                    <div class="row g-2">
                                        <div class="col-md-6"><input name="name" class="form-control form-control-sm" value="{{ $event->name }}" required></div>
                                        <div class="col-md-6"><input type="datetime-local" name="starts_at" class="form-control form-control-sm" value="{{ $event->starts_at?->format('Y-m-d\TH:i') }}" required></div>
                                        <div class="col-md-6"><input type="datetime-local" name="ends_at" class="form-control form-control-sm" value="{{ $event->ends_at?->format('Y-m-d\TH:i') }}"></div>
                                        <div class="col-md-6">
                                            <select name="timezone" class="form-select form-select-sm">
                                                @foreach ($timezones as $zone => $tz)
                                                    <option value="{{ $zone }}" @selected($event->timezone === $zone)>{{ $zone }} — {{ $tz }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12"><input name="venue" class="form-control form-control-sm" value="{{ $event->venue }}" placeholder="Nama tempat"></div>
                                        <div class="col-12"><input name="address" class="form-control form-control-sm" value="{{ $event->address }}" placeholder="Alamat lengkap"></div>
                                        <div class="col-md-6"><input type="number" step="any" name="lat" class="form-control form-control-sm" value="{{ $event->lat }}" placeholder="Latitude"></div>
                                        <div class="col-md-6"><input type="number" step="any" name="lng" class="form-control form-control-sm" value="{{ $event->lng }}" placeholder="Longitude"></div>
                                    </div>
                                    <button class="btn btn-sm btn-dark mt-2">Simpan</button>
                                </form>
                            </details>
                        </div>
                    @empty
                        <div class="empty-state small">Belum ada sesi acara.</div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card card-soft">
                <div class="card-body p-4">
                    <h2 class="h6 mb-3">Tambah sesi</h2>
                    <form method="POST" action="{{ route('events.store', $invitation) }}">
                        @csrf
                        <div class="mb-2"><label class="form-label small">Nama sesi</label><input name="name" class="form-control form-control-sm" placeholder="Akad nikah / Resepsi" required></div>
                        <div class="mb-2"><label class="form-label small">Mulai</label><input type="datetime-local" name="starts_at" class="form-control form-control-sm" required></div>
                        <div class="mb-2"><label class="form-label small">Selesai</label><input type="datetime-local" name="ends_at" class="form-control form-control-sm"></div>
                        <div class="mb-2">
                            <label class="form-label small">Zona waktu</label>
                            <select name="timezone" class="form-select form-select-sm">
                                @foreach ($timezones as $zone => $tz)<option value="{{ $zone }}">{{ $zone }}</option>@endforeach
                            </select>
                        </div>
                        <div class="mb-2"><label class="form-label small">Tempat</label><input name="venue" class="form-control form-control-sm"></div>
                        <div class="mb-2"><label class="form-label small">Alamat</label><input name="address" class="form-control form-control-sm"></div>
                        <div class="row g-2 mb-2">
                            <div class="col-6"><input type="number" step="any" name="lat" class="form-control form-control-sm" placeholder="Latitude"></div>
                            <div class="col-6"><input type="number" step="any" name="lng" class="form-control form-control-sm" placeholder="Longitude"></div>
                        </div>
                        <button class="btn btn-sm btn-dark w-100">Tambah sesi</button>
                    </form>
                    <p class="text-muted small mt-3 mb-0">Isi latitude & longitude untuk menampilkan peta lokasi di undangan.</p>
                </div>
            </div>
        </div>
    </div>
@elseif ($step === 5)
    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card card-soft">
                <div class="card-body p-4">
                    <h2 class="h6 mb-3">Unggah galeri</h2>
                    <form method="POST" action="{{ route('photos.store', $invitation) }}" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="photos[]" class="form-control form-control-sm" accept="image/*" multiple required>
                        <button class="btn btn-sm btn-dark w-100 mt-3">Unggah foto</button>
                        <p class="text-muted small mt-2 mb-0">Maksimal {{ config('undangan.max_photos') }} foto, tiap file 5 MB. Foto otomatis di-resize.</p>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card card-soft">
                <div class="card-body p-4">
                    <h2 class="h6 mb-3">Foto galeri ({{ $invitation->photos->count() }})</h2>
                    @if ($invitation->photos->isEmpty())
                        <div class="empty-state small">Belum ada foto.</div>
                    @else
                        <div id="photoGrid" class="row g-2">
                            @foreach ($invitation->photos as $photo)
                                <div class="col-4 col-md-3 photo-item" data-id="{{ $photo->id }}">
                                    <div class="position-relative">
                                        <img src="{{ $photo->thumbnailUrl() }}" alt="{{ $photo->caption }}" class="photo-thumb">
                                        <form method="POST" action="{{ route('photos.destroy', $photo) }}" data-confirm="Hapus foto ini?" class="position-absolute top-0 end-0 p-1">
                                            @csrf @method('DELETE') <button class="btn btn-sm btn-danger rounded-circle"><i class="bi bi-x"></i></button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-muted small mt-3 mb-0">Seret foto untuk mengubah urutan.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@elseif ($step === 6)
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card card-soft">
                <div class="card-body p-4">
                    <h2 class="h6 mb-3">Musik latar</h2>
                    <form method="POST" action="{{ route('invitations.update', $invitation) }}">
                        @csrf @method('PUT')
                        <input type="hidden" name="step" value="6">
                        <label class="form-label small">Pilih musik bawaan</label>
                        <select name="settings[music_track]" class="form-select mb-3">
                            @foreach (config('undangan.music_tracks') as $key => $track)
                                <option value="{{ $key }}" @selected($invitation->setting('music_track') === $key)>{{ $track['label'] }}</option>
                            @endforeach
                        </select>
                        <label class="form-label small">Link YouTube (opsional)</label>
                        <input name="settings[youtube_url]" class="form-control mb-3" value="{{ $invitation->setting('youtube_url') }}" placeholder="https://www.youtube.com/watch?v=...">
                        <button class="btn btn-dark">Simpan</button>
                    </form>
                    <hr>
                    <form method="POST" action="{{ route('music.store', $invitation) }}" enctype="multipart/form-data">
                        @csrf
                        <label class="form-label small">Atau unggah MP3 sendiri</label>
                        <input type="file" name="music" class="form-control form-control-sm" accept="audio/mpeg" required>
                        <button class="btn btn-sm btn-outline-dark mt-2">Unggah musik</button>
                    </form>
                    @if ($invitation->setting('music_file'))
                        <form method="POST" action="{{ route('music.destroy', $invitation) }}" class="mt-2" data-confirm="Hapus musik yang diunggah?">
                            @csrf @method('DELETE') <button class="btn btn-sm btn-link text-danger">Hapus musik unggahan</button>
                        </form>
                    @endif
                    <p class="text-muted small mt-3 mb-0">Musik tidak diputar otomatis. Tamu akan melihat tombol putar/jeda.</p>
                </div>
            </div>
        </div>
    </div>
@elseif ($step === 7)
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card card-soft">
                <div class="card-body p-4">
                    <h2 class="h6 mb-3">Pengaturan RSVP</h2>
                    <form method="POST" action="{{ route('invitations.update', $invitation) }}">
                        @csrf @method('PUT')
                        <input type="hidden" name="step" value="7">
                        <input type="hidden" name="settings[rsvp_enabled]" value="0">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="settings[rsvp_enabled]" value="1" id="rsvpEnabled" @checked($invitation->setting('rsvp_enabled'))>
                            <label class="form-check-label small" for="rsvpEnabled">Aktifkan konfirmasi kehadiran</label>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small">Batas waktu RSVP</label>
                                <input type="datetime-local" name="settings[rsvp_deadline]" class="form-control" value="{{ $invitation->setting('rsvp_deadline') ? \Carbon\Carbon::parse($invitation->setting('rsvp_deadline'))->format('Y-m-d\TH:i') : '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Maks. tamu tambahan (tanpa token)</label>
                                <input type="number" name="settings[max_extra_guests]" class="form-control" min="0" max="10" value="{{ $invitation->setting('max_extra_guests') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label small">Label pertanyaan utama</label>
                                <input name="settings[question_label]" class="form-control" value="{{ $invitation->setting('question_label') }}" placeholder="Apakah Anda akan hadir?">
                            </div>
                        </div>
                        <button class="btn btn-dark mt-3">Simpan pengaturan</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card card-soft">
                <div class="card-body p-4">
                    <h2 class="h6 mb-3">Pertanyaan tambahan</h2>
                    @foreach ($invitation->questions as $question)
                        <div class="d-flex align-items-center gap-2 border-bottom py-2">
                            <span class="small flex-grow-1 {{ $question->is_active ? '' : 'text-muted text-decoration-line-through' }}">{{ $question->label }}</span>
                            <form method="POST" action="{{ route('questions.update', $question) }}">
                                @csrf @method('PUT')
                                <input type="hidden" name="label" value="{{ $question->label }}">
                                <input type="hidden" name="is_active" value="{{ $question->is_active ? 0 : 1 }}">
                                <button class="btn btn-sm btn-light" title="Aktif/nonaktif"><i class="bi bi-{{ $question->is_active ? 'toggle-on' : 'toggle-off' }}"></i></button>
                            </form>
                            <form method="POST" action="{{ route('questions.destroy', $question) }}" data-confirm="Hapus pertanyaan ini?">
                                @csrf @method('DELETE') <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    @endforeach
                    <form method="POST" action="{{ route('questions.store', $invitation) }}" class="mt-3 d-flex gap-2">
                        @csrf
                        <input name="label" class="form-control form-control-sm" placeholder="Pertanyaan baru" required>
                        <button class="btn btn-sm btn-dark">Tambah</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@elseif ($step === 8)
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card card-soft">
                <div class="card-body p-4">
                    <h2 class="h6 mb-3">Amplop digital</h2>
                    @foreach ($invitation->gifts as $gift)
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <div class="small"><strong>{{ $gift->bank_name }}</strong> · {{ $gift->account_number }}<br><span class="text-muted">{{ $gift->account_name }} ({{ $gift->label() }})</span></div>
                            <form method="POST" action="{{ route('gifts.destroy', $gift) }}" data-confirm="Hapus rekening ini?">
                                @csrf @method('DELETE') <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    @endforeach
                    <form method="POST" action="{{ route('gifts.store', $invitation) }}" class="mt-3 row g-2">
                        @csrf
                        <div class="col-4">
                            <select name="type" class="form-select form-select-sm"><option value="bank">Bank</option><option value="ewallet">E-Wallet</option></select>
                        </div>
                        <div class="col-8"><input name="bank_name" class="form-control form-control-sm" placeholder="Nama bank" required></div>
                        <div class="col-6"><input name="account_number" class="form-control form-control-sm" placeholder="Nomor rekening" required></div>
                        <div class="col-6"><input name="account_name" class="form-control form-control-sm" placeholder="Atas nama" required></div>
                        <div class="col-12"><button class="btn btn-sm btn-dark w-100">Tambah rekening</button></div>
                    </form>
                    <form method="POST" action="{{ route('invitations.update', $invitation) }}" class="mt-4">
                        @csrf @method('PUT')
                        <input type="hidden" name="step" value="8">
                        <label class="form-label small">Alamat pengiriman hadiah</label>
                        <input name="settings[gift_address]" class="form-control" value="{{ $invitation->setting('gift_address') }}">
                        <input type="hidden" name="settings[show_wishes]" value="0">
                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" name="settings[show_wishes]" value="1" id="showWishes" @checked($invitation->setting('show_wishes'))>
                            <label class="form-check-label small" for="showWishes">Tampilkan buku tamu di undangan</label>
                        </div>
                        <button class="btn btn-dark btn-sm mt-3">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card card-soft">
                <div class="card-body p-4">
                    <h2 class="h6 mb-3">Tamu & link personal</h2>
                    <p class="small text-muted">Kelola daftar tamu, import Excel, dan kirim link personal berisi tiket QR.</p>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('guests.index', $invitation) }}" class="btn btn-sm btn-outline-dark"><i class="bi bi-people me-1"></i>Kelola tamu</a>
                        <a href="{{ route('guests.template', $invitation) }}" class="btn btn-sm btn-outline-dark"><i class="bi bi-download me-1"></i>Template Excel</a>
                        <a href="{{ route('reports.pdf', $invitation) }}" class="btn btn-sm btn-outline-dark"><i class="bi bi-file-earmark-pdf me-1"></i>Laporan PDF</a>
                    </div>
                    <hr>
                    <h3 class="h6 mt-4 mb-3">Link petugas check-in</h3>
                    @if (session('staff_link'))
                        <div class="alert alert-success small">
                            <strong>Link baru:</strong> <a href="{{ session('staff_link')['url'] }}" target="_blank" class="text-break">{{ session('staff_link')['url'] }}</a>
                            @if (session('staff_link')['pin'])<br>PIN: <strong>{{ session('staff_link')['pin'] }}</strong>@endif
                        </div>
                    @endif
                    @foreach ($invitation->staffLinks as $link)
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <div class="small"><strong>{{ $link->label }}</strong><br><a href="{{ $link->url() }}" target="_blank" class="text-muted text-break">{{ $link->url() }}</a> <span class="badge text-bg-secondary">{{ $link->hasPin() ? 'PIN' : 'Tanpa PIN' }}</span></div>
                            <form method="POST" action="{{ route('staff-links.destroy', $link) }}" data-confirm="Hapus link petugas ini?">
                                @csrf @method('DELETE') <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    @endforeach
                    <form method="POST" action="{{ route('staff-links.store', $invitation) }}" class="mt-3 row g-2">
                        @csrf
                        <div class="col-6"><input name="label" class="form-control form-control-sm" placeholder="Nama petugas" required></div>
                        <div class="col-3"><input name="pin" class="form-control form-control-sm" placeholder="PIN 6 digit" inputmode="numeric" maxlength="6"></div>
                        <div class="col-3"><button class="btn btn-sm btn-dark w-100">Buat link</button></div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card card-soft">
                <div class="card-body p-4">
                    <h2 class="h6 mb-3">Publikasi</h2>
                    @if ($invitation->status->value === 'published')
                        <p class="small text-muted">Undangan sudah dapat diakses publik.</p>
                        <form method="POST" action="{{ route('invitations.unpublish', $invitation) }}">
                            @csrf <button class="btn btn-outline-warning w-100">Jadikan draft</button>
                        </form>
                    @else
                        <p class="small text-muted">Publikasikan undangan agar tamu dapat mengaksesnya.</p>
                        <form method="POST" action="{{ route('invitations.publish', $invitation) }}">
                            @csrf <button class="btn btn-success w-100"><i class="bi bi-send me-1"></i>Publikasikan</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@if ($step === 5 && $invitation->photos->isNotEmpty())
    @push('scripts')
    <script>
        // Urutan galeri: kirim array id ke endpoint reorder setelah drag selesai.
        const grid = document.getElementById('photoGrid');
        let dragged = null;
        grid.querySelectorAll('.photo-item').forEach(function (item) {
            item.draggable = true;
            item.addEventListener('dragstart', function () { dragged = item; });
            item.addEventListener('dragover', function (event) { event.preventDefault(); });
            item.addEventListener('drop', function (event) {
                event.preventDefault();
                if (!dragged || dragged === item) return;
                const box = item.parentNode;
                box.insertBefore(dragged, item);
                const order = Array.from(box.children).map(function (child) { return child.dataset.id; });
                fetch('{{ route('photos.reorder', $invitation) }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ order: order }),
                });
                dragged = null;
            });
        });
    </script>
    @endpush
@endif
