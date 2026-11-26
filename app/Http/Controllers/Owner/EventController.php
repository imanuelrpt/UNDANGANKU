<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\ActivityLog;
use App\Models\Invitation;
use App\Models\InvitationEvent;
use Illuminate\Http\RedirectResponse;

class EventController extends Controller
{
    public function store(StoreEventRequest $request, Invitation $invitation): RedirectResponse
    {
        $this->authorize('update', $invitation);

        $event = $invitation->events()->create(array_merge($request->validated(), [
            'sort_order' => $invitation->events()->count(),
        ]));

        ActivityLog::record('event.created', "Sesi acara {$event->name} ditambahkan.", $invitation);

        return back()->with('success', 'Sesi acara berhasil ditambahkan.');
    }

    public function update(UpdateEventRequest $request, InvitationEvent $event): RedirectResponse
    {
        $this->authorize('update', $event->invitation);

        $event->update($request->validated());

        ActivityLog::record('event.updated', "Sesi acara {$event->name} diperbarui.", $event->invitation);

        return back()->with('success', 'Sesi acara berhasil diperbarui.');
    }

    public function destroy(InvitationEvent $event): RedirectResponse
    {
        $this->authorize('update', $event->invitation);

        $name = $event->name;
        $invitation = $event->invitation;
        $event->delete();

        ActivityLog::record('event.deleted', "Sesi acara {$name} dihapus.", $invitation);

        return back()->with('success', 'Sesi acara dihapus.');
    }
}
