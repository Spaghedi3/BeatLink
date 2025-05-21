<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\TrackStat;

class RebuildTrackStats extends Command
{
    protected $signature   = 'stats:rebuild-tracks';
    protected $description = 'Recompute all track_stats from user_interactions';

    public function handle()
    {
        // Zero-out existing cached stats
        TrackStat::query()->update([
            'total_listen_seconds' => 0,
            'love_count'           => 0,
            'hate_count'           => 0,
        ]);

        // Aggregate raw data from new "reaction" column
        $rows = DB::table('user_interactions')
            ->select(
                'beat_id',
                DB::raw('SUM(listen_duration) as listens'),
                DB::raw("SUM(CASE WHEN reaction = 'love' THEN 1 ELSE 0 END) as loves"),
                DB::raw("SUM(CASE WHEN reaction = 'hate' THEN 1 ELSE 0 END) as hates")
            )
            ->groupBy('beat_id')
            ->get();

        // Apply to track_stats
        foreach ($rows as $r) {
            TrackStat::updateOrCreate(
                ['track_id' => $r->beat_id],
                [
                    'total_listen_seconds' => $r->listens,
                    'love_count'           => $r->loves,
                    'hate_count'           => $r->hates,
                ]
            );
        }

        $this->info('Track stats rebuilt for ' . $rows->count() . ' tracks.');
    }
}
