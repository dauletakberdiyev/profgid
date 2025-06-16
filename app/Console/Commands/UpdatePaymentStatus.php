<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdatePaymentStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:update-status {sessionId} {status}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update payment status for testing purposes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sessionId = $this->argument('sessionId');
        $status = $this->argument('status');
        
        $allowedStatuses = ['pending', 'review', 'completed', 'failed'];
        
        if (!in_array($status, $allowedStatuses)) {
            $this->error('Invalid status. Allowed: ' . implode(', ', $allowedStatuses));
            return 1;
        }
        
        $testSession = \App\Models\TestSession::where('session_id', $sessionId)->first();
        
        if (!$testSession) {
            $this->error('Test session not found');
            return 1;
        }
        
        $oldStatus = $testSession->payment_status;
        $testSession->update(['payment_status' => $status]);
        
        if ($status === 'completed') {
            $testSession->update(['payment_confirmed_at' => now()]);
        }
        
        $this->info("Payment status updated from '{$oldStatus}' to '{$status}' for session {$sessionId}");
        
        return 0;
    }
}
