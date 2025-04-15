<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Username field -->
        <div class="mt-4">
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input
                id="username"
                class="block mt-1 w-full"
                type="text"
                name="username"
                value="{{ old('username') }}"
                required
                autofocus />
            @error('username')
            <div class="text-red-600">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Role Selection -->
        <div class="mt-6">
            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">How do you want to use this platform?</p>

            <div class="flex items-center gap-4">
                <label class="flex items-center space-x-2">
                    <input type="radio" name="is_artist" value="1" class="form-radio text-indigo-600" {{ old('is_artist') === '1' ? 'checked' : '' }}>
                    <span class="text-gray-700 dark:text-gray-300">I’m an Artist</span>
                </label>

                <label class="flex items-center space-x-2">
                    <input type="radio" name="is_artist" value="0" class="form-radio text-indigo-600" {{ old('is_artist') === '0' ? 'checked' : '' }}>
                    <span class="text-gray-700 dark:text-gray-300">I’m a Producer</span>
                </label>
            </div>

            @error('is_artist')
            <div class="text-red-600 mt-1">{{ $message }}</div>
            @enderror
        </div>


        <!-- Actions -->
        <div class="flex items-center justify-end mt-6">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>