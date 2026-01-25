<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use App\Services\PermissionService;
use Spatie\Permission\PermissionRegistrar;

class GrantAllPermissionsToFirstUser extends Command
{
    protected $signature = 'permissions:grant-all-first-user';

    protected $description = 'Sync permissions then grant all permissions to the first user';

    public function handle(PermissionService $permissionService)
    {
        $this->info('Syncing permissions...');
        $permissionService->syncPermissions();

        // Clear Spatie cached permissions to avoid stale lookups
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $firstUser = User::orderBy('id')->first();
        if (!$firstUser) {
            $this->error('No users found.');
            return 1;
        }

        $allPermissions = Permission::pluck('name')->all();
        $firstUser->syncPermissions($allPermissions);

        $this->info('Granted '.count($allPermissions).' permissions to user ID '.$firstUser->id);
        return 0;
    }
}
