<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Track>
 */
class TrackFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        $user = \App\Models\User::inRandomOrder()->first() ?? \App\Models\User::factory()->create();
        $username = $user->username;
        $trackName = Str::slug(fake()->words(2, true));
        $destPath = "tracks/{$username}/{$trackName}.mp3";

        $source = storage_path('app/public/default/dummy.mp3');
        $destination = storage_path("app/public/{$destPath}");

        if (!file_exists(dirname($destination))) {
            mkdir(dirname($destination), 0755, true);
        }

        if (!file_exists($destination)) {
            copy($source, $destination);
        }

        return [
            'user_id'      => $user->id,
            'name'         => $trackName,
            'file_path'    => $destPath,
            'category'     => fake()->randomElement(['instrumental', 'loopkit', 'drumkit', 'multikit']),
            'is_private'   => fake()->boolean(30),
            'type'         => fake()->randomElement(['lil uzi vert', 'future', 'drake', 'travis scott', 'playboi carti', 'lil baby', 'wu-tang', 'nas', 'j cole', 'kendrick lamar']),
            'tags'         => fake()->randomElement(['dark', 'chill', 'uplifting', 'aggressive', 'melodic', 'trap', 'boom bap', 'lo-fi']),
            'bpm'          => fake()->numberBetween(70, 160),
            'key'          => fake()->randomElement(['C', 'D', 'E', 'F']),
            'scale'        => fake()->randomElement(['major', 'minor']),
            'picture'      => '\app\public\images\default-profile.png',
            'folder_files' => null,
        ];
    }
}
