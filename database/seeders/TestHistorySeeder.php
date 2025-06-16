<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TestSession;
use App\Models\UserAnswer;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TestHistorySeeder extends Seeder
{
    public function run(): void
    {
        // Найдем пользователя для создания тестовых данных
        $user = User::first();
        
        if (!$user) {
            $this->command->info('No users found. Creating a test user.');
            $user = User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
                'language' => 'ru',
            ]);
        }

        $this->command->info("Creating test history for user: {$user->name}");

        // Создадим несколько тестовых сессий с разными статусами
        $testSessions = [
            [
                'status' => 'completed',
                'payment_status' => 'completed',
                'selected_plan' => 'premium',
                'answers_count' => 180,
                'created_days_ago' => 5,
            ],
            [
                'status' => 'completed', 
                'payment_status' => 'free',
                'selected_plan' => 'free',
                'answers_count' => 60,
                'created_days_ago' => 12,
            ],
            [
                'status' => 'completed',
                'payment_status' => 'pending',
                'selected_plan' => 'professional',
                'answers_count' => 180,
                'created_days_ago' => 3,
            ],
            [
                'status' => 'in_progress',
                'payment_status' => 'pending',
                'selected_plan' => null,
                'answers_count' => 45,
                'created_days_ago' => 1,
            ],
            [
                'status' => 'started',
                'payment_status' => 'pending',
                'selected_plan' => null,
                'answers_count' => 5,
                'created_days_ago' => 2,
            ],
        ];

        foreach ($testSessions as $sessionData) {
            $sessionId = Str::uuid()->toString();
            $createdAt = Carbon::now()->subDays($sessionData['created_days_ago']);
            
            $session = TestSession::create([
                'session_id' => $sessionId,
                'user_id' => $user->id,
                'payment_status' => $sessionData['payment_status'],
                'selected_plan' => $sessionData['selected_plan'],
                'status' => $sessionData['status'],
                'total_questions' => 180,
                'answered_questions' => $sessionData['answers_count'],
                'completion_percentage' => round(($sessionData['answers_count'] / 180) * 100, 2),
                'started_at' => $createdAt,
                'completed_at' => $sessionData['status'] === 'completed' ? $createdAt->addMinutes(rand(30, 120)) : null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // Создаем пользовательские ответы для этой сессии
            $this->createUserAnswers($session, $sessionData['answers_count'], $createdAt);
            
            // Обновляем временные метрики
            $session->updateTimeMetrics();
            
            $this->command->info("Created session: {$sessionId} with {$sessionData['answers_count']} answers");
        }

        $this->command->info('Test history data created successfully!');
    }

    private function createUserAnswers($session, $count, $baseTime)
    {
        for ($i = 1; $i <= $count; $i++) {
            UserAnswer::create([
                'test_session_id' => $session->session_id,
                'question_id' => $i,
                'selected_option' => rand(1, 5), // Random answer between 1-5
                'response_time_seconds' => rand(10, 60), // Random response time
                'answered_at' => $baseTime->copy()->addMinutes($i * 2), // Stagger answers
                'created_at' => $baseTime->copy()->addMinutes($i * 2),
                'updated_at' => $baseTime->copy()->addMinutes($i * 2),
            ]);
        }
    }
}
