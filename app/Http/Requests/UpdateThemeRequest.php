<?php

namespace App\Http\Requests;

use App\Services\InvitationThemeResolver;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateThemeRequest extends FormRequest
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
            'theme' => ['required', Rule::in(array_keys(config('themes.list', [])))],
            'active' => ['required', 'boolean'],
        ];
    }
}
