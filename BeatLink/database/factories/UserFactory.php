<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('parolamea'),
            'remember_token' => Str::random(10),
            'is_admin' => false,
            'is_artist' => fake()->boolean(30), // ~30% of users are artists
            'phone' => fake()->optional()->phoneNumber(),
            'social_links' => [
                'facebook'  => fake()->optional()->url(),
                'instagram' => fake()->optional()->url(),
                'tiktok'    => fake()->optional()->url(),
                'twitter'   => fake()->optional()->url(),
                'beatstars' => fake()->optional()->url(),
            ],
        ];
    }


    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
