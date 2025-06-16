<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" type="button"
        class="flex items-center gap-x-2 text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none">
        <span>{{ Auth::user()->name }}</span>
        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
            fill="currentColor">
            <path fill-rule="evenodd"
                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                clip-rule="evenodd" />
        </svg>
    </button>

    <!-- Dropdown menu -->
    <div x-show="open" @click.away="open = false"
        class="absolute right-0 mt-2 w-72 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
        role="menu" aria-orientation="vertical" aria-labelledby="user-menu">
        
        <!-- Flash Message -->
        @if(session('message'))
            <div class="p-3 mb-2 bg-green-50 border-l-4 border-green-400">
                <p class="text-sm text-green-700">{{ session('message') }}</p>
            </div>
        @endif


        <!-- Regular Menu Items -->
        <div class="py-1">
            <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem">{{ __('messages.profile') }}</a>
            <a href="{{ route('settings') }}"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem">{{ __('messages.settings') }}</a>
            <a href="{{ route('test.history') }}"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem">{{ __('messages.test_history') }}</a>
            
            <!-- My Professions Link -->
            <a href="{{ route('my-professions') }}"
                class="block px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 font-medium"
                role="menuitem">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                    Мои профессии
                    @if($favoriteProfessions->count() > 0)
                        <span class="ml-auto bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded-full">
                            {{ $favoriteProfessions->count() }}
                        </span>
                    @endif
                </div>
            </a>

            <a href="{{ route('my-spheres') }}"
                class="block px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 font-medium"
                role="menuitem">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                    Мои сферы
                    @if($favoriteSpheres->count() > 0)
                        <span class="ml-auto bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded-full">
                            {{ $favoriteSpheres->count() }}
                        </span>
                    @endif
                </div>
            </a>
            
            {{-- <!-- Profession Recommendations Link -->
            @if($favoriteProfessions->count() > 0)
                <a href="{{ route('profession-recommendations') }}"
                    class="block px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 font-medium"
                    role="menuitem">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                        Рекомендации профессий
                    </div>
                </a>
            @endif --}}
            
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    role="menuitem">{{ __('messages.logout') }}</button>
            </form>
        </div>
    </div>
</div>
