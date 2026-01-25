<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\AppNotification;
use App\Http\Controllers\Admin\NotificationController;
use Illuminate\Http\Request;

class TestNotificationSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the notification system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("🧪 Testing Notification System");
        $this->info("=============================");

        // Test 1: Check if users exist
        $this->info("\n1. Checking users...");
        $userCount = User::count();
        $this->info("   Total users: {$userCount}");
        
        if ($userCount === 0) {
            $this->error("   ❌ No users found!");
            return 1;
        }
        $this->info("   ✅ Users found");

        // Test 2: Check notification table
        $this->info("\n2. Checking notification table...");
        try {
            $notificationCount = AppNotification::count();
            $this->info("   Total notifications: {$notificationCount}");
            $this->info("   ✅ Notification table accessible");
        } catch (\Exception $e) {
            $this->error("   ❌ Notification table error: " . $e->getMessage());
            return 1;
        }

        // Test 3: Create a test notification
        $this->info("\n3. Creating test notification...");
        try {
            $testNotification = AppNotification::create([
                'user_id' => 1,
                'title' => 'Test Notification',
                'body' => 'This is a test notification created by the system',
                'type' => 'general',
                'is_read' => false
            ]);
            $this->info("   ✅ Test notification created with ID: {$testNotification->id}");
        } catch (\Exception $e) {
            $this->error("   ❌ Failed to create notification: " . $e->getMessage());
            return 1;
        }

        // Test 4: Test notification controller methods
        $this->info("\n4. Testing notification controller...");
        try {
            $controller = new NotificationController(app(\App\Services\FirebaseNotificationService::class));
            
            // Test sendToAll method
            $request = new Request([
                'title' => 'Test Notification from Controller',
                'body' => 'This notification was sent via the controller',
                'type' => 'general'
            ]);
            
            $response = $controller->sendToAll($request);
            $responseData = $response->getData(true);
            
            if ($responseData['success']) {
                $this->info("   ✅ Controller sendToAll method works");
                $this->info("   📊 Sent to: {$responseData['sent_count']} users");
            } else {
                $this->error("   ❌ Controller sendToAll failed: " . $responseData['message']);
            }
            
        } catch (\Exception $e) {
            $this->error("   ❌ Controller test failed: " . $e->getMessage());
        }

        // Test 5: Check final notification count
        $this->info("\n5. Final notification count...");
        $finalCount = AppNotification::count();
        $this->info("   Total notifications now: {$finalCount}");

        $this->info("\n🎉 Notification system test completed!");
        return 0;
    }
}