<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'invitation_id',
        'action',
        'description',
        'meta',
        'ip_address',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    public static function record(
        string $action,
        string $description,
        ?Invitation $invitation = null,
        array $meta = [],
        ?User $user = null,
    ): void {
        static::create([
            'user_id' => $user?->id ?? auth()->id(),
            'invitation_id' => $invitation?->id,
            'action' => $action,
            'description' => $description,
            'meta' => $meta,
            'ip_address' => request()->ip(),
        ]);
    }
}
