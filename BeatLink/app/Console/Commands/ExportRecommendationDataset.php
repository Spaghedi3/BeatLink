<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExportRecommendationDataset extends Command
{
    protected $signature = 'ml:export-hybrid-dataset';
    protected $description = 'Export hybrid ML dataset combining reactions and listen duration';

    public function handle()
    {
        $rows = DB::table('user_interactions')
            ->leftJoin('reactions', function ($join) {
                $join->on('user_interactions.user_id', '=', 'reactions.user_id')
                    ->on('user_interactions.beat_id', '=', 'reactions.track_id');
            })
            ->select(
                'user_interactions.user_id',
                'user_interactions.beat_id as track_id',
                DB::raw("
                COALESCE(
                    CASE
                        WHEN reactions.reaction = 'love' THEN 1
                        WHEN reactions.reaction = 'hate' THEN -1
                        ELSE 0
                    END, 0
                ) + LEAST(user_interactions.listen_duration / 60.0, 1) AS rating
            ")
            )
            ->get();

        $csv = "user_id,track_id,rating\n";
        foreach ($rows as $row) {
            $csv .= "{$row->user_id},{$row->track_id}," . round($row->rating, 2) . "\n";
        }

        $path = storage_path('app/public/hybrid_dataset.csv');
        file_put_contents($path, $csv);

        $this->info("Hybrid dataset exported to: {$path}");
    }
}
