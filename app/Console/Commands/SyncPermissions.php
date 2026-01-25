<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PermissionService;

class SyncPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:sync';
    

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync permissions with Arabic display names and descriptions';

    /**
     * Execute the console command.
     */
    public function handle(PermissionService $permissionService)
    {
        $this->info('Starting permission sync...');
        
        try {
            $result = $permissionService->syncPermissions();
            
            $this->info("✅ Sync completed successfully!");
            $this->info("📊 Created: {$result['created']} permissions");
            $this->info("📝 Updated: {$result['updated']} permissions");
            $this->info("📋 Total synced: {$result['total_synced']} permissions");
            
            $this->info("\n📋 Synced permissions:");
            foreach ($result['synced_permissions'] as $permission) {
                $this->line("  - {$permission}");
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Error during sync: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
