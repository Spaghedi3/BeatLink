<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Search users') }}
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto mt-8 p-4 bg-white dark:bg-gray-800 text-black dark:text-white shadow rounded">
        <form method="GET" action="{{ route('users.search') }}" class="mb-4">
            <input
                type="text"
                name="search"
                placeholder="Search users by username..."
                value="{{ request('search') }}"
                class="w-full p-2 border border-gray-400 dark:border-gray-600 rounded bg-white dark:bg-gray-900 text-black dark:text-white">
        </form>

        @if($users->count())
        <ul>
            @foreach ($users as $user)
            <li class="p-2 border-b border-gray-300 dark:border-gray-700">
                <a href="{{ route('profile.show', $user->username) }}" class="text-blue-600 hover:underline">
                    {{ $user->username }}
                </a>
            </li>
            @endforeach
        </ul>

        <div class="mt-4">
            {{ $users->withQueryString()->links() }}
        </div>
        @else
        <p>No users found.</p>
        @endif
    </div>
</x-app-layout>