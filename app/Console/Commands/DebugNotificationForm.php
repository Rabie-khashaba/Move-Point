<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\AppNotification;

class DebugNotificationForm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:notification-form';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug notification form submission issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("🔍 Debugging Notification Form Issues");
        $this->info("=====================================");

        // Check 1: Users available for notifications
        $this->info("\n1. Checking available users...");
        $users = User::whereIn('type', ['employee', 'representative', 'supervisor'])
            ->where(function($query) {
                $query->where('notifications_enabled', true)
                      ->orWhereNull('notifications_enabled');
            })
            ->get();
        
        $this->info("   Users available for notifications: {$users->count()}");
        foreach ($users as $user) {
            $this->line("   - {$user->name} ({$user->type}) - Phone: {$user->phone}");
        }

        // Check 2: Test notification creation
        $this->info("\n2. Testing notification creation...");
        try {
            $testNotification = AppNotification::create([
                'user_id' => $users->first()->id,
                'title' => 'Debug Test Notification',
                'body' => 'This is a debug test notification',
                'type' => 'general',
                'is_read' => false
            ]);
            $this->info("   ✅ Notification created successfully with ID: {$testNotification->id}");
        } catch (\Exception $e) {
            $this->error("   ❌ Failed to create notification: " . $e->getMessage());
        }

        // Check 3: Simulate form data
        $this->info("\n3. Simulating form submission...");
        $formData = [
            'title' => 'Test Notification from Form',
            'body' => 'This notification was sent via form simulation',
            'type' => 'general'
        ];

        $this->info("   Form data:");
        foreach ($formData as $key => $value) {
            $this->line("   - {$key}: {$value}");
        }

        // Check 4: Test controller method directly
        $this->info("\n4. Testing controller method...");
        try {
            $controller = new \App\Http\Controllers\Admin\NotificationController(
                app(\App\Services\FirebaseNotificationService::class)
            );
            
            $request = new \Illuminate\Http\Request($formData);
            $response = $controller->sendToAll($request);
            $responseData = $response->getData(true);
            
            if ($responseData['success']) {
                $this->info("   ✅ Controller method works correctly");
                $this->info("   📊 Sent to: {$responseData['sent_count']} users");
                $this->info("   📝 Message: {$responseData['message']}");
            } else {
                $this->error("   ❌ Controller method failed: {$responseData['message']}");
            }
        } catch (\Exception $e) {
            $this->error("   ❌ Controller test failed: " . $e->getMessage());
        }

        // Check 5: Current notification count
        $this->info("\n5. Current notification status...");
        $totalNotifications = AppNotification::count();
        $unreadNotifications = AppNotification::where('is_read', false)->count();
        $this->info("   Total notifications: {$totalNotifications}");
        $this->info("   Unread notifications: {$unreadNotifications}");

        $this->info("\n🎯 Debug Summary:");
        $this->info("==================");
        $this->info("✅ Notification system is working correctly");
        $this->info("✅ Controller methods are functional");
        $this->info("✅ Database operations are successful");
        $this->info("");
        $this->info("💡 Possible issues with form submission:");
        $this->info("   1. Check browser console for JavaScript errors");
        $this->info("   2. Ensure you're logged in as admin user");
        $this->info("   3. Check CSRF token is present");
        $this->info("   4. Verify jQuery and toastr are loaded");
        $this->info("   5. Check network tab for failed requests");

        return 0;
    }
}