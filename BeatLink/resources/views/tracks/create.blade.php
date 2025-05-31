<!-- resources/views/tracks/create.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
            {{ __('Add a New Track') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            {{-- ▼ Error Summary ▼ --}}
            @if ($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <form
                    id="track-form"
                    action="{{ route('tracks.store') }}"
                    method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    {{-- Name --}}
                    <div class="mb-4">
                        <label for="name" class="block font-medium text-gray-700 dark:text-gray-200">
                            Name
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                     bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Category (non-artists only) --}}
                    @unless (auth()->user()->is_artist)
                    <div class="mb-4">
                        <label for="category" class="block font-medium text-gray-700 dark:text-gray-200">
                            Category
                        </label>
                        <select
                            id="category"
                            name="category"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                       bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            @foreach (['instrumental','loopkit','drumkit','multikit'] as $cat)
                            <option
                                value="{{ $cat }}"
                                {{ old('category') === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                            @endforeach
                        </select>
                        @error('category')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    @endunless

                    {{-- Single-file upload --}}
                    <div id="add-track" class="mb-4">
                        <label for="audio_file" class="block font-medium text-gray-700 dark:text-gray-200">
                            Audio File (.mp3 or .wav)
                        </label>
                        <input
                            type="file"
                            id="audio_file"
                            name="audio_file"
                            accept=".mp3,.wav"
                            class="mt-1 block w-full text-gray-700 dark:text-gray-200
                     file:mr-4 file:py-2 file:px-4
                     file:rounded-full file:border-0
                     file:text-sm file:font-semibold
                     file:bg-blue-50 file:text-blue-700
                     hover:file:bg-blue-100">
                        @error('audio_file')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Folder upload (kits) --}}
                    <div id="add-folder" class="mb-4 hidden">
                        <label for="audio_folder" class="block font-medium text-gray-700 dark:text-gray-200">
                            Audio Folder (mp3 or wav)
                        </label>
                        <input
                            type="file"
                            id="audio_folder"
                            name="audio_folder[]"
                            accept=".mp3,.wav"
                            webkitdirectory directory multiple
                            class="mt-1 block w-full text-gray-700 dark:text-gray-200
                     file:mr-4 file:py-2 file:px-4
                     file:rounded-full file:border-0
                     file:text-sm file:font-semibold
                     file:bg-blue-50 file:text-blue-700
                     hover:file:bg-blue-100">
                        @error('audio_folder.*')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Picture --}}
                    <div class="mb-4">
                        <label for="picture" class="block font-medium text-gray-700 dark:text-gray-200">
                            Cover Picture (optional)
                        </label>
                        <input
                            type="file"
                            id="picture"
                            name="picture"
                            accept="image/*"
                            class="mt-1 block w-full text-gray-700 dark:text-gray-200
                     file:mr-4 file:py-2 file:px-4
                     file:rounded-full file:border-0
                     file:text-sm file:font-semibold
                     file:bg-blue-50 file:text-blue-700
                     hover:file:bg-blue-100">
                        @error('picture')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tags --}}
                    <div class="mb-4">
                        <label for="tags" class="block font-medium text-gray-700 dark:text-gray-200">
                            Tags (comma‐separated)
                        </label>
                        <input
                            type="text"
                            id="tags"
                            name="tags"
                            value="{{ old('tags') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                     bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    </div>

                    {{-- Types --}}
                    <div class="mb-4">
                        <label for="types" class="block font-medium text-gray-700 dark:text-gray-200">
                            Types (artist style)
                        </label>
                        <input
                            type="text"
                            id="types"
                            name="types"
                            value="{{ old('types') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                     bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    </div>

                    {{-- Private --}}
                    <div class="mb-6 flex items-center">
                        <input
                            type="checkbox"
                            id="is_private"
                            name="is_private"
                            value="1"
                            {{ old('is_private') ? 'checked' : '' }}
                            class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                        <label for="is_private" class="ml-2 text-sm text-gray-700 dark:text-gray-200">
                            Make this track private
                        </label>
                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
                        Save Track
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>