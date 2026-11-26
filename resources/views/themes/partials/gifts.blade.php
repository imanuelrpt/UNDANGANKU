@if ($gifts->isNotEmpty() || $invitation->setting('gift_address'))
<section class="uk-section" id="amplop" data-aos="fade-up">
    <div class="uk-container">
        <span class="uk-eyebrow">Amplop Digital</span>
        <h2 class="uk-title">Kirim Hadiah</h2>
        <p class="uk-subtitle">Kehadiran Anda adalah hadiah terbaik. Bagi yang ingin berbagi rezeki, silakan melalui rekening berikut.</p>
        <div class="row g-3 justify-content-center">
            @foreach ($gifts as $gift)
                <div class="col-md-6 col-lg-4">
                    <div class="uk-card uk-gift-card h-100">
                        <span class="uk-eyebrow">{{ $gift->label() }}</span>
                        <h3 class="h6 mt-2 mb-1">{{ $gift->bank_name }}</h3>
                        <div class="uk-account">{{ $gift->account_number }}</div>
                        <p class="uk-hint mb-3">a.n. {{ $gift->account_name }}</p>
                        <button type="button" class="uk-btn uk-btn-sm copy-account" data-account="{{ $gift->account_number }}"><i class="bi bi-copy me-1"></i>Salin nomor</button>
                    </div>
                </div>
            @endforeach
        </div>
        @if ($invitation->setting('gift_address'))
            <div class="uk-card mt-3 text-center"><i class="bi bi-truck me-2"></i>{{ $invitation->setting('gift_address') }}</div>
        @endif
    </div>
</section>
@endif
