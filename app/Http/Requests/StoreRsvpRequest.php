<?php

namespace App\Http\Requests;

use App\Enums\Attendance;
use App\Models\Guest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRsvpRequest extends FormRequest
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
        $hasGuest = Guest::where('token', $this->route('token') ?? $this->input('token'))->exists();

        return [
            'name' => [$hasGuest ? 'nullable' : 'required', 'string', 'max:100'],
            'attendance' => ['required', Rule::enum(Attendance::class)],
            'pax' => ['required', 'integer', 'min:1', 'max:20'],
            'event_ids' => ['nullable', 'array'],
            'event_ids.*' => ['integer'],
            'answers' => ['nullable', 'array'],
            'answers.*' => ['nullable', 'string', 'max:300'],
            'note' => ['nullable', 'string', 'max:500'],
            'website' => ['nullable', 'size:0'],
        ];
    }
}
