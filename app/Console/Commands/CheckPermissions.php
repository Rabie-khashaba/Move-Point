<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Permission;

class CheckPermissions extends Command
{
    protected $signature = 'permissions:check {module?}';
    protected $description = 'Check permissions by module';

    public function handle()
    {
        $module = $this->argument('module') ?? 'hr';
        
        $this->info("Checking permissions for module: {$module}");
        $this->line('');
        
        $permissions = Permission::where('module', $module)->get(['name', 'display_name']);
        
        if ($permissions->count() > 0) {
            $this->table(['Permission Name', 'Display Name'], $permissions->map(function($p) {
                return [$p->name, $p->display_name];
            }));
        } else {
            $this->warn("No permissions found for module: {$module}");
        }
        
        return 0;
    }
}
