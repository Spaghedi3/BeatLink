<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'username' => 'TestUser',
        //     'email' => 'test@example.com',
        // ]);

        \App\Models\User::factory(50)->create();

        $this->call([
            TagSeeder::class,
            TypeSeeder::class,
            TrackSeeder::class,
            InteractionSeeder::class,
            AdminUserSeeder::class,
            ReportSeeder::class,
        ]);
    }
}
