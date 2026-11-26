<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class InvitationPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'invitation_id',
        'path',
        'thumbnail_path',
        'caption',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function url(): string
    {
        return Storage::url($this->path);
    }

    public function thumbnailUrl(): string
    {
        return Storage::url($this->thumbnail_path ?: $this->path);
    }
}
