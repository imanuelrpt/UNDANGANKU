<?php

namespace App\Enums;

enum Attendance: string
{
    case Hadir = 'hadir';
    case TidakHadir = 'tidak_hadir';
    case Ragu = 'ragu';

    public function label(): string
    {
        return match ($this) {
            self::Hadir => 'Hadir',
            self::TidakHadir => 'Tidak Hadir',
            self::Ragu => 'Masih Ragu',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Hadir => 'bi-check-circle-fill',
            self::TidakHadir => 'bi-x-circle-fill',
            self::Ragu => 'bi-question-circle-fill',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Hadir => 'bg-success',
            self::TidakHadir => 'bg-danger',
            self::Ragu => 'bg-warning text-dark',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $attendance): array => [$attendance->value => $attendance->label()])->all();
    }
}
