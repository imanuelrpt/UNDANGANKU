<?php

namespace App\Services;

use App\Models\Guest;

class QrTicketService
{
    /**
     * Payload hanya berisi identifier, bukan data pribadi tamu.
     */
    public function sign(Guest $guest): string
    {
        $payload = $this->encode([
            'i' => $guest->invitation_id,
            't' => $guest->token,
            'v' => 1,
        ]);

        return $payload.'.'.$this->signature($payload);
    }

    /**
     * @return array{i: int, t: string}|null
     */
    public function verify(string $signed): ?array
    {
        if (! str_contains($signed, '.')) {
            return null;
        }

        [$payload, $signature] = explode('.', $signed, 2);

        if (! hash_equals($this->signature($payload), $signature)) {
            return null;
        }

        $decoded = json_decode($this->decode($payload), true);

        if (! is_array($decoded) || ! isset($decoded['i'], $decoded['t'])) {
            return null;
        }

        return ['i' => (int) $decoded['i'], 't' => (string) $decoded['t']];
    }

    private function encode(array $data): string
    {
        return rtrim(strtr(base64_encode(json_encode($data, JSON_THROW_ON_ERROR)), '+/', '-_'), '=');
    }

    private function decode(string $payload): string
    {
        $remainder = strlen($payload) % 4;
        $padding = $remainder ? str_repeat('=', 4 - $remainder) : '';

        return base64_decode(strtr($payload.$padding, '-_', '+/')) ?: '';
    }

    private function signature(string $payload): string
    {
        return hash_hmac('sha256', $payload, (string) config('app.key'));
    }
}
