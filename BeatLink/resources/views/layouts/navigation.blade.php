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
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3 overflow-visible">
                @auth
                <!-- Notification Icon -->
                <div class="relative w-10 h-10 flex items-center justify-center">
                    @php $unread = auth()->user()->unreadNotifications->count(); @endphp
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="relative flex items-center justify-center text-gray-400 hover:text-white translate-y-[1px]">
                                <!-- Bell Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M14.25 17h-4.5m9-6V8a6.75 6.75 0 10-13.5 0v3a2.25 2.25 0 01-.75 1.687l-.75.563a.75.75 0 00.75 1.25h15a.75.75 0 00.75-1.25l-.75-.563A2.25 2.25 0 0119.5 11z" />
                                </svg>

                                @if($unread > 0)
                                <!-- Badge -->
                                <span class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-red-500 text-white text-[10px]
                             font-semibold rounded-full flex items-center justify-center leading-none">
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
                                <div class="flex justify-center mt-2">
                                    <form action="{{ route('notifications.read.all') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-sm text-blue-500 hover:underline">
                                            Mark all as read
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>


                <!-- Chat Icon -->
                <div class="relative flex items-center justify-center text-gray-400 hover:text-white translate-y-[1px]">
                    @php $unread = auth()->user()->getUnreadMessageCount(); @endphp
                    <a href="{{ route('messages') }}" class="text-gray-400 hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 45.779 45.779" class="w-6 h-6">
                            <path d="M37.426,2.633H8.362C3.746,2.633,0,6.369,0,10.985v17.003c0,4.615,3.747,8.344,8.362,8.344h18.48l3.902,5.604 
             c0.527,0.756,1.39,1.209,2.311,1.211c0.92,0.002,1.785-0.443,2.314-1.197l4.129-5.865c3.611-0.924,6.281-4.198,6.281-8.098V10.985 
             C45.779,6.369,42.042,2.633,37.426,2.633z M15.431,22.203c-1.505,0-2.726-1.215-2.726-2.717c0-1.499,1.221-2.716,2.726-2.716 
             c1.506,0,2.726,1.217,2.726,2.716C18.157,20.988,16.937,22.203,15.431,22.203z M22.894,22.203c-1.505,0-2.726-1.215-2.726-2.717 
             c0-1.499,1.221-2.716,2.726-2.716c1.506,0,2.725,1.217,2.725,2.716C25.619,20.988,24.4,22.203,22.894,22.203z M30.357,22.203 
             c-1.506,0-2.727-1.215-2.727-2.717c0-1.499,1.221-2.716,2.727-2.716s2.726,1.217,2.726,2.716 
             C33.083,20.988,31.863,22.203,30.357,22.203z" />
                        </svg>
                        @if($unread > 0)
                        <!-- Badge -->
                        <span class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-red-500 text-white text-[10px]
                             font-semibold rounded-full flex items-center justify-center leading-none">
                            {{ $unread }}
                        </span>
                        @endif
                    </a>
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
                            <div class=" ms-1">
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
                        <x-dropdown-link :href="route('profile.show', ['username' => auth()->user()->username])">
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