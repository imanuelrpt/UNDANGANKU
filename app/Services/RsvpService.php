<?php

namespace App\Services;

use App\Enums\Attendance;
use App\Models\Guest;
use App\Models\Invitation;
use App\Models\Rsvp;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RsvpService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function submit(Invitation $invitation, array $data, ?Guest $guest): Rsvp
    {
        if (! (bool) $invitation->setting('rsvp_enabled', true)) {
            throw ValidationException::withMessages(['rsvp' => 'Maaf, konfirmasi kehadiran sedang dinonaktifkan.']);
        }

        $deadline = $invitation->setting('rsvp_deadline');
        if (filled($deadline) && Carbon::parse($deadline)->isPast()) {
            throw ValidationException::withMessages(['rsvp' => 'Batas waktu konfirmasi kehadiran sudah terlewat.']);
        }

        $maxPax = $guest
            ? (int) $guest->pax_quota
            : 1 + (int) $invitation->setting('max_extra_guests', 3);

        $pax = (int) ($data['pax'] ?? 1);
        if ($pax > $maxPax) {
            throw ValidationException::withMessages([
                'pax' => "Jumlah orang maksimal {$maxPax} orang sesuai kuota yang diberikan.",
            ]);
        }

        $attendance = Attendance::from($data['attendance']);
        $name = $guest?->name ?? $data['name'];

        return DB::transaction(function () use ($invitation, $data, $guest, $attendance, $name, $pax): Rsvp {
            $rsvp = Rsvp::updateOrCreate(
                [
                    'invitation_id' => $invitation->id,
                    'guest_id' => $guest?->id,
                    'name' => $name,
                ],
                [
                    'attendance' => $attendance->value,
                    'pax' => $pax,
                    'answers' => $data['answers'] ?? [],
                    'note' => $data['note'] ?? null,
                    'ip_address' => request()->ip(),
                ],
            );

            $eventIds = collect($data['event_ids'] ?? [])
                ->filter()
                ->intersect($invitation->events()->pluck('id'));

            $rsvp->events()->sync($eventIds->all());

            return $rsvp->load('events');
        });
    }
}
