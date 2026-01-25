<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class AssignAllPermissionsToAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:assign-all-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign all permissions to the admin user';

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

        $this->info("Found admin user: {$admin->name} ({$admin->phone})");

        // Get all permissions
        $permissions = Permission::all();
        $this->info("Total permissions available: {$permissions->count()}");

        // Get current permissions count
        $currentPermissionsCount = $admin->getAllPermissions()->count();
        $this->info("Current permissions: {$currentPermissionsCount}");

        // Assign all permissions
        $admin->givePermissionTo($permissions);

        // Verify
        $newPermissionsCount = $admin->getAllPermissions()->count();
        $this->info("New permissions count: {$newPermissionsCount}");

        if ($newPermissionsCount > $currentPermissionsCount) {
            $this->info("✅ Successfully assigned all permissions to admin user!");
        } else {
            $this->warn("⚠️  No new permissions were assigned. Admin might already have all permissions.");
        }

        return 0;
    }
}