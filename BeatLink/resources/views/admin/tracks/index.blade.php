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
            placeholder="Search titles…"
            class="border rounded-l px-3 py-2 w-64">
        <button type="submit" class="bg-gray-600 text-white rounded-r px-4 py-2">
            Search
        </button>
    </form>
</div>

<div class="bg-white shadow rounded-lg overflow-x-auto">
    <table class="min-w-full">
        <thead class="bg-gray-100 text-left">
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
            <tr class="{{ $track->trashed() ? 'bg-red-100 text-gray-500 italic' : '' }}">
                <td class="border-t px-4 py-2">{{ $track->id }}</td>
                <td class="border-t px-4 py-2">{{ Str::limit($track->title, 30) }}</td>
                <td class="border-t px-4 py-2">
                    <a href="{{ route('admin.users.show', $track->user_id) }}"
                        class="text-blue-600 hover:underline">
                        {{ $track->user->username }}
                    </a>
                </td>
                <td class="border-t px-4 py-2">{{ ucfirst($track->category ?? '—') }}</td>
                <td class="border-t px-4 py-2">{{ $track->created_at->diffForHumans() }}</td>
                <td class="border-t px-4 py-2 space-x-2">
                    <a href="{{ route('admin.tracks.show', $track->id) }}"
                        class="text-blue-600 hover:underline">View</a>

                    @if($track->trashed())
                    <form action="{{ route('admin.tracks.restore', $track->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-green-600 hover:underline">
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
    <div class="p-4">
        {{ $tracks->withQueryString()->links() }}
    </div>
</div>
@endsection