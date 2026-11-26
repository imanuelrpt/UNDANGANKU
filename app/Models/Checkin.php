<?php

namespace App\Models;

use App\Enums\CheckinMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Checkin extends Model
{
    use HasFactory;

    protected $fillable = [
        'invitation_id',
        'guest_id',
        'name',
        'group',
        'pax_actual',
        'method',
        'device_info',
        'staff_link_id',
        'checked_in_at',
    ];

    protected $casts = [
        'method' => CheckinMethod::class,
        'pax_actual' => 'integer',
        'checked_in_at' => 'datetime',
    ];

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function staffLink(): BelongsTo
    {
        return $this->belongsTo(CheckinStaffLink::class);
    }
}
