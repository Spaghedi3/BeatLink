<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Track;

class TrackController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->is_artist) {
            $request->request->remove('category');
        }

        $query = $request->input('search');
        $categories = $request->input('category');

        $tracksQuery = Track::where('user_id', Auth::id());

        if ($query) {
            $tracksQuery->where(function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                    ->orWhere('category', 'like', '%' . $query . '%')
                    ->orWhereHas('tags', fn($tagQ) => $tagQ->where('name', 'like', '%' . $query . '%'))
                    ->orWhereHas('types', fn($typeQ) => $typeQ->where('name', 'like', '%' . $query . '%'));
            });
        }

        if ($categories) {
            $tracksQuery->whereIn('category', $categories);
        }

        $tracksQuery->with(['tags', 'types']);
        $tracks = $tracksQuery->get();

        return view('tracks.index', compact('tracks')); // you can rename view later
    }

    public function create()
    {
        return view('tracks.create'); // you can rename view later
    }

    public function store(Request $request)
    {
        if (Auth::user()->is_artist) {
            $request->request->remove('category');
            $request->request->remove('audio_folder');
        }

        if (Auth::user()->is_artist) {
            $request->validate([
                'name' => 'required|string|max:255|unique:tracks,name,NULL,id,user_id,' . Auth::id(),
                'audio_file' => 'required|file|mimetypes:audio/mpeg,audio/wav',
                'picture' => 'nullable|image',
                'is_private' => 'sometimes|boolean',
            ]);
        } else {
            $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('tracks')->where(fn($q) => $q->where('user_id', Auth::id())),
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
        }

        $audioPath = null;
        $folderFilesJson = null;
        $username = Auth::user()->username;

        if ($request->hasFile('audio_file')) {
            $audioPath = $request->file('audio_file')->store("tracks/{$username}", 'public');
        } elseif ($request->hasFile('audio_folder')) {
            $folderFiles = [];
            $sanitizedName = Str::slug($request->name);
            foreach ($request->file('audio_folder') as $file) {
                $path = str_replace('\\', '/', $file->storeAs("kits/{$username}/{$sanitizedName}", $file->getClientOriginalName(), 'public'));
                $folderFiles[] = $path;
            }
            $audioPath = 'kits/' . $request->name;
            $folderFilesJson = json_encode($folderFiles);
        }

        $picturePath = $request->hasFile('picture')
            ? $request->file('picture')->store('track_pictures', 'public')
            : null;

        $track = Track::create([
            'user_id'       => $request->user()->id,
            'name'          => $request->name,
            'file_path'     => $audioPath,
            'picture'       => $picturePath,
            'category'      => $request->category,
            'is_private'    => $request->boolean('is_private'),
            'folder_files'  => $folderFilesJson ?? null,
            'type'          => Auth::user()->is_artist ? 'song' : 'beat',
        ]);
        $this->attachTagsToTrack($track, $request->tags);
        $this->attachTypesToTrack($track, $request->types);

        return redirect()->route('tracks.index')->with('success', 'Track uploaded successfully.');
    }

    public function edit(Track $track)
    {
        $this->authorize('update', $track);
        return view('tracks.edit', compact('track')); // update view name later
    }

    public function update(Request $request, Track $track)
    {
        if (Auth::user()->is_artist) {
            $request->request->remove('category');
            $request->request->remove('audio_folder');
        }

        $this->authorize('update', $track);
        $folderFilesJson = $track->folder_files;

        if (Auth::user()->is_artist) {
            $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('tracks')->where(fn($q) => $q->where('user_id', Auth::id()))->ignore($track->id),
                ],
                'audio_file' => 'nullable|file|mimetypes:audio/mpeg,audio/wav',
                'picture' => 'nullable|image',
                'is_private' => 'sometimes|boolean',
            ]);
        } else {
            $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('tracks')->where(fn($q) => $q->where('user_id', Auth::id()))->ignore($track->id),
                ],
                'audio_file' => 'nullable|file|mimetypes:audio/mpeg,audio/wav',
                'picture'    => 'nullable|image',
                'category'   => 'nullable|string',
                'is_private' => 'sometimes|boolean',
            ], [
                'name.unique' => 'You already have an entry with this name.',
            ]);
        }

        $username = Auth::user()->username;

        if ($request->hasFile('audio_file')) {
            $audioPath = $request->file('audio_file')->store("tracks/{$username}", 'public');
        } else {
            $audioPath = $track->file_path;
        }

        if ($request->hasFile('audio_folder')) {
            $folderFiles = [];
            $sanitizedName = Str::slug($request->name);
            foreach ($request->file('audio_folder') as $file) {
                $path = str_replace('\\', '/', $file->storeAs("kits/{$username}/{$sanitizedName}", $file->getClientOriginalName(), 'public'));
                $folderFiles[] = $path;
            }
            $folderFilesJson = json_encode($folderFiles);
        }

        $picturePath = $request->hasFile('picture')
            ? $request->file('picture')->store('track_pictures', 'public')
            : $track->picture;

        $track->update([
            'name'       => $request->name,
            'file_path'  => $audioPath,
            'picture'    => $picturePath,
            'category'   => $request->category,
            'is_private' => $request->boolean('is_private'),
            'folder_files'  => $folderFilesJson,
            'type' => Auth::user()->is_artist ? 'song' : 'beat',
        ]);

        $this->attachTagsToTrack($track, $request->tags);
        $this->attachTypesToTrack($track, $request->types);

        return redirect()->route('tracks.index')->with('success', 'Updated successfully.');
    }

    public function destroyConfirm(Track $track)
    {
        $this->authorize('delete', $track);
        return view('tracks.destroy-confirm', compact('track')); // update view name later
    }

    public function destroy(Track $track)
    {
        $this->authorize('delete', $track);

        if ($track->file_path) {
            Storage::delete('public/' . $track->file_path);
        }

        if ($track->picture) {
            Storage::delete('public/' . $track->picture);
        }

        if ($track->folder_files) {
            $files = json_decode($track->folder_files, true);
            foreach ($files as $file) {
                Storage::delete('public/' . $file);
            }
        }

        $track->delete();

        return redirect()->route('tracks.index')->with('success', 'Track deleted successfully.');
    }

    public function userTracks(Request $request, $username)
    {
        $user = User::where('username', $username)->firstOrFail();

        $search = $request->input('search');
        $categories = $request->input('category');

        $tracksQuery = Track::where('user_id', $user->id);

        if (!(Auth::check() && Auth::id() === $user->id)) {
            $tracksQuery->where('is_private', false);
        }

        if ($search) {
            $tracksQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('category', 'like', '%' . $search . '%')
                    ->orWhereHas('types', fn($typeQ) => $typeQ->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('tags', fn($tagQ) => $tagQ->where('name', 'like', '%' . $search . '%'));
            });
        }

        if ($categories) {
            $tracksQuery->whereIn('category', $categories);
        }

        $tracksQuery->with(['tags', 'types']);
        $tracks = $tracksQuery->get();

        return view('tracks.user-index', [
            'tracks'     => $tracks,
            'ownerName'  => $user->username,
            'owner'      => $user,            // track owner (the profile being viewed)
            'viewer'     => Auth::user(),     // currently logged in user
        ]);
    }

    public function checkName(Request $request)
    {
        $exists = Track::where('user_id', Auth::id())
            ->where('name', $request->query('name'))
            ->when($request->has('except_id'), fn($q) => $q->where('id', '!=', $request->query('except_id')))
            ->exists();

        return response()->json(['exists' => $exists]);
    }

    private function attachTagsToTrack(Track $track, ?string $rawTags): void
    {
        if (!$rawTags) return;

        $tags = collect(explode(',', $rawTags))
            ->map(fn($tag) => trim(strtolower($tag)))
            ->filter()
            ->unique();

        $tagIds = $tags->map(fn($tagName) => \App\Models\Tag::firstOrCreate(['name' => $tagName])->id);

        $track->tags()->sync($tagIds);
    }

    private function attachTypesToTrack(Track $track, ?string $rawTypes): void
    {
        if (!$rawTypes) return;

        $types = collect(explode(',', $rawTypes))
            ->map(fn($type) => trim(strtolower($type)))
            ->filter()
            ->unique();

        $typeIds = $types->map(fn($typeName) => \App\Models\Type::firstOrCreate(['name' => $typeName])->id);

        $track->types()->sync($typeIds);
    }
}
