<div class="min-h-screen bg-white py-8 md:py-16 px-4">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-12 md:mb-16">
            <h1 class="text-2xl md:text-3xl lg:text-4xl font-light text-gray-900 mb-4">Выберите тарифный план</h1>
с            @if($testSession)
                <p class="text-lg md:text-xl text-gray-600 font-light">Получите полный доступ к результатам вашего теста талантов</p>
            @else
                <p class="text-lg md:text-xl text-gray-600 font-light">Ознакомьтесь с нашими тарифными планами</p>
                <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg inline-block">
                    <p class="text-sm text-blue-700">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Для получения результатов необходимо сначала пройти тест талантов
                    </p>
                </div>
            @endif
        </div>

        <!-- Pricing Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 md:gap-12 mb-12 md:mb-16">
            @foreach($plans as $key => $plan)
                <div class="relative bg-white border {{ $loop->last ? 'border-yellow-300 scale-105' : 'border-gray-200' }} rounded-xl transition-all duration-300 hover:border-gray-300 flex flex-col {{ $loop->last ? 'lg:transform lg:scale-105' : '' }}">
                    
                    <!-- Crown for the last plan -->
                    @if($loop->last)
                        <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 z-10">
                            <div class="bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-full p-3">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M5 16L3 6l5.5 4L12 4l3.5 6L21 6l-2 10H5z"/>
                                </svg>
                            </div>
                        </div>
                    @endif
                    
                    <div class="p-8 md:p-10 flex flex-col flex-grow">
                        <!-- Plan Header -->
                        <div class="text-center mb-8">
                            <h3 class="text-xl md:text-2xl font-medium text-gray-900 mb-4 {{ $loop->last ? 'mt-4' : '' }}">
                                {{ $plan['name'] }}
                                @if($loop->last)
                                    <span class="block text-sm text-yellow-600 font-normal mt-1">Рекомендуемый</span>
                                @endif
                            </h3>
                            <div class="mb-6">
                                <span class="text-4xl md:text-5xl font-light text-gray-900">{{ number_format($plan['price']) }}</span>
                                <span class="text-gray-500 ml-2 text-lg">{{ $plan['currency'] }}</span>
                            </div>
                        </div>

                        <!-- Features -->
                        <ul class="space-y-4 mb-8 flex-grow">
                            @foreach($plan['features'] as $feature)
                                <li class="flex items-start">
                                    <div class="w-1.5 h-1.5 bg-blue-500 rounded-full mt-2.5 mr-3 flex-shrink-0"></div>
                                    <span class="text-sm md:text-base text-gray-700 leading-relaxed">{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <!-- CTA Button -->
                        <button 
                            wire:click="selectPlan('{{ $key }}')"
                            class="w-full py-4 px-6 rounded-lg font-medium transition-all duration-200 text-sm md:text-base border mt-auto
                                {{ $loop->last ? 'bg-gradient-to-r from-yellow-500 to-yellow-600 text-white border-yellow-500 hover:from-yellow-600 hover:to-yellow-700' : '' }}
                                {{ $plan['color'] === 'blue' && !$loop->last ? 'bg-blue-500 text-white border-blue-500 hover:bg-blue-600' : '' }}
                                {{ $plan['color'] === 'purple' && !$loop->last ? 'bg-purple-500 text-white border-purple-500 hover:bg-purple-600' : '' }}
                            ">
                            @if($testSession)
                                @if($loop->last)
                                    Выбрать лучший план
                                @else
                                    Выбрать план
                                @endif
                            @else
                                @if($loop->last)
                                    Пройти тест и выбрать план
                                @else
                                    Пройти ��ест для этого плана
                                @endif
                            @endif
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Back to Test Button -->
        <div class="text-center">
            <a href="{{ route('talent-test') }}" class="inline-flex items-center text-gray-600 hover:text-gray-800 font-light text-sm md:text-base transition-colors">
                <svg class="w-4 h-4 md:w-5 md:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Вернуться к тесту
            </a>
        </div>
    </div>
</div>
