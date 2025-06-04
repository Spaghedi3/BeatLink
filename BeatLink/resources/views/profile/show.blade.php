<x-app-layout>
    <div class="min-h-[90vh] text-white px-6 pt-8 pb-12 flex flex-col justify-center items-center">
        <h1 class="text-2xl font-bold mb-5 text-center">{{ $user->username}}'s Profile </h1>

        {{-- Cards Container --}}
        <div class="w-full max-w-[85vw] flex flex-col md:flex-row justify-center items-stretch gap-12">

            {{-- Profile Card --}}
            <div class="relative dark:bg-gray-800 rounded-xl p-10 w-full md:w-[38%] flex flex-col items-center justify-center shadow-lg">
                {{-- Report Flag Icon (top-right) --}}
                @if (auth()->check() && auth()->id() !== $user->id)
                <a href="{{ route('report.user', $user->id) }}"
                    class="absolute top-3 right-3 text-red-500 hover:text-red-700"
                    title="Report user">
                    <svg viewBox="0 0 24 24" version="1.1" xml:space="preserve"
                        xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                        fill="currentColor" class="w-6 h-6">
                        <path d="M17.7,4.7c-1.4-0.7-3-0.7-4.4,0c-0.8,0.4-1.8,0.4-2.6,0l-0.4-0.2C9.3,4,8.1,3.9,6.9,4.1L6.2,4.3C5,4.6,4.2,5.6,4,6.9 
                            C4,6.9,4,7,4,7v0.2v6.4V19c0,0.6,0.4,1,1,1s1-0.4,1-1v-4.6L7.4,14c0.7-0.2,1.4-0.1,2,0.2l0.5,0.2c0.7,0.3,1.4,0.5,2.2,0.5 
                            c0.8,0,1.5-0.2,2.3-0.5c0.8-0.4,1.8-0.4,2.6,0h0c0.7,0.3,1.5,0.3,2.1-0.1c0.7-0.4,1.1-1.1,1.1-1.9v-4C20,6.9,19.1,5.5,17.7,4.7z 
                            M18,12.5c0,0.1-0.1,0.1-0.1,0.2c-0.1,0-0.1,0-0.2,0c-1.4-0.7-3-0.7-4.4,0c-0.8,0.4-1.8,0.4-2.6,0l-0.5-0.2 
                            c-0.7-0.3-1.4-0.5-2.2-0.5c-0.4,0-0.8,0-1.2,0.1L6,12.3V7.2c0-0.4,0.3-0.8,0.7-0.9l0.7-0.2c0.7-0.2,1.4-0.1,2,0.2l0.4,0.2 
                            c1.4,0.7,3,0.7,4.4,0c0.8-0.4,1.8-0.4,2.6,0c0.8,0.4,1.2,1.2,1.2,2V12.5z"></path>
                    </svg>
                </a>
                @endif
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
        </div>
        @else
        <br>
        <div class="">
            <div class="text-center">
                <a href="{{ route('user', ['id' => $user->id]) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
                    Message me
                </a>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>