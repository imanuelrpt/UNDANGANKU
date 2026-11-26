<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CheckinStaffLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'invitation_id',
        'token',
        'pin_hash',
        'label',
        'is_active',
    ];

    protected $hidden = [
        'pin_hash',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $link): void {
            if (! filled($link->token)) {
                $link->token = Str::random(config('undangan.staff_token_length', 32));
            }
        });
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function checkins(): HasMany
    {
        return $this->hasMany(Checkin::class);
    }

    public function hasPin(): bool
    {
        return filled($this->pin_hash);
    }

    public function url(): string
    {
        return route('staff.login', $this->token);
    }
}
