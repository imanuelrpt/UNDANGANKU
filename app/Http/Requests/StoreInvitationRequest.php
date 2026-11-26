<?php

namespace App\Http\Requests;

use App\Enums\EventType;
use App\Services\InvitationThemeResolver;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvitationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (filled($this->slug)) {
            $this->merge(['slug' => strtolower((string) $this->slug)]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'event_type' => ['required', Rule::enum(EventType::class)],
            'slug' => [
                'required',
                'string',
                'max:80',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('invitations', 'slug'),
                Rule::notIn(config('undangan.reserved_slugs', [])),
            ],
            'theme' => ['required', Rule::in(array_keys(app(InvitationThemeResolver::class)->options()))],
            'expires_at' => ['nullable', 'date', 'after:now'],
            'settings' => ['nullable', 'array'],
            'settings.rsvp_deadline' => ['nullable', 'date'],
            'settings.max_extra_guests' => ['nullable', 'integer', 'min:0', 'max:10'],
            'settings.question_label' => ['nullable', 'string', 'max:150'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'slug.regex' => 'Slug hanya boleh berisi huruf kecil, angka, dan tanda hubung.',
            'slug.unique' => 'Slug ini sudah digunakan undangan lain.',
            'slug.not_in' => 'Slug ini dilarang karena merupakan reserved word.',
        ];
    }
}
