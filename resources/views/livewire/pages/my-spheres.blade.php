<div class="min-h-screen bg-white py-8 px-4">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-12 text-center">
            <h1 class="text-4xl font-light text-gray-900 mb-3 tracking-wide">Избранные сферы</h1>
        </div>

        <!-- Flash Message -->
        @if(session('message'))
            <div class="mb-8 text-center">
                <p class="text-green-600 font-medium">{{ session('message') }}</p>
            </div>
        @endif

        @if($favoriteSpheres->count() > 0)
            <!-- Desktop Table View -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-6 pr-8 text-sm font-medium text-gray-900 tracking-wider uppercase">
                                Сфера
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($favoriteSpheres as $sphere)
                            <tr class="group hover:bg-gray-50/50 transition-colors duration-200 cursor-pointer" 
                                wire:click="toggleExpanded({{ $sphere->id }})">
                                <td class="py-8 pr-8 align-top">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <!-- Color indicator -->
                                            <div class="w-4 h-4 rounded-full mr-4 flex-shrink-0" style="background-color: {{ $sphere->color }}"></div>
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $sphere->localized_name }}</h3>
                                                <div class="text-xs text-gray-500 uppercase tracking-wide">
                                                    ID: {{ $sphere->id }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-gray-400 hover:text-blue-600 transition-colors duration-200 p-1 ml-4">
                                            <svg class="w-5 h-5 transform transition-transform duration-200 {{ in_array($sphere->id, $expandedSpheres) ? 'rotate-90' : '' }}" 
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </div>
                                    </div>
                                    @if(in_array($sphere->id, $expandedSpheres))
                                        <div class="mt-4 pt-4 border-t border-gray-100">
                                            @if($sphere->localized_description)
                                                <p class="text-sm text-gray-700 leading-relaxed mb-4">
                                                    {{ $sphere->localized_description }}
                                                </p>
                                            @endif
                                            <div class="flex justify-end">
                                                <button wire:click.stop="removeSphere({{ $sphere->id }})" 
                                                    class="text-gray-400 hover:text-red-600 transition-colors duration-200 p-2 text-sm flex items-center"
                                                    title="Удалить из избранного">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                    </svg>
                                                    Удалить
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="lg:hidden space-y-6">
                @foreach($favoriteSpheres as $sphere)
                    <div class="border border-gray-200 bg-white cursor-pointer" 
                         wire:click="toggleExpanded({{ $sphere->id }})">
                        <table class="w-full">
                            <tbody>
                                <tr class="border-b border-gray-100">
                                    <td class="py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wide w-1/3">
                                        Сфера
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <div class="text-gray-400 hover:text-blue-600 transition-colors duration-200 p-1">
                                            <svg class="w-4 h-4 transform transition-transform duration-200 {{ in_array($sphere->id, $expandedSpheres) ? 'rotate-90' : '' }}" 
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="border-b border-gray-100">
                                    <td colspan="2" class="py-4 px-6">
                                        <div class="flex items-center">
                                            <div class="w-4 h-4 rounded-full mr-3 flex-shrink-0" style="background-color: {{ $sphere->color }}"></div>
                                            <h3 class="text-lg font-semibold text-gray-900">{{ $sphere->localized_name }}</h3>
                                        </div>
                                    </td>
                                </tr>
                                @if(in_array($sphere->id, $expandedSpheres))
                                    @if($sphere->localized_description)
                                        <tr class="border-b border-gray-100">
                                            <td class="py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wide align-top">
                                                Описание
                                            </td>
                                            <td class="py-4 px-6 text-sm text-gray-700">
                                                {{ $sphere->localized_description }}
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td colspan="2" class="py-4 px-6 text-right border-t border-gray-100">
                                            <button wire:click.stop="removeSphere({{ $sphere->id }})" 
                                                class="text-gray-400 hover:text-red-600 transition-colors duration-200 p-2 text-sm flex items-center ml-auto"
                                                title="Удалить из избранного">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                    </svg>
                                                    Удалить
                                                </button>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                @endforeach
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
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-2.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 009.586 13H7"/>
                                    </svg>
                                </div>
                                <h2 class="text-2xl font-light text-gray-900 mb-4">Нет избранных сфер</h2>
                                <p class="text-gray-600 mb-8 max-w-md">Добавьте сферы в избранное или изучите карту профессий</p>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-6 text-center border-t border-gray-200">
                                <a href="{{ route('profession-map') }}" 
                                   class="inline-block text-blue-600 hover:text-blue-800 font-medium text-sm tracking-wide uppercase transition-colors duration-200 border-b border-blue-600 hover:border-blue-800 pb-1">
                                    Исследовать карту профессий
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
