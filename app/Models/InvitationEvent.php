<?php

namespace App\Models;

use App\Services\CalendarService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class InvitationEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'invitation_id',
        'name',
        'starts_at',
        'ends_at',
        'timezone',
        'venue',
        'address',
        'lat',
        'lng',
        'maps_url',
        'sort_order',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'lat' => 'float',
        'lng' => 'float',
        'sort_order' => 'integer',
    ];

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function rsvps(): BelongsToMany
    {
        return $this->belongsToMany(Rsvp::class, 'rsvp_events');
    }

    public function getGoogleCalendarUrlAttribute(): string
    {
        return app(CalendarService::class)->googleUrl($this);
    }

    public function getIcsUrlAttribute(): string
    {
        return route('calendar.ics', $this);
    }

    public function dateLabel(): string
    {
        return $this->starts_at?->translatedFormat('l, d F Y') ?? '-';
    }

    public function timeLabel(): string
    {
        if (! $this->starts_at) {
            return '-';
        }

        $start = $this->starts_at->format('H.i');
        $end = $this->ends_at?->format('H.i');

        return $end ? "{$start} - {$end} {$this->timezone}" : "{$start} {$this->timezone}";
    }
}
