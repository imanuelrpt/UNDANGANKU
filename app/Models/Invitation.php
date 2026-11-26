<?php

namespace App\Models;

use App\Enums\EventType;
use App\Enums\InvitationStatus;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Invitation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'event_type',
        'title',
        'slug',
        'theme',
        'status',
        'expires_at',
        'settings',
        'view_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'event_type' => EventType::class,
        'status' => InvitationStatus::class,
        'expires_at' => 'datetime',
        'view_count' => 'integer',
    ];

    public function getSettingsAttribute(mixed $value): array
    {
        $stored = is_array($value) ? $value : (json_decode((string) $value, true) ?: []);

        return array_merge(config('undangan.default_settings', []), $stored);
    }

    public function setSettingsAttribute(mixed $value): void
    {
        $this->attributes['settings'] = json_encode(is_array($value) ? $value : [], JSON_UNESCAPED_UNICODE);
    }

    public function setting(string $key, mixed $default = null): mixed
    {
        return $this->settings[$key] ?? $default;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hosts(): HasMany
    {
        return $this->hasMany(InvitationHost::class)->orderBy('sort_order');
    }

    public function events(): HasMany
    {
        return $this->hasMany(InvitationEvent::class)->orderBy('starts_at');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(InvitationPhoto::class)->orderBy('sort_order');
    }

    public function gifts(): HasMany
    {
        return $this->hasMany(InvitationGift::class)->orderBy('sort_order');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(InvitationQuestion::class)->orderBy('id');
    }

    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class)->latest();
    }

    public function rsvps(): HasMany
    {
        return $this->hasMany(Rsvp::class)->latest();
    }

    public function wishes(): HasMany
    {
        return $this->hasMany(Wish::class)->latest();
    }

    public function checkins(): HasMany
    {
        return $this->hasMany(Checkin::class)->latest();
    }

    public function staffLinks(): HasMany
    {
        return $this->hasMany(CheckinStaffLink::class)->latest();
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class)->latest();
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isPreviewableBy(?User $user): bool
    {
        if ($user === null) {
            return false;
        }

        return $user->isAdmin() || ($user->isOwner() && $user->id === $this->user_id);
    }

    public function path(?Guest $guest = null): string
    {
        return $guest === null
            ? route('invitation.public', $this->slug)
            : route('invitation.public.guest', [$this->slug, $guest->token]);
    }

    public function coverUrl(): ?string
    {
        $cover = $this->setting('cover_image');

        return $cover ? Storage::url($cover) : null;
    }
}
