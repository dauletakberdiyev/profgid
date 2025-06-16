<div class="min-h-screen bg-white py-8 px-4">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-12 text-center">
            <h1 class="text-4xl font-light text-gray-900 mb-3 tracking-wide">Избранные профессии</h1>
            <div class="w-16 h-px bg-blue-600 mx-auto"></div>
        </div>

        <!-- Flash Message -->
        @if(session('message'))
            <div class="mb-8 text-center">
                <p class="text-green-600 font-medium">{{ session('message') }}</p>
            </div>
        @endif

        @if($favoriteProfessions->count() > 0)
            <!-- Desktop Table View -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-6 pr-8 text-sm font-medium text-gray-900 tracking-wider uppercase">
                                Профессия
                            </th>
                            <th class="text-left py-6 px-8 text-sm font-medium text-gray-900 tracking-wider uppercase">
                                Описание
                            </th>
                            <th class="text-left py-6 px-8 text-sm font-medium text-gray-900 tracking-wider uppercase">
                                Ключевые таланты
                            </th>
                            <th class="text-center py-6 pl-8 text-sm font-medium text-gray-900 tracking-wider uppercase">
                                Действия
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($favoriteProfessions as $profession)
                            <tr class="group hover:bg-gray-50/50 transition-colors duration-200">
                                <td class="py-8 pr-8 align-top">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $profession->name }}</h3>
                                            <div class="text-xs text-gray-500 uppercase tracking-wide">
                                                ID: {{ $profession->id }}
                                            </div>
                                        </div>
                                        <button wire:click="toggleExpanded({{ $profession->id }})" 
                                            class="text-gray-400 hover:text-blue-600 transition-colors duration-200 p-1 ml-4"
                                            title="{{ in_array($profession->id, $expandedProfessions) ? 'Скрыть детали' : 'Показать детали' }}">
                                            <svg class="w-5 h-5 transform transition-transform duration-200 {{ in_array($profession->id, $expandedProfessions) ? 'rotate-180' : '' }}" 
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                                <td class="py-8 px-8 align-top max-w-xs">
                                    @if(in_array($profession->id, $expandedProfessions))
                                        <p class="text-sm text-gray-700 leading-relaxed">
                                            {{ $profession->description }}
                                        </p>
                                    @else
                                        <p class="text-sm text-gray-500 italic">
                                            Нажмите стрелку для просмотра описания
                                        </p>
                                    @endif
                                </td>
                                <td class="py-8 px-8 align-top">
                                    @if(in_array($profession->id, $expandedProfessions))
                                        <div class="space-y-1">
                                            @foreach($profession->talents as $talent)
                                                <div class="text-sm text-gray-800 border-l-2 border-blue-600 pl-3">
                                                    {{ $talent->name }}
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-500 italic">
                                            {{ $profession->talents->count() }} 
                                            {{ $profession->talents->count() === 1 ? 'талант' : ($profession->talents->count() < 5 ? 'таланта' : 'талантов') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-8 pl-8 text-center align-top">
                                    <button wire:click="removeProfession({{ $profession->id }})" 
                                        class="text-gray-400 hover:text-red-600 transition-colors duration-200 p-2"
                                        title="Удалить из избранного">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="lg:hidden space-y-6">
                @foreach($favoriteProfessions as $profession)
                    <div class="border border-gray-200 bg-white">
                        <table class="w-full">
                            <tbody>
                                <tr class="border-b border-gray-100">
                                    <td class="py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wide w-1/3">
                                        Профессия
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <div class="flex items-center justify-end space-x-2">
                                            <button wire:click="toggleExpanded({{ $profession->id }})" 
                                                class="text-gray-400 hover:text-blue-600 transition-colors duration-200 p-1"
                                                title="{{ in_array($profession->id, $expandedProfessions) ? 'Скрыть детали' : 'Показать детали' }}">
                                                <svg class="w-4 h-4 transform transition-transform duration-200 {{ in_array($profession->id, $expandedProfessions) ? 'rotate-180' : '' }}" 
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                                        d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </button>
                                            <button wire:click="removeProfession({{ $profession->id }})" 
                                                class="text-gray-400 hover:text-red-600 transition-colors duration-200 p-1"
                                                title="Удалить">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="border-b border-gray-100">
                                    <td colspan="2" class="py-4 px-6">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $profession->name }}</h3>
                                    </td>
                                </tr>
                                @if(in_array($profession->id, $expandedProfessions))
                                    <tr class="border-b border-gray-100">
                                        <td class="py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wide align-top">
                                            Описание
                                        </td>
                                        <td class="py-4 px-6 text-sm text-gray-700">
                                            {{ $profession->description }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wide align-top">
                                            Таланты
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="space-y-1">
                                                @foreach($profession->talents as $talent)
                                                    <div class="text-sm text-gray-800 border-l-2 border-blue-600 pl-3">
                                                        {{ $talent->name }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="2" class="py-4 px-6 text-center">
                                            <p class="text-sm text-gray-500 italic">
                                                {{ $profession->talents->count() }} 
                                                {{ $profession->talents->count() === 1 ? 'талант' : ($profession->talents->count() < 5 ? 'таланта' : 'талантов') }}
                                                • Нажмите стрелку для просмотра деталей
                                            </p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>

            <!-- Bottom Actions -->
            <div class="mt-16 text-center">
                <div class="inline-block">
                    <table class="mx-auto">
                        <thead>
                            <tr>
                                <th class="py-4 px-8 text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">
                                    Следующие шаги
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="py-6 px-8 text-center">
                                    <a href="{{ route('test') }}" 
                                       class="inline-block text-blue-600 hover:text-blue-800 font-medium text-sm tracking-wide uppercase transition-colors duration-200 border-b border-blue-600 hover:border-blue-800 pb-1">
                                        Пройти тест заново
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <!-- Empty State Table -->
            <div class="text-center">
                <table class="mx-auto max-w-2xl">
                    <thead>
                        <tr>
                            <th class="py-6 text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">
                                Состояние
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="py-12 px-8 text-center">
                                <div class="w-16 h-16 mx-auto mb-6 border border-gray-200 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" 
                                            d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                    </svg>
                                </div>
                                <h2 class="text-2xl font-light text-gray-900 mb-4">Нет избранных профессий</h2>
                                <p class="text-gray-600 mb-8 max-w-md">Добавьте профессии в избранное или получите рекомендации на основе ваших результатов тестирования</p>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-6 text-center border-t border-gray-200">
                                <a href="{{ route('test') }}" 
                                   class="inline-block text-blue-600 hover:text-blue-800 font-medium text-sm tracking-wide uppercase transition-colors duration-200 border-b border-blue-600 hover:border-blue-800 pb-1">
                                    Пройти тест талантов
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
