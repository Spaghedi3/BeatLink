<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
            Chatify Conversations of {{ $reported->username }} and {{ $reporter->username }}
        </h2>
    </x-slot>

    <div class="max-w-6xl mx-auto py-10 sm:px-6 lg:px-8 space-y-6">
        @forelse ($messages as $message)
        <div class="mb-2 {{ $message->from_id === $reporter->id ? 'text-blue-400' : 'text-red-400' }}">
            <strong>
                {{ $message->from_id === $reporter->id ? $reporter->username : $reported->username }}
            </strong>:
            {{ $message->body }}
            <span class="text-xs text-gray-400 ml-2">
                ({{ $message->created_at->format('Y-m-d H:i') }})
            </span>
        </div>
        @empty
        <p class="text-gray-400">No messages found between these users.</p>
        @endforelse
    </div>

    <button id="myBtn"
        title="Go to top"
        class="fixed bottom-8 right-8 bg-blue-600 text-white px-4 py-2 rounded shadow hidden z-50">
        Top
    </button>
</x-app-layout>