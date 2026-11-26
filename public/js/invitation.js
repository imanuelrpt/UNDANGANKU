document.addEventListener('DOMContentLoaded', function () {
    // Musik latar: selalu melalui interaksi pengguna, tidak autoplay.
    const musicButton = document.getElementById('musicToggle');
    const music = document.getElementById('invitationMusic');
    if (musicButton && music) {
        musicButton.addEventListener('click', function () {
            if (music.paused) {
                music.play().then(function () {
                    musicButton.classList.add('playing');
                    musicButton.innerHTML = '<i class="bi bi-volume-up-fill"></i>';
                }).catch(function () {
                    alert('Browser menolak pemutaran audio. Silakan coba lagi.');
                });
            } else {
                music.pause();
                musicButton.classList.remove('playing');
                musicButton.innerHTML = '<i class="bi bi-music-note-beamed"></i>';
            }
        });
    }

    // Tombol kembali ke atas.
    const topButton = document.getElementById('backToTop');
    if (topButton) {
        window.addEventListener('scroll', function () {
            topButton.classList.toggle('show', window.scrollY > 500);
        });
        topButton.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // Hitung mundur menuju sesi pertama.
    document.querySelectorAll('.uk-countdown').forEach(function (element) {
        const target = new Date(element.dataset.date).getTime();
        if (!target) return;
        function tick() {
            const diff = target - Date.now();
            if (diff <= 0) { element.textContent = 'Acara dimulai!'; return; }
            const days = Math.floor(diff / 86400000);
            const hours = Math.floor(diff / 3600000) % 24;
            const minutes = Math.floor(diff / 60000) % 60;
            const seconds = Math.floor(diff / 1000) % 60;
            element.innerHTML = [
                [days, 'hari'], [hours, 'jam'], [minutes, 'menit'], [seconds, 'detik'],
            ].map(function (part) {
                return '<div><strong>' + String(part[0]).padStart(2, '0') + '</strong><span>' + part[1] + '</span></div>';
            }).join('');
        }
        tick();
        setInterval(tick, 1000);
    });

    // Muat ucapan berikutnya tanpa reload.
    const loadButton = document.getElementById('loadWishes');
    if (loadButton) {
        loadButton.addEventListener('click', function () {
            const page = Number(loadButton.dataset.page) + 1;
            fetch(loadButton.dataset.url + '?page=' + page, { headers: { Accept: 'application/json' } })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    const list = document.getElementById('wishList');
                    data.wishes.forEach(function (wish) {
                        const item = document.createElement('div');
                        item.className = 'uk-wish';
                        item.innerHTML = '<div class="uk-wish-avatar">' + wish.name.charAt(0).toUpperCase() + '</div>' +
                            '<div><strong>' + escapeHtml(wish.name) + '</strong>' +
                            '<span class="uk-hint"> ' + escapeHtml(wish.time) + '</span>' +
                            '<p class="mb-1">' + escapeHtml(wish.message) + '</p>' +
                            (wish.reply ? '<div class="uk-reply"><i class="bi bi-reply me-1"></i>' + escapeHtml(wish.reply) + '</div>' : '') + '</div>';
                        list.appendChild(item);
                    });
                    loadButton.dataset.page = page;
                    if (!data.has_more) loadButton.remove();
                })
                .catch(function () { loadButton.textContent = 'Gagal memuat. Coba lagi.'; });
        });
    }

    // Salin nomor rekening / tautan undangan.
    document.querySelectorAll('.copy-account, #copyShare').forEach(function (button) {
        button.addEventListener('click', function () {
            const value = button.dataset.account || button.dataset.link;
            navigator.clipboard.writeText(value).then(function () {
                const original = button.innerHTML;
                button.innerHTML = '<i class="bi bi-check me-1"></i>Tersalin';
                setTimeout(function () { button.innerHTML = original; }, 1600);
            });
        });
    });

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = String(text ?? '');
        return div.innerHTML;
    }
});
