<!-- resources/views/for-you.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="flex font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('For You Page') }}
        </h2>
    </x-slot>

    <div class="pt-6 pb-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('tracks._tracks-grid', ['tracks'=> $tracks, 'showAddButton' => false])
        </div>
    </div>
</x-app-layout>