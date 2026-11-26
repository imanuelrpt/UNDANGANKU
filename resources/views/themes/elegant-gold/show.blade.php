@extends('layouts.public')

@section('content')
<header class="uk-hero" id="home">
    <div class="uk-hero-overlay"></div>
    <div class="uk-container uk-hero-content" data-aos="fade-up">
        <span class="uk-eyebrow">{{ $invitation->event_type->label() }} · {{ $invitation->setting('dress_code') ?: 'Dress Code: Formal' }}</span>
        <div class="uk-ornament">✦</div>
        <p class="uk-cover-message">{{ $invitation->setting('cover_message', 'Kepada Yth.') }}</p>
        <p class="uk-guest-name">{{ $guest ? $guest->name : 'Bapak/Ibu/Saudara/i' }}</p>
        <h1 class="uk-hero-title">{{ $invitation->title }}</h1>
        <p class="uk-hero-quote">{{ $invitation->setting('quote') ?: 'Dengan penuh sukacita, kami mengundang Anda untuk hadir dalam acara kami.' }}</p>
        @if ($events->first())
            <p class="uk-hero-date">{{ $events->first()->dateLabel() }} · {{ $events->first()->timeLabel() }}</p>
            <div class="uk-countdown" data-date="{{ $events->first()->starts_at?->toIso8601String() }}"></div>
        @endif
        <a href="#sambutan" class="uk-btn mt-4"><i class="bi bi-arrow-down me-2"></i>Buka undangan</a>
    </div>
</header>

<section class="uk-section" id="sambutan" data-aos="fade-up">
    <div class="uk-container text-center">
        <span class="uk-eyebrow">Assalamu'alaikum Wr. Wb.</span>
        <h2 class="uk-title">Selamat Datang</h2>
        <p class="uk-story">{!! nl2br(e($invitation->setting('story') ?: 'Terima kasih telah menjadi bagian dari hari spesial kami. Mohon kehadiran Anda untuk menyempurnakan acara ini.')) !!}</p>
        <div class="row g-4 justify-content-center mt-4">
            @foreach ($hosts as $host)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="uk-host-photo">
                        @if ($host->photoUrl()) <img src="{{ $host->photoUrl() }}" alt="{{ $host->name }}" loading="lazy"> @else <i class="bi bi-person"></i> @endif
                    </div>
                    <p class="uk-eyebrow mt-3">{{ ucfirst($host->type) }}</p>
                    <h3 class="h6 mb-0">{{ $host->name }}</h3>
                    @if ($host->nickname) <p class="uk-hint mb-0">{{ $host->nickname }}</p> @endif
                    @if ($host->parent_name) <p class="uk-hint small">{{ $host->parent_name }}</p> @endif
                </div>
            @endforeach
        </div>
    </div>
</section>

@include('themes.partials.events')
@include('themes.partials.gallery')
@include('themes.partials.gifts')
@include('themes.partials.rsvp')
@include('themes.partials.wishes')
@include('themes.partials.share')

<footer class="uk-footer">© {{ date('Y') }} {{ $invitation->title }} · UndanganKu</footer>
@endsection
