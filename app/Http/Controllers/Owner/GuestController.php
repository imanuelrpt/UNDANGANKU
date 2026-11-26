<?php

namespace App\Http\Controllers\Owner;

use App\Enums\Attendance;
use App\Enums\GuestGroup;
use App\Http\Controllers\Controller;
use App\Http\Requests\BulkGuestsRequest;
use App\Http\Requests\ImportGuestsRequest;
use App\Http\Requests\StoreGuestRequest;
use App\Http\Requests\UpdateGuestRequest;
use App\Exports\GuestTemplateExport;
use App\Exports\GuestsExport;
use App\Models\ActivityLog;
use App\Models\Guest;
use App\Models\Invitation;
use App\Services\GuestImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class GuestController extends Controller
{
    public function __construct(protected GuestImportService $importService)
    {
    }

    public function index(Request $request, Invitation $invitation): View
    {
        $this->authorize('manageGuests', $invitation);

        $query = $invitation->guests()->with(['rsvp', 'checkin']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->string('search').'%')
                ->orWhere('phone', 'like', '%'.$request->string('search').'%');
        }

        if ($request->filled('group')) {
            $query->where('group', $request->string('group'));
        }

        if ($request->filled('attendance')) {
            $query->whereHas('rsvp', fn ($builder) => $builder->where('attendance', $request->string('attendance')));
        }

        if ($request->boolean('checked_in')) {
            $query->whereHas('checkin');
        }

        return view('owner.guests.index', [
            'invitation' => $invitation,
            'guests' => $query->paginate(20)->withQueryString(),
            'groups' => GuestGroup::options(),
            'attendances' => Attendance::options(),
            'stats' => [
                'total' => $invitation->guests()->count(),
                'pax' => (int) $invitation->guests()->sum('pax_quota'),
                'responded' => $invitation->rsvps()->count(),
                'checked_in' => $invitation->checkins()->count(),
            ],
            'importResult' => $request->session()->pull('import_result'),
            'whatsappTemplate' => $invitation->setting('whatsapp_template') ?: config('undangan.default_whatsapp_template'),
        ]);
    }

    public function create(Invitation $invitation): View
    {
        $this->authorize('manageGuests', $invitation);

        return view('owner.guests.form', [
            'invitation' => $invitation,
            'guest' => null,
            'groups' => GuestGroup::options(),
        ]);
    }

    public function store(StoreGuestRequest $request, Invitation $invitation): RedirectResponse
    {
        $this->authorize('manageGuests', $invitation);

        $invitation->guests()->create(array_merge($request->validated(), [
            'token' => Str::random(config('undangan.guest_token_length', 24)),
        ]));

        ActivityLog::record('guest.created', 'Tamu baru ditambahkan.', $invitation);

        return redirect()->route('guests.index', $invitation)->with('success', 'Tamu berhasil ditambahkan.');
    }

    public function edit(Guest $guest): View
    {
        $this->authorize('manageGuests', $guest->invitation);

        return view('owner.guests.form', [
            'invitation' => $guest->invitation,
            'guest' => $guest,
            'groups' => GuestGroup::options(),
        ]);
    }

    public function update(UpdateGuestRequest $request, Guest $guest): RedirectResponse
    {
        $this->authorize('manageGuests', $guest->invitation);

        $guest->update($request->validated());

        ActivityLog::record('guest.updated', "Data tamu {$guest->name} diperbarui.", $guest->invitation);

        return redirect()->route('guests.index', $guest->invitation)->with('success', 'Data tamu diperbarui.');
    }

    public function destroy(Guest $guest): RedirectResponse
    {
        $this->authorize('manageGuests', $guest->invitation);

        $invitation = $guest->invitation;
        $guest->delete();

        ActivityLog::record('guest.deleted', 'Tamu dihapus.', $invitation);

        return back()->with('success', 'Tamu dihapus.');
    }

    public function bulk(BulkGuestsRequest $request, Invitation $invitation): RedirectResponse
    {
        $this->authorize('manageGuests', $invitation);

        $guests = $invitation->guests()->whereIn('id', $request->input('ids'))->get();

        if ($request->string('action') === 'delete') {
            foreach ($guests as $guest) {
                $guest->delete();
            }
            $message = "{$guests->count()} tamu dihapus.";
        } else {
            foreach ($guests as $guest) {
                $guest->update(['sent_at' => now()]);
            }
            $message = "{$guests->count()} tamu ditandai sudah dikirim.";
        }

        ActivityLog::record('guest.bulk', $message, $invitation);

        return back()->with('success', $message);
    }

    public function import(ImportGuestsRequest $request, Invitation $invitation): RedirectResponse
    {
        $this->authorize('manageGuests', $invitation);

        $result = $this->importService->import($invitation, $request->file('file'));

        ActivityLog::record('guest.imported', "{$result['imported']} tamu diimpor.", $invitation, [
            'failed' => count($result['failed']),
        ]);

        return back()->with('success', "{$result['imported']} tamu berhasil diimpor.")
            ->with('import_result', $result);
    }

    public function template(Invitation $invitation)
    {
        $this->authorize('manageGuests', $invitation);

        return Excel::download(new GuestTemplateExport(), 'template-tamu-undanganku.xlsx');
    }

    public function export(Invitation $invitation)
    {
        $this->authorize('manageGuests', $invitation);

        return Excel::download(new GuestsExport($invitation), 'tamu-'.$invitation->slug.'.xlsx');
    }

    public function exportPdf(Invitation $invitation)
    {
        $this->authorize('manageGuests', $invitation);

        $guests = $invitation->guests()->with(['rsvp', 'checkin'])->get();

        $pdf = app('dompdf.wrapper')->loadView('owner.guests.pdf', [
            'invitation' => $invitation,
            'guests' => $guests,
        ]);

        return $pdf->download('tamu-'.$invitation->slug.'.pdf');
    }

    public function markSent(Guest $guest): RedirectResponse
    {
        $this->authorize('manageGuests', $guest->invitation);

        $guest->update(['sent_at' => now()]);

        return back()->with('success', "Undangan untuk {$guest->name} ditandai sudah dikirim.");
    }
}
