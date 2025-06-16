<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateTestSession extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:create-session {userId?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test session for testing payment flow';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('userId') ?? 1; // По умолчанию пользователь с ID 1
        
        $user = \App\Models\User::find($userId);
        
        if (!$user) {
            $this->error("User with ID {$userId} not found");
            return 1;
        }
        
        $sessionId = 'test-' . uniqid();
        
        $testSession = \App\Models\TestSession::create([
            'session_id' => $sessionId,
            'user_id' => $userId,
            'status' => 'completed',
            'payment_status' => 'pending',
            'total_questions' => 120,
            'answered_questions' => 120,
            'completion_percentage' => 100,
            'started_at' => now()->subHour(),
            'completed_at' => now(),
            'total_time_spent' => 3600,
            'average_response_time' => 30
        ]);
        
        $this->info("Test session created successfully:");
        $this->info("Session ID: {$sessionId}");
        $this->info("User: {$user->name} ({$user->email})");
        $this->info("Payment URL: " . url("/payment/{$sessionId}"));
        
        return 0;
    }
}
