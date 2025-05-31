<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Track;

class UpdateTrackRequest extends FormRequest
{
    public function authorize(): bool
    {
        // ensures the user is allowed to update this specific track
        $track = $this->route('track');
        return $track && $this->user()->can('update', $track);
    }

    public function rules(): array
    {
        $user  = $this->user();
        $track = $this->route('track');

        if ($user->is_artist) {
            return [
                'name'       => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('tracks', 'name')
                        ->where(fn($q) => $q->where('user_id', $user->id))
                        ->ignore($track->id),
                ],
                'audio_file' => 'nullable|file|mimes:mp3,wav',
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
                    ->where(fn($q) => $q->where('user_id', $user->id))
                    ->ignore($track->id),
            ],
            'audio_file'     => 'nullable|file|mimes:mp3,wav',
            'audio_folder'   => 'nullable|array',
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
