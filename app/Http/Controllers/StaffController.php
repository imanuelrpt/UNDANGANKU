<?php

namespace App\Http\Controllers;

use App\Enums\CheckinMethod;
use App\Http\Requests\ManualCheckinRequest;
use App\Http\Requests\ScanQrRequest;
use App\Http\Requests\StaffLoginRequest;
use App\Http\Requests\UpdateCheckinRequest;
use App\Models\ActivityLog;
use App\Models\Checkin;
use App\Models\CheckinStaffLink;
use App\Models\Guest;
use App\Services\CheckinService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function __construct(protected CheckinService $checkinService) {}

    public function login(string $token): View|RedirectResponse
    {
        $staffLink = $this->findLink($token);

        if (! $staffLink->hasPin()) {
            $this->authorizeStaff($token, $staffLink);

            return redirect()->route('staff.scanner', $token);
        }

        return view('staff.login', [
            'staffLink' => $staffLink,
            'invitation' => $staffLink->invitation,
        ]);
    }

    public function authenticate(StaffLoginRequest $request, string $token): RedirectResponse
    {
        $staffLink = $this->findLink($token);

        if ($staffLink->hasPin() && ! Hash::check((string) $request->input('pin'), (string) $staffLink->pin_hash)) {
            return back()->with('error', 'PIN tidak benar. Silakan coba lagi.');
        }

        $this->authorizeStaff($token, $staffLink);

        return redirect()->route('staff.scanner', $token);
    }

    public function scanner(Request $request, string $token): View|RedirectResponse
    {
        $staffLink = $this->resolveStaff($request, $token);

        if ($staffLink === null) {
            return $this->loginRequired($token);
        }

        return view('staff.scanner', [
            'staffLink' => $staffLink,
            'invitation' => $staffLink->invitation,
            'checkedIn' => $staffLink->invitation->checkins()->count(),
            'totalGuests' => $staffLink->invitation->guests()->count(),
        ]);
    }

    public function scan(ScanQrRequest $request, string $token): JsonResponse|RedirectResponse
    {
        $staffLink = $this->resolveStaff($request, $token);

        if ($staffLink === null) {
            return $this->loginRequired($token);
        }

        $invitation = $staffLink->invitation;
        $result = $this->checkinService->verify($invitation, $request->validated('token'));

        if ($result['status'] === 'ok' && $request->boolean('confirm') && $result['guest'] !== null) {
            $checkin = $this->checkinService->checkin($invitation, $result['guest'], [
                'pax_actual' => $result['guest']->pax_quota,
                'method' => CheckinMethod::Scan->value,
            ], $staffLink);

            ActivityLog::record('checkin.scan', "Check-in QR untuk {$checkin->name}.", $invitation);

            return response()->json([
                'status' => 'checked_in',
                'guest' => [
                    'name' => $checkin->name,
                    'group' => $checkin->guest?->group->label() ?? $checkin->group,
                    'pax' => $checkin->pax_actual,
                ],
                'message' => 'Check-in berhasil. Selamat datang!',
            ]);
        }

        return response()->json([
            'status' => $result['status'],
            'guest' => $result['guest'] ? [
                'id' => $result['guest']->id,
                'name' => $result['guest']->name,
                'group' => $result['guest']->group->label(),
                'pax' => $result['guest']->pax_quota,
            ] : null,
            'message' => $result['message'],
            'checked_in_at' => $result['checked_in_at'],
            'checkin_url' => route('staff.manual', $token),
        ]);
    }

    public function manual(ManualCheckinRequest $request, string $token): JsonResponse|RedirectResponse
    {
        $staffLink = $this->resolveStaff($request, $token);

        if ($staffLink === null) {
            return $this->loginRequired($token);
        }

        $invitation = $staffLink->invitation;
        $data = $request->validated();

        if (filled($data['guest_id'] ?? null)) {
            $guest = Guest::where('invitation_id', $invitation->id)->findOrFail($data['guest_id']);
            $checkin = $this->checkinService->checkin($invitation, $guest, [
                'pax_actual' => $data['pax_actual'],
                'method' => $data['method'] ?? CheckinMethod::Manual->value,
            ], $staffLink);
        } else {
            $checkin = $this->checkinService->walkIn($invitation, $data, $staffLink);
        }

        ActivityLog::record('checkin.manual', "Check-in {$checkin->name} secara manual.", $invitation);

        return response()->json([
            'status' => 'checked_in',
            'guest' => [
                'name' => $checkin->name,
                'group' => $checkin->group,
                'pax' => $checkin->pax_actual,
            ],
            'message' => 'Check-in berhasil. Selamat datang!',
        ], 201);
    }

    public function attendance(Request $request, string $token): View|RedirectResponse
    {
        $staffLink = $this->resolveStaff($request, $token);

        if ($staffLink === null) {
            return $this->loginRequired($token);
        }

        return view('staff.attendance', array_merge($this->checkinService->attendance($staffLink->invitation), [
            'staffLink' => $staffLink,
            'invitation' => $staffLink->invitation,
        ]));
    }

    public function updateCheckin(UpdateCheckinRequest $request, string $token, Checkin $checkin): JsonResponse|RedirectResponse
    {
        $staffLink = $this->resolveStaff($request, $token);

        if ($staffLink === null) {
            return $this->loginRequired($token);
        }

        if ($checkin->invitation_id !== $staffLink->invitation_id) {
            abort(404);
        }

        $checkin->update(['pax_actual' => (int) $request->validated('pax_actual')]);

        return response()->json([
            'status' => 'updated',
            'pax_actual' => $checkin->pax_actual,
            'message' => 'Jumlah tamu berhasil diperbarui.',
        ]);
    }

    private function findLink(string $token): CheckinStaffLink
    {
        return CheckinStaffLink::where('token', $token)->where('is_active', true)->firstOrFail();
    }

    private function authorizeStaff(string $token, CheckinStaffLink $staffLink): void
    {
        session()->put("staff.{$token}", [
            'link_id' => $staffLink->id,
            'name' => $staffLink->label,
            'at' => now()->timestamp,
        ]);
    }

    private function resolveStaff(Request $request, string $token): ?CheckinStaffLink
    {
        $staffLink = $this->findLink($token);

        return $request->session()->has("staff.{$token}") ? $staffLink : null;
    }

    private function loginRequired(string $token): RedirectResponse
    {
        return redirect()->route('staff.login', $token)->with('error', 'Silakan masukkan PIN petugas terlebih dahulu.');
    }
}
