<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Left side: Logo & Nav links -->
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('For You') }}
                    </x-nav-link>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('tracks.index')" :active="request()->routeIs('tracks.index')">
                        {{ __('Tracks') }}
                    </x-nav-link>
                </div>
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('tracks.favorites')" :active="request()->routeIs('tracks.favorites')">
                        {{ __('Favorites') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Right side: Settings / Auth -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-4 overflow-visible">
                @auth
                <div class="relative w-6 h-6 -ml-1.5 mt-[2px]">
                    @php $unread = auth()->user()->unreadNotifications->count(); @endphp

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="relative">
                                <!-- Bell Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-6 h-6 text-gray-400 hover:text-white">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M14.25 17h-4.5m9-6V8a6.75 6.75 0 10-13.5 0v3a2.25 2.25 0 01-.75 1.687l-.75.563a.75.75 0 00.75 1.25h15a.75.75 0 00.75-1.25l-.75-.563A2.25 2.25 0 0119.5 11z" />
                                </svg>
                                @if($unread)
                                <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-xs 
                     text-white rounded-full flex items-center justify-center">
                                    {{ $unread }}
                                </span>
                                @endif
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="py-1">
                                @forelse(auth()->user()->unreadNotifications as $note)
                                @php
                                $data = $note->data;
                                $actorUsername = $data['actor_username']
                                ?? \App\Models\User::find($data['actor_id'])->username;
                                @endphp

                                <x-dropdown-link :href="route('notifications.read.one', $note->id)">
                                    {{ $note->data['message'] }}
                                    <span class="block text-xs text-gray-500">
                                        {{ $note->created_at->diffForHumans() }}
                                    </span>
                                </x-dropdown-link>

                                @empty
                                <div class="px-4 py-2 text-sm text-gray-500">
                                    No new notifications
                                </div>
                                @endforelse
                                <form action="{{ route('notifications.read.all') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-sm text-blue-500 hover:underline">
                                        Mark all as read
                                    </button>
                                </form>
                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- Authenticated user dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4
                                       font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800
                                       hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition
                                       ease-in-out duration-150">
                            <div>{{ Auth::user()->username }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1
                                               0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1
                                               0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
                @else
                <!-- Guest Links -->
                <a href="{{ route('login') }}" class="me-4 text-white hover:text-gray-300">Login</a>
                <a href=" {{ route('register') }}" class="text-white hover:text-gray-300">Register</a>
                @endauth
            </div>

            <!-- Hamburger (mobile) -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400
                           dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400
                           hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none
                           focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500
                           dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }"
                            class="inline-flex" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }"
                            class="hidden" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <!-- Main Nav Link (For You) -->
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('For You') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings / Auth -->
        @auth
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">
                    {{ Auth::user()->username }}
                </div>
                <div class="font-medium text-sm text-gray-500">
                    {{ Auth::user()->email }}
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        @else
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">
                    Guest
                </div>
                <div class="font-medium text-sm text-gray-500">
                    No email
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('login')">
                    {{ __('Login') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('register')">
                    {{ __('Register') }}
                </x-responsive-nav-link>
            </div>
        </div>
        @endauth
    </div>
</nav>