<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recommendation;
use Illuminate\Support\Facades\Storage;

class RecommendationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = storage_path('app/recommendations.csv');

        if (!file_exists($file)) {
            $this->command->error("recommendations.csv not found.");
            return;
        }

        $rows = array_map('str_getcsv', file($file));
        array_shift($rows); // remove header

        foreach ($rows as $row) {
            [$userId, $trackId, $score] = $row;

            Recommendation::create([
                'user_id' => $userId,
                'track_id' => $trackId,
                'predicted_rating' => $score,
            ]);
        }

        $this->command->info(count($rows) . " recommendations seeded.");
    }
}
