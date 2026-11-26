<?php

namespace App\Http\Requests;

use App\Enums\GuestGroup;
use App\Models\Guest;
use App\Models\Invitation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateGuestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Guest $guest */
        $guest = $this->route('guest');
        /** @var Invitation $invitation */
        $invitation = $this->route('invitation') ?? $guest->invitation;

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                function (string $attribute, mixed $value, callable $fail) use ($invitation, $guest): void {
                    $query = Guest::where('invitation_id', $invitation->id)
                        ->where('id', '!=', $guest->id)
                        ->whereRaw('LOWER(name) = ?', [Str::lower((string) $value)])
                        ->whereRaw('LOWER(COALESCE(phone, \'\')) = ?', [Str::lower((string) $this->input('phone'))]);

                    if ($query->exists()) {
                        $fail('Tamu dengan nama dan nomor WhatsApp ini sudah terdaftar.');
                    }
                },
            ],
            'group' => ['required', Rule::enum(GuestGroup::class)],
            'phone' => ['nullable', 'string', 'max:30'],
            'pax_quota' => ['required', 'integer', 'min:1', 'max:50'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
