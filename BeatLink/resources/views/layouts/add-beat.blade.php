<!-- resources/views/layouts/add-beat.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add Beat') }}
        </h2>
    </x-slot>

    <!-- Main content slot -->
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                {{ $slot }}
            </div>
        </div>
    </div>
</x-app-layout>