@if ($photos->isNotEmpty())
<section class="uk-section" id="galeri" data-aos="fade-up">
    <div class="uk-container">
        <span class="uk-eyebrow">Momen Kami</span>
        <h2 class="uk-title">Galeri Foto</h2>
        <div class="uk-gallery">
            @foreach ($photos as $photo)
                <a href="{{ $photo->url() }}" class="uk-gallery-item" data-fancybox="gallery" title="{{ $photo->caption }}">
                    <img src="{{ $photo->thumbnailUrl() }}" alt="{{ $photo->caption }}" loading="lazy">
                </a>
            @endforeach
        </div>
    </div>
</section>
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css">
@endpush
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.Fancybox) { Fancybox.bind('[data-fancybox="gallery"]'); }
    });
</script>
@endpush
@endif
