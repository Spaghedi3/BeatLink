<!-- resources/views/beats/index.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('All Beats') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="bg-green-500 text-white p-3 rounded mb-4">
                {{ session('success') }}
            </div>
            @endif

            @if($beats->isEmpty())
            <div class="bg-gray-800 text-white p-6 rounded">
                <h1 class="text-xl font-semibold mb-2">No Beats Yet</h1>
                <p>It looks like there are no beats available. Seems like this user does not have any work posted yet!</p>
            </div>
            @else
            <!-- Responsive grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($beats as $beat)
                <!-- Card -->
                <div class="bg-gray-900 text-white p-4 rounded shadow-md">
                    <!-- Image container -->
                    <div class="w-full h-48 border-2 border-purple-500 flex items-center justify-center overflow-hidden">
                        @if($beat->picture)
                        <img
                            src="{{ Storage::url($beat->picture) }}"
                            alt="Beat Image"
                            class="object-cover w-full h-full">
                        @else
                        <span class="text-gray-500">No Image</span>
                        @endif
                    </div>

                    <!-- Beat name -->
                    <p class="mt-2 text-center font-semibold">
                        Name: {{ $beat->name }}
                    </p>

                    <!-- Audio element (no controls) -->
                    <audio id="audio-{{ $beat->id }}">
                        <source src="{{ Storage::url($beat->file_path) }}" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>

                    <!-- Icon row (config, play, delete) -->
                    <div class="flex items-center justify-evenly mt-4">
                        <!-- Example config icon -->
                        <button class="hover:text-purple-300">
                            @if(auth()->check() && auth()->id() === $beat->user_id)
                            <a href="{{ route('beats.edit', $beat) }}" class="hover:text-purple-300">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor"
                                    class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M10.5 6h.75m-2.25 0h1.5M5.25 6a2.25 2.25 0 104.5 0v0a2.25 2.25 0 00-4.5 0v0zm0 0h4.5M13.5 18h.75m-2.25 0h1.5M8.25 18a2.25 2.25 0 104.5 0v0a2.25 2.25 0 00-4.5 0v0zm0 0h4.5M16.5 12h.75m-2.25 0h1.5M11.25 12a2.25 2.25 0 104.5 0v0a2.25 2.25 0 00-4.5 0v0zm0 0h4.5" />
                                </svg>
                            </a>
                            @endif
                        </button>

                        <!-- Custom Play/Pause button goes here -->
                        <button
                            class="flex items-center justify-center w-10 h-10 bg-purple-600 rounded-full hover:bg-purple-700"
                            onclick="togglePlay({{ $beat->id }})"
                            id="playBtn-{{ $beat->id }}">
                            <!-- Play icon -->
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor"
                                class="w-6 h-6 text-white"
                                id="playIcon-{{ $beat->id }}">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M5.25 5.25v13.5l13.5-6.75-13.5-6.75z" />
                            </svg>

                            <!-- Pause icon (hidden by default) -->
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor"
                                class="w-6 h-6 text-white hidden"
                                id="pauseIcon-{{ $beat->id }}">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 5.25v13.5m-7.5-13.5v13.5" />
                            </svg>
                        </button>

                        <!-- Delete / close button (only if the user owns the beat) -->
                        @if(auth()->id() === $beat->user_id)
                        <a href="{{ route('beats.destroy.confirm', $beat) }}" class="hover:text-purple-300">
                            <!-- X icon -->
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor"
                                class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</x-app-layout>