<?php

namespace App\Http\Requests;

use App\Models\Guest;
use Illuminate\Foundation\Http\FormRequest;

class StoreWishRequest extends FormRequest
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
            'message' => ['required', 'string', 'max:300'],
            'website' => ['nullable', 'size:0'],
        ];
    }
}
