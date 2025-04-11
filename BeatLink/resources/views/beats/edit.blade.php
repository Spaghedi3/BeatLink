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

                    <!-- Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Beat Name</label>
                        <input
                            type="text"
                            class="form-control bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100"
                            id="name"
                            name="name"
                            value="{{ old('name', $beat->name) }}"
                            required>
                    </div>
                    <input type="hidden" id="original_name" value="{{ $beat->name }}">
                    <p id="name-warning" class="text-red-500 text-sm mt-1"></p>

                    <!-- Category -->
                    <div class="mb-3">
                        <label for="category" class="form-label text-gray-800 dark:text-gray-200">Category</label>
                        @if($beat->category === 'instrumental')
                        <!-- Read-only category (instrumental) -->
                        <input type="text"
                            name="category"
                            id="category"
                            value="instrumental"
                            readonly
                            class="form-control bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100 cursor-not-allowed">
                        @else
                        <!-- Editable dropdown for other categories -->
                        <select class="form-select bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100"
                            id="category"
                            name="category"
                            required>
                            <option value="loopkit" {{ old('category', $beat->category) === 'loopkit' ? 'selected' : '' }}>Loopkit</option>
                            <option value="drumkit" {{ old('category', $beat->category) === 'drumkit' ? 'selected' : '' }}>Drumkit</option>
                            <option value="fx" {{ old('category', $beat->category) === 'fx' ? 'selected' : '' }}>MultiKit</option>
                        </select>
                        @endif
                    </div>


                    @if($beat->category === 'instrumental')
                    <!-- Audio File input -->
                    <div class="mb-3">
                        <label for="audio_file" class="form-label text-gray-800 dark:text-gray-200">Audio File (.mp3 or .wav)</label>
                        <input type="file"
                            id="audio_file"
                            name="audio_file"
                            accept=".mp3,.wav"
                            class="block w-full text-sm text-gray-500
                      file:mr-4 file:py-2 file:px-4
                      file:rounded-full file:border-0
                      file:text-sm file:font-semibold
                      file:bg-blue-50 file:text-blue-700
                      hover:file:bg-blue-100">
                        @if($beat->file_path)
                        <p class="mt-2 text-sm text-gray-400">
                            Current file: <strong>{{ basename($beat->file_path) }}</strong>
                        </p>
                        @endif
                    </div>
                    @else
                    <!-- Folder input -->
                    <div class="mb-3">
                        <label for="audio_folder" class="form-label text-gray-800 dark:text-gray-200">Folder</label>
                        <input type="file"
                            id="audio_folder"
                            name="audio_folder[]"
                            webkitdirectory
                            directory
                            multiple
                            accept=".mp3,.wav"
                            class="block w-full text-sm text-gray-500
                      file:mr-4 file:py-2 file:px-4
                      file:rounded-full file:border-0
                      file:text-sm file:font-semibold
                      file:bg-blue-50 file:text-blue-700
                      hover:file:bg-blue-100">

                        @if ($errors->has('audio_folder.*'))
                        <div class="text-red-500 mb-4">
                            Some files in the folder are not valid audio formats (.mp3 or .wav).
                        </div>
                        @endif

                        @if($beat->folder_files)
                        <p class="mt-2 text-sm text-gray-400">
                            {{ count(json_decode($beat->folder_files, true)) }} file(s) uploaded
                        </p>
                        @endif
                    </div>
                    @endif

                    <!-- Picture (optional) -->
                    <div class="mb-3">
                        <label for="picture" class="form-label text-gray-800 dark:text-gray-200">Cover Picture (optional)</label>
                        <input type="file" class="block w-full text-sm text-gray-500 
              file:mr-4 file:py-2 file:px-4 
              file:rounded-full file:border-0 
              file:text-sm file:font-semibold 
              file:bg-blue-50 file:text-blue-700 
              hover:file:bg-blue-100" id="picture" name="picture" accept="image/*">
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
                            class="form-control bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100"
                            id="tags"
                            name="tags"
                            value="{{ old('tags', $beat->tags ? $beat->tags->pluck('name')->implode(', ') : '') }}">
                    </div>


                    <!-- Type Beat -->
                    <div class="mb-3">
                        <label for="type_beat" class="form-label">Type (artist style)</label>
                        <input
                            type="text"
                            class="form-control bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100"
                            id="type_beat"
                            name="type_beat"
                            value="{{ old('type_beat', $beat->types ? $beat->types->pluck('name')->implode(', ') : '') }}">
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

                    <button type="submit" id="updateBtn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Update
                    </button>
                    <a href="{{ route('beats.index') }}" class="ml-4 text-blue-500 hover:underline">
                        Cancel
                    </a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>