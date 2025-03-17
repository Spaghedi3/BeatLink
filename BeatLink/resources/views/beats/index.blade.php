<!-- resources/views/beats/index.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('All Beats') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="container mt-5">
                        @auth
                        <!-- Button to add a beat (visible only to logged-in users) -->
                        <a href="{{ route('beats.create') }}" class="btn btn-primary mb-4">
                            Add Beat
                        </a>
                        @else
                        <!-- Encourage guests to log in or register -->
                        <p>
                            <a href="{{ route('login') }}">Log in</a> or
                            <a href="{{ route('register') }}">register</a> to add your own beats.
                        </p>
                        @endauth
                        @if($beats->isEmpty())
                        <!-- Empty State -->
                        <div class="alert alert-info">
                            <h1>No Beats Yet</h1>
                            <p>It looks like there are no beats available. Why not create the first one?</p>


                        </div>
                        @else
                        <!-- Beats List -->
                        <div class="row">
                            @foreach($beats as $beat)
                            <div class="col-md-4 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $beat->name }}</h5>
                                        <audio controls class="w-100">
                                            <source src="{{ Storage::url($beat->file_path) }}" type="audio/mpeg">
                                            Your browser does not support the audio element.
                                        </audio>

                                        <p class="card-text">
                                            <strong>Tags:</strong> {{ $beat->tags }}
                                        </p>
                                        <p class="card-text">
                                            <strong>Category:</strong> {{ $beat->category }}
                                        </p>
                                        <p class="card-text">
                                            <strong>Type Beat:</strong> {{ $beat->type_beat }}
                                        </p>
                                        <p class="card-text">
                                            <small>Date Added: {{ $beat->created_at->format('d-m-Y') }}</small>
                                        </p>

                                        @if(auth()->check() && auth()->id() === $beat->user_id)

                                        <!-- Edit and Delete Buttons for Owner -->
                                        <a href="{{ route('beats.edit', $beat) }}" class="btn btn-secondary">Edit</a>
                                        <!-- Delete form/button here -->
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>