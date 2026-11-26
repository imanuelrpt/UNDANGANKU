document.addEventListener('DOMContentLoaded', function () {
    const config = window.scannerConfig;
    if (!config) return;

    const startButton = document.getElementById('startScanner');
    const stopButton = document.getElementById('stopScanner');
    const resultBox = document.getElementById('scanResult');
    const confirmBox = document.getElementById('confirmBox');
    const guestName = document.getElementById('guestName');
    const guestMeta = document.getElementById('guestMeta');
    const paxInput = document.getElementById('paxActual');
    let scanner = null;
    let pendingToken = null;
    let busy = false;

    function post(url, body) {
        return fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': config.csrf, Accept: 'application/json' },
            body: JSON.stringify(body),
        }).then(function (response) { return response.json(); });
    }

    function showResult(data) {
        resultBox.innerHTML = '<i class="bi ' + (data.status === 'invalid' ? 'bi-x-circle text-danger' : 'bi-person-check text-success') + ' fs-1"></i>' +
            '<p class="mt-2 small">' + escapeHtml(data.message) + '</p>';
        if (data.guest && ['ok', 'already'].includes(data.status)) {
            guestName.textContent = data.guest.name;
            guestMeta.textContent = data.guest.group + ' · kuota ' + data.guest.pax + ' orang' + (data.checked_in_at ? ' · check-in ' + data.checked_in_at : '');
            confirmBox.classList.remove('d-none');
        } else {
            confirmBox.classList.add('d-none');
        }
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = String(text ?? '');
        return div.innerHTML;
    }

    if (startButton && window.Html5Qrcode) {
        startButton.addEventListener('click', function () {
            if (scanner) return;
            scanner = new Html5Qrcode('reader');
            scanner.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: { width: 250, height: 250 } },
                function (decodedText) {
                    if (busy) return;
                    busy = true;
                    post(config.scanUrl, { token: decodedText })
                        .then(function (data) {
                            pendingToken = data.status === 'ok' ? decodedText : null;
                            paxInput.value = data.guest ? data.guest.pax : 1;
                            showResult(data);
                            if (data.status === 'checked_in') {
                                Swal.fire({ icon: 'success', title: 'Check-in berhasil', text: data.message, timer: 2200, showConfirmButton: false });
                            }
                        })
                        .catch(function () { showResult({ status: 'invalid', message: 'Gagal memverifikasi QR.' }); })
                        .finally(function () { busy = false; });
                },
                function () { /* abaikan frame tanpa QR */ },
            ).then(function () {
                startButton.disabled = true;
                stopButton.disabled = false;
            }).catch(function (error) {
                showResult({ status: 'invalid', message: 'Kamera tidak tersedia: ' + error });
            });
        });

        stopButton.addEventListener('click', function () {
            if (!scanner) return;
            scanner.stop().then(function () {
                scanner.clear();
                scanner = null;
                startButton.disabled = false;
                stopButton.disabled = true;
            });
        });

        document.getElementById('confirmCheckin').addEventListener('click', function () {
            if (!pendingToken) return;
            post(config.scanUrl, { token: pendingToken, confirm: true, pax_actual: Number(paxInput.value) })
                .then(function (data) {
                    showResult(data);
                    pendingToken = null;
                    Swal.fire({ icon: 'success', title: 'Check-in berhasil', text: data.message, timer: 2200, showConfirmButton: false });
                });
        });
    }

    const manualForm = document.getElementById('manualForm');
    if (manualForm) {
        const guestSelect = document.getElementById('guestSelect');
        const walkInName = document.getElementById('walkInName');
        guestSelect.addEventListener('change', function () { if (guestSelect.value) walkInName.value = ''; });
        walkInName.addEventListener('input', function () { if (walkInName.value) guestSelect.value = ''; });

        manualForm.addEventListener('submit', function (event) {
            event.preventDefault();
            const body = {
                guest_id: guestSelect.value || null,
                name: guestSelect.value ? null : walkInName.value,
                pax_actual: Number(document.getElementById('manualPax').value) || 1,
                method: guestSelect.value ? 'manual' : 'walk_in',
            };
            if (!body.guest_id && !body.name) { alert('Pilih tamu atau isi nama walk-in.'); return; }
            post(config.manualUrl, body).then(function (data) {
                if (data.status === 'checked_in') {
                    Swal.fire({ icon: 'success', title: 'Check-in berhasil', text: data.message, timer: 2200, showConfirmButton: false });
                } else {
                    showResult(data);
                }
            });
        });
    }
});
