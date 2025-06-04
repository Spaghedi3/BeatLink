{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') &ndash; Admin</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body class="min-h-screen bg-gray-100 font-sans antialiased">

    {{-- Top header --}}
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-5xl mx-auto px-4 py-4 flex justify-between items-center">
            <div class="text-xl font-bold">
                Admin Panel
            </div>
            <div class="flex items-center space-x-4">
                {{-- Greet by name or username --}}
                <span class="text-gray-700">
                    Hello, {{ auth()->user()->name ?: auth()->user()->username }}
                </span>
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="text-red-600 hover:underline">
                    Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </div>

        {{-- Secondary nav: Dashboard, Reports, Users, Tracks --}}
        <nav class="bg-gray-50 border-t">
            <div class="max-w-5xl mx-auto px-4 py-2 flex space-x-6">
                <a href="{{ route('admin.dashboard') }}"
                    class="text-gray-800 hover:text-gray-600 {{ request()->routeIs('admin.dashboard') ? 'font-semibold' : '' }}">
                    Dashboard
                </a>
                <a href="{{ route('admin.reports.index') }}"
                    class="text-gray-800 hover:text-gray-600 {{ request()->routeIs('admin.reports.*') ? 'font-semibold' : '' }}">
                    Reports
                </a>
                <a href="{{ route('admin.users.index') }}"
                    class="text-gray-800 hover:text-gray-600 {{ request()->routeIs('admin.users.*') ? 'font-semibold' : '' }}">
                    Users
                </a>
                <a href="{{ route('admin.tracks.index') }}"
                    class="text-gray-800 hover:text-gray-600 {{ request()->routeIs('admin.tracks.*') ? 'font-semibold' : '' }}">
                    Tracks
                </a>
            </div>
        </nav>
    </header>

    {{-- Main content area --}}
    <main class="max-w-5xl mx-auto px-4 py-8">
        {{-- Page heading --}}
        <h1 class="text-3xl font-semibold mb-6">@yield('page-heading')</h1>

        {{-- Flash messages --}}
        @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
            {{ session('error') }}
        </div>
        @endif

        {{-- Page content --}}
        @yield('content')
    </main>

</body>

</html>