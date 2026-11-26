<?php

return [

    /*
    | Kontrak tema: setiap tema wajib memiliki `view` dengan variabel yang sama
    | (lihat README bagian "Menambah Tema Baru").
    */

    'default' => 'elegant-gold',

    'list' => [
        'elegant-gold' => [
            'name' => 'Elegant Gold',
            'description' => 'Hitam elegan dengan aksen emas, tipografi serif mewah.',
            'view' => 'themes.elegant-gold.show',
            'css' => 'css/themes/elegant-gold.css',
            'preview' => '/images/demo/theme-elegant.svg',
            'color' => '#c9a227',
            'active' => true,
        ],
        'rustic-green' => [
            'name' => 'Rustic Green',
            'description' => 'Hijau daun dan krema hangat, nuansa garden wedding.',
            'view' => 'themes.rustic-green.show',
            'css' => 'css/themes/rustic-green.css',
            'preview' => '/images/demo/theme-rustic.svg',
            'color' => '#587a52',
            'active' => true,
        ],
        'minimal-modern' => [
            'name' => 'Minimal Modern',
            'description' => 'Monokrom bersih dengan aksen coral, tipografi sans.',
            'view' => 'themes.minimal-modern.show',
            'css' => 'css/themes/minimal-modern.css',
            'preview' => '/images/demo/theme-minimal.svg',
            'color' => '#e76f51',
            'active' => true,
        ],
    ],

];
