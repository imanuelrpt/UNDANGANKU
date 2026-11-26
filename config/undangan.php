<?php

return [

    /*
    |--------------------------------------------------------------------------
    | UndanganKu Application Settings
    |--------------------------------------------------------------------------
    */

    'max_photos' => 12,

    'max_music_size_kb' => 5120,

    'max_upload_size_kb' => 5120,

    'guest_token_length' => 24,

    'staff_token_length' => 32,

    'reserved_slugs' => [
        'admin', 'login', 'register', 'logout', 'api', 'dashboard', 'u', 'petugas',
        'tiket', 'storage', 'css', 'js', 'images', 'favicon', 'index', 'home',
        'about', 'contact', 'help', 'support', 'mail', 'password', 'verify',
        'email', 'phone', 'user', 'users', 'owner', 'staff', 'test', 'demo',
        'www', 'ftp', 'root', 'null', 'true', 'false', 'undefined',
    ],

    'default_whatsapp_template' => 'Hai {nama}, kami mengundangmu ke acara kami. Silakan buka undangan digital berikut: {link}. Sampai jumpa di {tanggal}!',

    'music_tracks' => [
        'none' => ['label' => 'Tanpa musik', 'file' => null],
        'morning-light' => ['label' => 'Morning Light (royalty free)', 'file' => '/audio/morning-light.mp3'],
        'gentle-breeze' => ['label' => 'Gentle Breeze (royalty free)', 'file' => '/audio/gentle-breeze.mp3'],
        'golden-hour' => ['label' => 'Golden Hour (royalty free)', 'file' => '/audio/golden-hour.mp3'],
    ],

    'default_settings' => [
        'story' => '',
        'quote' => '',
        'dress_code' => '',
        'cover_image' => null,
        'cover_message' => 'Kepada Yth.',
        'youtube_url' => null,
        'music_track' => 'none',
        'music_file' => null,
        'rsvp_enabled' => true,
        'rsvp_deadline' => null,
        'max_extra_guests' => 3,
        'question_label' => null,
        'gift_address' => null,
        'show_wishes' => true,
        'whatsapp_template' => null,
    ],

    'event_types' => [
        'wedding' => 'Pernikahan',
        'birthday' => 'Ulang Tahun',
        'aqiqah' => 'Aqiqah / Syukuran',
        'reunion' => 'Reuni',
        'general' => 'Acara Umum',
    ],

    'timezones' => [
        'WIB' => 'Asia/Jakarta',
        'WITA' => 'Asia/Makassar',
        'WIT' => 'Asia/Jayapura',
    ],

    'guest_groups' => [
        'keluarga' => 'Keluarga',
        'teman' => 'Teman',
        'rekan_kerja' => 'Rekan Kerja',
        'vip' => 'VIP',
    ],

];
