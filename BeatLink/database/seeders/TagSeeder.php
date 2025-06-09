<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $tags = ['chill', 'trap', 'lofi', 'ambient', 'happy', 'sad', 'dark', 'upbeat', 'slow', 'jazzy'];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(['name' => strtolower($tag)]);
        }
    }
}
