<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use App\Models\Talent;
use App\Models\Answer;
use App\Models\TestSession;
use App\Models\UserAnswer;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class TalentTest extends Component
{
    public $allQuestions = [];
    public $currentQuestionIndex = 0;
    public $selectedAnswer = null;
    public $answers = [];
    public $progress = 0;
    public $timePerQuestion = 20; // 20 seconds per question
    public $timeRemaining;
    public $testSessionId; // ID сессии теста
    public $questionStartTime; // время начала текущего вопроса
    public $responseTimes = []; // массив времен ответов
    public $isProcessing = false; // флаг для предотвращения двойных кликов
    private $testSessionModel = null; // кэш для модели TestSession

    public function mount()
    {
        // Генерируем уникальный ID для сессии теста
        $this->testSessionId = Str::uuid()->toString();
        
        // Получаем все ответы (вопросы) из базы данных
        $answers = Answer::with('talent')->orderBy('id')->get();

        // Добавляем все 87 вопросов в массив
        foreach ($answers as $index => $answer) {
            $this->allQuestions[] = [
                'id' => $answer->id,
                'question' => $answer->question,
                'talent_name' => $answer->talent ? $answer->talent->name : 'Unknown',
                'talent_id' => $answer->talent ? $answer->talent->id : null,
                'question_number' => $index + 1, // This will be used as question_id (1-87)
            ];
        }

        // Перемешиваем вопросы для случайного порядка
        shuffle($this->allQuestions);

        // Создаем запись TestSession
        TestSession::create([
            'session_id' => $this->testSessionId,
            'user_id' => Auth::id() ?? 1, // Временно используем ID 1 если пользователь не авторизован
            'payment_status' => 'pending',
            'selected_plan' => null,
            'total_questions' => count($this->allQuestions),
            'answered_questions' => 0,
            'completion_percentage' => 0,
            'status' => 'started',
            'started_at' => now(),
        ]);

        // Initialize answers array with null values
        $this->answers = array_fill(0, count($this->allQuestions), null);
        
        // Initialize response times array
        $this->responseTimes = array_fill(0, count($this->allQuestions), null);

        // Set the initial selected answer if it exists
        $this->selectedAnswer = $this->answers[$this->currentQuestionIndex] ?? null;
        
        // Initialize timer for the first question
        $this->timeRemaining = $this->timePerQuestion;
        
        // Записываем время начала первого вопроса
        $this->questionStartTime = now();
    }
    
    private function getTestSession()
    {
        if ($this->testSessionModel === null) {
            $this->testSessionModel = TestSession::where('session_id', $this->testSessionId)->first();
        }
        return $this->testSessionModel;
    }

    public function nextQuestion()
    {
        // Save the current answer regardless of whether it's null
        if ($this->selectedAnswer !== null) {
            $this->answers[$this->currentQuestionIndex] = (int)$this->selectedAnswer;
        }

        if ($this->currentQuestionIndex < count($this->allQuestions) - 1) {
            // Move to next question
            $this->currentQuestionIndex++;
            // Set the selected answer for the next question if it exists
            $this->selectedAnswer = $this->answers[$this->currentQuestionIndex] ?? null;
            // Reset timer for the new question
            $this->timeRemaining = $this->timePerQuestion;
            
            // Dispatch event to notify Alpine.js
            $this->dispatch(event: 'question-changed');
        } else {
            // If it's the last question, submit the form
            $this->submit();
            return;
        }

        $this->updateProgress();
    }

    public function previousQuestion()
    {
        if ($this->currentQuestionIndex > 0) {
            // Save the current answer before moving back
            if ($this->selectedAnswer !== null) {
                $this->answers[$this->currentQuestionIndex] = (int)$this->selectedAnswer;
            }

            // Move to previous question
            $this->currentQuestionIndex--;
            
            // Set the selected answer for the previous question if it exists
            $this->selectedAnswer = $this->answers[$this->currentQuestionIndex] ?? null;
            
            // Reset timer for the previous question
            $this->timeRemaining = $this->timePerQuestion;

            $this->updateProgress();
        }
    }

    public function updateProgress()
    {
        $answered = count(array_filter($this->answers, function($answer) {
            return $answer !== null;
        }));
        $this->progress = ($answered / count($this->allQuestions)) * 100;
        
        // Обновляем прогресс в TestSession только периодически для уменьшения нагрузки
        // Обновляем каждые 5% прогресса или при завершении
        static $lastUpdatedProgress = 0;
        $progressRounded = round($this->progress, 0);
        
        if ($progressRounded >= $lastUpdatedProgress + 5 || $progressRounded >= 100) {
            $testSession = $this->getTestSession();
            if ($testSession) {
                $testSession->update([
                    'answered_questions' => $answered,
                    'completion_percentage' => $this->progress,
                    'status' => $answered > 0 ? 'in_progress' : 'started'
                ]);
                $lastUpdatedProgress = $progressRounded;
            }
        }
    }
    
    public function updateProgressLocal()
    {
        // Локальное обновление прогресса без обращения к БД
        $answered = count(array_filter($this->answers, function($answer) {
            return $answer !== null;
        }));
        $this->progress = ($answered / count($this->allQuestions)) * 100;
    }
    
    public function saveProgressBatch()
    {
        // Сохраняем только новые/измененные ответы для улучшения производительности
        $newAnswers = [];
        $existingAnswers = [];
        
        // Получаем уже существующие ответы для этой сессии одним запросом
        $existingUserAnswers = UserAnswer::where('test_session_id', $this->testSessionId)
            ->pluck('answer_value', 'question_id')
            ->toArray();
        
        foreach ($this->answers as $index => $value) {
            if ($value !== null) {
                $question = $this->allQuestions[$index];
                $questionId = $question['id'];
                $responseTime = $this->responseTimes[$index] ?? $this->timePerQuestion;
                
                // Проверяем, нужно ли обновлять этот ответ
                if (!isset($existingUserAnswers[$questionId]) || $existingUserAnswers[$questionId] != $value) {
                    if (isset($existingUserAnswers[$questionId])) {
                        // Ответ существует, но изменился - обновляем
                        $existingAnswers[] = [
                            'question_id' => $questionId,
                            'answer_value' => $value,
                            'response_time_seconds' => $responseTime,
                            'answered_at' => now(),
                        ];
                    } else {
                        // Новый ответ - создаем
                        $newAnswers[] = [
                            'user_id' => Auth::id() ?? 1,
                            'question_id' => $questionId,
                            'test_session_id' => $this->testSessionId,
                            'answer_value' => $value,
                            'response_time_seconds' => $responseTime,
                            'answered_at' => now(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }
        }
        
        // Вставляем новые ответы одним запросом
        if (!empty($newAnswers)) {
            UserAnswer::insert($newAnswers);
        }
        
        // Обновляем существующие ответы пакетно
        foreach ($existingAnswers as $answerData) {
            UserAnswer::where('test_session_id', $this->testSessionId)
                ->where('question_id', $answerData['question_id'])
                ->update([
                    'answer_value' => $answerData['answer_value'],
                    'response_time_seconds' => $answerData['response_time_seconds'],
                    'answered_at' => $answerData['answered_at'],
                    'updated_at' => now(),
                ]);
        }
        
        // Обновляем прогресс в TestSession только если есть изменения
        if (!empty($newAnswers) || !empty($existingAnswers)) {
            $this->updateProgress();
        }
    }
    
    public function set($property, $value)
    {
        if ($property === 'selectedAnswer') {
            // Ensure only one answer can be selected
            $this->selectedAnswer = (int)$value;
            
            // Update the answers array immediately
            $this->answers[$this->currentQuestionIndex] = $this->selectedAnswer;
            
            // Update progress
            $this->updateProgress();
        }
    }

    public function submit()
    {
        // Записываем время ответа на последний вопрос
        if ($this->questionStartTime) {
            $responseTime = now()->diffInSeconds($this->questionStartTime);
            $this->responseTimes[$this->currentQuestionIndex] = $responseTime;
        }
        
        // Сохраняем все ответы одним пакетом
        $this->saveProgressBatch();

        // Обновляем TestSession при завершении теста
        $testSession = $this->getTestSession();
        if ($testSession) {
            $testSession->updateProgress();
            $testSession->updateTimeMetrics();
            $testSession->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);
        }

        session()->flash('message', 'Ваш тест завершен! Выберите тарифный план для получения результатов.');
        session()->put('last_test_session_id', $this->testSessionId);
        return redirect()->route('payment', ['sessionId' => $this->testSessionId]);
    }

    public function decrementTimer()
    {
        if ($this->timeRemaining > 0) {
            $this->timeRemaining--;
        } else {
            // Time's up, move to the next question or submit
            $this->nextQuestion();
        }
    }
    
    public function handleTimerExpired()
    {
        // Auto-submit the test when time expires
        $this->submit();
    }
    
    public function selectAnswerAndNext($answerValue)
    {
        // Защита от двойного клика
        if ($this->isProcessing) {
            return;
        }
        
        $this->isProcessing = true;
        
        try {
            // Устанавливаем выбранный ответ
            $this->selectedAnswer = (int)$answerValue;
            $this->answers[$this->currentQuestionIndex] = $this->selectedAnswer;
            
            // Записываем время ответа на текущий вопрос
            if ($this->questionStartTime) {
                $responseTime = now()->diffInSeconds($this->questionStartTime);
                $this->responseTimes[$this->currentQuestionIndex] = $responseTime;
            } else {
                $this->responseTimes[$this->currentQuestionIndex] = 1;
            }
            
            // Обновляем прогресс асинхронно (только локально, без БД)
            $this->updateProgressLocal();
            
            // Переходим к следующему вопросу или завершаем тест
            if ($this->currentQuestionIndex < count($this->allQuestions) - 1) {
                // Переход к следующему вопросу
                $this->currentQuestionIndex++;
                $this->selectedAnswer = $this->answers[$this->currentQuestionIndex] ?? null;
                $this->timeRemaining = $this->timePerQuestion;
                $this->questionStartTime = now();
                
                // Автосохранение каждые 20 вопросов для предотвращения потери данных
                // Уменьшаем частоту для улучшения производительности
                if ($this->currentQuestionIndex > 0 && $this->currentQuestionIndex % 20 === 0) {
                    $this->saveProgressBatch();
                }
                
                $this->dispatch('question-changed');
            } else {
                // Если это последний вопрос, завершаем тест
                $this->submit();
                return; // Выходим, не сбрасывая флаг, так как тест завершен
            }
        } finally {
            // Сбрасываем флаг обработки
            $this->isProcessing = false;
        }
    }

    public function render()
    {
        $currentQuestion = $this->allQuestions[$this->currentQuestionIndex] ?? null;
        $totalQuestions = count($this->allQuestions);
        $currentNumber = $this->currentQuestionIndex + 1;

        return view('livewire.pages.talent-test', [
            'currentQuestion' => $currentQuestion,
            'totalQuestions' => $totalQuestions,
            'currentNumber' => $currentNumber,
            'minutes' => floor($this->timeRemaining / 60),
            'seconds' => $this->timeRemaining % 60,
            'timePerQuestion' => $this->timePerQuestion,
        ]);
    }
}
