<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Checkin;
use App\Models\Guest;
use App\Models\Invitation;
use App\Models\Rsvp;
use App\Models\User;
use App\Models\Wish;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'invitations' => Invitation::count(),
                'published' => Invitation::where('status', 'published')->count(),
                'users' => User::count(),
                'guests' => Guest::count(),
                'rsvps' => Rsvp::count(),
                'wishes' => Wish::count(),
                'checkins' => Checkin::count(),
            ],
            'invitations' => Invitation::with('user')
                ->withCount(['guests', 'rsvps', 'checkins'])
                ->latest()
                ->take(8)
                ->get(),
            'activities' => ActivityLog::with(['user', 'invitation'])->latest()->take(10)->get(),
        ]);
    }
}
