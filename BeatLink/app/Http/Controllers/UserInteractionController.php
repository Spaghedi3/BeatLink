<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserInteraction;
use App\Models\TrackStat;

class UserInteractionController extends Controller
{
    public function store(Request $request)
    {
        // 1) Validate exactly what you need
        $data = $request->validate([
            'beat_id'         => 'required|exists:tracks,id',
            'user_id'         => 'required|exists:users,id',
            'reaction'        => 'nullable|in:love,hate',
            'listen_duration' => 'nullable|integer|min:1',
        ]);

        $beatId         = $data['beat_id'];
        $userId         = $data['user_id'];
        $newReaction    = $data['reaction'] ?? null;
        $listenSeconds  = $data['listen_duration'] ?? 0;

        // 2) Grab or create the user_interaction row
        $ui = UserInteraction::firstOrNew([
            'user_id'  => $userId,
            'beat_id'  => $beatId,
        ]);

        \Log::debug("Before save UI: old reaction={$ui->reaction}, existing listen_duration={$ui->listen_duration}");

        // 3) Update listen_duration
        if ($listenSeconds > 0) {
            $ui->listen_duration = ($ui->listen_duration ?? 0) + $listenSeconds;
        }

        // 4) Update reaction on the model
        if ($newReaction !== null) {
            $ui->reaction = $newReaction;
        }

        \Log::debug("Saving UI: new reaction={$ui->reaction}, new listen_duration={$ui->listen_duration}");

        $ui->save();

        // 5) Ensure a track_stat row exists
        $stat = TrackStat::firstOrCreate(
            ['track_id' => $beatId],
            ['total_listen_seconds' => 0, 'love_count' => 0, 'hate_count' => 0]
        );

        \Log::debug("After firstOrCreate Stat: love={$stat->love_count}, hate={$stat->hate_count}, time={$stat->total_listen_seconds}");

        // 6) Apply listen_time to track_stats
        if ($listenSeconds > 0) {
            $stat->increment('total_listen_seconds', $listenSeconds);
            \Log::debug("After increment time: total_listen_seconds={$stat->total_listen_seconds}");
        }

        // 7) Apply reaction delta logic
        if (array_key_exists('reaction', $data)) {
            \Log::debug("Reaction change: old={$oldReaction}, new={$newReaction}");
            // … your reaction‐count code …
            \Log::debug("After reaction adjustment: love={$stat->love_count}, hate={$stat->hate_count}");
        }

        return response()->json([
            'status'      => 'ok',
            'reaction'    => $newReaction,
            'love_count'  => $stat->love_count,
            'hate_count'  => $stat->hate_count,
        ]);
    }
}
