<?php

namespace App\Services;

use App\Enums\CheckinMethod;
use App\Models\Checkin;
use App\Models\CheckinStaffLink;
use App\Models\Guest;
use App\Models\Invitation;
use Illuminate\Validation\ValidationException;

class CheckinService
{
    public function __construct(protected QrTicketService $ticketService)
    {
    }

    /**
     * @return array{status: string, guest: Guest|null, message: string, checked_in_at: string|null}
     */
    public function verify(Invitation $invitation, string $signedToken): array
    {
        $payload = $this->ticketService->verify($signedToken);

        if ($payload === null || $payload['i'] !== $invitation->id) {
            return [
                'status' => 'invalid',
                'guest' => null,
                'message' => 'QR tidak valid atau bukan untuk undangan ini.',
                'checked_in_at' => null,
            ];
        }

        $guest = Guest::where('invitation_id', $invitation->id)
            ->where('token', $payload['t'])
            ->first();

        if ($guest === null) {
            return [
                'status' => 'invalid',
                'guest' => null,
                'message' => 'Tamu tidak ditemukan pada undangan ini.',
                'checked_in_at' => null,
            ];
        }

        $checkin = $invitation->checkins()->where('guest_id', $guest->id)->first();

        if ($checkin !== null) {
            return [
                'status' => 'already',
                'guest' => $guest,
                'message' => 'Sudah check-in pukul '.$checkin->checked_in_at->format('H:i').'.',
                'checked_in_at' => $checkin->checked_in_at->format('H:i'),
            ];
        }

        return [
            'status' => 'ok',
            'guest' => $guest,
            'message' => 'QR valid. Silakan konfirmasi kehadiran.',
            'checked_in_at' => null,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function checkin(
        Invitation $invitation,
        Guest $guest,
        array $data,
        CheckinStaffLink $staffLink,
    ): Checkin {
        if ($guest->invitation_id !== $invitation->id) {
            throw ValidationException::withMessages(['guest' => 'Tamu tidak terdaftar pada undangan ini.']);
        }

        if ($invitation->checkins()->where('guest_id', $guest->id)->exists()) {
            throw ValidationException::withMessages(['guest' => 'Tamu ini sudah check-in.']);
        }

        return Checkin::create([
            'invitation_id' => $invitation->id,
            'guest_id' => $guest->id,
            'staff_link_id' => $staffLink->id,
            'name' => $guest->name,
            'group' => $guest->group->value,
            'pax_actual' => min(max((int) ($data['pax_actual'] ?? 1), 1), (int) $guest->pax_quota),
            'method' => $data['method'] ?? CheckinMethod::Scan->value,
            'device_info' => request()->userAgent(),
            'checked_in_at' => now(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function walkIn(
        Invitation $invitation,
        array $data,
        CheckinStaffLink $staffLink,
    ): Checkin {
        return Checkin::create([
            'invitation_id' => $invitation->id,
            'guest_id' => null,
            'staff_link_id' => $staffLink->id,
            'name' => $data['name'],
            'group' => $data['group'] ?? null,
            'pax_actual' => max((int) ($data['pax_actual'] ?? 1), 1),
            'method' => CheckinMethod::WalkIn->value,
            'device_info' => request()->userAgent(),
            'checked_in_at' => now(),
        ]);
    }

    /**
     * @return array{checked_in: int, total_guests: int, total_pax: int, checkins: \Illuminate\Database\Eloquent\Collection}
     */
    public function attendance(Invitation $invitation): array
    {
        $checkins = $invitation->checkins()->with('guest')->latest('checked_in_at')->get();

        return [
            'checked_in' => $checkins->count(),
            'total_guests' => $invitation->guests()->count(),
            'total_pax' => (int) $checkins->sum('pax_actual'),
            'checkins' => $checkins,
        ];
    }
}
