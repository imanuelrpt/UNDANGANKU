@extends('layouts.public')

@section('content')
<header class="uk-hero" id="home">
    <div class="uk-container">
        <div class="uk-hero-card" data-aos="zoom-in">
            <span class="uk-eyebrow">🌿 {{ $invitation->event_type->label() }}</span>
            <h1 class="uk-hero-title">{{ $invitation->title }}</h1>
            <p class="uk-cover-message">{{ $invitation->setting('cover_message', 'Kepada Yth.') }}</p>
            <p class="uk-guest-name">{{ $guest ? $guest->name : 'Teman & Keluarga' }}</p>
            <div class="uk-divider"><span>✿</span></div>
            @if ($events->first())
                <p class="uk-hero-date">{{ $events->first()->dateLabel() }} · {{ $events->first()->timeLabel() }}</p>
            @endif
            <p class="uk-hero-quote">{{ $invitation->setting('quote') ?: 'Kami mengundangmu untuk merayakan hari yang penuh berkah bersama kami.' }}</p>
            <a href="#sambutan" class="uk-btn mt-3"><i class="bi bi-arrow-down me-2"> </i>Lihat undangan</a>
        </div>
    </div>
</header>

<section class="uk-section" id="sambutan" data-aos="fade-up">
    <div class="uk-container">
        <div class="row align-items-center g-4">
            <div class="col-md-5 text-center">
                <div class="uk-frame">
                    @if ($hosts->first()?->photoUrl())
                        <img src="{{ $hosts->first()->photoUrl() }}" alt="{{ $hosts->first()->name }}" class="uk-frame-img">
                    @else
                        <div class="uk-frame-placeholder"><i class="bi bi-image"></i></div>
                    @endif
                </div>
            </div>
            <div class="col-md-7">
                <span class="uk-eyebrow">Cerita Kami</span>
                <h2 class="uk-title">Sebuah Kisah Baru</h2>
                <p class="uk-story">{!! nl2br(e($invitation->setting('story') ?: 'Terima kasih sudah menjadi bagian dari perjalanan kami. Mari rayakan hari ini bersama.')) !!}</p>
                <div class="row g-3 mt-3">
                    @foreach ($hosts as $host)
                        <div class="col-6">
                            <div class="uk-card p-3 text-center h-100">
                                <strong>{{ $host->name }}</strong>
                                <p class="uk-hint mb-0">{{ $host->nickname ?: ucfirst($host->type) }}</p>
                                @if ($host->parent_name) <small class="uk-hint">{{ $host->parent_name }}</small> @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

@include('themes.partials.events')
@include('themes.partials.gallery')
@include('themes.partials.gifts')
@include('themes.partials.rsvp')
@include('themes.partials.wishes')
@include('themes.partials.share')

<footer class="uk-footer">Dibuat dengan cinta · UndanganKu</footer>
@endsection
