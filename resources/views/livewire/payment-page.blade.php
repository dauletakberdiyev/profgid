<div class="min-h-screen bg-white py-8 md:py-16 px-4">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-12 md:mb-16">
            <h1 class="text-2xl md:text-3xl lg:text-4xl font-light text-gray-900 mb-4">Выберите тарифный план</h1>
            <p class="text-lg md:text-xl text-gray-600 font-light">Получите полный доступ к результатам вашего теста талантов</p>
        </div>

        <!-- Pricing Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 md:gap-12 mb-12 md:mb-16">
            @foreach($plans as $key => $plan)
                <div class="relative bg-white border border-gray-200 rounded-xl overflow-hidden transition-all duration-300 hover:border-gray-300 flex flex-col">
                    
                    <div class="p-8 md:p-10 flex flex-col flex-grow">
                        <!-- Plan Header -->
                        <div class="text-center mb-8">
                            <h3 class="text-xl md:text-2xl font-medium text-gray-900 mb-4">{{ $plan['name'] }}</h3>
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
                                {{ $plan['color'] === 'blue' ? 'bg-blue-500 text-white border-blue-500 hover:bg-blue-600' : '' }}
                                {{ $plan['color'] === 'purple' ? 'bg-purple-500 text-white border-purple-500 hover:bg-purple-600' : '' }}
                            ">
                            Выбрать план
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
