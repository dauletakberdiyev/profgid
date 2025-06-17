<div class="min-h-screen bg-white px-4 py-4">
    <div class="max-w-4xl mx-auto text-center">
        
        <!-- Заголовок -->
        <div class="mb-4 md:mb-12">
            <h1 class="text-xl md:text-4xl lg:text-5xl font-light text-gray-900 mb-2 md:mb-4">
                Вы готовы к прохождению теста
            </h1>
            <p class="text-sm md:text-xl text-gray-600 font-light">
                Несколько важных моментов перед началом
            </p>
        </div>

        <!-- Инструкции -->
        <div class="grid grid-cols-2 md:grid-cols-2 gap-3 md:gap-8 mb-4 md:mb-16">
            <!-- Время -->
            <div class="text-left">
                <div class="flex items-center mb-1 md:mb-3">
                    <div class="w-6 h-6 md:w-8 md:h-8 bg-blue-600 rounded-full flex items-center justify-center mr-2 md:mr-3 flex-shrink-0">
                        <span class="text-white text-xs md:text-sm font-medium">1</span>
                    </div>
                    <h3 class="text-sm md:text-lg font-medium text-gray-900">15-20 минут</h3>
                </div>
                <p class="text-xs md:text-base text-gray-600 ml-8 md:ml-11">
                    Убедитесь, что у вас есть достаточно времени
                </p>
            </div>

            <!-- Количество вопросов -->
            <div class="text-left">
                <div class="flex items-center mb-1 md:mb-3">
                    <div class="w-6 h-6 md:w-8 md:h-8 bg-blue-600 rounded-full flex items-center justify-center mr-2 md:mr-3 flex-shrink-0">
                        <span class="text-white text-xs md:text-sm font-medium">2</span>
                    </div>
                    <h3 class="text-sm md:text-lg font-medium text-gray-900">87 вопросов</h3>
                </div>
                <p class="text-xs md:text-base text-gray-600 ml-8 md:ml-11">
                    Каждый вопрос поможет определить ваши таланты
                </p>
            </div>

            <!-- Время на вопрос -->
            <div class="text-left">
                <div class="flex items-center mb-1 md:mb-3">
                    <div class="w-6 h-6 md:w-8 md:h-8 bg-blue-600 rounded-full flex items-center justify-center mr-2 md:mr-3 flex-shrink-0">
                        <span class="text-white text-xs md:text-sm font-medium">3</span>
                    </div>
                    <h3 class="text-sm md:text-lg font-medium text-gray-900">~20 секунд</h3>
                </div>
                <p class="text-xs md:text-base text-gray-600 ml-8 md:ml-11">
                    Отвечайте интуитивно
                </p>
            </div>

            <!-- Честность -->
            <div class="text-left">
                <div class="flex items-center mb-1 md:mb-3">
                    <div class="w-6 h-6 md:w-8 md:h-8 bg-blue-600 rounded-full flex items-center justify-center mr-2 md:mr-3 flex-shrink-0">
                        <span class="text-white text-xs md:text-sm font-medium">4</span>
                    </div>
                    <h3 class="text-sm md:text-lg font-medium text-gray-900">Будьте честными</h3>
                </div>
                <p class="text-xs md:text-base text-gray-600 ml-8 md:ml-11">
                    Отвечайте искренне
                </p>
            </div>
        </div>

        <!-- Что получите -->
        <div class="bg-gray-50 rounded-2xl p-4 md:p-8 mb-4 md:mb-12">
            <h2 class="text-lg md:text-2xl font-light text-gray-900 mb-3 md:mb-6">
                Что вы получите
            </h2>
            
            <div class="grid grid-cols-3 md:grid-cols-3 gap-3 md:gap-6 text-center">
                <div>
                    <div class="text-xl md:text-3xl font-light text-blue-600 mb-1 md:mb-2">24</div>
                    <p class="text-xs md:text-base text-gray-600">персональных таланта</p>
                </div>
                <div>
                    <div class="text-xl md:text-3xl font-light text-blue-600 mb-1 md:mb-2">15+</div>
                    <p class="text-xs md:text-base text-gray-600">рекомендаций профессий</p>
                </div>
                <div>
                    <div class="text-xl md:text-3xl font-light text-blue-600 mb-1 md:mb-2">4</div>
                    <p class="text-xs md:text-base text-gray-600">домена способностей</p>
                </div>
            </div>
        </div>

        <!-- Кнопка начать тест -->
        <div>
            <button wire:click="startTest" 
                    class="w-full sm:w-auto inline-flex items-center justify-center px-8 md:px-12 py-3 md:py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium text-base md:text-lg rounded-xl transition-colors duration-200 hover:shadow-xl cursor-pointer">
                Начать тест
            </button>
            
            <p class="text-xs md:text-sm text-gray-500 mt-2 md:mt-4 px-4 md:px-0">
                После завершения теста вы получите подробный анализ<br class="hidden md:block">
                ваших талантов и персональные рекомендации профессий
            </p>
        </div>

    </div>
</div>
