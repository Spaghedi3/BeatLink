<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Beat;


class BeatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $beats = Beat::all();
        return view('beats.index', compact('beats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('beats.create');
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'audio'     => 'required|file|mimes:mp3,wav',
            'picture'   => 'nullable|image',
            'tags'      => 'nullable|string',
            'category'  => 'nullable|string',
            'type_beat' => 'nullable|string',
        ]);

        // Handle file upload for the audio file
        if ($request->hasFile('audio')) {
            $audioPath = $request->file('audio')->store('public/beats');
        }

        // handle picture upload
        if ($request->hasFile('picture')) {
            $picturePath = $request->file('picture')->store('public/beat_pictures');
        } else {
            $picturePath = null;
        }

        // (created_at)
        Beat::create([
            'user_id'   => auth()->id,
            'name'      => $request->name,
            'file_path' => $audioPath,
            'picture'   => $picturePath,
            'tags'      => $request->tags,
            'category'  => $request->category,
            'type_beat' => $request->type_beat,
        ]);

        return redirect()->route('beats.index')->with('success', 'Beat uploaded successfully.');
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
