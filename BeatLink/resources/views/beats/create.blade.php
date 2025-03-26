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
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('beats.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- Beat Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Beat Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>

                    <!-- Audio File -->
                    <div class="mb-3">
                        <label for="audio" class="form-label">Audio File (.mp3 or .wav)</label>
                        <input type="file" class="form-control" id="audio" name="audio" accept=".mp3,.wav" required>
                    </div>

                    <!-- Picture (optional) -->
                    <div class="mb-3">
                        <label for="picture" class="form-label">Cover Picture (optional)</label>
                        <input type="file" class="form-control" id="picture" name="picture" accept="image/*">
                    </div>

                    <!-- Tags -->
                    <div class="mb-3">
                        <label for="tags" class="form-label">Tags (comma-separated)</label>
                        <input type="text" class="form-control" id="tags" name="tags">
                    </div>

                    <!-- Category -->
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category">
                            <option value="instrumental">Instrumental</option>
                            <option value="loopkit">Loopkit</option>
                            <option value="drumkit">Drumkit</option>
                            <option value="fx">FX</option>
                            <!-- more if needed -->
                        </select>
                    </div>

                    <!-- Type Beat -->
                    <div class="mb-3">
                        <label for="type_beat" class="form-label">Type Beat (artist style)</label>
                        <input type="text" class="form-control" id="type_beat" name="type_beat">
                    </div>


                    <div class="mb-4">
                        <label for="is_private" class="block font-semibold mb-1">Private?</label>
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
                    <button type="submit" class="btn btn-primary">
                        Save Beat
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>