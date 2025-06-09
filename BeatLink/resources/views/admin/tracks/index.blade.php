@extends('layouts.admin')

@section('title', 'All Tracks')
@section('page-heading', 'All Tracks')

@section('content')
<div class="mb-6">
    <form method="GET" action="{{ route('admin.tracks.index') }}" class="flex space-x-2">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search titles, users, categories"
            class="px-4 py-2 w-64 rounded-l bg-gray-800 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring focus:ring-blue-500">
        <button type="submit" class="bg-gray-600 hover:bg-gray-500 text-white px-4 py-2 rounded-r">
            Search
        </button>
    </form>
</div>

<div class="shadow rounded-lg overflow-x-auto">
    <table class="min-w-full text-sm text-left text-white bg-gray-900">
        <thead class="bg-gray-700">
            <tr>
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Title</th>
                <th class="px-4 py-2">Uploader</th>
                <th class="px-4 py-2">Category</th>
                <th class="px-4 py-2">Uploaded</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tracks as $track)
            <tr class="{{ $track->trashed() ? 'bg-gray-800 hover:bg-gray-700 text-gray-400 italic' : 'bg-gray-800 hover:bg-gray-700' }}">
                <td class="border-t px-4 py-2">{{ $track->id }}</td>
                <td class="border-t px-4 py-2">
                    {{ Str::limit($track->name, 30) }}
                </td>
                <td class="border-t px-4 py-2">
                    <a href="{{ route('admin.users.show', $track->user->username) }}" class="text-blue-400 hover:underline">
                        {{ $track->user->username }}
                    </a>
                </td>
                <td class="border-t px-4 py-2">{{ ucfirst($track->category ?? '—') }}</td>
                <td class="border-t px-4 py-2">{{ $track->created_at->diffForHumans() }}</td>
                <td class="border-t px-4 py-2 space-x-2">
                    <a href="{{ route('admin.tracks.show', $track->id) }}" class="text-blue-500 hover:underline">
                        View
                    </a>

                    @if($track->trashed())
                    <form action="{{ route('admin.tracks.restore', $track->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-green-500 hover:underline">
                            Restore
                        </button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                    No tracks found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="bg-gray-900 p-4 flex justify-center">
        {{ $tracks->withQueryString()->links() }}
    </div>
</div>
@endsection