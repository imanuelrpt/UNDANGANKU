<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateWishRequest;
use App\Models\ActivityLog;
use App\Models\Invitation;
use App\Models\Wish;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishController extends Controller
{
    public function index(Request $request, Invitation $invitation): View
    {
        $this->authorize('manageWishes', $invitation);

        $query = $invitation->wishes()->with('guest');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->string('search').'%')
                ->orWhere('message', 'like', '%'.$request->string('search').'%');
        }

        if ($request->filled('status')) {
            $query->where('is_hidden', $request->string('status') === 'hidden');
        }

        return view('owner.wishes.index', [
            'invitation' => $invitation,
            'wishes' => $query->paginate(20)->withQueryString(),
            'stats' => [
                'total' => $invitation->wishes()->count(),
                'visible' => $invitation->wishes()->visible()->count(),
                'replied' => $invitation->wishes()->whereNotNull('reply_message')->count(),
            ],
        ]);
    }

    public function update(UpdateWishRequest $request, Wish $wish): RedirectResponse
    {
        $this->authorize('manageWishes', $wish->invitation);

        $wish->update([
            'is_hidden' => $request->boolean('is_hidden'),
            'reply_message' => $request->input('reply_message') ?: null,
        ]);

        ActivityLog::record('wish.updated', 'Ucapan diperbarui.', $wish->invitation);

        return back()->with('success', 'Ucapan berhasil diperbarui.');
    }

    public function destroy(Wish $wish): RedirectResponse
    {
        $this->authorize('manageWishes', $wish->invitation);

        $invitation = $wish->invitation;
        $wish->delete();

        ActivityLog::record('wish.deleted', 'Ucapan dihapus.', $invitation);

        return back()->with('success', 'Ucapan dihapus.');
    }
}
