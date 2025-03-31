<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

use Illuminate\Support\Facades\Auth;

use App\Models\Beat;


class BeatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // BeatController@index
    public function index(Request $request)
    {
        // Get the search query from the request, if provided
        $query = $request->input('search');

        // Start with the logged-in user's beats (both public and private)
        $beatsQuery = Beat::where('user_id', Auth::id());

        // If there's a search query, add filtering for name, tags, category, and type beat
        if ($query) {
            $beatsQuery->where(function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                    ->orWhere('tags', 'like', '%' . $query . '%')
                    ->orWhere('category', 'like', '%' . $query . '%')
                    ->orWhere('type_beat', 'like', '%' . $query . '%');
            });
        }

        $beats = $beatsQuery->get();

        return view('beats.index', compact('beats'));
    }




    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Just return the create form view
        return view('beats.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'audio'      => 'required|file|mimes:mp3,wav',
            'picture'    => 'nullable|image',
            'tags'       => 'nullable|string',
            'category'   => 'nullable|string',
            'type_beat'  => 'nullable|string',
            'is_private' => 'sometimes|boolean',
        ]);

        // Handle file uploads
        $audioPath = $request->hasFile('audio')
            ? $request->file('audio')->store('beats', 'public')
            : null;

        $picturePath = $request->hasFile('picture')
            ? $request->file('picture')->store('beat_pictures', 'public')
            : null;

        // Create the beat record
        Beat::create([
            'user_id'    => $request->user()->id,
            'name'       => $request->name,
            'file_path'  => $audioPath,
            'picture'    => $picturePath,
            'tags'       => $request->tags,
            'category'   => $request->category,
            'type_beat'  => $request->type_beat,
            'is_private' => $request->boolean('is_private'),
        ]);

        return redirect()
            ->route('beats.index')
            ->with('success', 'Beat uploaded successfully.');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Beat $beat)
    {
        $this->authorize('update', $beat);
        return view('beats.edit', compact('beat'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Beat $beat)
    {
        $this->authorize('update', $beat);

        $request->validate([
            'name'       => 'required|string|max:255',
            'audio'      => 'nullable|file|mimes:mp3,wav',
            'picture'    => 'nullable|image',
            'tags'       => 'nullable|string',
            'category'   => 'nullable|string',
            'type_beat'  => 'nullable|string',
            'is_private' => 'sometimes|boolean',
        ]);

        $audioPath = $request->hasFile('audio')
            ? $request->file('audio')->store('beats', 'public')
            : $beat->file_path;

        $picturePath = $request->hasFile('picture')
            ? $request->file('picture')->store('beat_pictures', 'public')
            : $beat->picture;

        $beat->update([
            'name'       => $request->name,
            'file_path'  => $audioPath,
            'picture'    => $picturePath,
            'tags'       => $request->tags,
            'category'   => $request->category,
            'type_beat'  => $request->type_beat,
            'is_private' => $request->boolean('is_private'),
        ]);

        return redirect()
            ->route('beats.index')
            ->with('success', 'Beat updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroyConfirm(Beat $beat)
    {
        // Ensure the user can delete this beat
        $this->authorize('delete', $beat);

        return view('beats.destroy-confirm', compact('beat'));
    }

    public function destroy(Beat $beat)
    {
        $this->authorize('delete', $beat);

        // if ($beat->file_path) {
        //     Storage::delete($beat->file_path);
        // }
        // if ($beat->picture) {
        //     Storage::delete($beat->picture);
        // }

        $beat->delete();

        return redirect()
            ->route('beats.index')
            ->with('success', 'Beat deleted successfully.');
    }

    public function userBeats(Request $request, $username)
    {
        // Find the user by username.
        $user = User::where('username', $username)->firstOrFail();

        // Get the search term from the query string.
        $search = $request->input('search');

        // Start a query for that user's beats.
        $beatsQuery = Beat::where('user_id', $user->id);

        // If the logged-in user is NOT the owner, only show public beats.
        if (!(Auth::check() && Auth::id() === $user->id)) {
            $beatsQuery->where('is_private', false);
        }

        // If there's a search term, add filtering conditions.
        if ($search) {
            $beatsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('tags', 'like', '%' . $search . '%')
                    ->orWhere('category', 'like', '%' . $search . '%')
                    ->orWhere('type_beat', 'like', '%' . $search . '%');
            });
        }

        $beats = $beatsQuery->get();

        return view('beats.user-index', [
            'beats'     => $beats,
            'ownerName' => $user->username,
        ]);
    }
}
