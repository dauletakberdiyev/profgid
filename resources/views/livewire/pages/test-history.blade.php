<div class="min-h-screen bg-white py-8 px-4">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-12 text-center">
            <h1 class="text-3xl font-light text-gray-900 mb-2">
                История сдачи тестов
            </h1>
        </div>
        <!-- Filters and Search -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <!-- Filters -->
                <div class="flex flex-col sm:flex-row flex-wrap gap-3">
                    <select wire:model.live="statusFilter" 
                            class="px-0 py-2 border-0 border-b border-gray-200 focus:border-blue-500 focus:ring-0 bg-transparent text-sm">
                        <option value="all">Все тесты</option>
                        <option value="completed">Завершенные</option>
                        <option value="pending">В процессе</option>
                        <option value="failed">Не завершенные</option>
                    </select>
                    <select wire:model.live="timeFilter" 
                            class="px-0 py-2 border-0 border-b border-gray-200 focus:border-blue-500 focus:ring-0 bg-transparent text-sm">
                        <option value="all">За все время</option>
                        <option value="month">За месяц</option>
                        <option value="week">За неделю</option>
                    </select>
                    <select wire:model.live="paymentFilter" 
                            class="px-0 py-2 border-0 border-b border-gray-200 focus:border-blue-500 focus:ring-0 bg-transparent text-sm">
                        <option value="all">Все тесты</option>
                        <option value="paid">Оплаченные</option>
                        <option value="unpaid">Неоплаченные</option>
                    </select>
                </div>
                
                <!-- Search -->
                <div class="relative w-full lg:w-64">
                    <input wire:model.live.debounce.300ms="search" 
                           type="text" 
                           placeholder="Поиск..." 
                           class="w-full px-0 py-2 pl-8 border-0 border-b border-gray-200 focus:border-blue-500 focus:ring-0 bg-transparent text-sm placeholder-gray-400">
                    <svg class="absolute left-0 top-2.5 h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
        </div>
        <!-- Desktop Table View -->
        <div class="hidden lg:block">
            <div class="border border-gray-100 rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Тест</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Статус</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Прогресс</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Тариф</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-50">
                        @forelse ($filteredTests as $test)
                            <tr class="hover:bg-gray-25 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div class="h-8 w-8 rounded-full bg-blue-50 flex items-center justify-center">
                                                <svg class="h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">{{ $test['name'] }}</div>
                                            <div class="text-xs text-gray-500">{{ Str::limit($test['session_id'], 8) }}...</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $test['date'] }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($test['status'] === 'completed')
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-50 text-green-700 border border-green-200">Завершен</span>
                                    @elseif ($test['status'] === 'in_progress')
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-yellow-50 text-yellow-700 border border-yellow-200">В процессе</span>
                                    @elseif ($test['status'] === 'started')
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-blue-50 text-blue-700 border border-blue-200">Начат</span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-red-50 text-red-700 border border-red-200">Прерван</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $test['result'] }}</div>
                                    @if($test['status'] === 'completed')
                                        <div class="w-full bg-gray-100 rounded-full h-1.5 mt-1">
                                            <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $test['completion_percentage'] }}%"></div>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">{{ round($test['completion_percentage'], 1) }}%</div>
                                    @elseif(in_array($test['status'], ['started', 'in_progress']))
                                        <div class="w-full bg-gray-100 rounded-full h-1.5 mt-1">
                                            <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ $test['completion_percentage'] }}%"></div>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">{{ round($test['completion_percentage'], 1) }}%</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($test['selected_plan'])
                                        @if($test['selected_plan'] === 'talents')
                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-50 text-gray-700 border border-gray-200">Таланты</span>
                                        @elseif($test['selected_plan'] === 'talents_spheres')
                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-blue-50 text-blue-700 border border-blue-200">Таланты + Сферы</span>
                                        @elseif($test['selected_plan'] === 'talents_spheres_professions')
                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-purple-50 text-purple-700 border border-purple-200">Полный</span>
                                        @endif
                                    @else
                                        <span class="text-sm text-gray-400">Не выбран</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ $test['action_url'] }}" 
                                       class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 border border-blue-600 rounded-lg transition-colors duration-200">
                                        {{ $test['action_text'] }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">
                                    Нет данных для отображения
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Card View -->
        <div class="lg:hidden space-y-4">
            @forelse ($filteredTests as $test)
                <div class="bg-gray-50 border border-gray-100 rounded-lg p-4">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-8 w-8">
                                <div class="h-8 w-8 rounded-full bg-blue-50 flex items-center justify-center">
                                    <svg class="h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">{{ $test['name'] }}</div>
                                <div class="text-xs text-gray-500">{{ $test['date'] }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            @if ($test['status'] === 'completed')
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-50 text-green-700 border border-green-200">Завершен</span>
                            @elseif ($test['status'] === 'in_progress')
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-yellow-50 text-yellow-700 border border-yellow-200">В процессе</span>
                            @elseif ($test['status'] === 'started')
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-blue-50 text-blue-700 border border-blue-200">Начат</span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-red-50 text-red-700 border border-red-200">Прерван</span>
                            @endif
                        </div>
                    </div>

                    @if($test['status'] === 'completed' || in_array($test['status'], ['started', 'in_progress']))
                        <div class="mb-3">
                            <div class="flex justify-between text-xs text-gray-600 mb-1">
                                <span>Прогресс</span>
                                <span>{{ round($test['completion_percentage'], 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                @if($test['status'] === 'completed')
                                    <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $test['completion_percentage'] }}%"></div>
                                @else
                                    <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ $test['completion_percentage'] }}%"></div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-center gap-4 text-xs text-gray-600">
                            @if($test['selected_plan'])
                                <div>
                                    <span class="font-medium">Тариф:</span>
                                    @if($test['selected_plan'] === 'talents')
                                        <span class="text-gray-700">Таланты</span>
                                    @elseif($test['selected_plan'] === 'talents_spheres')
                                        <span class="text-blue-700">Таланты + Сферы</span>
                                    @elseif($test['selected_plan'] === 'talents_spheres_professions')
                                        <span class="text-purple-700">Полный</span>
                                    @endif
                                </div>
                            @endif
                            
                            @if ($test['is_paid'])
                                <div class="flex items-center text-green-600">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Оплачен
                                </div>
                            @else
                                <div class="flex items-center text-red-600">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                    Не оплачен
                                </div>
                            @endif
                        </div>
                        
                        <a href="{{ $test['action_url'] }}" 
                           class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 border border-blue-600 rounded-lg transition-colors duration-200">
                            {{ $test['action_text'] }}
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <p class="text-gray-500">Нет данных для отображения</p>
                </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        <div class="mt-8">
            {{ $filteredTests->links('livewire.pagination-links') }}
        </div>
        
        
    </div>
</div>