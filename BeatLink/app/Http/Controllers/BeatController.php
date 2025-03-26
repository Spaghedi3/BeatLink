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
    public function index()
    {
        // Ensure this route is behind auth, so Auth::id() is always available
        $beats = Beat::where('user_id', Auth::id())->get();

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
            'audio'      => 'required|file|mimes:mp3,wav',
            'picture'    => 'nullable|image',
            'tags'       => 'nullable|string',
            'category'   => 'nullable|string',
            'type_beat'  => 'nullable|string',
            'is_private' => 'sometimes|boolean',
        ]);

        $audioPath = $request->hasFile('audio')
            ? $request->file('audio')->store('beats', 'public')
            : $beat->audio;

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

    public function userBeats($username)
    {
        // 1) Find the user by username
        $user = User::where('username', $username)->firstOrFail();

        // 2) Check if the logged-in user is the same as $user
        if (Auth::check() && Auth::id() === $user->id) {
            // The owner sees all their own beats
            $beats = Beat::where('user_id', $user->id)->get();
        } else {
            // Other people see only the public beats
            $beats = Beat::where('user_id', $user->id)
                ->where('is_private', false)
                ->get();
        }

        // 3) Reuse your existing Blade
        return view('beats.user-index', [
            'beats'     => $beats,
            'ownerName' => $user->username,
        ]);
    }
}
