<div class="min-h-screen bg-white py-4 px-4">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-6 text-center">
            <h1 class="text-2xl font-light text-gray-900 mb-1">{{ __('all.my_professions.title') }}</h1>
            <p class="text-sm text-gray-600">{{ __('all.my_professions.desc') }}</p>
        </div>

        <!-- Flash Message -->
{{--        @if(session('message'))--}}
{{--            <div class="mb-4 text-center">--}}
{{--                <p class="text-green-600 text-sm font-medium">{{ session('message') }}</p>--}}
{{--            </div>--}}
{{--        @endif--}}

        @if($favoriteProfessions->count() > 0)
            <!-- Professions List -->
            <div class="space-y-2">
                @foreach($favoriteProfessions as $profession)
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden transition-all duration-200 hover:border-gray-300">
                        <!-- Profession Header -->
                        <div class="p-3 cursor-pointer" wire:click="toggleExpanded({{ $profession->id }})">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2 flex-1 min-w-0">
                                    <h3 class="text-base font-medium text-gray-900 truncate">{{ $profession->localized_name }}</h3>
                                </div>

                                <div class="flex items-center space-x-1">
                                    <button wire:click.stop="removeProfession({{ $profession->id }})"
                                        class="text-gray-400 hover:text-red-600 transition-colors p-0.5 cursor-pointer"
                                        title="Удалить из избранного">
                                        <div class="w-4 h-4 text-gray-500">{{ svg('ionicon-heart-dislike-outline') }}</div>
                                    </button>

                                    @if($profession->description)
                                        <div class="text-gray-400 transition-transform duration-200 {{ in_array($profession->id, $expandedProfessions) ? 'rotate-90' : '' }}">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Expanded Content -->
                        @if(in_array($profession->id, $expandedProfessions) && $profession->description)
                            <div class="border-t border-gray-100 bg-gray-50">
                                <div class="p-3">
                                    <h4 class="text-xs font-medium text-gray-900 mb-2">{{ __('all.profession_map.professions.desc') }}</h4>
                                    <p class="text-xs text-gray-700 leading-relaxed">{{ $profession->localized_description }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                </div>
                <h2 class="text-xl font-light text-gray-900 mb-2">Нет избранных профессий</h2>
                <p class="text-gray-600 mb-6">Добавьте профессии в избранное для быстрого доступа</p>
                <a href="{{ route('talent-test') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    Пройти тест талантов
                </a>
            </div>
        @endif
    </div>
</div>
