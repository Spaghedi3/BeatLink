{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') – Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-900 text-gray-100 font-sans antialiased">

    <div class="flex h-screen overflow-hidden">
        {{-- Sidebar --}}
        <aside class="w-64 bg-gray-800 flex flex-col space-y-4 p-6">
            <h1 class="text-2xl font-bold mb-2">Admin Panel</h1>
            <div class="text-sm text-gray-300 mb-6">
                Hello, <span class="font-semibold">{{ auth()->user()->name ?: auth()->user()->username }}</span>
            </div>

            <nav class="flex flex-col space-y-3 text-gray-300 text-base">
                <a href="{{ route('admin.dashboard') }}"
                    class="{{ request()->routeIs('admin.dashboard') ? 'text-white font-semibold' : 'hover:text-white' }}">
                    📊 Dashboard
                </a>
                <a href="{{ route('admin.reports.index') }}"
                    class="{{ request()->routeIs('admin.reports.*') ? 'text-white font-semibold' : 'hover:text-white' }}">
                    🚩 Reports
                </a>
                <a href="{{ route('admin.users.index') }}"
                    class="{{ request()->routeIs('admin.users.*') ? 'text-white font-semibold' : 'hover:text-white' }}">
                    👤 Users
                </a>
                <a href="{{ route('admin.tracks.index') }}"
                    class="{{ request()->routeIs('admin.tracks.*') ? 'text-white font-semibold' : 'hover:text-white' }}">
                    🎵 Tracks
                </a>
                <form action="{{ route('logout') }}" method="POST" class="mt-6">
                    @csrf
                    <button type="submit"
                        class="text-red-400 hover:text-red-600 text-sm font-medium">
                        🚪 Logout
                    </button>
                </form>
            </nav>
        </aside>

        {{-- Main content --}}
        <main class="flex-1 p-10 overflow-y-auto bg-gray-900">
            {{-- Page heading --}}
            <h2 class="text-3xl font-bold text-white mb-6">@yield('page-heading')</h2>

            {{-- Flash messages --}}
            @if(session('success'))
            <div class="mb-4 p-4 bg-green-600 text-white rounded">
                {{ session('success') }}
            </div>
            @endif
            @if(session('error') && !request()->is('admin*'))
            <div class="mb-4 p-4 bg-red-600 text-white rounded">
                {{ session('error') }}
            </div>
            @endif
            {{-- Page content --}}
            @yield('content')
        </main>
    </div>

</body>

</html>