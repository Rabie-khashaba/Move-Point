<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use Illuminate\Support\Facades\Http;

class DebugFirebase extends Command
{
    protected $signature = 'debug:firebase {--test-token=} {--user-id=}';
    protected $description = 'Debug Firebase configuration and test notifications';

    public function handle()
    {
        $this->info('🔍 Firebase Debug Information');
        $this->line('');

        // Check Firebase configuration
        $this->checkFirebaseConfig();
        
        // Check users with device tokens
        $this->checkUsersWithTokens();
        
        // Test Firebase connection if token provided
        if ($testToken = $this->option('test-token')) {
            $this->testFirebaseConnection($testToken);
        }
        
        // Test notification to user if user ID provided
        if ($userId = $this->option('user-id')) {
            $this->testNotificationToUser($userId);
        }

        return 0;
    }

    private function checkFirebaseConfig()
    {
        $this->info('📋 Firebase Configuration:');
        
        $serverKey = config('firebase.server_key');
        $configExists = file_exists(config_path('firebase.php'));
        
        $this->line("Config file exists: " . ($configExists ? '✅ Yes' : '❌ No'));
        $this->line("Server key configured: " . (!empty($serverKey) ? '✅ Yes' : '❌ No'));
        
        if ($serverKey) {
            $this->line("Server key length: " . strlen($serverKey));
            $this->line("Server key prefix: " . substr($serverKey, 0, 10) . '...');
        }
        
        $this->line('');
    }

    private function checkUsersWithTokens()
    {
        $this->info('👥 Users with Device Tokens:');
        
        $users = User::whereNotNull('device_tokens')
            ->where('notifications_enabled', true)
            ->get();
            
        if ($users->isEmpty()) {
            $this->warn('No users found with device tokens and notifications enabled.');
        } else {
            $this->line("Found {$users->count()} users with device tokens:");
            
            foreach ($users as $user) {
                $tokenCount = is_array($user->device_tokens) ? count($user->device_tokens) : 1;
                $this->line("- User ID: {$user->id}, Name: {$user->name}, Type: {$user->type}, Tokens: {$tokenCount}");
            }
        }
        
        $this->line('');
    }

    private function testFirebaseConnection($testToken)
    {
        $this->info("🧪 Testing Firebase Connection with token: " . substr($testToken, 0, 20) . '...');
        
        $serverKey = config('firebase.server_key');
        
        if (empty($serverKey)) {
            $this->error('❌ Firebase server key not configured!');
            return;
        }
        
        $payload = [
            'to' => $testToken,
            'notification' => [
                'title' => 'Test Notification',
                'body' => 'This is a test notification from Laravel',
                'sound' => 'default',
            ],
            'data' => [
                'type' => 'test',
                'timestamp' => now()->toISOString()
            ],
            'priority' => 'high',
        ];
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', $payload);
            
            $this->line("HTTP Status: " . $response->status());
            $this->line("Response: " . $response->body());
            
            if ($response->successful()) {
                $responseData = $response->json();
                $this->line("Success count: " . ($responseData['success'] ?? 0));
                $this->line("Failure count: " . ($responseData['failure'] ?? 0));
                
                if (isset($responseData['results'])) {
                    foreach ($responseData['results'] as $index => $result) {
                        $this->line("Result {$index}: " . json_encode($result));
                    }
                }
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Exception: ' . $e->getMessage());
        }
        
        $this->line('');
    }

    private function testNotificationToUser($userId)
    {
        $this->info("📱 Testing notification to user ID: {$userId}");
        
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("❌ User with ID {$userId} not found.");
            return;
        }
        
        $this->line("User: {$user->name} ({$user->type})");
        $this->line("Notifications enabled: " . ($user->notifications_enabled ? 'Yes' : 'No'));
        
        if ($user->device_tokens) {
            $tokenCount = is_array($user->device_tokens) ? count($user->device_tokens) : 1;
            $this->line("Device tokens: {$tokenCount}");
        } else {
            $this->warn("No device tokens found for this user.");
            return;
        }
        
        $firebaseService = new FirebaseNotificationService();
        
        $result = $firebaseService->sendToUser($user, 'Debug Test', 'This is a debug test notification', [
            'type' => 'debug_test',
            'timestamp' => now()->toISOString()
        ], 'debug');
        
        $this->line("Notification result: " . ($result ? '✅ Success' : '❌ Failed'));
        $this->line('');
    }
}