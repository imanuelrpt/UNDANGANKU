<section class="uk-section" id="rsvp" data-aos="fade-up">
    <div class="uk-container">
        <span class="uk-eyebrow">Konfirmasi Kehadiran</span>
        <h2 class="uk-title">Apakah Anda akan hadir?</h2>
        <p class="uk-subtitle">Mohon konfirmasi kehadiran Anda sebelum acara dimulai.</p>

        @php($rsvpEnabled = (bool) $invitation->setting('rsvp_enabled', true) && ! $isExpired)
        @if (! $rsvpEnabled)
            <div class="uk-card text-center"><p class="mb-0">Konfirmasi kehadiran sedang dinonaktifkan.</p></div>
        @else
            <form method="POST" action="{{ route('invitation.rsvp.store', $invitation->slug) }}" class="uk-card uk-rsvp-form">
                @csrf
                @if ($guest)
                    <input type="hidden" name="token" value="{{ $guest->token }}">
                    <div class="uk-form-group">
                        <label class="uk-label">Nama tamu</label>
                        <input class="uk-input" value="{{ $guest->name }} ({{ $guest->group->label() }})" readonly>
                    </div>
                @else
                    <div class="uk-form-group">
                        <label class="uk-label" for="rsvpName">Nama Anda <span class="text-danger">*</span></label>
                        <input class="uk-input @error('name') is-invalid @enderror" id="rsvpName" name="name" value="{{ old('name') }}" required>
                        @error('name') <div class="uk-error">{{ $message }}</div> @enderror
                    </div>
                @endif

                <div class="uk-form-group">
                    <label class="uk-label">{{ $invitation->setting('question_label') ?: 'Kehadiran' }} <span class="text-danger">*</span></label>
                    <div class="uk-attendance">
                        @foreach (App\Enums\Attendance::cases() as $attendance)
                            <label class="uk-attendance-option">
                                <input type="radio" name="attendance" value="{{ $attendance->value }}" @checked(old('attendance') === $attendance->value) required>
                                <i class="bi {{ $attendance->icon() }}"></i>
                                <span>{{ $attendance->label() }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('attendance') <div class="uk-error">{{ $message }}</div> @enderror
                </div>

                <div class="uk-form-group">
                    <label class="uk-label" for="rsvpPax">Jumlah orang (termasuk Anda) <span class="text-danger">*</span></label>
                    <input type="number" class="uk-input" id="rsvpPax" name="pax" min="1" max="{{ $guest ? $guest->pax_quota : 1 + (int) $invitation->setting('max_extra_guests', 3) }}" value="{{ old('pax', 1) }}" required>
                    <small class="uk-hint">Maksimal {{ $guest ? $guest->pax_quota : 1 + (int) $invitation->setting('max_extra_guests', 3) }} orang.</small>
                    @error('pax') <div class="uk-error">{{ $message }}</div> @enderror
                </div>

                @if ($events->isNotEmpty())
                    <div class="uk-form-group">
                        <label class="uk-label">Sesi yang akan dihadiri</label>
                        @foreach ($events as $event)
                            <label class="uk-check-row">
                                <input type="checkbox" name="event_ids[]" value="{{ $event->id }}" @checked(in_array($event->id, old('event_ids', [])))>
                                <span><strong>{{ $event->name }}</strong> <small class="uk-hint">{{ $event->dateLabel() }} · {{ $event->timeLabel() }}</small></span>
                            </label>
                        @endforeach
                    </div>
                @endif

                @if ($questions->isNotEmpty())
                    <div class="uk-form-group">
                        <label class="uk-label">Pertanyaan tambahan</label>
                        @foreach ($questions as $question)
                            <div class="mb-3">
                                <label class="uk-label small" for="q{{ $question->id }}">{{ $question->label }}</label>
                                <input class="uk-input" id="q{{ $question->id }}" name="answers[{{ $question->id }}]" value="{{ old("answers.$question->id") }}" maxlength="300">
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="uk-form-group">
                    <label class="uk-label" for="rsvpNote">Pesan / catatan (opsional)</label>
                    <textarea class="uk-input" id="rsvpNote" name="note" rows="3" maxlength="500">{{ old('note') }}</textarea>
                </div>

                <input type="text" name="website" class="uk-honeypot" tabindex="-1" autocomplete="off">
                <button type="submit" class="uk-btn w-100"><i class="bi bi-send me-2"></i>Kirim konfirmasi</button>
            </form>
        @endif
    </div>
</section>
