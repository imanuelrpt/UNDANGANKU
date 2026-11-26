<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRsvpRequest;
use App\Models\ActivityLog;
use App\Models\Guest;
use App\Models\Invitation;
use App\Services\RsvpService;
use Illuminate\Http\RedirectResponse;

class PublicRsvpController extends Controller
{
    public function __construct(protected RsvpService $rsvpService)
    {
    }

    public function store(StoreRsvpRequest $request, string $slug): RedirectResponse
    {
        $invitation = Invitation::where('slug', $slug)->firstOrFail();

        if ($invitation->status->value !== 'published') {
            abort(404, 'Undangan ini belum tersedia.');
        }

        $guest = null;
        $token = $request->route('token') ?? $request->input('token');
        if (filled($token)) {
            $guest = Guest::where('invitation_id', $invitation->id)
                ->where('token', $token)
                ->firstOrFail();
        }

        $rsvp = $this->rsvpService->submit($invitation, $request->validated(), $guest);

        ActivityLog::record('rsvp.submitted', "RSVP diterima dari {$rsvp->name}.", $invitation, [
            'attendance' => $rsvp->attendance->value,
            'pax' => $rsvp->pax,
        ]);

        return back()->with('success', 'Terima kasih! Konfirmasi kehadiran Anda sudah kami terima.');
    }
}
