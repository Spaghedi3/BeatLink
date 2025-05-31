<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class StoreTrackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        $user = $this->user();

        if ($user->is_artist) {
            return [
                'name'       => [
                    'required',
                    'string',
                    'max:255',
                    // unique per user
                    Rule::unique('tracks', 'name')
                        ->where(fn($q) => $q->where('user_id', $user->id))
                ],
                'audio_file' => 'required|file|mimes:mp3,wav',
                'picture'    => 'nullable|image',
                'is_private' => 'sometimes|boolean',
            ];
        }

        return [
            'name'           => [
                'required',
                'string',
                'max:255',
                Rule::unique('tracks', 'name')
                    ->where(fn($q) => $q->where('user_id', $user->id)),
            ],
            'audio_file'     => 'required_if:category,instrumental|file|mimes:mp3,wav',
            'audio_folder'   => 'required_unless:category,instrumental|array',
            'audio_folder.*' => 'file|mimes:mp3,wav',
            'picture'        => 'nullable|image',
            'category'       => 'nullable|string',
            'is_private'     => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'You already have an entry with this name.',
        ];
    }
}
