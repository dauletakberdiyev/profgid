<div class="min-h-screen bg-white py-4 md:py-8 px-4">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8 md:mb-12">
            <h1 class="text-2xl md:text-4xl font-light text-gray-900 mb-2 md:mb-3">Карта профессий</h1>
            <p class="text-base md:text-lg text-gray-600 font-light">Исследуйте различные сферы деятельности</p>
        </div>

        <!-- Flash Messages -->
        @if(session('sphere-added'))
            <div class="mb-6 mx-auto max-w-md">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-green-800 font-medium">{{ session('sphere-added') }}</p>
                    </div>
                </div>
            </div>
        @endif
        @if(session('profession-added'))
            <div class="mb-6 mx-auto max-w-md">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-green-800 font-medium">{{ session('profession-added') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('sphere-exists'))
            <div class="mb-6 mx-auto max-w-md">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-blue-800 font-medium">{{ session('sphere-exists') }}</p>
                    </div>
                </div>
            </div>
        @endif


        <!-- Spheres Table -->
        @foreach($spheres as $sphere)
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden transition-all duration-200 hover:border-gray-300 mb-3">
                <!-- Sphere Header -->
                <div class="p-3 cursor-pointer" wire:click="toggleSphere({{ $sphere->id }})">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 text-gray-500">{{ svg($sphere->icon ?? 'heroicon-o-home') }}</div>
                            <h3 class="text-base font-medium text-gray-900">{{ $sphere->localized_name }}</h3>
                        </div>

                        <div class="flex items-center space-x-1">
                            <!-- Info Button -->
                            <button wire:click.stop="showSphereInfo({{ $sphere->id }})"
                                    class="p-1 text-gray-400 hover:text-blue-500 transition-colors flex-shrink-0 mr-1"
                                    title="Показать информацию о сфере">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </button>

                            <!-- Like Button -->
                            @auth
                                <button wire:click.stop="likeSphere({{ $sphere->id }})"
                                        class="p-1 {{ $sphere->is_favorite ? 'text-red-500 hover:text-red-600' : 'text-gray-400 hover:text-red-500' }} transition-colors flex-shrink-0"
                                        title="{{ $sphere->is_favorite ? 'Убрать из избранного' : 'Добавить в избранное' }}">
                                    @if($sphere->is_favorite)
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                        </svg>
                                    @else
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                        </svg>
                                    @endif
                                </button>
                            @endauth

                            <div class="text-gray-400 transition-transform duration-200 {{ in_array($sphere->id, $expandedSpheres) ? 'rotate-90' : '' }}">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Expanded Content -->
                @if(in_array($sphere->id, $expandedSpheres))
                    <div class="border-t border-gray-100 bg-gray-50">
                        <!-- Description -->
{{--                        @if($sphere->localized_description)--}}
{{--                            <div class="p-3 border-b border-gray-200">--}}
{{--                                <p class="text-xs text-gray-700 leading-relaxed">{{ $sphere->localized_description }}</p>--}}
{{--                            </div>--}}
{{--                        @endif--}}

                        <!-- Professions List -->
                        @if($sphere->professions->count() > 0)
                            <div class="p-3">
                                <h4 class="text-xs font-medium text-gray-900 mb-2">Профессии в этой сфере:</h4>
                                <div class="grid grid-cols-1 gap-1">
                                    @foreach($sphere->professions as $profession)
                                        <div class="bg-white border flex justify-between items-center border-gray-200 rounded px-2 py-1 text-xs text-gray-700 hover:bg-gray-50 transition-colors">
                                            {{ $profession->name }}

                                            <div>
                                                @if($profession->description)
                                                    <button wire:click.stop="showProfessionInfo({{ $profession->id }})"
                                                            class="ml-2 p-1 text-gray-400 hover:text-blue-500 transition-colors flex-shrink-0"
                                                            title="Показать описание профессии">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                    </button>
                                                @endif

                                                @auth
                                                    <button wire:click.stop="likeProfession({{ $profession->id }})"
                                                            class="p-1 {{ $profession->is_favourite ? 'text-red-500 hover:text-red-600' : 'text-gray-400 hover:text-red-500' }} transition-colors flex-shrink-0"
                                                            title="{{ $profession->is_favourite ? 'Убрать из избранного' : 'Добавить в избранное' }}">
                                                        @if($profession->is_favourite)
                                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                                            </svg>
                                                        @else
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                                            </svg>
                                                        @endif
                                                    </button>
                                                @endauth
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            @if($spheres->isEmpty())
                <div class="text-center py-12 md:py-16 px-4">
                    <div class="text-gray-400 mb-3">
                        <svg class="w-8 h-8 md:w-12 md:h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-2.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 009.586 13H7"/>
                        </svg>
                    </div>
                    <h3 class="text-base md:text-lg font-medium text-gray-900 mb-2">Нет доступных сфер</h3>
                    <p class="text-sm md:text-base text-gray-500">Сферы профессий пока не созданы.</p>
                </div>
            @endif
        @endforeach

