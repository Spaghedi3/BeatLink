<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __("$ownerName's Tracks") }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($beats->isEmpty())
            <div class="bg-gray-800 text-white p-6 rounded">
                <h1 class="text-xl font-semibold mb-2">No Tracks Found</h1>
                <p>It looks like this user hasn't posted any tracks yet.</p>
            </div>
            @else
            @include('beats._beats-grid', ['beats' => $beats, 'showAddButton' => false])
            @endif
        </div>
    </div>
</x-app-layout>