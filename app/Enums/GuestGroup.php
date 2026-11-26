<?php

namespace App\Enums;

enum GuestGroup: string
{
    case Keluarga = 'keluarga';
    case Teman = 'teman';
    case RekanKerja = 'rekan_kerja';
    case Vip = 'vip';

    public function label(): string
    {
        return config("undangan.guest_groups.{$this->value}", $this->value);
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $group): array => [$group->value => $group->label()])->all();
    }
}
