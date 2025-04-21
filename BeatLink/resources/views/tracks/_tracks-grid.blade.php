<div class="w-full px-4">
    <!-- Always visible Search Bar -->
    <div class="mb-4">
        <form action="{{ url()->current() }}" method="GET">
            <div class="flex">
                <input type="text" name="search" placeholder="Search tracks by name, tag, category, or type..."
                    value="{{ request('search') }}"
                    class="border border-gray-300 rounded px-4 py-2 flex-1" />

                <button type="submit" class="ml-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Search
                </button>
            </div>

            <!-- Move checkboxes outside the flex row -->
            @if (! auth()->user()->is_artist)
            <div class="flex flex-wrap items-center gap-4 mt-4">
                <label class="text-gray-200 font-semibold mr-2">Category:</label>

                @foreach(['instrumental', 'loopkit', 'drumkit', 'multikit'] as $cat)
                <label for="cat_{{ $cat }}" class="inline-flex items-center space-x-2 cursor-pointer">
                    <input
                        class="rounded text-blue-600 focus:ring-blue-500 border-gray-300"
                        type="checkbox"
                        name="category[]"
                        id="cat_{{ $cat }}"
                        value="{{ $cat }}"
                        @if(collect(request()->input('category'))->contains($cat)) checked @endif
                    >
                    <span class="text-sm text-gray-300">{{ ucfirst($cat) }}</span>
                </label>
                @endforeach
            </div>
            @endif
        </form>
    </div>




    <!-- tracks Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($tracks as $track)
        <!-- track Card -->
        <div class="bg-gray-900 text-white p-4 rounded shadow-md group-relative">
            <!-- Image container -->
            <div class="relative group w-full h-48 border-2 border-purple-500 flex items-center justify-center overflow-hidden">
                @if($track->picture)
                <img src="{{ Storage::url($track->picture) }}" alt="track Image" class="object-cover w-full h-full group-hover:opacity-30">
                @else
                <span class="text-gray-500">No Image</span>
                @endif
                @if($track->folder_files)
                <div class="absolute inset-0 bg-gray-900 bg-opacity-90 opacity-0 group-hover:opacity-100 transition-opacity duration-300 overflow-y-auto p-2">
                    <ul class="space-y-2">
                        @foreach(json_decode($track->folder_files, true) as $file)
                        <li class="flex items-center space-x-2 text-sm text-white">
                            <button type="button"
                                onclick="toggleHoverPlay(this, '{{ Storage::url($file) }}')"
                                class="flex items-center justify-center w-10 h-10 bg-purple-600 rounded-full hover:bg-purple-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white play-icon" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M5.25 5.25v13.5l13.5-6.75-13.5-6.75z" />
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white pause-icon hidden" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 5.25v13.5m-7.5-13.5v13.5" />
                                </svg>
                            </button>

                            <span class="truncate">{{ basename($file) }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

            </div>
            <audio id="hover-audio-player" class="hidden"></audio>

            <!-- track name -->
            <p class="mt-2 text-center font-semibold">
                Name: {{ $track->name }}
            </p>

            <!-- Audio element (no controls) -->
            @if($track->category !== 'loopkit' && $track->category !== 'drumkit' && $track->category !== 'multikit' && $track->file_path)
            <audio id="audio-{{ $track->id }}">
                <source src="{{ Storage::url($track->file_path) }}" type="audio/mpeg">
                Your browser does not support the audio element.
            </audio>
            @endif

            <!-- Icon row (config, play, delete) -->
            <div class="flex items-center justify-evenly mt-4">
                @if(Auth::check() && Auth::id() === $track->user_id)
                <a href="{{ route('tracks.edit', $track) }}" class="hover:text-purple-300">
                    <!-- Edit icon SVG -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M10.5 6h.75m-2.25 0h1.5M5.25 6a2.25 2.25 0 104.5 0v0a2.25 2.25 0 00-4.5 0v0zm0 0h4.5M13.5 18h.75m-2.25 0h1.5M8.25 18a2.25 2.25 0 104.5 0v0a2.25 2.25 0 00-4.5 0v0zm0 0h4.5M16.5 12h.75m-2.25 0h1.5M11.25 12a2.25 2.25 0 104.5 0v0a2.25 2.25 0 00-4.5 0v0zm0 0h4.5" />
                    </svg>
                </a>
                @else
                <button
                    class="reaction-button {{ $track->userReactedWith === 'love' ? 'text-red-500' : '' }}"
                    data-track-id="{{ $track->id }}"
                    data-owner-id="{{ $track->user_id }}"
                    data-reaction="love">
                    <!-- Like icon SVG -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="feather feather-heart">
                        <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 1 0-7.8 7.8l1 1L12 21l7.8-7.8 1-1a5.5 5.5 0 0 0 0-7.8z" />
                    </svg>
                </button>
                @endif
                @if($track->category !== 'loopkit' && $track->category !== 'drumkit' && $track->category !== 'multikit')
                <!-- Custom Play/Pause button -->
                <button class="flex items-center justify-center w-10 h-10 bg-purple-600 rounded-full hover:bg-purple-700"
                    onclick="togglePlay({{ $track->id }})" id="playBtn-{{ $track->id }}">
                    <!-- Play icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-white"
                        id="playIcon-{{ $track->id }}">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M5.25 5.25v13.5l13.5-6.75-13.5-6.75z" />
                    </svg>
                    <!-- Pause icon (hidden by default) -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-white hidden"
                        id="pauseIcon-{{ $track->id }}">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 5.25v13.5m-7.5-13.5v13.5" />
                    </svg>
                </button>
                @endif
                @if(auth()->id() === $track->user_id)
                <a href="{{ route('tracks.destroy.confirm', $track) }}" class="hover:text-purple-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </a>
                @else
                <button
                    class="reaction-button {{ $track->userReactedWith === 'hate' ? 'text-red-500' : '' }}"
                    data-track-id="{{ $track->id }}"
                    data-owner-id="{{ $track->user_id }}"
                    data-reaction="hate">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="feather feather-heart-crack">
                        <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 1 0-7.8 7.8l1 1L12 21l7.8-7.8 1-1a5.5 5.5 0 0 0 0-7.8z" />
                        <path d="M12 8l-2 3 2 2-2 3 2 2" />
                    </svg>
                </button>
                @endif
            </div>
        </div>
        @endforeach

        <!-- Add track Card (only for owner's view, if $showAddButton is true) -->
        @if(isset($showAddButton) && $showAddButton)
        <a href="{{ route('tracks.create') }}" class="bg-gray-900 text-white p-4 rounded shadow-md flex items-center justify-center">
            <div class="w-full h-full border-2 border-dashed border-gray-500 flex items-center justify-center">
                <span class="text-6xl leading-none">+</span>
            </div>
        </a>
        @endif
    </div>
</div>
<script>
    window.routes = {
        react: "{{ route('reaction.react') }}"
    };
</script>