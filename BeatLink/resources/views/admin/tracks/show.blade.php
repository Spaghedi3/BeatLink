{{-- resources/views/admin/tracks/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Track: ' . $track->title)
@section('page-heading', 'Track Details')

@section('content')

<body class="bg-gray-900 text-gray-100 font-sans antialiased">
    <h2 class="text-2xl font-semibold mb-4">{{ $track->title }} (ID: {{ $track->id }})</h2>

    <div class="mb-4 space-y-2">
        <div>
            <strong>Uploaded By:</strong>
            {{-- Link back to that user’s admin page --}}
            @if($track->user)
            <a href="{{ route('admin.users.show', $track->user) }}" class="text-blue-600 hover:underline">
                Username {{ $track->user->username }}
            </a>
            @else
            <span class="text-gray-400 italic">Deleted user</span>
            @endif


        </div>
        <div><strong>Uploaded At:</strong> {{ $track->created_at->toDayDateTimeString() }}</div>
        @if(isset($track->bpm))
        <div><strong>BPM:</strong> {{ $track->bpm }}</div>
        @endif
        @if(isset($track->key))
        <div><strong>Key:</strong> {{ $track->key }}</div>
        @endif
        @if(isset($track->scale))
        <div><strong>Scale:</strong> {{ $track->scale }}</div>
        @endif
        @if(isset($track->category))
        <div><strong>Category:</strong> {{ ucfirst($track->category) }}</div>
        @endif
    </div>

    @if($track->description)
    <div class="mb-4">
        <strong>Description:</strong>
        <p class="mt-1">{{ $track->description }}</p>
    </div>
    @endif

    {{-- Track Preview --}}
    @if($track->file_path && !in_array($track->category, ['loopkit', 'drumkit', 'multikit']))
    <div class="mb-6">
        <strong>Audio Preview:</strong>
        <div class="relative mt-2">
            <audio controls class="w-full rounded shadow" id="track-preview">
                <source src="{{ Storage::url($track->file_path) }}" type="audio/mpeg">
                Your browser does not support the audio element.
            </audio>
        </div>
    </div>
    @endif

    {{-- Folder preview (for multikit/loopkit/drumkit) --}}
    @if($track->folder_files)
    <div class="mb-6">
        <strong>Preview Contents:</strong>
        <ul class="mt-2 space-y-2">
            @foreach($track->folder_files as $file)
            <li class="flex items-center gap-3">
                <button type="button"
                    onclick="toggleHoverPlay(this, '{{ Storage::url($file) }}')"
                    class="text-white px-3 py-1 rounded-full text-sm flex items-center justify-center w-8 h-8">
                    <span class="play-icon block">▶</span>
                    <span class="pause-icon hidden">⏸</span>
                </button>

                <span class="truncate">{{ basename($file) }}</span>
            </li>
            @endforeach
        </ul>
        <audio id="hover-audio-player" class="hidden"></audio>
    </div>

    {{-- Hidden audio player --}}
    <audio id="hover-audio-player" class="hidden"></audio>
    @endif



    @if($track->reactions->count())
    <div class="mb-6">
        <strong>Reactions ({{ $track->reactions->count() }}):</strong>
        <ul class="list-disc list-inside mt-2">
            @foreach($track->reactions as $react)
            <li>User ID {{ $react->user_id }} &ndash; {{ ucfirst($react->reaction) }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Moderation Actions --}}
    <h2 class="text-lg font-semibold">Moderation Actions</h2>
    <div class="flex space-x-4 mt-6">
        {{-- Delete Track --}}
        <form action="{{ route('admin.tracks.destroy', $track) }}" method="POST"
            onsubmit="return confirm('Are you sure you want to permanently delete this track?');">
            @csrf
            @method('DELETE')
            <button class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded w-full h-full">
                Permanently Delete Track
            </button>
        </form>

        <div>
            <a href="{{ route('admin.reports.index') }}"
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded font-medium h-full inline-flex items-center justify-center">
                Back to Reports
            </a>
        </div>
    </div>
    </div>
    @endsection