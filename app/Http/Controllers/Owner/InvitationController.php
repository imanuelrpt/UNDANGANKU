<?php

namespace App\Http\Controllers\Owner;

use App\Enums\InvitationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvitationRequest;
use App\Http\Requests\StoreMusicRequest;
use App\Http\Requests\UpdateInvitationRequest;
use App\Models\ActivityLog;
use App\Models\Invitation;
use App\Services\InvitationService;
use App\Services\InvitationThemeResolver;
use App\Services\UploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function __construct(
        protected InvitationService $invitationService,
        protected InvitationThemeResolver $themeResolver,
        protected UploadService $uploadService,
    ) {
    }

    public function index(Request $request): View
    {
        $query = auth()->user()->invitations()->withCount(['guests', 'rsvps', 'wishes', 'checkins']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('search')) {
            $query->where(function ($builder) use ($request): void {
                $builder->where('title', 'like', '%'.$request->string('search').'%')
                    ->orWhere('slug', 'like', '%'.$request->string('search').'%');
            });
        }

        return view('owner.invitations.index', [
            'invitations' => $query->latest()->paginate(12)->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('owner.invitations.create', [
            'themes' => $this->themeResolver->options(),
        ]);
    }

    public function store(StoreInvitationRequest $request): RedirectResponse
    {
        $invitation = $this->invitationService->create($request->validated(), $request->user());

        ActivityLog::record('invitation.created', "Undangan {$invitation->title} dibuat.", $invitation);

        return redirect()->route('invitations.edit', $invitation)
            ->with('success', 'Undangan berhasil dibuat. Lanjutkan mengisi detailnya.');
    }

    public function edit(Request $request, Invitation $invitation): View
    {
        $this->authorize('update', $invitation);

        $invitation->load(['hosts', 'events', 'photos', 'gifts', 'questions']);

        return view('owner.invitations.edit', [
            'invitation' => $invitation,
            'step' => max(1, min(9, (int) $request->query('step', 1))),
            'themes' => $this->themeResolver->options(),
            'eventTypes' => \App\Enums\EventType::options(),
            'timezones' => config('undangan.timezones', []),
        ]);
    }

    public function update(UpdateInvitationRequest $request, Invitation $invitation): RedirectResponse
    {
        $this->authorize('update', $invitation);

        $data = $request->validated();
        $settings = array_merge($invitation->settings, $data['settings'] ?? []);
        unset($data['settings']);

        $data['settings'] = $settings;
        $this->invitationService->update($invitation, $data);

        if ($request->has('hosts')) {
            $this->syncHosts($invitation, $request->input('hosts', []));
        }

        ActivityLog::record('invitation.updated', "Undangan {$invitation->title} diperbarui.", $invitation);

        return redirect()->route('invitations.edit', [$invitation, 'step' => $request->input('step', 1)])
            ->with('success', 'Perubahan tersimpan.');
    }

    public function publish(Invitation $invitation): RedirectResponse
    {
        $this->authorize('update', $invitation);
        $invitation->update(['status' => InvitationStatus::Published]);
        ActivityLog::record('invitation.published', "Undangan {$invitation->title} dipublikasikan.", $invitation);

        return back()->with('success', 'Undangan sudah dipublikasikan.');
    }

    public function unpublish(Invitation $invitation): RedirectResponse
    {
        $this->authorize('update', $invitation);
        $invitation->update(['status' => InvitationStatus::Draft]);
        ActivityLog::record('invitation.unpublished', "Undangan {$invitation->title} dijadikan draft.", $invitation);

        return back()->with('success', 'Undangan dijadikan draft.');
    }

    public function duplicate(Invitation $invitation): RedirectResponse
    {
        $this->authorize('update', $invitation);
        $copy = $this->invitationService->duplicate($invitation);
        ActivityLog::record('invitation.duplicated', "Undangan {$invitation->title} diduplikat.", $copy);

        return redirect()->route('invitations.edit', $copy)->with('success', 'Undangan berhasil diduplikat.');
    }

    public function destroy(Invitation $invitation): RedirectResponse
    {
        $this->authorize('delete', $invitation);

        ActivityLog::record('invitation.deleted', "Undangan {$invitation->title} dihapus.", $invitation);
        $this->invitationService->delete($invitation);

        return redirect()->route('invitations.index')->with('success', 'Undangan dihapus.');
    }

    public function preview(Invitation $invitation): RedirectResponse
    {
        $this->authorize('view', $invitation);

        return redirect()->to($invitation->path().'?preview=1');
    }

    public function uploadMusic(StoreMusicRequest $request, Invitation $invitation): RedirectResponse
    {
        $this->authorize('update', $invitation);

        $settings = $invitation->settings;
        if (filled($settings['music_file']) && ! str_starts_with((string) $settings['music_file'], '/audio/')) {
            $this->uploadService->delete($settings['music_file']);
        }

        $settings['music_file'] = $this->uploadService->storeAudio($request->file('music'));
        $settings['music_track'] = 'upload';
        $invitation->update(['settings' => $settings]);

        return back()->with('success', 'Musik latar berhasil diunggah.');
    }

    public function deleteMusic(Invitation $invitation): RedirectResponse
    {
        $this->authorize('update', $invitation);

        $settings = $invitation->settings;
        if (filled($settings['music_file']) && ! str_starts_with((string) $settings['music_file'], '/audio/')) {
            $this->uploadService->delete($settings['music_file']);
        }

        $settings['music_file'] = null;
        $settings['music_track'] = 'none';
        $invitation->update(['settings' => $settings]);

        return back()->with('success', 'Musik latar dihapus.');
    }

    /**
     * @param  array<int, array<string, mixed>>  $hosts
     */
    private function syncHosts(Invitation $invitation, array $hosts): void
    {
        $existing = $invitation->hosts()->get()->values();

        foreach ($hosts as $index => $hostData) {
            $host = $existing->get($index);

            if ($host) {
                $host->update($hostData);
            } else {
                $invitation->hosts()->create($hostData);
            }
        }

        foreach ($existing->slice(count($hosts)) as $obsolete) {
            $this->uploadService->deleteImage($obsolete->photo_path);
            $obsolete->delete();
        }
    }
}
