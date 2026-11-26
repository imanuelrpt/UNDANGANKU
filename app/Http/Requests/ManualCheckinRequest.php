<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ManualCheckinRequest extends FormRequest
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
        return [
            'guest_id' => ['nullable', 'integer', 'exists:guests,id'],
            'name' => ['required_without:guest_id', 'nullable', 'string', 'max:100'],
            'group' => ['nullable', 'string', 'max:20'],
            'pax_actual' => ['required', 'integer', 'min:1', 'max:50'],
            'method' => ['nullable', 'string', 'in:scan,manual,walk_in'],
        ];
    }
}
