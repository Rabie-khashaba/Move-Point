<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Create or update admin user
        $admin = User::updateOrCreate(
            ['phone' => '01028805921'],
            [
                'password' => Hash::make('password123'),
                'type' => 'admin',
            ]
        );

        // Create admin role
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Define comprehensive permissions for all modules
        $permissions = [
            // User Management
            'view_users', 'create_users', 'edit_users', 'delete_users',
            
            // Company Management
            'view_companies', 'create_companies', 'edit_companies', 'delete_companies',
            
            // Department Management
            'view_departments', 'create_departments', 'edit_departments', 'delete_departments',
            
            // Governorate Management
            'view_governorates', 'create_governorates', 'edit_governorates', 'delete_governorates',
            
            // Location Management
            'view_locations', 'create_locations', 'edit_locations', 'delete_locations',
            
            // Representative Management
            'view_representatives', 'create_representatives', 'edit_representatives', 'delete_representatives',
            
            // Supervisor Management
            'view_supervisors', 'create_supervisors', 'edit_supervisors', 'delete_supervisors',
            
            // Employee Management
            'view_employees', 'create_employees', 'edit_employees', 'delete_employees',
            
            // Lead Management
            'view_leads', 'create_leads', 'edit_leads', 'delete_leads', 'add_followup',
            
            // Permission & Role Management
            'view_permissions', 'create_permissions', 'edit_permissions', 'delete_permissions',
            'view_roles', 'create_roles', 'edit_roles', 'delete_roles',
            
            // Source Management
            'view_sources', 'create_sources', 'edit_sources', 'delete_sources',
            
            // Dashboard & Reports
            'view_dashboard', 'view_reports', 'export_data',
            
            // System Settings
            'manage_settings', 'view_logs', 'manage_backups',
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Sync all permissions with admin role
        $adminRole->syncPermissions($permissions);

        // Assign admin role to user (avoid duplicates)
        if (!$admin->hasRole($adminRole->name)) {
            $admin->assignRole('admin');
        }

        // Give user direct permissions as well (super admin)
        $admin->givePermissionTo($permissions);

        $this->command->info("Admin user {$admin->phone} created/updated with ALL permissions!");
    }
}
