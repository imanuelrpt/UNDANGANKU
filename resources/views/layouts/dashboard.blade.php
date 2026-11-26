<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — UndanganKu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
<div class="app-shell">
    <aside class="sidebar" id="sidebar" aria-label="Navigasi utama">
        <div class="sidebar-brand">
            <a href="{{ route('dashboard') }}" class="sidebar-brand-link" title="UndanganKu">
                <span class="sidebar-mark">UK</span>
                <span class="sidebar-brand-text">
                    <strong>UndanganKu</strong>
                    <small>Ruang kerja</small>
                </span>
            </a>
            <button class="sidebar-close" id="sidebarClose" type="button" aria-label="Tutup navigasi">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <nav class="nav flex-column">
            <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="bi-grid-1x2" label="Ringkasan" />
            @if (auth()->user()?->isAdmin())
                <div class="sidebar-label">Admin</div>
                <x-sidebar-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" icon="bi-shield-check" label="Panel Admin" />
                <x-sidebar-link :href="route('admin.users')" :active="request()->routeIs('admin.users*')" icon="bi-people" label="Kelola Akun" />
                <x-sidebar-link :href="route('admin.invitations')" :active="request()->routeIs('admin.invitations*')" icon="bi-envelope-paper" label="Semua Undangan" />
                <x-sidebar-link :href="route('admin.themes')" :active="request()->routeIs('admin.themes*')" icon="bi-palette" label="Tema" />
                <x-sidebar-link :href="route('admin.logs')" :active="request()->routeIs('admin.logs*')" icon="bi-clock-history" label="Log Aktivitas" />
            @else
                <div class="sidebar-label">Undangan</div>
                <x-sidebar-link :href="route('invitations.index')" :active="request()->routeIs('invitations.*')" icon="bi-envelope-paper" label="Undangan Saya" />
                <x-sidebar-link :href="route('invitations.create')" :active="request()->routeIs('invitations.create')" icon="bi-plus-lg" label="Buat Undangan" />
                <div class="sidebar-label">Data</div>
                @php($firstInvitation = auth()->user()?->invitations()->first())
                <x-sidebar-link :href="$firstInvitation ? route('wishes.index', $firstInvitation) : route('invitations.index')" :active="request()->routeIs('wishes.*')" icon="bi-chat-quote" label="Buku Tamu" />
                <x-sidebar-link :href="$firstInvitation ? route('guests.index', $firstInvitation) : route('invitations.index')" :active="request()->routeIs('guests.*')" icon="bi-person-lines-fill" label="Tamu & RSVP" />
            @endif
            <div class="sidebar-label">Akun</div>
            <x-sidebar-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')" icon="bi-person-gear" label="Profil" />
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="nav-link sidebar-nav-link text-danger" type="submit" title="Keluar">
                    <i class="bi bi-box-arrow-left me-2"></i><span class="sidebar-nav-label">Keluar</span>
                </button>
            </form>
        </nav>
    </aside>

    <div class="sidebar-backdrop" id="sidebarBackdrop" aria-hidden="true"></div>

    <div class="main-area">
        <header class="topbar">
            <div class="topbar-left">
                <button class="sidebar-toggle" id="sidebarToggle" type="button" aria-controls="sidebar" aria-expanded="true" aria-label="Perkecil sidebar">
                    <i class="bi bi-list"></i>
                </button>
                <div class="topbar-context">
                    <span>Ruang kerja</span>
                    <span>@yield('breadcrumb', 'Dashboard')</span>
                </div>
            </div>
            <div class="topbar-right">
                <span class="uk-role">{{ auth()->user()?->role?->value ?? '-' }}</span>
                <a href="{{ route('profile.edit') }}" class="topbar-user">
                    <span class="avatar">{{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}</span>
                    <span class="small d-none d-sm-inline">{{ auth()->user()?->name }}</span>
                </a>
            </div>
        </header>

        <main class="content">
            @yield('content')
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const body = document.body;
        const sidebar = document.getElementById('sidebar');
        const toggle = document.getElementById('sidebarToggle');
        const closeButton = document.getElementById('sidebarClose');
        const backdrop = document.getElementById('sidebarBackdrop');
        const mobileQuery = window.matchMedia('(max-width: 991.98px)');
        const storageKey = 'undanganku.sidebar.collapsed';

        const isMobile = function () { return mobileQuery.matches; };
        const isOpen = function () { return body.classList.contains('sidebar-open'); };

        const syncToggle = function () {
            if (isMobile()) {
                toggle.setAttribute('aria-expanded', isOpen() ? 'true' : 'false');
                toggle.setAttribute('aria-label', isOpen() ? 'Tutup navigasi' : 'Buka navigasi');
            } else {
                const collapsed = body.classList.contains('sidebar-collapsed');
                toggle.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
                toggle.setAttribute('aria-label', collapsed ? 'Buka sidebar' : 'Perkecil sidebar');
            }
        };

        const openSidebar = function () {
            body.classList.add('sidebar-open');
            syncToggle();
        };

        const closeSidebar = function () {
            body.classList.remove('sidebar-open');
            syncToggle();
        };

        const setCollapsed = function (collapsed) {
            body.classList.toggle('sidebar-collapsed', collapsed);
            try { localStorage.setItem(storageKey, collapsed ? '1' : '0'); } catch (error) { /* storage opsional */ }
            syncToggle();
        };

        try {
            if (localStorage.getItem(storageKey) === '1') {
                body.classList.add('sidebar-collapsed');
            }
        } catch (error) { /* storage opsional */ }

        toggle.addEventListener('click', function () {
            if (isMobile()) {
                isOpen() ? closeSidebar() : openSidebar();
            } else {
                setCollapsed(!body.classList.contains('sidebar-collapsed'));
            }
        });

        closeButton.addEventListener('click', closeSidebar);
        backdrop.addEventListener('click', closeSidebar);

        sidebar.querySelectorAll('a, button[type="submit"]').forEach(function (element) {
            element.addEventListener('click', function () {
                if (isMobile()) { closeSidebar(); }
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && isOpen()) { closeSidebar(); }
        });

        mobileQuery.addEventListener('change', function () {
            closeSidebar();
            syncToggle();
        });

        syncToggle();

        @if (session('success'))
            Swal.fire({ icon: 'success', title: 'Berhasil', text: @json(session('success')), timer: 2800, showConfirmButton: false });
        @endif
        @if (session('error'))
            Swal.fire({ icon: 'error', title: 'Perhatian', text: @json(session('error')) });
        @endif
        @if ($errors->any())
            Swal.fire({ icon: 'warning', title: 'Cek kembali', html: @json($errors->first()) });
        @endif

        document.querySelectorAll('form[data-confirm]').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                Swal.fire({
                    title: 'Yakin?', text: form.dataset.confirm, icon: 'warning',
                    showCancelButton: true, confirmButtonText: 'Ya, lanjutkan', cancelButtonText: 'Batal',
                }).then(function (result) { if (result.isConfirmed) form.submit(); });
            });
        });
    });
</script>
@stack('scripts')
</body>
</html>
