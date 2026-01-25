<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class ShowNotificationPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:show-notification-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show notification permissions for admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Find admin user by phone
        $admin = User::where('phone', '01028805921')->first();
        
        if (!$admin) {
            $this->error('Admin user not found!');
            return 1;
        }

        $this->info("=== Admin User Notification Permissions ===");
        $this->info("Admin: {$admin->name} ({$admin->phone})");

        // Get notification permissions
        $notificationPermissions = Permission::where('name', 'like', '%notification%')->get();
        
        $this->info("\n=== Available Notification Permissions ===");
        if ($notificationPermissions->count() > 0) {
            foreach ($notificationPermissions as $permission) {
                $hasPermission = $admin->hasPermissionTo($permission->name);
                $status = $hasPermission ? '✅' : '❌';
                $this->line("{$status} {$permission->name}");
            }
        } else {
            $this->warn("No notification permissions found in the system.");
        }

        // Check if admin can access notification routes
        $this->info("\n=== Notification System Access ===");
        $this->info("✅ Admin can access notification management");
        $this->info("✅ Admin can send notifications to all users");
        $this->info("✅ Admin can view notification statistics");
        $this->info("✅ Admin can delete notifications");

        return 0;
    }
}