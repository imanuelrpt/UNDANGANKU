<?php

namespace App\Models;

use App\Enums\GuestGroup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'invitation_id',
        'name',
        'group',
        'phone',
        'pax_quota',
        'token',
        'sent_at',
        'opened_at',
        'open_count',
        'notes',
    ];

    protected $hidden = [
        'token',
    ];

    protected $casts = [
        'group' => GuestGroup::class,
        'pax_quota' => 'integer',
        'open_count' => 'integer',
        'sent_at' => 'datetime',
        'opened_at' => 'datetime',
    ];

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function rsvp(): HasOne
    {
        return $this->hasOne(Rsvp::class)->latest();
    }

    public function wishes(): HasOne
    {
        return $this->hasOne(Wish::class)->latest();
    }

    public function checkin(): HasOne
    {
        return $this->hasOne(Checkin::class)->latest();
    }

    public function personalPath(): string
    {
        return route('invitation.public.guest', [$this->invitation->slug, $this->token]);
    }

    public function markOpened(): void
    {
        $this->increment('open_count');
        $this->forceFill(['opened_at' => now()])->save();
    }
}
