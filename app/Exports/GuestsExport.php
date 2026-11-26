<?php

namespace App\Exports;

use App\Models\Guest;
use App\Models\Invitation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class GuestsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(protected Invitation $invitation)
    {
    }

    public function collection(): Collection
    {
        return $this->invitation->guests()->with(['rsvp', 'checkin'])->latest()->get();
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'Nama', 'Grup', 'WhatsApp', 'Kuota', 'Kehadiran', 'Jumlah Hadir',
            'Check-in', 'Waktu Check-in', 'Link Personal', 'Catatan',
        ];
    }

    /**
     * @param  Guest  $guest
     * @return array<int, mixed>
     */
    public function map($guest): array
    {
        return [
            $guest->name,
            $guest->group->label(),
            $guest->phone,
            $guest->pax_quota,
            $guest->rsvp?->attendance->label() ?? 'Belum jawab',
            $guest->rsvp?->isAttending() ? $guest->rsvp->pax : 0,
            $guest->checkin ? 'Ya' : 'Tidak',
            $guest->checkin?->checked_in_at?->format('d/m/Y H:i'),
            route('invitation.public.guest', [$this->invitation->slug, $guest->token]),
            $guest->notes,
        ];
    }
}
