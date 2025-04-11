<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Str;

use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Auth;

use Illuminate\Validation\Rule;

use App\Models\User;

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
        $categories = $request->input('category');

        // Start with the logged-in user's beats (both public and private)
        $beatsQuery = Beat::where('user_id', Auth::id());

        // If there's a search query, add filtering for name, tags, category, and type beat
        if ($query) {
            $beatsQuery->where(function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                    ->orWhere('category', 'like', '%' . $query . '%')
                    ->orWhereHas('tags', function ($tagQ) use ($query) {
                        $tagQ->where('name', 'like', '%' . $query . '%');
                    })
                    ->orWhereHas('types', function ($typeQ) use ($query) {
                        $typeQ->where('name', 'like', '%' . $query . '%');
                    });
            });
        }
        if ($categories) {
            $beatsQuery->whereIn('category', $categories);
        }
        $beatsQuery->with(['tags', 'types']);
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
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('beats')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                }),
            ],
            'audio_file' => 'required_if:category,instrumental|file|mimetypes:audio/mpeg,audio/wav',
            'audio_folder' => 'required_unless:category,instrumental|array',
            'audio_folder.*' => 'file|mimetypes:audio/mpeg,audio/wav',
            'picture'    => 'nullable|image',
            'category'   => 'nullable|string',
            'is_private' => 'sometimes|boolean',
        ], [
            'name.unique' => 'You already have an entry with this name.',
        ]);

        // Handle file uploads
        $audioPath = null;
        $folderFilesJson = null;
        $username = Auth::user()->username;

        if ($request->category === 'instrumental' && $request->hasFile('audio_file')) {
            $originalName = $request->file('audio_file')->getClientOriginalName();
            $audioPath = $request->file('audio_file')->store("beats/{$username}", 'public');
        } elseif ($request->hasFile('audio_folder')) {
            $folderFiles = [];
            $sanitizedName = Str::slug($request->name);
            foreach ($request->file('audio_folder') as $file) {
                $originalName = $file->getClientOriginalName();
                $path = str_replace('\\', '/', $file->storeAs("kits/{$username}/{$sanitizedName}", $originalName, 'public'));
                $folderFiles[] = $path;
            }

            // Save all file paths as JSON
            $audioPath = 'kits/' . $request->name; // still optional if you want
            $folderFilesJson = json_encode($folderFiles);
        }

        $picturePath = $request->hasFile('picture')
            ? $request->file('picture')->store('beat_pictures', 'public')
            : null;

        // Create the beat record
        $beat = Beat::create([
            'user_id'       => $request->user()->id,
            'name'          => $request->name,
            'file_path'     => $audioPath,
            'picture'       => $picturePath,
            'category'      => $request->category,
            'is_private'    => $request->boolean('is_private'),
            'folder_files'  => $folderFilesJson ?? null,
        ]);

        $this->attachTagsToBeat($beat, $request->tags);
        $this->attachTypesToBeat($beat, $request->types);


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
        $folderFilesJson = $beat->folder_files; // fallback to old JSON if nothing new

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('beats')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                })->ignore($beat->id),
            ],
            'audio'      => 'nullable|file|mimes:mp3,wav',
            'picture'    => 'nullable|image',
            'category'   => 'nullable|string',
            'is_private' => 'sometimes|boolean',

        ], [
            'name.unique' => 'You already have an entry with this name.',
        ]);

        $username = Auth::user()->username;

        if ($request->hasFile('audio_file')) {
            $audioPath = $request->file('audio_file')->store("beats/{$username}", 'public');
        } else {
            $audioPath = $beat->file_path;
        }

        if ($request->hasFile('audio_folder')) {
            $folderFiles = [];
            $sanitizedName = Str::slug($request->name);
            foreach ($request->file('audio_folder') as $file) {
                $originalName = $file->getClientOriginalName();
                $path = str_replace('\\', '/', $file->storeAs("kits/{$username}/{$sanitizedName}", $originalName, 'public'));
                $folderFiles[] = $path;
            }

            $folderFilesJson = json_encode($folderFiles);
        }

        $picturePath = $request->hasFile('picture')
            ? $request->file('picture')->store('beat_pictures', 'public')
            : $beat->picture;

        $beat->update([
            'name'       => $request->name,
            'file_path'  => $audioPath,
            'picture'    => $picturePath,
            'category'   => $request->category,
            'is_private' => $request->boolean('is_private'),
            'folder_files'  => $folderFilesJson,
        ]);

        $this->attachTagsToBeat($beat, $request->tags);
        $this->attachTypesToBeat($beat, $request->types);


        return redirect()
            ->route('beats.index')
            ->with('success', 'Updated successfully.');
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

        if ($beat->file_path) {
            Storage::delete('public/' . $beat->file_path);
        }
        if ($beat->picture) {
            Storage::delete('public/' . $beat->picture);
        }

        if ($beat->folder_files) {
            $files = json_decode($beat->folder_files, true);
            foreach ($files as $file) {
                Storage::delete('public/' . $file);
            }
        }

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
        $categories = $request->input('category');

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
                    ->orWhere('category', 'like', '%' . $search . '%')
                    ->orWhereHas('types', function ($typeQ) use ($search) {
                        $typeQ->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('tags', function ($tagQ) use ($search) {
                        $tagQ->where('name', 'like', '%' . $search . '%');
                    });
            });
        }
        if ($categories) {
            $beatsQuery->whereIn('category', $categories);
        }
        $beatsQuery->with(['tags', 'types']);
        $beats = $beatsQuery->get();


        return view('beats.user-index', [
            'beats'     => $beats,
            'ownerName' => $user->username,
        ]);
    }

    public function checkName(Request $request)
    {
        $query = Beat::where('user_id', Auth::id())
            ->where('name', $request->query('name'));

        if ($request->has('except_id')) {
            $query->where('id', '!=', $request->query('except_id'));
        }

        $exists = $query->exists();

        return response()->json(['exists' => $exists]);
    }

    private function attachTagsToBeat(Beat $beat, ?string $rawTags): void
    {
        if (!$rawTags) return;

        $tags = collect(explode(',', $rawTags))
            ->map(fn($tag) => trim(strtolower($tag)))
            ->filter()
            ->unique();

        $tagIds = $tags->map(function ($tagName) {
            return \App\Models\Tag::firstOrCreate(['name' => $tagName])->id;
        });

        $beat->tags()->sync($tagIds);
    }

    private function attachTypesToBeat(Beat $beat, ?string $rawTypes): void
    {
        if (!$rawTypes) return;

        $types = collect(explode(',', $rawTypes))
            ->map(fn($type) => trim(strtolower($type)))
            ->filter()
            ->unique();

        $typeIds = $types->map(function ($typeName) {
            return \App\Models\Type::firstOrCreate(['name' => $typeName])->id;
        });

        $beat->types()->sync($typeIds);
    }
}
