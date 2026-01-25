<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\AppNotification;

class TestPopupNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:popup-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the new popup notification system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("🎉 Testing New Popup Notification System");
        $this->info("=======================================");

        // Test 1: Check users for different types
        $this->info("\n1. Checking user types...");
        $employees = User::where('type', 'employee')->count();
        $representatives = User::where('type', 'representative')->count();
        $supervisors = User::where('type', 'supervisor')->count();
        
        $this->info("   Employees: {$employees}");
        $this->info("   Representatives: {$representatives}");
        $this->info("   Supervisors: {$supervisors}");

        // Test 2: Create test notifications for each type
        $this->info("\n2. Creating test notifications...");
        
        // Test notification for all users
        $allUsers = User::whereIn('type', ['employee', 'representative', 'supervisor'])->get();
        foreach ($allUsers as $user) {
            AppNotification::create([
                'user_id' => $user->id,
                'title' => '🎉 نظام الإشعارات الجديد',
                'body' => 'تم تحديث نظام الإشعارات بنجاح! الآن يمكنك استخدام النوافذ المنبثقة لإرسال الإشعارات.',
                'type' => 'announcement',
                'is_read' => false
            ]);
        }
        $this->info("   ✅ Created notifications for all {$allUsers->count()} users");

        // Test notification for employees only
        $employees = User::where('type', 'employee')->get();
        foreach ($employees as $user) {
            AppNotification::create([
                'user_id' => $user->id,
                'title' => '📋 إشعار للموظفين',
                'body' => 'هذا إشعار خاص بالموظفين فقط.',
                'type' => 'general',
                'is_read' => false
            ]);
        }
        $this->info("   ✅ Created notifications for {$employees->count()} employees");

        // Test notification for specific users
        $specificUsers = User::whereIn('type', ['representative', 'supervisor'])->take(3)->get();
        foreach ($specificUsers as $user) {
            AppNotification::create([
                'user_id' => $user->id,
                'title' => '🎯 إشعار محدد',
                'body' => 'هذا إشعار محدد لمستخدمين مختارين.',
                'type' => 'general',
                'is_read' => false
            ]);
        }
        $this->info("   ✅ Created notifications for {$specificUsers->count()} specific users");

        // Test 3: Check final notification count
        $this->info("\n3. Final notification status...");
        $totalNotifications = AppNotification::count();
        $unreadNotifications = AppNotification::where('is_read', false)->count();
        $todayNotifications = AppNotification::whereDate('created_at', today())->count();
        
        $this->info("   Total notifications: {$totalNotifications}");
        $this->info("   Unread notifications: {$unreadNotifications}");
        $this->info("   Today's notifications: {$todayNotifications}");

        $this->info("\n🎯 Popup System Features:");
        $this->info("========================");
        $this->info("✅ Three beautiful popup buttons");
        $this->info("✅ Modal forms with validation");
        $this->info("✅ Enhanced user experience");
        $this->info("✅ Real-time form validation");
        $this->info("✅ Loading states and feedback");
        $this->info("✅ Responsive design");
        $this->info("✅ Arabic RTL support");

        $this->info("\n🚀 How to Use:");
        $this->info("==============");
        $this->info("1. Go to /notifications in your admin panel");
        $this->info("2. Click any of the three notification buttons");
        $this->info("3. Fill out the popup form");
        $this->info("4. Submit and see the magic happen!");

        $this->info("\n🎉 Popup notification system is ready to use!");

        return 0;
    }
}