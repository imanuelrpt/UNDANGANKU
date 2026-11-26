<?php

namespace App\Enums;

enum EventType: string
{
    case Wedding = 'wedding';
    case Birthday = 'birthday';
    case Aqiqah = 'aqiqah';
    case Reunion = 'reunion';
    case General = 'general';

    public function label(): string
    {
        return config("undangan.event_types.{$this->value}", $this->value);
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $type): array => [$type->value => $type->label()])->all();
    }

    public function isWedding(): bool
    {
        return $this === self::Wedding;
    }
}
