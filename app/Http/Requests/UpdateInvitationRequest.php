<?php

namespace App\Http\Requests;

use App\Models\Invitation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInvitationRequest extends FormRequest
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
        /** @var Invitation|null $invitation */
        $invitation = $this->route('invitation');

        return [
            'title' => ['sometimes', 'required', 'string', 'max:150'],
            'event_type' => ['sometimes', 'required', 'string'],
            'slug' => [
                'sometimes',
                'required',
                'string',
                'max:80',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('invitations', 'slug')->ignore($invitation?->id),
                Rule::notIn(config('undangan.reserved_slugs', [])),
            ],
            'theme' => ['sometimes', 'required', 'string'],
            'status' => ['sometimes', 'required', 'string', Rule::in(['draft', 'published', 'archived'])],
            'expires_at' => ['nullable', 'date'],
            'settings' => ['nullable', 'array'],
            'settings.story' => ['nullable', 'string', 'max:5000'],
            'settings.quote' => ['nullable', 'string', 'max:500'],
            'settings.dress_code' => ['nullable', 'string', 'max:200'],
            'settings.cover_message' => ['nullable', 'string', 'max:200'],
            'settings.youtube_url' => ['nullable', 'string', 'max:255'],
            'settings.music_track' => ['nullable', 'string', 'max:50'],
            'settings.rsvp_enabled' => ['nullable', 'boolean'],
            'settings.rsvp_deadline' => ['nullable', 'date'],
            'settings.max_extra_guests' => ['nullable', 'integer', 'min:0', 'max:10'],
            'settings.question_label' => ['nullable', 'string', 'max:150'],
            'settings.gift_address' => ['nullable', 'string', 'max:500'],
            'settings.show_wishes' => ['nullable', 'boolean'],
            'settings.whatsapp_template' => ['nullable', 'string', 'max:1000'],
            'hosts' => ['nullable', 'array', 'max:4'],
            'hosts.*.type' => ['required_with:hosts', 'string', 'in:bride,groom,host'],
            'hosts.*.name' => ['required_with:hosts', 'string', 'max:100'],
            'hosts.*.nickname' => ['nullable', 'string', 'max:50'],
            'hosts.*.parent_name' => ['nullable', 'string', 'max:150'],
            'hosts.*.birth_order' => ['nullable', 'string', 'max:50'],
            'hosts.*.description' => ['nullable', 'string', 'max:500'],
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
