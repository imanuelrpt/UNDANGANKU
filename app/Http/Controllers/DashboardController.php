<?php

namespace App\Http\Controllers;

use App\Models\Checkin;
use App\Models\Invitation;
use App\Models\Rsvp;
use App\Models\Wish;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        if (auth()->user()?->isAdmin()) {
            return view('dashboard', [
                'invitations' => Invitation::with('user')->withCount(['guests', 'rsvps', 'wishes', 'checkins'])->latest()->get(),
                'isAdminView' => true,
                'stats' => [
                    'invitations' => Invitation::count(),
                    'users' => \App\Models\User::count(),
                    'guests' => \App\Models\Guest::count(),
                    'rsvps' => Rsvp::count(),
                ],
            ]);
        }

        return view('dashboard', [
            'invitations' => auth()->user()->invitations()
                ->withCount(['guests', 'rsvps', 'wishes', 'checkins'])
                ->latest()
                ->get(),
            'isAdminView' => false,
            'stats' => [],
        ]);
    }

    public function stats(Invitation $invitation): JsonResponse
    {
        $this->authorize('viewStats', $invitation);

        $stats = Cache::remember("invitation.stats.{$invitation->id}", now()->addSeconds(10), function () use ($invitation): array {
            return $this->buildStats($invitation);
        });

        return response()->json($stats);
    }

    /**
     * @return array<string, mixed>
     */
    public static function buildStats(Invitation $invitation): array
    {
        $totalGuests = $invitation->guests()->count();
        $totalPax = (int) $invitation->guests()->sum('pax_quota');

        $attendance = Rsvp::where('invitation_id', $invitation->id)
            ->selectRaw('attendance, COUNT(*) as total, COALESCE(SUM(pax), 0) as pax')
            ->groupBy('attendance')
            ->get()
            ->mapWithKeys(fn (Rsvp $row): array => [
                $row->attendance->value => ['total' => (int) $row->total, 'pax' => (int) $row->pax],
            ])
            ->all();

        $responded = collect($attendance)->sum('total');

        $timeline = Rsvp::where('invitation_id', $invitation->id)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->all();

        $groupAttendance = Checkin::where('invitation_id', $invitation->id)
            ->selectRaw('COALESCE(`group`, "lainnya") as guest_group, SUM(pax_actual) as total')
            ->groupBy('guest_group')
            ->pluck('total', 'guest_group')
            ->all();

        $checkins = $invitation->checkins()->count();

        return [
            'total_guests' => $totalGuests,
            'total_pax' => $totalPax,
            'hadir' => $attendance['hadir']['total'] ?? 0,
            'tidak_hadir' => $attendance['tidak_hadir']['total'] ?? 0,
            'ragu' => $attendance['ragu']['total'] ?? 0,
            'belum_jawab' => max($totalGuests - $responded, 0),
            'pax_hadir' => $attendance['hadir']['pax'] ?? 0,
            'response_rate' => $totalGuests > 0 ? round($responded / $totalGuests * 100) : 0,
            'checkins' => $checkins,
            'checkin_rate' => $totalGuests > 0 ? round($checkins / $totalGuests * 100) : 0,
            'wishes' => Wish::where('invitation_id', $invitation->id)->where('is_hidden', false)->count(),
            'views' => $invitation->view_count,
            'timeline' => $timeline,
            'group_attendance' => $groupAttendance,
        ];
    }
}
