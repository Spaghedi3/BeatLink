<!-- resources/views/beats/edit.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Beat') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
            <div class="mb-4 alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <form action="{{ route('beats.update', $beat) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Beat Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Beat Name</label>
                        <input
                            type="text"
                            class="form-control bg-gray-100 text-gray-800"
                            id="name"
                            name="name"
                            value="{{ old('name', $beat->name) }}"
                            required>
                    </div>

                    <!-- Audio File -->
                    <div class="mb-3">
                        <label for="audio" class="form-label">Audio File (mp3 or wav)</label>
                        <input
                            type="file"
                            class="form-control bg-gray-100 text-gray-800"
                            id="audio"
                            name="audio"
                            accept=".mp3,.wav">
                        @if($beat->file_path)
                        <p class="mt-2 text-sm text-gray-400">
                            Current file: <strong>{{ $beat->name }}</strong>
                        </p>
                        @endif
                    </div>

                    <!-- Picture -->
                    <div class="mb-3">
                        <label for="picture" class="form-label">Picture (optional)</label>
                        <input
                            type="file"
                            class="form-control bg-gray-100 text-gray-800"
                            id="picture"
                            name="picture"
                            accept="image/*">
                        @if($beat->picture)
                        <div class="mt-2">
                            <img
                                src="{{ Storage::url($beat->picture) }}"
                                alt="Current Picture"
                                class="h-32 w-32 object-cover">
                        </div>
                        @endif
                    </div>

                    <!-- Tags -->
                    <div class="mb-3">
                        <label for="tags" class="form-label">Tags (comma-separated)</label>
                        <input
                            type="text"
                            class="form-control bg-gray-100 text-gray-800"
                            id="tags"
                            name="tags"
                            value="{{ old('tags', $beat->tags) }}">
                    </div>

                    <!-- Category -->
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select bg-gray-100 text-gray-800" id="category" name="category">
                            <option value="instrumental" {{ $beat->category === 'instrumental' ? 'selected' : '' }}>Instrumental</option>
                            <option value="loopkit" {{ $beat->category === 'loopkit' ? 'selected' : '' }}>Loopkit</option>
                            <option value="drumkit" {{ $beat->category === 'drumkit' ? 'selected' : '' }}>Drumkit</option>
                            <option value="fx" {{ $beat->category === 'fx' ? 'selected' : '' }}>FX</option>
                            <!-- add more if needed -->
                        </select>
                    </div>

                    <!-- Type Beat -->
                    <div class="mb-3">
                        <label for="type_beat" class="form-label">Type Beat (artist style)</label>
                        <input
                            type="text"
                            class="form-control bg-gray-100 text-gray-800"
                            id="type_beat"
                            name="type_beat"
                            value="{{ old('type_beat', $beat->type_beat) }}">
                    </div>


                    <div class="mb-4">
                        <label for="is_private" class="block font-semibold mb-1">Private?</label>
                        <input
                            type="checkbox"
                            name="is_private"
                            id="is_private"
                            value="1"
                            @checked(old('is_private', $beat->is_private ?? false))
                        >
                        <span class="ml-2 text-sm text-gray-600">
                            Check this box to make the beat private (only you can see it).
                        </span>
                    </div>

                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Update Beat
                    </button>
                    <a href="{{ route('beats.index') }}" class="ml-4 text-blue-500 hover:underline">
                        Cancel
                    </a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>