{{--        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">--}}
{{--            <div>--}}
{{--                <table class="w-full">--}}
{{--                    <tbody class="divide-y divide-gray-100">--}}
{{--                        @foreach($spheres as $sphere)--}}
{{--                            <!-- Основная строка сферы -->--}}
{{--                            <tr class="hover:bg-gray-50 transition-colors">--}}
{{--                                <td class="px-2 md:px-4 py-1 md:py-2">--}}
{{--                                    <div class="flex items-center justify-between">--}}
{{--                                        <div class="flex items-center flex-1 min-w-0 cursor-pointer" wire:click="toggleSphere({{ $sphere->id }})">--}}
{{--                                            <div class="flex-1 min-w-0">--}}
{{--                                                <div class="text-xs md:text-sm font-medium text-gray-900 truncate">--}}
{{--                                                    {{ $sphere->localized_name }}--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                            --}}
{{--                                            <!-- Аккордеон стрелка -->--}}
{{--                                            @if($sphere->professions->count() > 0)--}}
{{--                                                <div class="text-gray-400 transition-transform duration-200 mr-2 {{ in_array($sphere->id, $expandedSpheres) ? 'rotate-90' : '' }}">--}}
{{--                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
{{--                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" />--}}
{{--                                                    </svg>--}}
{{--                                                </div>--}}
{{--                                            @endif--}}
{{--                                        </div>--}}
{{--                                        --}}
{{--                                        <!-- Info Button -->--}}
{{--                                        <button wire:click.stop="showSphereInfo({{ $sphere->id }})"--}}
{{--                                                class="p-1 text-gray-400 hover:text-blue-500 transition-colors flex-shrink-0 mr-1"--}}
{{--                                                title="Показать информацию о сфере">--}}
{{--                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
{{--                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>--}}
{{--                                            </svg>--}}
{{--                                        </button>--}}
{{--                                        --}}
{{--                                        <!-- Like Button -->--}}
{{--                                        @auth--}}
{{--                                            <button wire:click.stop="likeSphere({{ $sphere->id }})"--}}
{{--                                                    class="p-1 {{ $sphere->is_favorite ? 'text-red-500 hover:text-red-600' : 'text-gray-400 hover:text-red-500' }} transition-colors flex-shrink-0"--}}
{{--                                                    title="{{ $sphere->is_favorite ? 'Убрать из избранного' : 'Добавить в избранное' }}">--}}
{{--                                                @if($sphere->is_favorite)--}}
{{--                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">--}}
{{--                                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>--}}
{{--                                                    </svg>--}}
{{--                                                @else--}}
{{--                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
{{--                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>--}}
{{--                                                    </svg>--}}
{{--                                                @endif--}}
{{--                                            </button>--}}
{{--                                        @endauth--}}
{{--                                    </div>--}}
{{--                                </td>                            --}}
{{--                            </tr>--}}
{{--                            --}}
{{--                            <!-- Раскрывающийся список профессий -->--}}
{{--                            @if(in_array($sphere->id, $expandedSpheres) && $sphere->professions->count() > 0)--}}
{{--                                <tr>--}}
{{--                                    <td class="px-2 md:px-4 py-0 border-t border-gray-100 bg-gray-50">--}}
{{--                                        <div class="py-2">--}}
{{--                                            <div class="grid grid-cols-1 gap-1">--}}
{{--                                                @foreach($sphere->professions as $profession)--}}
{{--                                                    <div class="bg-white border border-gray-200 rounded px-2 py-1 text-xs text-gray-700 hover:bg-gray-50 transition-colors">--}}
{{--                                                        <div class="flex items-center justify-between">--}}
{{--                                                            <span class="flex-1">{{ $profession->name }}</span>--}}
{{--                                                            @if($profession->description)--}}
{{--                                                                <button wire:click.stop="showProfessionInfo({{ $profession->id }})"--}}
{{--                                                                        class="ml-2 p-1 text-gray-400 hover:text-blue-500 transition-colors flex-shrink-0"--}}
{{--                                                                        title="Показать описание профессии">--}}
{{--                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
{{--                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>--}}
{{--                                                                    </svg>--}}
{{--                                                                </button>--}}
{{--                                                            @endif--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                @endforeach--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </td>--}}
{{--                                </tr>--}}
{{--                            @endif--}}
{{--                        @endforeach--}}
{{--                    </tbody>--}}
{{--                </table>--}}

{{--                @if($spheres->isEmpty())--}}
{{--                    <div class="text-center py-12 md:py-16 px-4">--}}
{{--                        <div class="text-gray-400 mb-3">--}}
{{--                            <svg class="w-8 h-8 md:w-12 md:h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
{{--                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-2.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 009.586 13H7"/>--}}
{{--                            </svg>--}}
{{--                        </div>--}}
{{--                        <h3 class="text-base md:text-lg font-medium text-gray-900 mb-2">Нет доступных сфер</h3>--}}
{{--                        <p class="text-sm md:text-base text-gray-500">Сферы профессий пока не созданы.</p>--}}
{{--                    </div>--}}
{{--                @endif--}}
{{--            </div>--}}
{{--        </div>--}}

        <!-- Modal for Sphere Info -->
        @if($showModal && $selectedSphere)
            <div class="fixed inset-0 bg-gray-500/75 flex items-center justify-center z-50" wire:click="closeModal">
                <div class="bg-white rounded-lg max-w-4xl w-full mx-4 max-h-[90vh] overflow-hidden" wire:click.stop>
                    <!-- Modal Header -->
                    <div class="px-4 py-3 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-medium text-gray-900">
                                {{ $selectedSphere->localized_name }}
                            </h3>
                            <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <div class="px-4 py-3 overflow-y-auto max-h-[calc(90vh-120px)]">
                        @if($selectedSphere->localized_description)
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Описание сферы</h4>
                                <p class="text-sm text-gray-700 leading-relaxed">{{ $selectedSphere->localized_description }}</p>
                            </div>
                        @else
                            <div class="text-center py-6">
                                <div class="text-gray-400 mb-2">
                                    <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-2.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 009.586 13H7"/>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-500">Описание для этой сферы пока не добавлено</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Modal for Profession Info -->
        @if($showProfessionModal && $selectedProfession)
            <div class="fixed inset-0 bg-gray-500/75 flex items-center justify-center z-50" wire:click="closeProfessionModal">
                <div class="bg-white rounded-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-hidden" wire:click.stop>
                    <!-- Modal Header -->
                    <div class="px-4 py-3 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-medium text-gray-900">
                                {{ $selectedProfession->name }}
                            </h3>
                            <button wire:click="closeProfessionModal" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <div class="px-4 py-3 overflow-y-auto max-h-[calc(90vh-120px)]">
                        @if($selectedProfession->description)
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Описание профессии</h4>
                                <p class="text-sm text-gray-700 leading-relaxed">{{ $selectedProfession->description }}</p>
                            </div>
                        @else
                            <div class="text-center py-6">
                                <div class="text-gray-400 mb-2">
                                    <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-2.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 009.586 13H7"/>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-500">Описание для этой профессии пока не добавлено</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

    </div>

    <script>
        // Handle sphere liked event
        window.addEventListener('sphere-liked', event => {
            const sphereId = event.detail.sphereId;
            showNotification('Сфера добавлена в избранное!', 'success');
        });

        function addToFavorites(professionId) {
            fetch('/profession/add-to-favorites', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ profession_id: professionId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Профессия добавлена в избранное!', 'success');
                } else {
                    showNotification(data.message || 'Ошибка при добавлении в избранное', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Произошла ошибка', 'error');
            });
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-lg text-white z-50 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
            notification.textContent = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    </script>
</div>
