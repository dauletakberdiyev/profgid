<div class="min-h-screen bg-gray-50 py-4 md:py-8 px-4" wire:poll.1000ms="decrementTimer">
    <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-md overflow-hidden">
        <!-- Progress Bar -->
        <div class="bg-gray-100 p-3 md:p-4">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-2">
                <span class="text-sm font-medium text-gray-700 mb-2 sm:mb-0">
                    Вопрос {{ $currentNumber }} из {{ $totalQuestions }}
                </span>
                <div class="flex items-center justify-between sm:justify-end">
                    <!-- Timer Display -->
                    <div class="flex items-center mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5 text-gray-500 mr-1" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm font-medium text-gray-500">
                            {{ $seconds }} сек.
                        </span>
                    </div>
                    <span class="text-sm font-medium text-gray-500">
                        {{ round($progress) }}%
                    </span>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $progress }}%"></div>
            </div>
        </div>

        <!-- Question Card -->
        <div class="p-4 md:p-6">
            @if ($currentQuestion)
                <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-4 md:mb-6 leading-relaxed">{{ $currentQuestion['question'] }}</h2>

                <!-- Answer Options -->
                <div class="space-y-3 md:space-y-4 mb-6 md:mb-8" wire:key="question-{{ $currentQuestionIndex }}">
                    @foreach (['Совсем не согласен', 'Скорее не согласен', 'Затрудняюсь ответить', 'Скорее согласен', 'Полностью согласен'] as $index => $option)
                        <label
                            class="flex items-center p-3 md:p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition-all duration-200 {{ $selectedAnswer == $index + 1 ? 'border-blue-500 bg-blue-50 scale-[1.02]' : 'border-gray-200' }}"
                            wire:click="selectAnswerAndNext({{ $index + 1 }})">
                            <input type="radio" class="h-4 w-4 md:h-5 md:w-5 text-blue-600 focus:ring-blue-500 flex-shrink-0"
                                wire:model="selectedAnswer" name="question_answer" value="{{ $index + 1 }}">
                            <span class="ml-3 text-sm md:text-base text-gray-700">{{ $option }}</span>
                        </label>
                    @endforeach
                </div>

                <!-- Navigation Buttons -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center pt-4 border-t space-y-3 sm:space-y-0">
                        <button wire:click="previousQuestion" @if ($currentQuestionIndex === 0) disabled @endif
                        class="w-full sm:w-auto px-4 md:px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors text-sm md:text-base">
                        Назад
                    </button>

                    @if ($currentQuestionIndex === $totalQuestions - 1)
                        <button wire:click="submit"
                            class="w-full sm:w-auto px-4 md:px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors text-sm md:text-base">
                            Завершить тест
                        </button>
                    @else
                        <div class="text-xs md:text-sm text-gray-500 flex items-center justify-center sm:justify-end">
                            <svg class="w-3 h-3 md:w-4 md:h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.122 2.122"></path>
                            </svg>
                            Выберите ответ для перехода
                        </div>
                    @endif
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-gray-600">Вопросы не найдены.</p>
                </div>
            @endif
        </div>
    </div>
</div>
