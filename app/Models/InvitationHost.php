<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class InvitationHost extends Model
{
    use HasFactory;

    protected $fillable = [
        'invitation_id',
        'type',
        'name',
        'nickname',
        'photo_path',
        'parent_name',
        'birth_order',
        'description',
        'sort_order',
    ];

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function photoUrl(): ?string
    {
        return $this->photo_path ? Storage::url($this->photo_path) : null;
    }
}
