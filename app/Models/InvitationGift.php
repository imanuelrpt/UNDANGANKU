<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvitationGift extends Model
{
    use HasFactory;

    protected $fillable = [
        'invitation_id',
        'type',
        'bank_name',
        'account_number',
        'account_name',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function label(): string
    {
        return $this->type === 'ewallet' ? 'E-Wallet' : 'Bank';
    }
}
