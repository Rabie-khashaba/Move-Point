<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class FinalAdminPermissionCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:final-permission-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Final comprehensive check of ALL admin permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Find admin user by phone
        $admin = User::where('phone', '01028805921')->first();
        
        if (!$admin) {
            $this->error('❌ Admin user not found!');
            return 1;
        }

        $this->info("🔍 FINAL ADMIN PERMISSION VERIFICATION");
        $this->info("=====================================");
        $this->info("Admin: {$admin->name} ({$admin->phone})");
        $this->info("User ID: {$admin->id}");
        $this->info("User Type: {$admin->type}");

        // Get all permissions that actually exist in the system
        $allPermissions = Permission::orderBy('name')->get();
        $adminPermissions = $admin->getAllPermissions();
        
        $this->info("\n📊 PERMISSION SUMMARY");
        $this->info("====================");
        $this->info("Total permissions in system: {$allPermissions->count()}");
        $this->info("Admin user permissions: {$adminPermissions->count()}");
        
        if ($adminPermissions->count() === $allPermissions->count()) {
            $this->info("✅ SUCCESS: Admin has ALL {$allPermissions->count()} permissions!");
        } else {
            $this->error("❌ ERROR: Admin missing " . ($allPermissions->count() - $adminPermissions->count()) . " permissions");
        }

        // Show ALL permissions with status
        $this->info("\n📋 COMPLETE PERMISSION LIST");
        $this->info("===========================");
        
        $permissionCount = 0;
        $hasAllPermissions = true;
        
        foreach ($allPermissions as $permission) {
            $permissionCount++;
            $hasPermission = $admin->hasPermissionTo($permission->name);
            $status = $hasPermission ? '✅' : '❌';
            $this->line(sprintf("%2d. %s %s", $permissionCount, $status, $permission->name));
            
            if (!$hasPermission) {
                $hasAllPermissions = false;
            }
        }

        // Group by functionality
        $this->info("\n🏷️  PERMISSIONS BY FUNCTIONALITY");
        $this->info("================================");
        
        $groups = [
            'Users' => $allPermissions->filter(fn($p) => str_contains($p->name, 'user')),
            'Companies' => $allPermissions->filter(fn($p) => str_contains($p->name, 'compan')),
            'Departments' => $allPermissions->filter(fn($p) => str_contains($p->name, 'department')),
            'Employees' => $allPermissions->filter(fn($p) => str_contains($p->name, 'employee')),
            'Leads' => $allPermissions->filter(fn($p) => str_contains($p->name, 'lead')),
            'Notifications' => $allPermissions->filter(fn($p) => str_contains($p->name, 'notification')),
            'Permissions' => $allPermissions->filter(fn($p) => str_contains($p->name, 'permission')),
            'Roles' => $allPermissions->filter(fn($p) => str_contains($p->name, 'role')),
            'Reports' => $allPermissions->filter(fn($p) => str_contains($p->name, 'report')),
            'Settings' => $allPermissions->filter(fn($p) => str_contains($p->name, 'setting')),
            'Dashboard' => $allPermissions->filter(fn($p) => str_contains($p->name, 'dashboard')),
            'Logs' => $allPermissions->filter(fn($p) => str_contains($p->name, 'log')),
            'Backups' => $allPermissions->filter(fn($p) => str_contains($p->name, 'backup')),
            'Export' => $allPermissions->filter(fn($p) => str_contains($p->name, 'export')),
            'Passwords' => $allPermissions->filter(fn($p) => str_contains($p->name, 'password')),
        ];

        foreach ($groups as $groupName => $permissions) {
            if ($permissions->count() > 0) {
                $this->info("\n--- {$groupName} ({$permissions->count()}) ---");
                foreach ($permissions as $permission) {
                    $hasPermission = $admin->hasPermissionTo($permission->name);
                    $status = $hasPermission ? '✅' : '❌';
                    $this->line("  {$status} {$permission->name}");
                }
            }
        }

        // Final result
        $this->info("\n🎯 FINAL RESULT");
        $this->info("===============");
        
        if ($hasAllPermissions) {
            $this->info("🎉 ADMIN USER HAS ALL {$allPermissions->count()} PERMISSIONS!");
            $this->info("✅ Complete system access confirmed");
            $this->info("✅ Ready for full administration");
            $this->info("✅ All features accessible");
            return 0;
        } else {
            $this->error("⚠️  ADMIN USER IS MISSING SOME PERMISSIONS");
            $this->error("❌ System access incomplete");
            return 1;
        }
    }
}