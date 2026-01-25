<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class VerifyAllAdminPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:verify-all-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify ALL permissions are assigned to admin user with detailed list';

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

        $this->info("=== COMPREHENSIVE ADMIN PERMISSION VERIFICATION ===");
        $this->info("Admin: {$admin->name} ({$admin->phone})");
        $this->info("User ID: {$admin->id}");
        $this->info("User Type: {$admin->type}");

        // Get all permissions in system
        $allPermissions = Permission::orderBy('name')->get();
        $adminPermissions = $admin->getAllPermissions();
        
        $this->info("\n=== PERMISSION SUMMARY ===");
        $this->info("Total permissions in system: {$allPermissions->count()}");
        $this->info("Admin user permissions: {$adminPermissions->count()}");
        
        if ($adminPermissions->count() === $allPermissions->count()) {
            $this->info("✅ SUCCESS: Admin user has ALL permissions!");
        } else {
            $this->error("❌ ERROR: Admin user is missing " . ($allPermissions->count() - $adminPermissions->count()) . " permissions");
        }

        // Check each permission individually
        $this->info("\n=== DETAILED PERMISSION CHECK ===");
        $missingPermissions = [];
        $hasAllPermissions = true;

        foreach ($allPermissions as $permission) {
            $hasPermission = $admin->hasPermissionTo($permission->name);
            $status = $hasPermission ? '✅' : '❌';
            $this->line("{$status} {$permission->name}");
            
            if (!$hasPermission) {
                $missingPermissions[] = $permission->name;
                $hasAllPermissions = false;
            }
        }

        // Show missing permissions if any
        if (!empty($missingPermissions)) {
            $this->error("\n=== MISSING PERMISSIONS ===");
            foreach ($missingPermissions as $missing) {
                $this->error("❌ {$missing}");
            }
        }

        // Group permissions by category
        $this->info("\n=== PERMISSIONS BY CATEGORY ===");
        $categories = [
            'User Management' => ['view_users', 'create_users', 'edit_users', 'delete_users'],
            'Company Management' => ['view_companies', 'create_companies', 'edit_companies', 'delete_companies'],
            'Department Management' => ['view_departments', 'create_departments', 'edit_departments', 'delete_departments'],
            'Lead Management' => ['view_leads', 'create_leads', 'edit_leads', 'delete_leads'],
            'Notification Management' => ['view_notifications', 'send_notifications', 'delete_notifications'],
            'Financial Management' => ['view_finances', 'create_finances', 'edit_finances', 'delete_finances'],
            'Report Management' => ['view_reports', 'create_reports', 'edit_reports', 'delete_reports'],
        ];

        foreach ($categories as $category => $permissions) {
            $this->info("\n--- {$category} ---");
            foreach ($permissions as $permission) {
                $hasPermission = $admin->hasPermissionTo($permission);
                $status = $hasPermission ? '✅' : '❌';
                $this->line("{$status} {$permission}");
            }
        }

        // Final verification
        $this->info("\n=== FINAL VERIFICATION ===");
        if ($hasAllPermissions) {
            $this->info("🎉 ADMIN USER HAS ALL {$allPermissions->count()} PERMISSIONS!");
            $this->info("✅ Full system access confirmed");
            $this->info("✅ Ready for complete administration");
        } else {
            $this->error("⚠️  ADMIN USER IS MISSING PERMISSIONS");
            $this->error("❌ System access incomplete");
        }

        return $hasAllPermissions ? 0 : 1;
    }
}