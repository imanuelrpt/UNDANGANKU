<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="{{ $theme['color'] }}">
    <meta name="description" content="Undangan digital {{ $invitation->title }} — {{ $invitation->event_type->label() }}">
    <title>{{ $invitation->title }} — Undangan Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">
    <link href="{{ asset('css/themes/base.css') }}" rel="stylesheet">
    <link href="{{ asset($theme['css']) }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="uk-body">
    @if ($isPreview)
        <div class="uk-preview-bar"><i class="bi bi-eye me-2"></i>Mode pratinjau — undangan belum dipublikasikan. <a href="{{ route('invitations.edit', $invitation) }}">Kembali ke editor</a></div>
    @endif

    @if ($isExpired)
        <div class="uk-preview-bar">Undangan ini sudah melewati tanggal kedaluwarsa.</div>
    @endif

    @yield('content')

    @if ($musicUrl)
        <button type="button" id="musicToggle" class="uk-music-btn" title="Putar/jeda musik" aria-label="Putar atau jeda musik">
            <i class="bi bi-music-note-beamed"></i>
        </button>
        <audio id="invitationMusic" loop preload="none" src="{{ $musicUrl }}"></audio>
    @endif

    <button type="button" id="backToTop" class="uk-top-btn" title="Kembali ke atas"><i class="bi bi-arrow-up"></i></button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        if (window.AOS) { AOS.init({ duration: 750, once: true, offset: 60 }); }
        @if (session('success'))
            Swal.fire({ icon: 'success', title: 'Terima kasih', text: @json(session('success')), timer: 3500, showConfirmButton: false });
        @endif
    </script>
    <script src="{{ asset('js/invitation.js') }}"></script>
    @stack('scripts')
</body>
</html>
