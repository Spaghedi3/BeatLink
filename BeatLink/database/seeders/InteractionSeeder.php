<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Track;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InteractionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run()
    {
        $users = User::all();
        $tracks = Track::all();

        foreach ($users as $user) {
            foreach ($tracks->random(30) as $track) {
                DB::table('user_interactions')->insert([
                    'user_id' => $user->id,
                    'beat_id' => $track->id,
                    'listen_duration' => rand(10, 60),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                if (rand(0, 100) < 70) {
                    // Check if this reaction already exists
                    $exists = DB::table('reactions')
                        ->where('owner_id', $track->user_id)
                        ->where('user_id', $user->id)
                        ->where('track_id', $track->id)
                        ->exists();

                    if (!$exists) {
                        DB::table('reactions')->insert([
                            'owner_id' => $track->user_id,
                            'user_id' => $user->id,
                            'track_id' => $track->id,
                            'reaction' => rand(0, 1) ? 'love' : 'hate',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }
}
