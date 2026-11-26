@extends('layouts.public')

@section('content')
<header class="uk-hero" id="home">
    <div class="uk-container">
        <div class="row align-items-center g-5 min-vh-100 py-5">
            <div class="col-lg-7" data-aos="fade-right">
                <span class="uk-badge">{{ $invitation->event_type->label() }}</span>
                <h1 class="uk-hero-title mt-3">{{ $invitation->title }}</h1>
                @if ($events->first())
                    <p class="uk-hero-date">{{ $events->first()->dateLabel() }} · {{ $events->first()->timeLabel() }}</p>
                @endif
                <p class="uk-hero-quote">{{ $invitation->setting('quote') ?: 'Sebuah momen yang ingin kami bagikan bersama Anda.' }}</p>
                <a href="#sambutan" class="uk-btn mt-3"><i class="bi bi-arrow-down me-2"></i>Jelajahi undangan</a>
            </div>
            <div class="col-lg-5" data-aos="fade-left">
                <div class="uk-hero-panel">
                    <span class="uk-eyebrow">{{ $invitation->setting('cover_message', 'Kepada Yth.') }}</span>
                    <p class="uk-guest-name">{{ $guest ? $guest->name : 'Anda' }}</p>
                    <hr>
                    @foreach ($hosts as $host)
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="uk-avatar">@if ($host->photoUrl()) <img src="{{ $host->photoUrl() }}" alt=""> @else {{ strtoupper(substr($host->name, 0, 1)) }} @endif</div>
                            <div><strong class="d-block">{{ $host->name }}</strong><small class="uk-hint">{{ $host->nickname ?: ucfirst($host->type) }}</small></div>
                        </div>
                    @endforeach
                    @if ($invitation->setting('dress_code'))<span class="uk-badge mt-2"><i class="bi bi-person me-1"></i>{{ $invitation->setting('dress_code') }}</span>@endif
                </div>
            </div>
        </div>
    </div>
</header>

<section class="uk-section" id="sambutan" data-aos="fade-up">
    <div class="uk-container">
        <span class="uk-eyebrow">Tentang Acara</span>
        <h2 class="uk-title">Cerita Kami</h2>
        <p class="uk-story">{!! nl2br(e($invitation->setting('story') ?: 'Terima kasih telah hadir dalam cerita kami. Mari rayakan momen ini bersama.')) !!}</p>
    </div>
</section>

@include('themes.partials.events')
@include('themes.partials.gallery')
@include('themes.partials.gifts')
@include('themes.partials.rsvp')
@include('themes.partials.wishes')
@include('themes.partials.share')

<footer class="uk-footer">© {{ date('Y') }} UndanganKu</footer>
@endsection
