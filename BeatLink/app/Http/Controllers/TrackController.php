<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTrackRequest;
use App\Http\Requests\UpdateTrackRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Track;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class TrackController extends Controller
{

    function store(StoreTrackRequest $r)
    {
        Track::createFromRequest($r);
        return redirect()->route('tracks.index')->with('success', 'Track uploaded.');
    }

    function update(UpdateTrackRequest $r, Track $track)
    {
        $track->updateFromRequest($r);
        return redirect()
            ->route('tracks.index')
            ->with('success', 'Track updated successfully.');
    }

    function destroy(Track $track)
    {
        $track->deleteWithFiles();
        return redirect()->route('tracks.index')->with('success', 'Deleted.');
    }

    function index(Request $r)
    {
        $tracks = Track::ownedBy(Auth::id())
            ->search($r->search)
            ->filterBpm($r->bpm_range)
            ->filterKey($r->key)
            ->filterScale($r->scale)
            ->filterCategory($r->category)
            ->with(['tags', 'types', 'user', 'reactions'])
            ->get()
            ->each(fn($t) => $t->userReactedWith = optional(
                $t->reactions->firstWhere('user_id', Auth::id())
            )->reaction);

        return view('tracks.index', compact('tracks'));
    }

    public function create()
    {
        return view('tracks.create');
    }

    public function edit(Track $track)
    {
        $this->authorize('update', $track);
        return view('tracks.edit', compact('track'));
    }

    public function userTracks(Request $request, string $username)
    {
        $tracks = Track::listForUser($request, $username);

        $owner = User::where('username', $username)->firstOrFail();

        return view('tracks.user-index', [
            'tracks'    => $tracks,
            'ownerName' => $username,
            'owner'     => $owner,
            'viewer'    => Auth::user(),
        ]);
    }

    function react(Request $r)
    {
        $r->validate([
            'owner_id' => 'required|exists:users,id',
            'track_id' => 'required|exists:tracks,id',
            'reaction' => ['required', Rule::in(['love', 'hate'])],
        ]);
        return response()->json(
            Track::findOrFail($r->track_id)->react($r->reaction)
        );
    }

    public function favorites(Request $request)
    {
        $tracks = Track::favoritesForUser($request);

        return view('tracks.favorites', compact('tracks'));
    }
}
