<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Delete Beat') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <h3 class="text-lg font-semibold mb-4">Are you sure you want to delete this?</h3>
                <p class="mb-4">
                    <strong>Name:</strong> {{ $beat->name }}<br>
                    @if($beat->picture)
                    <img src="{{ Storage::url($beat->picture) }}" alt="Beat Picture" class="w-32 h-32 object-cover mt-2">
                    @endif
                </p>
                <p class="mb-4">This action cannot be undone.</p>

                <form action="{{ route('beats.destroy', $beat) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        Yes, Delete.
                    </button>
                    <a href="{{ route('beats.index') }}" class="ml-4 text-blue-500 hover:underline">
                        Cancel
                    </a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>