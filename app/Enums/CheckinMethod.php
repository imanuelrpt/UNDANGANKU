<?php

namespace App\Enums;

enum CheckinMethod: string
{
    case Scan = 'scan';
    case Manual = 'manual';
    case WalkIn = 'walk_in';

    public function label(): string
    {
        return match ($this) {
            self::Scan => 'Scan QR',
            self::Manual => 'Manual',
            self::WalkIn => 'Tamu Walk-in',
        };
    }
}
