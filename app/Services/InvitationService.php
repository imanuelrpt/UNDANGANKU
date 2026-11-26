<?php

namespace App\Services;

use App\Enums\InvitationStatus;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Str;

class InvitationService
{
    public function __construct(protected UploadService $uploadService)
    {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data, User $user): Invitation
    {
        return Invitation::create(array_merge($data, [
            'user_id' => $user->id,
            'status' => InvitationStatus::Draft,
        ]));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Invitation $invitation, array $data): Invitation
    {
        $invitation->update($data);

        return $invitation->refresh();
    }

    public function duplicate(Invitation $original): Invitation
    {
        $copy = Invitation::create([
            'user_id' => $original->user_id,
            'event_type' => $original->event_type->value,
            'title' => $original->title.' (Salinan)',
            'slug' => $this->uniqueSlug($original->slug.'-copy'),
            'theme' => $original->theme,
            'status' => InvitationStatus::Draft,
            'expires_at' => $original->expires_at,
            'settings' => $original->settings,
        ]);

        foreach ($original->hosts as $host) {
            $copy->hosts()->create($host->only([
                'type', 'name', 'nickname', 'photo_path', 'parent_name',
                'birth_order', 'description', 'sort_order',
            ]));
        }

        foreach ($original->events as $event) {
            $copy->events()->create($event->only([
                'name', 'starts_at', 'ends_at', 'timezone', 'venue',
                'address', 'lat', 'lng', 'maps_url', 'sort_order',
            ]));
        }

        foreach ($original->gifts as $gift) {
            $copy->gifts()->create($gift->only([
                'type', 'bank_name', 'account_number', 'account_name', 'sort_order',
            ]));
        }

        foreach ($original->questions as $question) {
            $copy->questions()->create($question->only(['label', 'is_active']));
        }

        foreach ($original->photos as $photo) {
            $copy->photos()->create([
                'path' => $photo->path,
                'thumbnail_path' => $photo->thumbnail_path,
                'caption' => $photo->caption,
                'sort_order' => $photo->sort_order,
            ]);
        }

        return $copy;
    }

    public function delete(Invitation $invitation): void
    {
        foreach ($invitation->photos as $photo) {
            $this->uploadService->deleteImage($photo->path);
            $this->uploadService->delete($photo->thumbnail_path);
        }

        foreach ($invitation->hosts as $host) {
            $this->uploadService->deleteImage($host->photo_path);
        }

        $music = $invitation->setting('music_file');
        if (filled($music) && ! str_starts_with((string) $music, '/audio/')) {
            $this->uploadService->delete($music);
        }

        $invitation->delete();
    }

    private function uniqueSlug(string $base): string
    {
        $slug = Str::slug($base);

        while (Invitation::where('slug', $slug)->exists()) {
            $slug = Str::slug($base).'-'.Str::lower(Str::random(4));
        }

        return $slug;
    }
}
