{{-- resources/views/admin/users/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'User: ' . $user->name)
@section('page-heading', 'User Details')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-semibold mb-4">{{ $user->name }} (ID: {{ $user->id }})</h2>

    <div class="mb-4 space-y-2">
        <div><strong>Username:</strong> {{ $user->username }}</div>
        <div><strong>Email:</strong> {{ $user->email }}</div>
        <div><strong>Registered At:</strong> {{ $user->created_at->toDayDateTimeString() }}</div>
        <div><strong>Role:</strong> {{ $user->is_admin ? 'Admin' : 'User' }}</div>
        <div><strong>Type:</strong> {{ $user->is_artist ? 'Artist' : 'Producer' }}</div>
        <div><strong>Active Status:</strong> {{ $user->active_status ? 'Online' : 'Offline' }}</div>
    </div>

    @if(isset($user->favoriteTracks) && $user->favoriteTracks->count())
    <div class="mb-4">
        <strong>Favorite Tracks ({{ $user->favoriteTracks->count() }}):</strong>
        <ul class="list-disc list-inside mt-2">
            @foreach($user->favoriteTracks as $favTrack)
            <li>{{ $favTrack->title }} (ID: {{ $favTrack->id }})</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Ban Button (only if the user is NOT an admin and not yourself) --}}
    @if (!$user->is_admin && $user->id !== auth()->id())
    <form action="{{ route('admin.users.ban', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to ban this user?');">
        @csrf
        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded">
            Ban User
        </button>
    </form>
    @endif

    <div>
        <a href="{{ route('admin.reports.index') }}"
            class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">
            Back to Reports
        </a>
    </div>
</div>
@endsection