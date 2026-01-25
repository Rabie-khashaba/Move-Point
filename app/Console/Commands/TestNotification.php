<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\FirebaseNotificationService;

class TestNotification extends Command
{
    protected $signature = 'test:notification {user_id} {--title=Test Notification} {--body=This is a test notification}';
    protected $description = 'Test sending a notification to a specific user';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $title = $this->option('title');
        $body = $this->option('body');

        $user = User::find($userId);
        
        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return 1;
        }

        $this->info("Testing notification for user: {$user->name} (ID: {$user->id})");
        $this->info("User type: {$user->type}");
        $this->info("Notifications enabled: " . ($user->notifications_enabled ? 'Yes' : 'No'));
        
        if ($user->device_tokens) {
            $tokenCount = is_array($user->device_tokens) ? count($user->device_tokens) : 1;
            $this->info("Device tokens count: {$tokenCount}");
            $this->info("Device tokens: " . json_encode($user->device_tokens));
        } else {
            $this->warn("No device tokens found for this user.");
        }

        if (!$user->notifications_enabled || empty($user->device_tokens)) {
            $this->error("Cannot send notification - user has notifications disabled or no device tokens.");
            return 1;
        }

        $firebaseService = new FirebaseNotificationService();
        
        $this->info("Sending notification...");
        $result = $firebaseService->sendToUser($user, $title, $body, [
            'type' => 'test',
            'timestamp' => now()->toISOString()
        ], 'test');

        if ($result) {
            $this->info("✅ Notification sent successfully!");
        } else {
            $this->error("❌ Failed to send notification.");
        }

        return 0;
    }
}