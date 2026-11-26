<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStaffLinkRequest;
use App\Models\ActivityLog;
use App\Models\CheckinStaffLink;
use App\Models\Invitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class StaffLinkController extends Controller
{
    public function store(StoreStaffLinkRequest $request, Invitation $invitation): RedirectResponse
    {
        $this->authorize('manageStaff', $invitation);

        $validated = $request->validated();
        $pin = $validated['pin'] ?? null;

        $link = $invitation->staffLinks()->create([
            'label' => $validated['label'],
            'pin_hash' => filled($pin) ? Hash::make($pin) : null,
            'is_active' => true,
        ]);

        ActivityLog::record('staff_link.created', "Link petugas {$link->label} dibuat.", $invitation);

        return back()
            ->with('success', 'Link petugas berhasil dibuat.')
            ->with('staff_link', [
                'url' => $link->url(),
                'label' => $link->label,
                'pin' => $pin,
            ]);
    }

    public function destroy(CheckinStaffLink $staffLink): RedirectResponse
    {
        $this->authorize('manageStaff', $staffLink->invitation);

        $invitation = $staffLink->invitation;
        $staffLink->delete();

        ActivityLog::record('staff_link.deleted', 'Link petugas dinonaktifkan.', $invitation);

        return back()->with('success', 'Link petugas dihapus.');
    }
}
