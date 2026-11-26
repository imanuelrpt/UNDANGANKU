<?php

namespace Database\Seeders;

use App\Enums\Attendance;
use App\Enums\CheckinMethod;
use App\Enums\EventType;
use App\Enums\GuestGroup;
use App\Enums\InvitationStatus;
use App\Enums\UserRole;
use App\Models\ActivityLog;
use App\Models\Checkin;
use App\Models\CheckinStaffLink;
use App\Models\Guest;
use App\Models\Invitation;
use App\Models\InvitationEvent;
use App\Models\InvitationGift;
use App\Models\InvitationHost;
use App\Models\InvitationPhoto;
use App\Models\InvitationQuestion;
use App\Models\Rsvp;
use App\Models\User;
use App\Models\Wish;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with repeatable demo data.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@undangan.test',
            'password' => Hash::make('password'),
            'role' => UserRole::Admin,
        ]);

        $owner = User::factory()->create([
            'name' => 'Rina Wijaya',
            'email' => 'owner@undangan.test',
            'password' => Hash::make('password'),
            'role' => UserRole::Owner,
        ]);

        $wedding = $this->seedWedding($owner);
        $birthday = $this->seedBirthday($owner);

        $this->seedActivityLogs($admin, $owner, $wedding, $birthday);
    }

    private function seedWedding(User $owner): Invitation
    {
        $wedding = Invitation::factory()->for($owner)->published()->create([
            'event_type' => EventType::Wedding,
            'title' => 'Rina & Dimas',
            'slug' => 'rina-dan-dimas',
            'theme' => 'elegant-gold',
            'expires_at' => now()->addYear(),
            'view_count' => 248,
            'settings' => [
                'story' => "Assalamu'alaikum Wr. Wb.\n\nDengan penuh syukur, kami mengundang Bapak/Ibu/Saudara/i untuk hadir di pernikahan kami.\n\nRina & Dimas",
                'quote' => 'Cinta adalah ketika dua orang saling memilih untuk tumbuh bersama.',
                'dress_code' => 'Formal, warna netral',
                'cover_message' => 'Kepada Yth.',
                'music_track' => 'golden-hour',
                'rsvp_enabled' => true,
                'rsvp_deadline' => now()->addDays(14)->toDateString(),
                'max_extra_guests' => 3,
                'question_label' => 'Konfirmasi kehadiran',
                'gift_address' => 'Jl. Melati No. 12, Jakarta Selatan',
                'show_wishes' => true,
                'whatsapp_template' => 'Hai {nama}, kami mengundangmu ke pernikahan Rina & Dimas. Buka undangannya di {link}. Sampai jumpa di {tanggal}!',
            ],
        ]);

        InvitationHost::factory()->for($wedding)->create([
            'type' => 'bride',
            'name' => 'Rina Wijaya',
            'nickname' => 'Rina',
            'photo_path' => 'demo/photo-1.svg',
            'parent_name' => 'Bapak Agus Wijaya & Ibu Sari Aminah',
            'birth_order' => 'Putri Pertama',
            'description' => 'Anak pertama dari Bapak Agus Wijaya dan Ibu Sari Aminah.',
            'sort_order' => 1,
        ]);

        InvitationHost::factory()->for($wedding)->create([
            'type' => 'groom',
            'name' => 'Dimas Prakoso',
            'nickname' => 'Dimas',
            'photo_path' => 'demo/photo-2.svg',
            'parent_name' => 'Bapak Bambang Prakoso & Ibu Endang Kusuma',
            'birth_order' => 'Putra Kedua',
            'description' => 'Anak kedua dari Bapak Bambang Prakoso dan Ibu Endang Kusuma.',
            'sort_order' => 2,
        ]);

        $akad = InvitationEvent::factory()->for($wedding)->create([
            'name' => 'Akad Nikah',
            'starts_at' => now()->addDays(21)->setTime(9, 0),
            'ends_at' => now()->addDays(21)->setTime(11, 0),
            'venue' => 'Masjid Agung Al-Azhar',
            'address' => 'Jl. Sisingamangaraja No. 10, Jakarta Selatan',
            'lat' => -6.2207,
            'lng' => 106.8094,
            'maps_url' => 'https://maps.google.com/?q=Masjid+Agung+Al-Azhar',
            'sort_order' => 1,
        ]);

        $resepsi = InvitationEvent::factory()->for($wedding)->create([
            'name' => 'Resepsi',
            'starts_at' => now()->addDays(21)->setTime(12, 0),
            'ends_at' => now()->addDays(21)->setTime(16, 0),
            'venue' => 'Gedung Graha Krida',
            'address' => 'Jl. Melati No. 12, Jakarta Selatan',
            'lat' => -6.2615,
            'lng' => 106.8106,
            'maps_url' => 'https://maps.google.com/?q=Graha+Krida+Jakarta',
            'sort_order' => 2,
        ]);

        foreach ([
            'Momen pertama kami',
            'Sesi foto bersama',
            'Detik bahagia',
            'Bersama keluarga',
            'Teman seperjuangan',
            'Doa dan harapan',
        ] as $index => $caption) {
            InvitationPhoto::factory()->for($wedding)->create([
                'path' => sprintf('demo/photo-%d.svg', $index + 1),
                'thumbnail_path' => sprintf('demo/photo-%d.svg', $index + 1),
                'caption' => $caption,
                'sort_order' => $index + 1,
            ]);
        }

        InvitationGift::factory()->for($wedding)->create([
            'type' => 'bank',
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_name' => 'Rina Wijaya',
            'sort_order' => 1,
        ]);

        InvitationGift::factory()->for($wedding)->create([
            'type' => 'ewallet',
            'bank_name' => 'GoPay',
            'account_number' => '081234567890',
            'account_name' => 'Dimas Prakoso',
            'sort_order' => 2,
        ]);

        $question = InvitationQuestion::factory()->for($wedding)->create([
            'label' => 'Pantangan makanan',
            'is_active' => true,
        ]);

        InvitationQuestion::factory()->for($wedding)->create([
            'label' => 'Butuh kursi bayi?',
            'is_active' => true,
        ]);

        $guests = $this->seedWeddingGuests($wedding);
        $this->seedWeddingRsvps($wedding, $guests, $question, [$akad, $resepsi]);
        $this->seedWeddingWishes($wedding, $guests);

        $staffWithPin = CheckinStaffLink::factory()->for($wedding)->create([
            'token' => 'staff-demo-123456',
            'pin_hash' => Hash::make('123456'),
            'label' => 'Petugas Gerbang Utama',
            'is_active' => true,
        ]);

        $staffWithoutPin = CheckinStaffLink::factory()->for($wedding)->create([
            'token' => 'staff-demo-cepat',
            'pin_hash' => null,
            'label' => 'Petugas Resepsi',
            'is_active' => true,
        ]);

        $this->seedCheckins($wedding, $guests, $staffWithPin, $staffWithoutPin);

        return $wedding;
    }

    /**
     * @return \Illuminate\Support\Collection<int, Guest>
     */
    private function seedWeddingGuests(Invitation $wedding): \Illuminate\Support\Collection
    {
        /** @var array<int, string> $names */
        $names = [
            'Dewi Anggraini', 'Rizky Pratama', 'Nadia Putri', 'Andi Saputra',
            'Maya Sari', 'Fajar Nugroho', 'Siti Rahmawati', 'Bagus Setiawan',
            'Intan Permata', 'Hendra Wijaya', 'Lina Marlina', 'Yusuf Mahendra',
            'Anisa Rahma', 'Dedi Kurniawan', 'Putri Amelia', 'Roni Setiadi',
            'Salsa Aulia', 'Tejo Mulyono', 'Vera Kusuma', 'Wahyu Hidayat',
            'Yanti Handayani', 'Zainal Arifin', 'Ayu Lestari', 'Bima Arya',
            'Citra Dewi', 'DQ Ananda', 'Eko Purnomo', 'Fitri Handari',
            'Galih Sudarma', 'Hana Wulandari', 'Irfan Hakim', 'Joko Susilo',
            'Kartika Sari', 'Luthfi Agung', 'Mira Anggraini', 'Nugroho Adi',
            'Oki Prasetyo', 'Pita Maharani', 'Reza Ramadhan', 'Sarah Azhari',
        ];

        /** @var array<int, GuestGroup> $groups */
        $groups = GuestGroup::cases();

        return Guest::factory()->for($wedding)->count(count($names))
            ->sequence(fn (Sequence $sequence): array => [
                'name' => $names[$sequence->index],
                'group' => $groups[$sequence->index % count($groups)],
                'phone' => '0812'.fake()->numerify('########'),
                'pax_quota' => ($sequence->index % 4) + 1,
                'token' => sprintf('tamu-%03d-demo', $sequence->index + 1),
                'sent_at' => now()->subDays(3),
                'opened_at' => $sequence->index < 24 ? now()->subDay() : null,
                'open_count' => $sequence->index < 24 ? 1 + ($sequence->index % 2) : 0,
            ])
            ->create();
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Guest>  $guests
     * @param  array<int, InvitationEvent>  $events
     */
    private function seedWeddingRsvps(
        Invitation $wedding,
        \Illuminate\Support\Collection $guests,
        InvitationQuestion $question,
        array $events,
    ): void {
        foreach ($guests as $index => $guest) {
            $attendance = match (true) {
                $index < 24 => Attendance::Hadir,
                $index < 32 => Attendance::TidakHadir,
                default => Attendance::Ragu,
            };

            $rsvp = Rsvp::factory()->for($wedding)->for($guest, 'guest')->create([
                'name' => $guest->name,
                'attendance' => $attendance,
                'pax' => $attendance === Attendance::Hadir ? min($guest->pax_quota, 2) : 1,
                'answers' => [
                    $question->id => $index % 3 === 0 ? 'Alergi udang' : 'Tidak ada',
                ],
                'note' => $attendance === Attendance::Hadir ? 'Sampai jumpa di acara!' : null,
            ]);

            if ($attendance === Attendance::Hadir && $index % 2 === 0) {
                $rsvp->events()->attach($events[$index % count($events)]->id);
            }
        }
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Guest>  $guests
     */
    private function seedWeddingWishes(Invitation $wedding, \Illuminate\Support\Collection $guests): void
    {
        /** @var array<int, string> $messages */
        $messages = [
            'Semoga pernikahan kalian penuh dengan cinta dan kebahagiaan. Amin!',
            'Selamat menempuh hidup baru, Rina & Dimas! Tuhan memberkati.',
            'Doaku semoga kalian selalu saling menguatkan dalam suka dan duka.',
            'Senang bisa jadi bagian dari hari istimewa kalian. Sampai jumpa!',
            'Wah, akhirnya menikah juga. Selamat ya berdua!',
            'Semoga rumah tangganya sakinah, mawaddah, dan rahmah.',
            'Terima kasih sudah mengundang kami. Doa terbaik untuk kalian.',
            'Cantik banget pengantinnya, ganteng juga pengantinnya 😍',
            'Semoga rezeki kalian berlimpah dan dipenuhi kebahagiaan.',
            'Selamat ya Dimas, selamat ya Rina. Kami doakan dari jauh.',
            'Buat pasangan yang baru, semoga selalu kompak ya!',
            'Aku datang dengan senang hati. Acaranya pasti meriah!',
            'Semoga cinta kalian semakin kuat setiap hari.',
            'Doaku untuk kalian berdua: sehat, bahagia, dan panjang umur.',
            'Makasih undangannya, sudah lama tidak ketemu Rina. Miss you!',
        ];

        foreach ($messages as $index => $message) {
            /** @var Guest $guest */
            $guest = $guests->get($index % $guests->count());

            Wish::factory()->for($wedding)->for($guest, 'guest')->create([
                'name' => $guest->name,
                'message' => $message,
                'is_hidden' => $index === 14,
                'reply_message' => $index === 2 ? 'Terima kasih atas doanya, kami sangat berterima kasih!' : null,
            ]);
        }
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Guest>  $guests
     */
    private function seedCheckins(
        Invitation $wedding,
        \Illuminate\Support\Collection $guests,
        CheckinStaffLink $staffWithPin,
        CheckinStaffLink $staffWithoutPin,
    ): void {
        foreach ($guests->take(10) as $guest) {
            Checkin::factory()->for($wedding)->for($guest, 'guest')->for($staffWithPin, 'staffLink')->create([
                'name' => $guest->name,
                'group' => $guest->group->value,
                'pax_actual' => min($guest->pax_quota, 2),
                'method' => CheckinMethod::Scan,
                'device_info' => 'Demo Scanner Chrome · Windows',
                'checked_in_at' => now()->subMinutes(rand(5, 90)),
            ]);
        }

        foreach ($guests->slice(10, 2) as $guest) {
            Checkin::factory()->for($wedding)->for($guest, 'guest')->for($staffWithoutPin, 'staffLink')->create([
                'name' => $guest->name,
                'group' => $guest->group->value,
                'pax_actual' => $guest->pax_quota,
                'method' => CheckinMethod::Manual,
                'device_info' => 'Dashboard Owner',
                'checked_in_at' => now()->subMinutes(rand(10, 60)),
            ]);
        }

        foreach (['Tamu Walk-in A', 'Tamu Walk-in B'] as $name) {
            Checkin::factory()->for($wedding)->for($staffWithPin, 'staffLink')->create([
                'guest_id' => null,
                'name' => $name,
                'group' => GuestGroup::Teman->value,
                'pax_actual' => 2,
                'method' => CheckinMethod::WalkIn,
                'device_info' => 'Petugas Resepsi',
                'checked_in_at' => now()->subMinutes(rand(15, 45)),
            ]);
        }
    }

    private function seedBirthday(User $owner): Invitation
    {
        $birthday = Invitation::factory()->for($owner)->published()->create([
            'event_type' => EventType::Birthday,
            'title' => 'Ulang Tahun Dinda ke-25',
            'slug' => 'ulang-tahun-dinda',
            'theme' => 'minimal-modern',
            'expires_at' => now()->addYear(),
            'view_count' => 96,
            'settings' => [
                'story' => 'Hai! Aku merayakan ulang tahunku yang ke-25. Yuk, datang dan rayakan bareng aku.',
                'quote' => 'Bertambah usia, bertambah pula syukur.',
                'dress_code' => 'Casual colorful',
                'cover_message' => 'Halo,',
                'music_track' => 'morning-light',
                'rsvp_enabled' => true,
                'max_extra_guests' => 2,
                'show_wishes' => true,
            ],
        ]);

        InvitationHost::factory()->for($birthday)->create([
            'type' => 'host',
            'name' => 'Dinda Alya',
            'nickname' => 'Dinda',
            'photo_path' => 'demo/photo-4.svg',
            'description' => 'Pesta kecil-kecilan di rumah, penuh kue dan tawa.',
            'sort_order' => 1,
        ]);

        InvitationEvent::factory()->for($birthday)->create([
            'name' => 'Pesta Ulang Tahun',
            'starts_at' => now()->addDays(45)->setTime(15, 0),
            'ends_at' => now()->addDays(45)->setTime(19, 0),
            'venue' => 'Rumah Dinda',
            'address' => 'Jl. Anggrek No. 7, Bandung',
            'lat' => -6.9147,
            'lng' => 107.6098,
            'maps_url' => 'https://maps.google.com/?q=Jl+Anggrek+Bandung',
            'sort_order' => 1,
        ]);

        foreach ([4, 5, 6] as $index => $photoNumber) {
            InvitationPhoto::factory()->for($birthday)->create([
                'path' => sprintf('demo/photo-%d.svg', $photoNumber),
                'thumbnail_path' => sprintf('demo/photo-%d.svg', $photoNumber),
                'caption' => ['Dinda kecil', 'Kue ulang tahun', 'Teman-teman'][$index],
                'sort_order' => $index + 1,
            ]);
        }

        InvitationGift::factory()->for($birthday)->create([
            'type' => 'ewallet',
            'bank_name' => 'OVO',
            'account_number' => '081298765432',
            'account_name' => 'Dinda Alya',
            'sort_order' => 1,
        ]);

        $guests = Guest::factory()->for($birthday)->count(8)
            ->sequence(fn (Sequence $sequence): array => [
                'name' => [
                    'Bunga Citra', 'Raffi Aditya', 'Sekar Ayu', 'Tio Pangestu',
                    'Umma Nasya', 'Vino Baskoro', 'Yasmin Putri', 'Zaki Ramadhan',
                ][$sequence->index],
                'group' => GuestGroup::Teman,
                'phone' => '0857'.fake()->numerify('########'),
                'pax_quota' => 2,
                'token' => sprintf('ultah-%03d-demo', $sequence->index + 1),
                'sent_at' => now()->subDay(),
            ])
            ->create();

        foreach ($guests->take(5) as $guest) {
            Rsvp::factory()->for($birthday)->for($guest, 'guest')->create([
                'name' => $guest->name,
                'attendance' => Attendance::Hadir,
                'pax' => 2,
            ]);
        }

        foreach ([
            'Selamat ulang tahun Dinda! Semoga kompornya makin jago 😄',
            'Happy birthday! Doaku selalu menyertaimu.',
            'Selamat bertambah usia, tetap jadi orang yang menyenangkan!',
            'Sampai ketemu di pesta ya, Dinda!',
        ] as $index => $message) {
            Wish::factory()->for($birthday)->for($guests->get($index), 'guest')->create([
                'name' => $guests->get($index)?->name,
                'message' => $message,
            ]);
        }

        return $birthday;
    }

    private function seedActivityLogs(User $admin, User $owner, Invitation $wedding, Invitation $birthday): void
    {
        ActivityLog::factory()->for($owner)->for($wedding)->create([
            'action' => 'invitation.published',
            'description' => 'Menerbitkan undangan Rina & Dimas.',
            'meta' => ['slug' => $wedding->slug],
        ]);

        ActivityLog::factory()->for($owner)->for($wedding)->create([
            'action' => 'guests.imported',
            'description' => 'Mengimpor 40 tamu ke undangan Rina & Dimas.',
            'meta' => ['imported' => 40],
        ]);

        ActivityLog::factory()->for($owner)->for($birthday)->create([
            'action' => 'invitation.created',
            'description' => 'Membuat undangan Ulang Tahun Dinda ke-25.',
            'meta' => ['slug' => $birthday->slug],
        ]);

        ActivityLog::factory()->for($admin)->for($wedding)->create([
            'action' => 'invitation.reviewed',
            'description' => 'Meninjau undangan Rina & Dimas.',
        ]);
    }
}
