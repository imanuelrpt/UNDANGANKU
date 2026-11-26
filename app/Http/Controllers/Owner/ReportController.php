<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Exports\GuestsExport;
use App\Models\Invitation;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function excel(Invitation $invitation)
    {
        $this->authorize('viewStats', $invitation);

        return Excel::download(new GuestsExport($invitation), 'laporan-'.$invitation->slug.'.xlsx');
    }

    public function pdf(Invitation $invitation)
    {
        $this->authorize('viewStats', $invitation);

        $stats = \App\Http\Controllers\DashboardController::buildStats($invitation);
        $events = $invitation->events()->get();
        $summary = $invitation->rsvps()->with('guest')->get();

        $pdf = app('dompdf.wrapper')->loadView('owner.reports.pdf', [
            'invitation' => $invitation,
            'stats' => $stats,
            'events' => $events,
            'summary' => $summary,
        ]);

        return $pdf->download('laporan-'.$invitation->slug.'.pdf');
    }
}
