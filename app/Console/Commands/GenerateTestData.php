<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TestSession;
use App\Models\UserAnswer;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GenerateTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:test-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate test data for talent test sessions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
                'language' => 'ru',
            ]);
            $this->info('Created test user: ' . $user->email);
        }

        // Получаем все таланты с их доменами
        $talents = \App\Models\Talent::with('domain')->orderBy('id')->get();
        $this->info('Found ' . $talents->count() . ' talents');

        // Создаем тестовую сессию
        $sessionId = Str::uuid()->toString();
        $session = TestSession::create([
            'session_id' => $sessionId,
            'user_id' => $user->id,
            'payment_status' => 'completed',
            'selected_plan' => 'premium',
            'status' => 'completed',
            'total_questions' => 180,
            'answered_questions' => $talents->count(),
            'completion_percentage' => 100,
            'started_at' => Carbon::now()->subHours(2),
            'completed_at' => Carbon::now()->subHour(),
        ]);

        $this->info('Created test session: ' . $sessionId);

        // Создаем ответы для каждого таланта (1 вопрос = 1 талант)
        foreach ($talents as $index => $talent) {
            $questionId = $index + 1; // question_id от 1 до 34
            
            // Варьируем ответы в зависимости от домена для большего разнообразия
            $answerValue = match($talent->domain->name ?? 'executing') {
                'executing' => rand(3, 5), // Высокие оценки для executing
                'influencing' => rand(2, 4), // Средние оценки для influencing
                'relationship_building' => rand(1, 3), // Низкие оценки для relationship
                'strategic_thinking' => rand(2, 5), // Разнообразные оценки для strategic
                default => rand(1, 5)
            };

            UserAnswer::create([
                'user_id' => $user->id,
                'test_session_id' => $sessionId,
                'question_id' => $questionId,
                'answer_value' => $answerValue,
                'response_time_seconds' => rand(10, 60),
                'answered_at' => Carbon::now()->subHours(2)->addMinutes($questionId * 2),
            ]);
        }

        $this->info('Created ' . $talents->count() . ' answers');

        // Обновляем метрики
        $session->updateTimeMetrics();
        $session->updateProgress();

        $this->info('Updated session metrics');
        
        // Показываем распределение по доменам
        $domainCounts = [];
        foreach ($talents as $talent) {
            $domainName = $talent->domain->name ?? 'unknown';
            $domainCounts[$domainName] = ($domainCounts[$domainName] ?? 0) + 1;
        }
        
        $this->info('Domain distribution:');
        foreach ($domainCounts as $domain => $count) {
            $this->info("  {$domain}: {$count} talents");
        }
        
        $this->info('Test data generation completed!');
    }
}
