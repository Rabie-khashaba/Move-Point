<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\AppNotification;

class TestPagination extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:pagination';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test pagination by creating multiple notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("🔢 Testing Pagination System");
        $this->info("===========================");

        // Get all users
        $users = User::whereIn('type', ['employee', 'representative', 'supervisor'])->get();
        
        if ($users->isEmpty()) {
            $this->error("No users found! Please run the seeders first.");
            return 1;
        }

        $this->info("Found {$users->count()} users");

        // Create 50 test notifications to test pagination
        $this->info("\nCreating 50 test notifications...");
        
        for ($i = 1; $i <= 50; $i++) {
            $user = $users->random();
            
            AppNotification::create([
                'user_id' => $user->id,
                'title' => "إشعار تجريبي رقم {$i}",
                'body' => "هذا إشعار تجريبي لاختبار نظام الصفحات. رقم الإشعار: {$i}",
                'type' => 'general',
                'is_read' => rand(0, 1) == 1,
                'created_at' => now()->subDays(rand(0, 30))->subHours(rand(0, 23))
            ]);
            
            if ($i % 10 == 0) {
                $this->info("   Created {$i} notifications...");
            }
        }

        // Check pagination
        $this->info("\nTesting pagination...");
        
        $totalNotifications = AppNotification::count();
        $this->info("Total notifications: {$totalNotifications}");
        
        // Test first page
        $firstPage = AppNotification::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        $this->info("First page items: {$firstPage->count()}");
        $this->info("Total pages: {$firstPage->lastPage()}");
        $this->info("Current page: {$firstPage->currentPage()}");
        $this->info("Per page: {$firstPage->perPage()}");
        
        // Test second page if exists
        if ($firstPage->hasMorePages()) {
            $secondPage = AppNotification::with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(20, ['*'], 'page', 2);
                
            $this->info("Second page items: {$secondPage->count()}");
        }

        $this->info("\n✅ Pagination test completed!");
        $this->info("Now you can test the pagination in your browser at /notifications");
        $this->info("You should see pagination controls at the bottom of the notifications list.");

        return 0;
    }
}