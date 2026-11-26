<section class="uk-section uk-share-section" data-aos="fade-up">
    <div class="uk-container text-center">
        <h2 class="uk-title">Bagikan Undangan</h2>
        <p class="uk-subtitle">Bantu sampaikan kabar baik ini kepada kerabat dan teman.</p>
        <div class="d-flex justify-content-center flex-wrap gap-2">
            <a href="https://wa.me/?text={{ rawurlencode('Undangan: '.$invitation->title.' — '.$shareUrl) }}" target="_blank" class="uk-btn uk-btn-sm"><i class="bi bi-whatsapp me-1"></i>WhatsApp</a>
            <button type="button" class="uk-btn uk-btn-sm uk-btn-outline" id="copyShare" data-link="{{ $shareUrl }}"><i class="bi bi-link-45deg me-1"></i>Salin tautan</button>
            @if ($guest)
                <a href="{{ route('ticket.pdf', $guest->token) }}" class="uk-btn uk-btn-sm uk-btn-outline" target="_blank"><i class="bi bi-ticket-perforated me-1"></i>Tiket QR (PDF)</a>
            @endif
        </div>
        <p class="uk-hint mt-4 mb-0">Dibuat dengan <strong>UndanganKu</strong></p>
    </div>
</section>
