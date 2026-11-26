<?php

namespace App\Models;

use App\Enums\Attendance;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Rsvp extends Model
{
    use HasFactory;

    protected $fillable = [
        'invitation_id',
        'guest_id',
        'name',
        'attendance',
        'pax',
        'answers',
        'note',
        'ip_address',
    ];

    protected $casts = [
        'attendance' => Attendance::class,
        'answers' => 'array',
        'pax' => 'integer',
    ];

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(InvitationEvent::class, 'rsvp_events', 'rsvp_id', 'event_id');
    }

    public function isAttending(): bool
    {
        return $this->attendance === Attendance::Hadir;
    }
}
