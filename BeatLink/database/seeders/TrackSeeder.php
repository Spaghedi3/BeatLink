<?php

namespace Database\Seeders;

use App\Models\Track;
use App\Models\Tag;
use App\Models\Type;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TrackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run()
    {
        $tags = Tag::pluck('id');
        $types = Type::pluck('id');
        $users = User::all();

        Track::factory(100)->create()->each(function ($track) use ($tags, $types) {
            $track->tags()->sync($tags->random(rand(2, 4)));
            $track->types()->sync($types->random(rand(1, 2)));
        });
    }
}
