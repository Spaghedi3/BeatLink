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

        $category = fake()->randomElement(['instrumental', 'loopkit', 'drumkit', 'multikit']);

        // Only assign folder_files if NOT instrumental
        $folderFiles = null;
        if (in_array($category, ['drumkit', 'loopkit', 'multikit'])) {
            $folderFiles = [];
            for ($i = 1; $i <= 5; $i++) {
                $folderFiles[] = $destPath;
            }
        }

        return [
            'user_id'      => $user->id,
            'name'         => $trackName,
            'file_path'    => $destPath,
            'category'     => $category,
            'is_private'   => fake()->boolean(30),
            'type'         => 'song',
            'bpm'          => fake()->numberBetween(70, 160),
            'key'          => fake()->randomElement(['C', 'D', 'E', 'F']),
            'scale'        => fake()->randomElement(['major', 'minor']),
            'picture'      => null,
            'folder_files' => $folderFiles, // Will be null for instrumentals
        ];
    }
}
