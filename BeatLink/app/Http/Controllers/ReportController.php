<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\User;
use App\Models\Track;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function createTrack(Track $track)
    {
        if ($track->user_id === Auth::id()) abort(403);
        return view('reports.create-track', compact('track'));
    }

    public function createUser($id)
    {
        $user = User::findOrFail($id);
        if ($user->id === Auth::id()) abort(403);
        return view('reports.create-user', compact('user'));
    }

    public function storeTrack(Request $request, Track $track)
    {
        if ($track->user_id === Auth::id()) abort(403);

        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $existing = Report::where('user_id', Auth::id())
            ->where('reportable_type', Track::class)
            ->where('reportable_id', $track->id)
            ->first();

        if ($existing) {
            return back()->withErrors(['You have already reported this track.']);
        }

        Report::create([
            'user_id' => Auth::id(),
            'reportable_type' => Track::class,
            'reportable_id' => $track->id,
            'type' => 'track',
            'reason' => $request->reason,
            'status' => 'open',
        ]);

        return redirect()->route('dashboard')->with('success', 'Track reported successfully.');
    }

    public function storeUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if ($user->id === Auth::id()) abort(403);

        $request->validate([
            'preset_reason' => 'nullable|string|max:1000',
            'custom_reason' => 'nullable|string|max:1000',
        ]);

        $reason = $request->input('custom_reason') ?: $request->input('preset_reason');

        if (!$reason) {
            return back()->withErrors(['reason' => 'Please provide a reason for the report.']);
        }

        $existing = Report::where('user_id', Auth::id())
            ->where('reportable_type', User::class)
            ->where('reportable_id', $user->id)
            ->first();

        if ($existing) {
            return back()->withErrors(['You have already reported this user.']);
        }

        Report::create([
            'user_id' => Auth::id(),
            'reportable_type' => User::class,
            'reportable_id' => $user->id,
            'type' => 'user',
            'reason' => $reason,
            'status' => 'open',
        ]);

        return redirect()->route('dashboard')->with('success', 'User reported successfully.');
    }
}
