<x-app-layout>
    <div class="min-h-[90vh] text-white px-6 pt-8 pb-12 flex flex-col justify-center items-center">
        <h1 class="text-2xl font-bold mb-5 text-center">{{ $user->username}}'s Profile </h1>

        {{-- Cards Container --}}
        <div class="w-full max-w-[85vw] flex flex-col md:flex-row justify-center items-stretch gap-12">

            {{-- Profile Card --}}
            <div class="dark:bg-gray-800 rounded-xl p-10 w-full md:w-[38%] flex flex-col items-center justify-center shadow-lg">
                <img src="{{ $user->profile_picture ? Storage::url($user->profile_picture) : asset('storage/images/default-profile.png') }}"
                    class="w-44 h-44 rounded-full object-cover mb-6 border-4 border-white"
                    alt="Profile Picture" />
                <p class="text-lg font-semibold mt-2">{{ $user->is_artist ? 'Artist' : 'Producer' }}</p>
                <p class="mt-2 underline underline-offset-4 decoration-dotted">{{ $user->email }}</p>
                <p class="mt-1 text-gray-300">{{ $user->phone ?? 'No phone number' }}</p>
            </div>

            {{-- Social Media + Button Column --}}
            <div class="w-full md:w-[38%] flex flex-col justify-between">
                {{-- Social Media Card --}}
                <div class="dark:bg-gray-800 rounded-xl p-10 shadow-md flex-grow">
                    <h2 class="text-xl font-bold mb-6 text-center">Social Media</h2>
                    <ul class="space-y-3 text-sm">
                        @foreach ([
                        'Beatstars Account' => $user->social_links['beatstars'] ?? null,
                        'Facebook Account' => $user->social_links['facebook'] ?? null,
                        'Instagram Account' => $user->social_links['instagram'] ?? null,
                        'Twitter (X) Account' => $user->social_links['twitter'] ?? null,
                        'TikTok Account' => $user->social_links['tiktok'] ?? null,
                        ] as $label => $link)
                        <li class="flex justify-between">
                            <span class="font-semibold">{{ $label }}:</span>
                            @if($link)
                            <a href="{{ $link }}" class="text-blue-300 hover:underline">
                                {{ parse_url($link, PHP_URL_HOST) }}
                            </a>
                            @else
                            <span class="text-gray-400">N/A</span>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Button aligned with bottom of profile card --}}
                <div class="mt-6 flex items-center justify-center">
                    <a href="{{ route('user.tracks', ['username' => $user->username]) }}"
                        class="w-full dark:bg-gray-800 hover:bg-[#1b2d44] text-white font-semibold py-3 px-6 rounded-full shadow-md transition text-lg text-center flex justify-center items-center gap-2">
                        Check out my tracks
                    </a>
                </div>

                <a href="{{ route('user', ['id' => $user->id]) }}">Message me</a>

            </div>
        </div>


        {{-- Edit Profile Link --}}
        @if(auth()->id() === $user->id)
        <br>
        <div class="">
            <div class="text-center">
                <a href="{{ route('profile.edit') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
                    Edit Profile
                </a>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>