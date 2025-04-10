<!-- resources/views/beats/create.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add a New Beat') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success or error messages -->
            @if ($errors->any())
            <div class="mb-4 alert alert-danger">
                <!-- <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul> -->
            </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('beats.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label text-gray-800 dark:text-gray-200">Name</label>
                        <input type="text" class="form-control bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100" id="name" name="name" required>
                    </div>

                    <p id="name-warning" class="text-red-500 text-sm mt-1"></p>

                    <!-- Category -->
                    <div class="mb-3">
                        <label for="category" class="form-label text-gray-800 dark:text-gray-200">Category</label>
                        <select class="form-select bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100"
                            id="category" name="category">
                            <option value="instrumental">Instrumental</option>
                            <option value="loopkit">Loopkit</option>
                            <option value="drumkit">Drumkit</option>
                            <option value="fx">MultiKit</option>
                            <!-- more if needed -->
                        </select>
                    </div>

                    <!-- Audio File -->
                    <div id="add-beat" class="mb-3">
                        <label for="audio" class="form-label text-gray-800 dark:text-gray-200">Audio File (.mp3 or .wav)</label>
                        <input type="file" class="block w-full text-sm text-gray-500 
              file:mr-4 file:py-2 file:px-4 
              file:rounded-full file:border-0 
              file:text-sm file:font-semibold 
              file:bg-blue-50 file:text-blue-700 
              hover:file:bg-blue-100" id="audio_file" name="audio_file" accept=".mp3,.wav" required>
                    </div>

                    <!-- Folder -->
                    <div id="add-folder" class="mb-3 hidden">
                        <label for="audio" class="form-label text-gray-800 dark:text-gray-200">Folder</label>
                        <input type="file" webkitdirectory directory multiple accept=".mp3,.wav" class="block w-full text-sm text-gray-500 
              file:mr-4 file:py-2 file:px-4 
              file:rounded-full file:border-0 
              file:text-sm file:font-semibold 
              file:bg-blue-50 file:text-blue-700 
              hover:file:bg-blue-100" id="audio_folder" name="audio_folder[]" accept=".mp3,.wav">
                    </div>
                    @if ($errors->has('audio_folder.*'))
                    <div class="text-red-500 mb-4">
                        Some files in the folder are not valid audio formats (.mp3 or .wav).
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
                    </div>

                    <!-- Tags -->
                    <div class="mb-3">
                        <label for="tags" class="form-label text-gray-800 dark:text-gray-200">Tags (comma-separated)</label>
                        <input type="text" class="form-control bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100" id="tags" name="tags">
                    </div>

                    <!-- Type Beat -->
                    <div class="mb-3">
                        <label for="type_beat" class="form-label text-gray-800 dark:text-gray-200">Type (artist style)</label>
                        <input type="text" class="form-control bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100" id="type_beat" name="type_beat">
                    </div>


                    <div class="mb-4">
                        <label for="is_private" class="block font-semibold mb-1 text-red-800 dark:text-red-800">Private?</label>
                        <input
                            type="checkbox"
                            name="is_private"
                            id="is_private"
                            value="1"
                            @checked(old('is_private'))>
                        <span class="ml-2 text-sm text-gray-600">
                            Check this box to make the beat private (only you can see it).
                        </span>
                    </div>

                    <!-- Submit -->
                    <button type="submit"
                        id="submitBtn"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                        Save
                    </button>

                </form>
            </div>
        </div>
    </div>

</x-app-layout>