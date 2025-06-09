<?php
// app/Http/Controllers/RecommendationController.php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Track;
use App\Models\TrackStat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RecommendationController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        $orderedStats = TrackStat::join('tracks', 'track_stats.track_id', '=', 'tracks.id')
            ->where('tracks.user_id', '!=', $userId)
            ->select([
                'track_stats.track_id',
                DB::raw(
                    <<<SQL
    (CASE 
       WHEN tracks.folder_files IS NOT NULL THEN 0.005 
       ELSE 0.01 
     END) * track_stats.total_listen_seconds
     + track_stats.love_count
     - track_stats.hate_count
   AS score
SQL
                )
            ])
            ->orderByDesc('score')
            ->pluck('track_id');

        $allOtherIds = Track::where('user_id', '!=', $userId)
            ->pluck('id');

        $orderedIds = $orderedStats
            ->merge($allOtherIds->diff($orderedStats))
            ->take(100);

        if ($orderedIds->isEmpty()) {
            return view('dashboard', ['tracks' => collect()]);
        }

        $idList = $orderedIds->implode(',');

        $tracks = Track::with([
            'user',
            'reactions' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }
        ])
            ->whereIn('id', $orderedIds)
            ->orderByRaw("FIELD(id, {$idList})")
            ->get();

        $tracks->each(function ($track) {
            $r = $track->reactions->first();
            $track->userReactedWith = $r?->reaction;
        });

        $query = $request->input('search');
        $categories = $request->input('category');

        $tracksQuery = Track::query()
            ->where('user_id', '!=', $userId);

        if ($query) {
            $tracksQuery->where(function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                    ->orWhere('category', 'like', '%' . $query . '%')
                    ->orWhereHas('tags', fn($tagQ) => $tagQ->where('name', 'like', '%' . $query . '%'))
                    ->orWhereHas('types', fn($typeQ) => $typeQ->where('name', 'like', '%' . $query . '%'));
            });
        }

        if ($request->filled('bpm_range')) {
            $input = trim($request->input('bpm_range'));

            if (str_contains($input, '-')) {
                [$min, $max] = array_map('trim', explode('-', $input, 2));

                if (is_numeric($min) && is_numeric($max) && (int)$min <= (int)$max) {
                    $tracksQuery->whereBetween('bpm', [(int)$min, (int)$max]);
                }
            } elseif (is_numeric($input)) {
                $tracksQuery->where('bpm', (int)$input);
            }
        }

        if ($request->filled('key')) {
            $tracksQuery->where('key', $request->input('key'));
        }

        if ($request->filled('scale')) {
            $tracksQuery->where('scale', $request->input('scale'));
        }

        if ($categories && !Auth::user()->is_artist) {
            $tracksQuery->whereIn('category', $categories);
        }

        $tracksQuery->with(['tags', 'types', 'user', 'reactions']);
        $tracks = $tracksQuery->get();


        // $stats = TrackStat::whereIn('track_id', $tracks->pluck('id'))
        //     ->get()
        //     ->keyBy('track_id');

        // foreach ($tracks as $track) {
        //     $stat = $stats->get($track->id, (object)[
        //         'total_listen_seconds' => 0,
        //         'love_count'           => 0,
        //         'hate_count'           => 0,
        //     ]);

        //     $multiplier = $track->folder_files ? 0.005 : 0.01;
        //     $score = $multiplier * $stat->total_listen_seconds
        //         + $stat->love_count
        //         - $stat->hate_count;

        //     Log::debug(sprintf(
        //         "Track %d (%s) — listens=%.0f, loves=%d, hates=%d → score=%.3f",
        //         $track->id,
        //         $track->name,
        //         $stat->total_listen_seconds,
        //         $stat->love_count,
        //         $stat->hate_count,
        //         $score
        //     ));
        // }

        // 4) now return the view
        return view('dashboard', compact('tracks'));
    }

    protected function updateAffinityStats(int $userId): void
    {
        // 1) user_tag_stats
        DB::statement(
            <<<'SQL'
INSERT INTO user_tag_stats (user_id, tag_id, score, created_at, updated_at)
SELECT
  ui.user_id,
  tt.tag_id,
  COALESCE(SUM(ui.listen_duration),0)
  + COALESCE(SUM(
      CASE r.reaction
        WHEN 'love' THEN  1
        WHEN 'hate' THEN -1
        ELSE 0
      END
    ),0) AS score,
  NOW(),
  NOW()
FROM user_interactions AS ui
  JOIN track_tag    AS tt ON ui.beat_id = tt.track_id
  LEFT JOIN reactions AS r
    ON r.user_id  = ui.user_id
   AND r.track_id = ui.beat_id
WHERE ui.user_id = ?
GROUP BY ui.user_id, tt.tag_id
ON DUPLICATE KEY UPDATE
  score      = VALUES(score),
  updated_at = VALUES(updated_at);
SQL,
            [$userId]
        );

        // 2) user_type_stats
        DB::statement(
            <<<'SQL'
INSERT INTO user_type_stats (user_id, type_id, score, created_at, updated_at)
SELECT
  ui.user_id,
  tp.type_id,
  COALESCE(SUM(ui.listen_duration),0)
  + COALESCE(SUM(
      CASE r.reaction
        WHEN 'love' THEN  1
        WHEN 'hate' THEN -1
        ELSE 0
      END
    ),0) AS score,
  NOW(),
  NOW()
FROM user_interactions AS ui
  JOIN track_type   AS tp ON ui.beat_id = tp.track_id
  LEFT JOIN reactions AS r
    ON r.user_id  = ui.user_id
   AND r.track_id = ui.beat_id
WHERE ui.user_id = ?
GROUP BY ui.user_id, tp.type_id
ON DUPLICATE KEY UPDATE
  score      = VALUES(score),
  updated_at = VALUES(updated_at);
SQL,
            [$userId]
        );
    }

    /**
     * Show personalized recommendations based on user_tag_stats / user_type_stats.
     * Falls back to index() if the user has no computed stats yet.
     */

    protected function weightedSampleByAffinity($tracks, array $tagScores, array $typeScores, int $limit)
    {
        // 1) Build up a map track_id => weight
        $weights = [];
        foreach ($tracks as $track) {
            $score = 0;
            foreach ($track->tags as $t) {
                $score += $tagScores[$t->id] ?? 0;
            }
            foreach ($track->types as $ty) {
                $score += $typeScores[$ty->id] ?? 0;
            }
            // +1 so that zero‐score tracks still have a small chance
            $weights[$track->id] = $score + 1;
        }

        // ** LOG EACH TRACK’S WEIGHT **
        foreach ($tracks as $track) {
            $w = $weights[$track->id];
            Log::debug(sprintf(
                "Affinity weight — Track %d (%s): %.3f",
                $track->id,
                $track->name,
                $w
            ));
        }

        // 2) Now draw up to $limit times without replacement
        $selected    = collect();
        $poolTracks  = $tracks->keyBy('id');
        $poolWeights = $weights;

        $draws = min($limit, count($poolWeights));
        for ($i = 0; $i < $draws; $i++) {
            $total = array_sum($poolWeights);
            $r     = lcg_value() * $total;   // float in [0,1)*$total

            foreach ($poolWeights as $id => $w) {
                if ($r <= $w) {
                    $selected->push($poolTracks->get($id));
                    // remove it from the pool
                    unset($poolTracks[$id], $poolWeights[$id]);
                    break;
                }
                $r -= $w;
            }
        }

        return $selected;
    }

    public function recommend(Request $request)
    {
        $userId = Auth::id();

        // Check if ML-based recommendations exist
        $mlRecs = \App\Models\Recommendation::where('user_id', $userId)
            ->orderByDesc('predicted_rating')
            ->pluck('track_id');

        if ($mlRecs->isEmpty()) {
            // Fall back to affinity-based or popularity if cold-start
            return $this->index($request);
        }

        // Retrieve full track data
        $idList = $mlRecs->take(100)->implode(',');
        $query = $request->input('search');
        $categories = $request->input('category');

        $tracksQuery = Track::query()
            ->with(['user', 'tags', 'types', 'reactions' => fn($q) => $q->where('user_id', $userId)])
            ->whereIn('id', $mlRecs);

        if ($query) {
            $tracksQuery->where(function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                    ->orWhere('category', 'like', '%' . $query . '%')
                    ->orWhereHas('tags', fn($tagQ) => $tagQ->where('name', 'like', '%' . $query . '%'))
                    ->orWhereHas('types', fn($typeQ) => $typeQ->where('name', 'like', '%' . $query . '%'));
            });
        }

        if ($request->filled('bpm_range')) {
            $input = trim($request->input('bpm_range'));
            if (str_contains($input, '-')) {
                [$min, $max] = array_map('trim', explode('-', $input, 2));
                if (is_numeric($min) && is_numeric($max) && (int)$min <= (int)$max) {
                    $tracksQuery->whereBetween('bpm', [(int)$min, (int)$max]);
                }
            } elseif (is_numeric($input)) {
                $tracksQuery->where('bpm', (int)$input);
            }
        }

        if ($request->filled('key')) {
            $tracksQuery->where('key', $request->input('key'));
        }

        if ($request->filled('scale')) {
            $tracksQuery->where('scale', $request->input('scale'));
        }

        if ($categories && !Auth::user()->is_artist) {
            $tracksQuery->whereIn('category', $categories);
        }

        // Maintain ML-based ordering
        $filteredTracks = $tracksQuery->get();
        $filteredTracks = $filteredTracks->sortBy(fn($track) => array_search($track->id, $mlRecs->toArray()))->values();

        $filteredTracks->each(function ($track) {
            $track->userReactedWith = $track->reactions->first()?->reaction;
        });

        return view('dashboard', ['tracks' => $filteredTracks]);
    }
}
