<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Track;
use Illuminate\Http\Request;
use App\Notifications\TrackRemovedByAdmin;
use App\Notifications\TrackRestoredByAdmin;

class TrackController extends Controller
{
    /**
     * Show a paginated list of all tracks in the admin panel.
     */
    public function index(Request $request)
    {
        $query = Track::with('user');
        $query = \App\Models\Track::withTrashed()->with('user');

        if ($request->filled('search')) {
            $q = $request->input('search');
            $query->where('title', 'like', "%{$q}%");
        }

        $tracks = $query->orderBy('created_at', 'desc')->paginate(15);


        return view('admin.tracks.index', compact('tracks'));
    }

    /**
     * Show a single track’s details in the admin panel (including soft-deleted ones).
     */
    public function show($id)
    {
        $track = Track::withTrashed()->with('user', 'reactions')->findOrFail($id);
        return view('admin.tracks.show', compact('track'));
    }

    /**
     * Soft-delete a track and notify the user if applicable.
     */
    public function destroy(Track $track)
    {
        $trackTitle = $track->name; // Must come before delete
        $uploader = $track->user;

        $track->delete();

        if ($uploader && ! $uploader->is_admin) {
            $uploader->notify(new \App\Notifications\TrackRemovedByAdmin(
                $trackTitle ?: 'Untitled Track',
                'Violation of platform rules'
            ));
        }

        return redirect()
            ->route('admin.tracks.index')
            ->with('success', 'Track deleted and user notified.');
    }


    public function restore($id)
    {
        $track = \App\Models\Track::withTrashed()->findOrFail($id);
        $track->restore();

        // Refresh it to reload title from DB if missing
        $track = $track->fresh();

        if ($track->user && ! $track->user->is_admin) {
            $track->user->notify(new \App\Notifications\TrackRestoredByAdmin(
                $track->name ?: 'Untitled Track'
            ));
        }

        return redirect()
            ->route('admin.tracks.index')
            ->with('success', 'Track restored and user notified.');
    }
}
