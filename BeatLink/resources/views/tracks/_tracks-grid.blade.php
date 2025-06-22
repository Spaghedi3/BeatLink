<div class="sticky top-0 z-30 bg-[#0e1320] p-4 shadow-md rounded">
    <form action="{{ url()->current() }}" method="GET">
        <div class="flex items-center gap-2">
            <!-- Always visible content Search Bar -->
            <!-- Search input -->
            <input
                type="text"
                name="search"
                placeholder="Search tracks by name, tag, category, or type..."
                value="{{ request('search') }}"
                class="border border-gray-300 rounded px-4 py-2 flex-1" />

            <!-- BPM input -->
            <input
                type="text"
                name="bpm_range"
                value="{{ request('bpm_range') }}"
                placeholder="BPM range"
                class="border border-gray-300 rounded px-4 py-2 w-32 text-center" />

            <!-- Key dropdown -->
            <select name="key" class="border border-gray-300 rounded px-4 py-2 w-24 text-center">
                <option value="">Key</option>
                @foreach (['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'] as $keyOption)
                <option value="{{ $keyOption }}" {{ request('key') == $keyOption ? 'selected' : '' }}>
                    {{ $keyOption }}
                </option>
                @endforeach
            </select>


            <!-- Scale dropdown -->
            <select name="scale" class="border border-gray-300 rounded px-4 py-2 w-24 text-center">
                <option value="">Scale</option>
                @foreach (['major', 'minor'] as $scaleOption)
                <option value="{{ $scaleOption }}" {{ request('scale') == $scaleOption ? 'selected' : '' }}>
                    {{ ucfirst($scaleOption) }}
                </option>
                @endforeach
            </select>


            <!-- Search button -->
            <button
                type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
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
<div class="w-full px-4">
    <!-- tracks Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($tracks as $track)
        <!-- track Card -->
        <div class="bg-gray-900 text-white p-4 rounded shadow-md group-relative">
            <div class="relative inline-block text-left float-right h-8 w-8">
                @if(auth()->id() === $track->user_id)
                <div x-data="{ open: false }" class="realtive">
                    <button @click="open = !open"
                        class="w-8 h-8 flex items-center justify-center hover:text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6v.01M12 12v.01M12 18v.01" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak @click.away="open = false"
                        class="absolute z-20 right-0 mt-2 w-32 bg-white text-gray-800 rounded shadow-md py-1">
                        <a href="{{ route('tracks.edit', $track) }}"
                            class="block px-4 py-2 text-sm hover:bg-gray-100">Edit</a>
                        <a href="{{ route('tracks.destroy.confirm', $track) }}"
                            class="block px-4 py-2 text-sm hover:bg-gray-100">Delete</a>
                    </div>
                </div>
                @endif
                @if ($track->user_id !== auth()->id())
                <div x-data="{ open: false }" class="relative inline-block text-left">
                    <!-- Arrow Icon -->
                    <button @click="open = !open" class="text-gray-500 hover:text-red-600 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6v.01M12 12v.01M12 18v.01" />
                        </svg>
                    </button>

                    <!-- Dropdown -->
                    <div x-show="open"
                        @click.outside="open = false"
                        x-transition
                        class="absolute mt-2 bg-white border border-gray-200 rounded shadow-md z-10">
                        <a href="{{ route('report.track', $track->id) }}"
                            class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                            Report track
                        </a>
                    </div>
                </div>
                @endif
            </div>
            <!-- Image container -->
            <div class="relative group w-full h-48 border-2 border-purple-500 flex items-center justify-center overflow-hidden">
                @if(!$track->folder_files)
                <div class="absolute top-0 left-0 bg-black opacity-0 group-hover:opacity-70 transition-opacity text-white text-xs p-2 rounded-br-lg z-20">
                    <div>BPM: {{ $track->bpm }}</div>
                    <div>Key: {{ $track->key }}</div>
                    <div>Scale: {{ ucfirst($track->scale) }}</div>
                </div>
                @endif
                @if($track->file_path && !in_array($track->category, ['loopkit', 'drumkit', 'multikit']))
                <!-- Transparent play button over image -->
                <button onclick="togglePlay({{ $track->id }})"
                    class="absolute z-10 w-14 h-14 flex items-center justify-center border border-white rounded-full text-white bg-white/10 hover:bg-white/20 transition-opacity duration-200 opacity-0 group-hover:opacity-100">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M5.25 5.25v13.5l13.5-6.75-13.5-6.75z" />
                    </svg>
                </button>
                @endif
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
            <audio
                id="hover-audio-player"
                data-track-id="{{ $track->id }}"
                class="hidden">
            </audio>


            <!-- track name -->
            <p class="mt-2 text-center font-semibold">
                {{ $track->name }}
            </p>
            <a href="{{ route('profile.show', $track->user) }}">
                <p class=" mt-2 text-center font-semibold">
                    By: {{ $track->user->username }}
                </p>
            </a>
            <!-- Audio element (no controls) -->
            @if($track->category !== 'loopkit' && $track->category !== 'drumkit' && $track->category !== 'multikit' && $track->file_path)
            <audio id="audio-{{ $track->id }}">
                <source src="{{ Storage::url($track->file_path) }}" type="audio/mpeg">
                Your browser does not support the audio element.
            </audio>
            @endif

            <!-- Icon row (love, hate) -->
            <div class="flex items-center justify-evenly mt-4">

                <div class="flex flex-col items-center">
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
                    <span id="count-love-{{ $track->id }}" class="text-sm mt-1 text-gray-300">{{ $track->loved_count }}</span>
                </div>

                <div class="flex flex-col items-center">
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
                    <span id="count-hate-{{ $track->id }}" class="text-sm mt-1 text-gray-300">{{ $track->hated_count }}</span>
                </div>
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
    <button id="myBtn"
        title="Go to top"
        class="fixed bottom-8 right-8 bg-blue-600 text-white px-4 py-2 rounded shadow hidden z-50">
        Top
    </button>
</div>
<script>
    window.routes = {
        react: "{{ route('reaction.react') }}"
    };
</script>