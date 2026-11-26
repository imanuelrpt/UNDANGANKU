<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePhotoRequest;
use App\Models\ActivityLog;
use App\Models\Invitation;
use App\Models\InvitationHost;
use App\Models\InvitationPhoto;
use App\Services\UploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PhotoController extends Controller
{
    public function __construct(protected UploadService $uploadService)
    {
    }

    public function store(StorePhotoRequest $request, Invitation $invitation): RedirectResponse
    {
        $this->authorize('update', $invitation);

        $remaining = (int) config('undangan.max_photos', 12) - $invitation->photos()->count();
        $files = collect($request->file('photos'))->take(max($remaining, 0));

        foreach ($files as $file) {
            $uploaded = $this->uploadService->storeImage($file, "invitations/{$invitation->id}/photos");

            $invitation->photos()->create([
                'path' => $uploaded['path'],
                'thumbnail_path' => $uploaded['thumbnail_path'],
                'caption' => null,
                'sort_order' => $invitation->photos()->count(),
            ]);
        }

        ActivityLog::record('photo.uploaded', "{$files->count()} foto ditambahkan.", $invitation);

        return back()->with('success', "{$files->count()} foto berhasil diunggah.");
    }

    public function destroy(InvitationPhoto $photo): RedirectResponse
    {
        $this->authorize('update', $photo->invitation);

        $invitation = $photo->invitation;
        $this->uploadService->deleteImage($photo->path);
        $photo->delete();

        ActivityLog::record('photo.deleted', 'Foto galeri dihapus.', $invitation);

        return back()->with('success', 'Foto berhasil dihapus.');
    }

    public function reorder(Request $request, Invitation $invitation): JsonResponse
    {
        $this->authorize('update', $invitation);

        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', Rule::exists('invitation_photos', 'id')],
        ]);

        foreach ($validated['order'] as $index => $photoId) {
            InvitationPhoto::where('id', $photoId)
                ->where('invitation_id', $invitation->id)
                ->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    public function hostPhoto(Request $request, Invitation $invitation, InvitationHost $host): RedirectResponse
    {
        $this->authorize('update', $invitation);

        $validated = $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if ($host->invitation_id !== $invitation->id) {
            abort(404);
        }

        $this->uploadService->deleteImage($host->photo_path);
        $uploaded = $this->uploadService->storeImage($validated['photo'], "invitations/{$invitation->id}/hosts");
        $host->update(['photo_path' => $uploaded['path']]);

        return back()->with('success', 'Foto pembimbing/acara berhasil diperbarui.');
    }
}
