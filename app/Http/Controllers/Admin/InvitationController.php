<?php

namespace App\Http\Controllers\Admin;

use App\Enums\InvitationStatus;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Invitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function index(Request $request): View
    {
        $query = Invitation::with('user')->withCount(['guests', 'rsvps', 'wishes', 'checkins']);

        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->string('search').'%')
                ->orWhere('slug', 'like', '%'.$request->string('search').'%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        return view('admin.invitations.index', [
            'invitations' => $query->latest()->paginate(20)->withQueryString(),
            'statuses' => InvitationStatus::options(),
        ]);
    }

    public function takedown(Invitation $invitation): RedirectResponse
    {
        $invitation->update(['status' => InvitationStatus::Archived]);

        ActivityLog::record('invitation.takedown', "Undangan {$invitation->title} diarsipkan oleh admin.", $invitation);

        return back()->with('success', 'Undangan berhasil diarsipkan dan tidak dapat diakses publik.');
    }
}
