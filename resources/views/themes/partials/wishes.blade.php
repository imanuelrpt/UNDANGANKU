@if ($invitation->setting('show_wishes', true))
<section class="uk-section" id="wishes" data-aos="fade-up">
    <div class="uk-container">
        <span class="uk-eyebrow">Buku Tamu</span>
        <h2 class="uk-title">Doa & Ucapan</h2>
        <p class="uk-subtitle">Tinggalkan doa dan ucapan untuk {{ $invitation->title }}.</p>

        <form method="POST" action="{{ route('invitation.wish.store', $invitation->slug) }}" class="uk-card uk-wish-form">
            @csrf
            @if ($guest)
                <input type="hidden" name="token" value="{{ $guest->token }}">
                <p class="uk-hint mb-2">Ucapan akan ditulis atas nama <strong>{{ $guest->name }}</strong>.</p>
            @else
                <div class="uk-form-group">
                    <label class="uk-label" for="wishName">Nama Anda <span class="text-danger">*</span></label>
                    <input class="uk-input @error('name') is-invalid @enderror" id="wishName" name="name" value="{{ old('name') }}" required>
                    @error('name') <div class="uk-error">{{ $message }}</div> @enderror
                </div>
            @endif
            <div class="uk-form-group">
                <label class="uk-label" for="wishMessage">Ucapan <span class="text-danger">*</span></label>
                <textarea class="uk-input" id="wishMessage" name="message" rows="3" maxlength="300" required placeholder="Tulis doa dan ucapan Anda...">{{ old('message') }}</textarea>
                @error('message') <div class="uk-error">{{ $message }}</div> @enderror
            </div>
            <input type="text" name="website" class="uk-honeypot" tabindex="-1" autocomplete="off">
            <button type="submit" class="uk-btn"><i class="bi bi-chat-heart me-2"></i>Kirim ucapan</button>
        </form>

        <div id="wishList" class="uk-wish-list mt-4">
            @forelse ($wishes as $wish)
                <div class="uk-wish">
                    <div class="uk-wish-avatar">{{ strtoupper(substr($wish->name, 0, 1)) }}</div>
                    <div>
                        <strong>{{ $wish->name }}</strong>
                        <span class="uk-hint">{{ $wish->created_at->diffForHumans() }}</span>
                        <p class="mb-1">{{ $wish->message }}</p>
                        @if ($wish->reply_message)
                            <div class="uk-reply"><i class="bi bi-reply me-1"></i>{{ $wish->reply_message }}</div>
                        @endif
                    </div>
                </div>
            @empty
                <p class="uk-subtitle text-center">Belum ada ucapan. Jadilah yang pertama!</p>
            @endforelse
        </div>
        @if ($wishesTotal > $wishes->count())
            <div class="text-center mt-3">
                <button type="button" class="uk-btn uk-btn-outline" id="loadWishes" data-url="{{ route('invitation.wish.load', $invitation->slug) }}" data-page="1">Muat ucapan lainnya</button>
            </div>
        @endif
    </div>
</section>
@endif
