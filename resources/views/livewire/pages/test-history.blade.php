<div class="min-h-screen bg-gray-50 py-6 px-4">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-medium text-gray-900">
                {{ __('all.test_history.title') }}
            </h1>
        </div>
        <!-- Filters and Search -->
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <!-- Filters -->
                <div class="flex flex-wrap gap-4">
                    <div class="relative">
                        <select wire:model.live="statusFilter"
                                class="appearance-none w-full sm:w-auto bg-white border border-gray-200 text-gray-700 py-2 px-3 pr-8 rounded-lg leading-tight focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm">
                            <option value="all">{{ __('all.test_history.filters.all_tests') }}</option>
                            <option value="completed">{{ __('all.test_history.filters.completed') }}</option>
                            <option value="pending">{{ __('all.test_history.filters.in_process') }}</option>
                            <option value="failed">{{ __('all.test_history.filters.not_complete') }}</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                        </div>
                    </div>
                    <div class="relative">
                        <select wire:model.live="timeFilter"
                                class="appearance-none w-full sm:w-auto bg-white border border-gray-200 text-gray-700 py-2 px-3 pr-8 rounded-lg leading-tight focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm">
                            <option value="all">{{ __('all.test_history.filters.all_time') }}</option>
                            <option value="month">{{ __('all.test_history.filters.month') }}</option>
                            <option value="week">{{ __('all.test_history.filters.week') }}</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                        </div>
                    </div>
                    <div class="relative">
                        <select wire:model.live="paymentFilter"
                                class="appearance-none w-full sm:w-auto bg-white border border-gray-200 text-gray-700 py-2 px-3 pr-8 rounded-lg leading-tight focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm">
                            <option value="all">{{ __('all.test_history.filters.all_tests') }}</option>
                            <option value="paid">{{ __('all.test_history.filters.paid') }}</option>
                            <option value="unpaid">{{ __('all.test_history.filters.not_paid') }}</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                        </div>
                    </div>
                </div>

                <!-- Search -->
                <div class="relative w-full sm:w-64">
                    <input wire:model.live.debounce.300ms="search"
                           type="text"
                           placeholder="{{__('all.test_history.filters.placeholder')}}"
                           class="w-full px-3 py-2 pl-9 text-sm border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-1 focus:ring-blue-500 bg-white placeholder-gray-400">
                    <svg class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
        </div>
        <!-- Desktop Table View -->
        <div class="hidden lg:block">
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <table class="min-w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{__('all.test_history.headers.name')}}</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{__('all.test_history.headers.date')}}</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{__('all.test_history.headers.school')}}</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{__('all.test_history.headers.class')}}</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{__('all.test_history.headers.tariff')}}</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{__('all.test_history.headers.action')}}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($filteredTests as $test)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-6 w-6">
                                            <div class="h-6 w-6 rounded-full bg-blue-100 flex items-center justify-center">
                                                <svg class="h-3 w-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">{{ $test['name'] }}</div>
{{--                                            <div class="text-xs text-gray-500">{{ Str::limit($test['session_id'], 8) }}...</div>--}}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-600">{{ $test['date'] }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-600">{{ $test['school'] }}</div>
{{--                                    @if ($test['status'] === 'completed')--}}
{{--                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-lg bg-green-100 text-green-700">Завершен</span>--}}
{{--                                    @elseif ($test['status'] === 'in_progress')--}}
{{--                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-lg bg-yellow-100 text-yellow-700">В процессе</span>--}}
{{--                                    @elseif ($test['status'] === 'started')--}}
{{--                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-lg bg-blue-100 text-blue-700">Начат</span>--}}
{{--                                    @else--}}
{{--                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-lg bg-red-100 text-red-700">Прерван</span>--}}
{{--                                    @endif--}}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-600">{{ $test['class'] }}</div>
{{--                                    @if($test['status'] === 'completed')--}}
{{--                                        <div class="w-20 bg-gray-200 rounded-full h-1 mt-1">--}}
{{--                                            <div class="bg-green-500 h-1 rounded-full" style="width: {{ $test['completion_percentage'] }}%"></div>--}}
{{--                                        </div>--}}
{{--                                        <div class="text-xs text-gray-500 mt-1">{{ round($test['completion_percentage'], 1) }}%</div>--}}
{{--                                    @elseif(in_array($test['status'], ['started', 'in_progress']))--}}
{{--                                        <div class="w-20 bg-gray-200 rounded-full h-1 mt-1">--}}
{{--                                            <div class="bg-blue-500 h-1 rounded-full" style="width: {{ $test['completion_percentage'] }}%"></div>--}}
{{--                                        </div>--}}
{{--                                        <div class="text-xs text-gray-500 mt-1">{{ round($test['completion_percentage'], 1) }}%</div>--}}
{{--                                    @endif--}}
                                </td>
                                <td class="px-4 py-3">
                                    @if($test['selected_plan'])
                                        @if($test['selected_plan'] === 'talents')
                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-lg bg-gray-100 text-gray-700">{{ __('all.payment.cards.middle.title') }}</span>
                                        @elseif($test['selected_plan'] === 'talents_spheres')
                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-lg bg-blue-100 text-blue-700">{{ __('all.payment.cards.bottom.title') }}</span>
                                        @elseif($test['selected_plan'] === 'talents_spheres_professions')
                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-lg bg-purple-100 text-purple-700">{{ __('all.payment.cards.top.title') }}</span>
                                        @else
                                            <span class="text-sm text-gray-400">—</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if(empty($test['action_url']))
                                        <span
                                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium {{ $test['payment_status'] === 'cancelled' ? 'text-red-600 hover:text-red-700' : 'text-yellow-600 hover:text-yellow-700' }} hover:bg-yellow-50 rounded-lg transition-colors">
                                            {{ $test['action_text'] }}
                                        </span>
                                    @else
                                        <a href="{{ $test['action_url'] }}"
                                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors">
                                            {{ $test['action_text'] }}
                                        </a>
                                    @endif
                                    @if($test['payment_status'] !== 'completed')
                                        <span class="text-sm text-gray-600">|</span>
                                        <a href="{{route('payment', ['sessionId' => $test['session_id']])}}"
                                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors">
                                            {{ __('all.test_history.actions.change_tariff') }}
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">
                                    {{ __('all.test_history.no_data') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Card View -->
        <div class="lg:hidden space-y-3">
            @forelse ($filteredTests as $test)
                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-6 w-6">
                                <div class="h-6 w-6 rounded-full bg-blue-100 flex items-center justify-center">
                                    <svg class="h-3 w-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">{{ $test['name'] }}</div>
                                <div class="text-xs text-gray-500">{{ $test['date'] }}</div>
                            </div>
                        </div>
{{--                        <div class="text-right">--}}
{{--                            @if ($test['status'] === 'completed')--}}
{{--                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-lg bg-green-100 text-green-700">Завершен</span>--}}
{{--                            @elseif ($test['status'] === 'in_progress')--}}
{{--                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-lg bg-yellow-100 text-yellow-700">В процессе</span>--}}
{{--                            @elseif ($test['status'] === 'started')--}}
{{--                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-lg bg-blue-100 text-blue-700">Начат</span>--}}
{{--                            @else--}}
{{--                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-lg bg-red-100 text-red-700">Прерван</span>--}}
{{--                            @endif--}}
{{--                        </div>--}}
                    </div>

{{--                    @if($test['status'] === 'completed' || in_array($test['status'], ['started', 'in_progress']))--}}
{{--                        <div class="mb-3">--}}
{{--                            <div class="flex justify-between text-xs text-gray-600 mb-1">--}}
{{--                                <span>Прогресс</span>--}}
{{--                                <span>{{ round($test['completion_percentage'], 1) }}%</span>--}}
{{--                            </div>--}}
{{--                            <div class="w-full bg-gray-200 rounded-full h-1">--}}
{{--                                @if($test['status'] === 'completed')--}}
{{--                                    <div class="bg-green-500 h-1 rounded-full" style="width: {{ $test['completion_percentage'] }}%"></div>--}}
{{--                                @else--}}
{{--                                    <div class="bg-blue-500 h-1 rounded-full" style="width: {{ $test['completion_percentage'] }}%"></div>--}}
{{--                                @endif--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    @endif--}}

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-center gap-4 text-xs text-gray-600">
                            <span>{{ __('all.test_history.tariff') }}</span>
                            @if($test['selected_plan'])
                                @if($test['selected_plan'] === 'talents')
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-lg bg-gray-100 text-gray-700">{{ __('all.payment.cards.middle.title') }}</span>
                                @elseif($test['selected_plan'] === 'talents_spheres')
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-lg bg-blue-100 text-blue-700">{{ __('all.payment.cards.bottom.title') }}</span>
                                @elseif($test['selected_plan'] === 'talents_spheres_professions')
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-lg bg-purple-100 text-purple-700">{{ __('all.payment.cards.top.title') }}</span>
                                @else
                                    <span class="text-sm text-gray-400">—</span>
                                @endif
                            @endif

{{--                            @if ($test['is_paid'])--}}
{{--                                <div class="flex items-center text-green-600">--}}
{{--                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">--}}
{{--                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>--}}
{{--                                    </svg>--}}
{{--                                    Оплачен--}}
{{--                                </div>--}}
{{--                            @else--}}
{{--                                <div class="flex items-center text-red-600">--}}
{{--                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">--}}
{{--                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>--}}
{{--                                    </svg>--}}
{{--                                    Не оплачен--}}
{{--                                </div>--}}
{{--                            @endif--}}
                        </div>

                        <div class="flex w-full justify-between items-center">
                            <a href="{{ $test['action_url'] }}"
                               class="inline-flex w-full items-center justify-center px-3 py-1.5 text-xs font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors">
                                {{ $test['action_text'] }}
                            </a>
                            @if($test['payment_status'] !== 'completed')
                                <a href="{{route('payment', ['sessionId' => $test['session_id']])}}"
                                   class="inline-flex w-full items-center justify-center px-3 py-1.5 text-xs font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors">
                                    {{ __('all.test_history.actions.change_tariff') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <p class="text-gray-500">{{ __('all.test_history.no_data') }}</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $filteredTests->links('livewire.pagination-links') }}
        </div>


    </div>
</div>
