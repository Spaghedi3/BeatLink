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
                        {{ __('Favorite Tracks') }}
                    </x-nav-link>
                </div>
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('tracks.privates')" :active="request()->routeIs('tracks.privates')">
                        {{ __('Private Tracks') }}
                    </x-nav-link>
                </div>
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('users.search')" :active="request()->routeIs('tracks.privates')">
                        {{ __('Search') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Right side: Settings / Auth -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3 overflow-visible">
                @auth
                <!-- Notification Icon with improved dropdown -->
                <div class="relative w-10 h-10 flex items-center justify-center">
                    @php $unread = auth()->user()->unreadNotifications->count(); @endphp
                    <x-dropdown align="right" width="wide" class="!w-[32rem]">
                        <x-slot name="trigger">
                            <button class="relative flex items-center justify-center text-gray-400 hover:text-white translate-y-[1px] transition-colors duration-200">
                                <!-- Bell Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M14.25 17h-4.5m9-6V8a6.75 6.75 0 10-13.5 0v3a2.25 2.25 0 01-.75 1.687l-.75.563a.75.75 0 00.75 1.25h15a.75.75 0 00.75-1.25l-.75-.563A2.25 2.25 0 0119.5 11z" />
                                </svg>
                                @if($unread > 0)
                                <span class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-red-500 text-white text-[10px] font-semibold rounded-full flex items-center justify-center leading-none">
                                    {{ $unread }}
                                </span>
                                @endif
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <!-- Header -->
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
                                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Notifications</h3>
                                @if($unread > 0)
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $unread }} unread</p>
                                @endif
                            </div>

                            <!-- Scrollable notifications container with adjusted spacing -->
                            <div class="notifications-scroll-container max-h-80 overflow-y-auto px-4 space-y-2">
                                @forelse(auth()->user()->unreadNotifications as $note)
                                @php
                                $data = $note->data;
                                $actor = isset($data['actor_id']) ? \App\Models\User::find($data['actor_id']) : null;
                                $isAdmin = $actor?->is_admin ?? false;
                                $actorUsername = $data['actor_username'] ?? ($actor?->username ?? 'System');

                                $iconSvg = '';
                                if (str_contains(strtolower($data['message']), 'removed')) {
                                $iconSvg = '<svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>';
                                } elseif (str_contains(strtolower($data['message']), 'restored')) {
                                $iconSvg = '<svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                                </svg>';
                                } else {
                                $iconSvg = '<svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                </svg>';
                                }
                                @endphp

                                @if($isAdmin)
                                <form method="POST" action="{{ route('notifications.read.one', $note->id) }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left block px-6 py-4 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200 max-w-full">
                                        <div class="flex items-start space-x-4">
                                            <div class="flex-shrink-0 w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mt-0.5">
                                                {!! $iconSvg !!}
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-[15px] font-medium text-gray-900 dark:text-gray-100 leading-relaxed break-words">
                                                    {{ $data['message'] }}
                                                </p>
                                                <div class="mt-3 flex items-center justify-between gap-3">
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $note->created_at->diffForHumans() }}
                                                    </span>
                                                    @if(!empty($data['reason']))
                                                    <span class="inline-block px-3 py-1 rounded-md text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 leading-snug max-w-[140px] break-words text-center">
                                                        Violation of platform<br>rules
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </button>
                                </form>
                                @else
                                <x-dropdown-link :href="route('notifications.read.one', $note->id)"
                                    class="block px-6 py-4 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200 max-w-full">
                                    <div class="flex items-start space-x-4">
                                        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mt-0.5">
                                            {!! $iconSvg !!}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-[15px] font-medium text-gray-900 dark:text-gray-100 leading-relaxed break-words">
                                                {{ $data['message'] }}
                                            </p>
                                            <div class="mt-3 flex items-center justify-between gap-3">
                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $note->created_at->diffForHumans() }}
                                                </span>
                                                @if(!empty($data['reason']))
                                                <span class="inline-block px-3 py-1 rounded-md text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 leading-snug max-w-[140px] break-words text-center">
                                                    Violation of platform<br>rules
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </x-dropdown-link>
                                @endif
                                @empty
                                <div class="px-6 py-8 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5v-7h5v7z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No notifications</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">You're all caught up!</p>
                                </div>
                                @endforelse
                            </div>

                            @if($unread > 0)
                            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
                                <form action="{{ route('notifications.read.all') }}" method="POST" class="flex justify-center">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200 dark:text-blue-400 dark:bg-blue-900 dark:hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        Mark all as read
                                    </button>
                                </form>
                            </div>
                            @endif
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