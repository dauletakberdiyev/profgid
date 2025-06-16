<div class="min-h-screen bg-white py-4 md:py-8 px-4">
    <div class="max-w-7xl mx-auto">
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

        <!-- Filters and Search -->
        <div class="bg-gray-50 rounded-lg p-4 md:p-6 mb-6 md:mb-8">
            <div class="space-y-4">
                <!-- Search -->
                <div class="w-full">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input wire:model.live="search" 
                               type="text" 
                               class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-lg bg-white placeholder-gray-400 focus:outline-none focus:border-blue-500" 
                               placeholder="Поиск по сферам...">
                    </div>
                </div>

                <!-- Filters Row -->
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-6">
                    <!-- Show inactive toggle -->
                    <label class="flex items-center">
                        <input wire:model.live="showInactive" 
                               type="checkbox" 
                               class="rounded border-gray-300 text-blue-600 focus:ring-0">
                        <span class="ml-2 text-sm text-gray-600">Показать неактивные</span>
                    </label>

                    <!-- Sort options -->
                    <div class="flex items-center gap-2 flex-1">
                        <span class="text-sm text-gray-600 hidden sm:inline">Сортировка:</span>
                        <select wire:model.live="sortBy" 
                                class="flex-1 sm:flex-initial text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none focus:border-blue-500">
                            <option value="sort_order">По порядку</option>
                            <option value="name">По названию</option>
                            <option value="professions_count">По количеству профессий</option>
                        </select>
                        
                        <button wire:click="sortBy('{{ $sortBy }}')" 
                                class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                            @if($sortDirection === 'asc')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                                </svg>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                                </svg>
                            @endif
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Spheres Table -->
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-medium text-gray-900">Сферы профессий</h2>
                <p class="text-sm text-gray-500 mt-1">Нажмите на сферу, чтобы увидеть профессии</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <tbody class="divide-y divide-gray-100">
                        @foreach($spheres as $sphere)
                            <!-- Основная строка сферы -->
                            <tr class="hover:bg-gray-50 transition-colors" >
                                <td class="px-4 md:px-6 py-4 md:py-5">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center cursor-pointer flex-1 min-w-0" wire:click="toggleSphere({{ $sphere->id }})">
                                            <!-- Кнопка раскрытия аккордеона -->
                                            <div class="flex items-center mr-3 md:mr-4 flex-shrink-0">
                                                @if(in_array($sphere->id, $expandedSpheres))
                                                    <svg class="w-4 h-4 md:w-5 md:h-5 text-gray-400 transform rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4 md:w-5 md:h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                    </svg>
                                                @endif
                                            </div>
                                            
                                            <!-- Color indicator -->
                                            <div class="w-3 h-3 md:w-4 md:h-4 rounded-full mr-3 md:mr-4 flex-shrink-0" style="background-color: {{ $sphere->color }}"></div>
                                            
                                            <div class="flex-1 min-w-0">
                                                <div class="text-sm md:text-base font-medium text-gray-900 truncate">
                                                    {{ $sphere->localized_name }}
                                                </div>
                                                <!-- Количество профессий - на мобильных под названием -->
                                                <div class="text-xs md:text-sm text-gray-500 mt-1 md:hidden">
                                                    {{ $sphere->professions_count }} {{ $sphere->professions_count == 1 ? 'профессия' : ($sphere->professions_count < 5 ? 'профессии' : 'профессий') }}
                                                </div>
                                            </div>
                                            
                                            <!-- Количество профессий - на десктопе справа -->
                                            <div class="hidden md:block text-sm text-gray-500 mr-4 flex-shrink-0">
                                                {{ $sphere->professions_count }} {{ $sphere->professions_count == 1 ? 'профессия' : ($sphere->professions_count < 5 ? 'профессии' : 'профессий') }}
                                            </div>
                                        </div>
                                        
                                        <!-- Like Button -->
                                        @auth
                                            <button wire:click="likeSphere({{ $sphere->id }})"
                                                    class="p-2 {{ $sphere->is_favorite ? 'text-red-500 hover:text-red-600' : 'text-gray-400 hover:text-red-500' }} transition-colors flex-shrink-0"
                                                    title="{{ $sphere->is_favorite ? 'Убрать из избранного' : 'Добавить в избранное' }}">
                                                <svg class="w-4 h-4" {{ $sphere->is_favorite ? 'fill="currentColor"' : 'fill="none"' }} stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                                </svg>
                                            </button>
                                        @endauth
                                    </div>
                                </td>                            
                            </tr>
                            
                            <!-- Описание сферы при раскрытии -->
                            @if(in_array($sphere->id, $expandedSpheres) && $sphere->localized_description)
                                <tr>
                                    <td class="px-0 py-0">
                                        <div class="bg-blue-50 border-l-4 border-l-blue-500">
                                            <div class="px-4 md:px-6 py-3 md:py-4">
                                                <p class="text-sm text-gray-700">{{ $sphere->localized_description }}</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            
                            <!-- Раскрытые профессии (аккордеон) -->
                            @if(in_array($sphere->id, $expandedSpheres) && $sphere->loadedProfessions->count() > 0)
                                <tr>
                                    <td class="px-0 py-0">
                                        <div class="bg-gray-50 border-l-4 border-l-blue-500">
                                            <div class="px-4 md:px-6 py-4 md:py-6">
                                                <h4 class="text-sm font-medium text-gray-900 mb-4">
                                                    Профессии в сфере "{{ $sphere->localized_name }}"
                                                </h4>
                                                
                                                <!-- Профессии в табличном формате для десктопа -->
                                                <div class="hidden md:block bg-white border border-gray-200 rounded-lg overflow-hidden">
                                                    <table class="w-full">
                                                        <thead class="bg-gray-50">
                                                            <tr>
                                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Профессия</th>
                                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-100">
                                                            @foreach($sphere->loadedProfessions as $profession)
                                                                <!-- Основная строка профессии -->
                                                                <tr class="hover:bg-gray-50 cursor-pointer" wire:click="toggleProfessionDescription({{ $profession->id }})">
                                                                    <td class="px-4 py-3">
                                                                        <div class="flex items-center">
                                                                            @if($profession->description)
                                                                                <div class="mr-2 p-1 text-gray-400">
                                                                                    @if(in_array($profession->id, $expandedProfessions))
                                                                                        <svg class="w-4 h-4 transform rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                                                        </svg>
                                                                                    @else
                                                                                        <svg class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                                                        </svg>
                                                                                    @endif
                                                                                </div>
                                                                            @else
                                                                                <div class="w-6 h-6 mr-2"></div>
                                                                            @endif
                                                                            <div>
                                                                                <span class="font-medium text-gray-900 text-sm">{{ $profession->name }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td class="px-4 py-3">
                                                                        @auth
                                                                            <button class="p-2 text-gray-400 hover:text-red-500 transition-colors"
                                                                                    onclick="event.stopPropagation(); addToFavorites({{ $profession->id }})"
                                                                                    title="Добавить в избранное">
                                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                                                                </svg>
                                                                            </button>
                                                                        @endauth
                                                                    </td>
                                                                </tr>
                                                                
                                                                <!-- Развернутое описание профессии -->
                                                                @if(in_array($profession->id, $expandedProfessions) && $profession->description)
                                                                    <tr>
                                                                        <td colspan="2" class="px-4 py-3 bg-blue-50">
                                                                            <div class="text-sm text-gray-700">
                                                                                {{ $profession->description }}
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <!-- Профессии в виде карточек для мобильных -->
                                                <div class="md:hidden space-y-3">
                                                    @foreach($sphere->loadedProfessions as $profession)
                                                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                                                            <div class="flex items-center justify-between mb-2">
                                                                <div class="flex items-center cursor-pointer flex-1 min-w-0" wire:click="toggleProfessionDescription({{ $profession->id }})">
                                                                    @if($profession->description)
                                                                        <div class="mr-2 text-gray-400 flex-shrink-0">
                                                                            @if(in_array($profession->id, $expandedProfessions))
                                                                                <svg class="w-4 h-4 transform rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                                                </svg>
                                                                            @else
                                                                                <svg class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                                                </svg>
                                                                            @endif
                                                                        </div>
                                                                    @endif
                                                                    <span class="font-medium text-gray-900 text-sm truncate">{{ $profession->name }}</span>
                                                                </div>
                                                                @auth
                                                                    <button class="p-2 text-gray-400 hover:text-red-500 transition-colors flex-shrink-0"
                                                                            onclick="event.stopPropagation(); addToFavorites({{ $profession->id }})"
                                                                            title="Добавить в избранное">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                                                        </svg>
                                                                    </button>
                                                                @endauth
                                                            </div>
                                                            
                                                            <!-- Развернутое описание профессии -->
                                                            @if(in_array($profession->id, $expandedProfessions) && $profession->description)
                                                                <div class="pt-3 border-t border-gray-100">
                                                                    <div class="text-sm text-gray-700">
                                                                        {{ $profession->description }}
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                                
                                                @if($sphere->loadedProfessions->isEmpty())
                                                    <div class="text-center py-6 md:py-8">
                                                        <div class="text-gray-400 mb-2">
                                                            <svg class="w-6 h-6 md:w-8 md:h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-2.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 009.586 13H7"/>
                                                            </svg>
                                                        </div>
                                                        <p class="text-xs md:text-sm text-gray-500">В этой сфере пока нет доступных профессий</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @elseif(in_array($sphere->id, $expandedSpheres) && $sphere->loadedProfessions->count() === 0)
                                <tr>
                                    <td class="px-0 py-0">
                                        <div class="bg-gray-50 border-l-4 border-l-blue-500">
                                            <div class="px-4 md:px-6 py-4 md:py-6 text-center">
                                                <div class="text-gray-400 mb-2">
                                                    <svg class="w-6 h-6 md:w-8 md:h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-2.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 009.586 13H7"/>
                                                    </svg>
                                                </div>
                                                <p class="text-xs md:text-sm text-gray-500">В этой сфере пока нет доступных профессий</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
                
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
            </div>
        </div>

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
