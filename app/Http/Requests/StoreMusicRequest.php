<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMusicRequest extends FormRequest
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
            'music' => ['required', 'file', 'mimetypes:audio/mpeg,audio/mp3', 'max:5120'],
        ];
    }
}
