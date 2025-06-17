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
        
        // Обновляем прогресс в TestSession
        $testSession = TestSession::where('session_id', $this->testSessionId)->first();
        if ($testSession) {
            $testSession->update([
                'answered_questions' => $answered,
                'completion_percentage' => $this->progress,
                'status' => $answered > 0 ? 'in_progress' : 'started'
            ]);
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
        // Сохраняем все ответы партиями для улучшения производительности
        $answersToSave = [];
        
        foreach ($this->answers as $index => $value) {
            if ($value !== null) {
                $question = $this->allQuestions[$index];
                $responseTime = $this->responseTimes[$index] ?? $this->timePerQuestion;
                
                $answersToSave[] = [
                    'user_id' => Auth::id() ?? 1,
                    'question_id' => $question['id'],
                    'test_session_id' => $this->testSessionId,
                    'answer_value' => $value,
                    'response_time_seconds' => $responseTime,
                    'answered_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        
        if (!empty($answersToSave)) {
            // Удаляем старые ответы для этой сессии
            UserAnswer::where('test_session_id', $this->testSessionId)->delete();
            // Вставляем все ответы одним запросом
            UserAnswer::insert($answersToSave);
        }
        
        // Обновляем прогресс в TestSession
        $this->updateProgress();
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
        $testSession = TestSession::where('session_id', $this->testSessionId)->first();
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
        
        // Переходим к следующему вопросу или завершаем тест
        if ($this->currentQuestionIndex < count($this->allQuestions) - 1) {
            // Переход к следующему вопросу
            $this->currentQuestionIndex++;
            $this->selectedAnswer = $this->answers[$this->currentQuestionIndex] ?? null;
            $this->timeRemaining = $this->timePerQuestion;
            $this->questionStartTime = now();
            
            // Обновляем прогресс асинхронно (только локально, без БД)
            $this->updateProgressLocal();
            
            // Автосохранение каждые 10 вопросов для предотвращения потери данных
            if (($this->currentQuestionIndex + 1) % 10 === 0) {
                $this->saveProgressBatch();
            }
            
            $this->dispatch('question-changed');
        } else {
            // Если это последний вопрос, завершаем тест
            $this->submit();
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
