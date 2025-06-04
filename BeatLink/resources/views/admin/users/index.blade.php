{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'All Users')
@section('page-heading', 'All Users')

@section('content')
<div class="mb-6">
    <form method="GET" action="{{ route('admin.users.index') }}" class="flex space-x-2">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search username or email…"
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
                <th class="px-4 py-2">Username</th>
                <th class="px-4 py-2">Email</th>
                <th class="px-4 py-2">Role</th>
                <th class="px-4 py-2">Registered</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr class="{{ $user->is_admin ? 'bg-yellow-50' : '' }}">
                <td class="border-t px-4 py-2">{{ $user->id }}</td>
                <td class="border-t px-4 py-2">{{ $user->username }}</td>
                <td class="border-t px-4 py-2">{{ $user->email }}</td>
                <td class="border-t px-4 py-2">
                    {{ $user->is_admin ? 'Admin' : ($user->is_artist ? 'Artist' : 'User') }}
                </td>
                <td class="border-t px-4 py-2">{{ $user->created_at->diffForHumans() }}</td>
                <td class="border-t px-4 py-2">
                    <a href="{{ route('admin.users.show', $user) }}"
                        class="text-blue-600 hover:underline">
                        View
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                    No users found.
                </td>
            </tr>
            @endforelse
        </tbody>
        <td class="border-t px-4 py-2 space-x-2">
            <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:underline">
                View
            </a>
            <a href="{{ route('admin.user.conversations', $user->id) }}" class="text-purple-600 hover:underline">
                View Chat Messages
            </a>
        </td>

    </table>

    {{-- Pagination --}}
    <div class="p-4">
        {{ $users->withQueryString()->links() }}
    </div>
</div>
@endsection