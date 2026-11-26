<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGiftRequest;
use App\Models\ActivityLog;
use App\Models\Invitation;
use App\Models\InvitationGift;
use Illuminate\Http\RedirectResponse;

class GiftController extends Controller
{
    public function store(StoreGiftRequest $request, Invitation $invitation): RedirectResponse
    {
        $this->authorize('update', $invitation);

        $invitation->gifts()->create(array_merge($request->validated(), [
            'sort_order' => $invitation->gifts()->count(),
        ]));

        ActivityLog::record('gift.created', 'Rekening/amplop digital ditambahkan.', $invitation);

        return back()->with('success', 'Amplop digital berhasil ditambahkan.');
    }

    public function destroy(InvitationGift $gift): RedirectResponse
    {
        $this->authorize('update', $gift->invitation);

        $invitation = $gift->invitation;
        $gift->delete();

        ActivityLog::record('gift.deleted', 'Rekening/amplop digital dihapus.', $invitation);

        return back()->with('success', 'Amplop digital dihapus.');
    }
}
