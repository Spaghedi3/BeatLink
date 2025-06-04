<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
            {{ __('Report Track') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            {{-- Error Summary --}}
            @if ($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Report Form --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <form action="{{ route('report.track.submit', $track->id) }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="block font-medium text-gray-700 dark:text-gray-200">
                            Reporting track:
                        </label>
                        <p class="text-gray-900 dark:text-gray-100 font-semibold mt-1">
                            {{ $track->title }}
                        </p>
                    </div>

                    {{-- Reason Dropdown --}}
                    <div class="mb-4">
                        <label for="reason" class="block font-medium text-gray-700 dark:text-gray-200">
                            Select a reason
                        </label>
                        <select id="reason" name="reason"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                                   bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            <option value="Inappropriate content">Inappropriate content</option>
                            <option value="Spam or misleading">Spam or misleading</option>
                            <option value="Hate speech or abuse">Hate speech or abuse</option>
                            <option value="">Other (write below)</option>
                        </select>
                    </div>

                    {{-- Optional Custom Reason --}}
                    <div class="mb-4">
                        <label for="custom_reason" class="block font-medium text-gray-700 dark:text-gray-200">
                            Write your own reason (optional)
                        </label>
                        <textarea id="custom_reason" name="reason"
                            rows="4"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                                   bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100"></textarea>
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded">
                        Submit Report
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>