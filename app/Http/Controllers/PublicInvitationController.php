<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Invitation;
use App\Services\InvitationThemeResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PublicInvitationController extends Controller
{
    public function __construct(protected InvitationThemeResolver $themeResolver)
    {
    }

    public function show(Request $request, string $slug, ?string $token = null): View
    {
        $invitation = Invitation::where('slug', $slug)
            ->with(['hosts', 'events', 'photos', 'gifts', 'questions'])
            ->firstOrFail();

        $isPreview = $request->boolean('preview') && $invitation->isPreviewableBy(auth()->user());

        if (! $isPreview && $invitation->status->value !== 'published') {
            abort(404, 'Undangan ini belum tersedia.');
        }

        $guest = null;
        if (filled($token)) {
            $guest = Guest::where('invitation_id', $invitation->id)->where('token', $token)->firstOrFail();

            if (! $request->session()->has("guest-opened.{$guest->id}")) {
                $guest->markOpened();
                $request->session()->put("guest-opened.{$guest->id}", true);
            }
        }

        if (! $isPreview) {
            $invitation->increment('view_count');
        }

        return view($this->themeResolver->view($invitation), [
            'invitation' => $invitation,
            'theme' => $this->themeResolver->resolve($invitation),
            'guest' => $guest,
            'hosts' => $invitation->hosts,
            'events' => $invitation->events,
            'photos' => $invitation->photos,
            'gifts' => $invitation->gifts,
            'questions' => $invitation->questions->where('is_active', true)->values(),
            'wishes' => $invitation->wishes()->visible()->latest()->take(12)->get(),
            'wishesTotal' => $invitation->wishes()->visible()->count(),
            'musicUrl' => $this->resolveMusic($invitation),
            'isPreview' => $isPreview,
            'isExpired' => $invitation->isExpired(),
            'shareUrl' => $guest ? $invitation->path($guest) : $invitation->path(),
        ]);
    }

    private function resolveMusic(Invitation $invitation): ?string
    {
        $settings = $invitation->settings;

        if (filled($settings['music_file'])) {
            return str_starts_with((string) $settings['music_file'], '/')
                ? (string) $settings['music_file']
                : Storage::url($settings['music_file']);
        }

        $track = config('undangan.music_tracks.'.$settings['music_track'].'.file');

        return $track ? url($track) : null;
    }
}
