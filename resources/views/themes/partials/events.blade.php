@if ($events->isNotEmpty())
<section class="uk-section" id="acara" data-aos="fade-up">
    <div class="uk-container">
        <span class="uk-eyebrow">Rangkaian Acara</span>
        <h2 class="uk-title">Jadwal Acara</h2>
        <div class="row g-3 justify-content-center">
            @foreach ($events as $event)
                <div class="col-md-6 col-lg-5">
                    <div class="uk-card uk-event-card h-100">
                        <div class="uk-event-date">{{ $event->starts_at?->translatedFormat('d') }} <span>{{ $event->starts_at?->translatedFormat('M Y') }}</span></div>
                        <h3 class="h5 mt-3 mb-1">{{ $event->name }}</h3>
                        <p class="uk-hint mb-2"><i class="bi bi-clock me-1"></i>{{ $event->dateLabel() }} · {{ $event->timeLabel() }}</p>
                        <p class="mb-2"><i class="bi bi-geo-alt me-1"></i><strong>{{ $event->venue ?: 'Lokasi acara' }}</strong><br><small class="uk-hint">{{ $event->address }}</small></p>
                        <div class="d-flex flex-wrap gap-2">
                            @if ($event->lat !== null && $event->lng !== null)
                                <a href="https://www.google.com/maps/search/?api=1&query={{ $event->lat }},{{ $event->lng }}" target="_blank" class="uk-btn uk-btn-sm"><i class="bi bi-map me-1"></i>Peta</a>
                            @elseif ($event->maps_url)
                                <a href="{{ $event->maps_url }}" target="_blank" class="uk-btn uk-btn-sm"><i class="bi bi-map me-1"></i>Peta</a>
                            @endif
                            <a href="{{ $event->google_calendar_url }}" target="_blank" class="uk-btn uk-btn-sm uk-btn-outline"><i class="bi bi-calendar-plus me-1"></i>Google Calendar</a>
                            <a href="{{ $event->ics_url }}" class="uk-btn uk-btn-sm uk-btn-outline"><i class="bi bi-download me-1"></i>.ICS</a>
                        </div>
                        @if ($event->lat !== null && $event->lng !== null)
                            <div class="uk-map mt-3" data-lat="{{ $event->lat }}" data-lng="{{ $event->lng }}" data-title="{{ $event->venue ?: $event->name }}"></div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.uk-map').forEach(function (element) {
            const lat = parseFloat(element.dataset.lat);
            const lng = parseFloat(element.dataset.lng);
            const map = L.map(element, { scrollWheelZoom: false }).setView([lat, lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
            }).addTo(map);
            L.marker([lat, lng]).addTo(map).bindPopup(element.dataset.title || 'Lokasi acara');
        });
    });
</script>
@endpush
@endif
