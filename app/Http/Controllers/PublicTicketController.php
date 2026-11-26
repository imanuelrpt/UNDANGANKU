<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\InvitationEvent;
use App\Services\CalendarService;
use App\Services\QrTicketService;
use Illuminate\Http\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PublicTicketController extends Controller
{
    public function __construct(
        protected QrTicketService $ticketService,
        protected CalendarService $calendarService,
    ) {
    }

    public function image(string $token): Response
    {
        $guest = $this->resolveGuest($token);
        $signed = $this->ticketService->sign($guest);
        $svg = QrCode::format('svg')->size(320)->margin(1)->generate($signed);

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'no-store',
        ]);
    }

    public function pdf(string $token): Response
    {
        $guest = $this->resolveGuest($token);
        $invitation = $guest->invitation;
        $signed = $this->ticketService->sign($guest);

        $pdf = app('dompdf.wrapper')->loadView('public.ticket-pdf', [
            'guest' => $guest,
            'invitation' => $invitation,
            'events' => $invitation->events,
            'qr' => base64_encode(QrCode::format('svg')->size(300)->margin(1)->generate($signed)),
        ]);

        return $pdf->download('tiket-'.$guest->name.'.pdf');
    }

    public function ics(InvitationEvent $event): Response
    {
        return response($this->calendarService->ics($event), 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="acara-'.$event->id.'.ics"',
        ]);
    }

    private function resolveGuest(string $token): Guest
    {
        return Guest::where('token', $token)->firstOrFail();
    }
}
