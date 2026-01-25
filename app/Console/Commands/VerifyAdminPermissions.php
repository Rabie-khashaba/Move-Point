<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class VerifyAdminPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:verify-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify admin user permissions';

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

        $this->info("=== Admin User Details ===");
        $this->info("Name: {$admin->name}");
        $this->info("Phone: {$admin->phone}");
        $this->info("Type: {$admin->type}");
        $this->info("ID: {$admin->id}");

        // Get all permissions
        $totalPermissions = Permission::count();
        $adminPermissions = $admin->getAllPermissions()->count();
        
        $this->info("\n=== Permissions Summary ===");
        $this->info("Total permissions in system: {$totalPermissions}");
        $this->info("Admin user permissions: {$adminPermissions}");
        
        if ($adminPermissions === $totalPermissions) {
            $this->info("✅ Admin user has ALL permissions!");
        } else {
            $this->warn("⚠️  Admin user is missing " . ($totalPermissions - $adminPermissions) . " permissions");
        }

        // Show some sample permissions
        $this->info("\n=== Sample Permissions ===");
        $samplePermissions = $admin->getAllPermissions()->take(10);
        foreach ($samplePermissions as $permission) {
            $this->line("- {$permission->name}");
        }
        
        if ($adminPermissions > 10) {
            $this->info("... and " . ($adminPermissions - 10) . " more permissions");
        }

        return 0;
    }
}