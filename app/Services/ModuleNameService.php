<?php

namespace App\Services;

class ModuleNameService
{
    /**
     * Get Arabic display name for a module
     */
    public static function getArabicName($module)
    {
        $arabicNames = [
            'users' => 'المستخدمين',
            'roles' => 'الأدوار',
            'permissions' => 'الصلاحيات',
            'leads' => 'العملاء المحتملين',
            'representatives' => 'الممثلون',
            'interviews' => 'المقابلات',
            'messages' => 'الرسائل',
            'dashboard' => 'لوحة التحكم',
            'reports' => 'التقارير',
            'settings' => 'الإعدادات',
            'companies' => 'الشركات',
            'departments' => 'الأقسام',
            'governorates' => 'المحافظات',
            'locations' => 'المواقع',
            'supervisors' => 'المشرفين',
            'employees' => 'الموظفين',
            'sources' => 'المصادر',
            'system' => 'النظام',
            'Supervisor' => 'المشرف',
            'other' => 'أخرى',
        ];

        return $arabicNames[$module] ?? ucfirst($module);
    }

    /**
     * Get all available module names from permissions
     */
    public static function getAvailableModules($permissions)
    {
        $modules = [];
        
        foreach ($permissions as $permission) {
            $module = $permission->module ?? 'other';
            if (!isset($modules[$module])) {
                $modules[$module] = self::getArabicName($module);
            }
        }

        return $modules;
    }

    /**
     * Group permissions by module with Arabic names
     */
    public static function groupPermissionsByModule($permissions)
    {
        $groups = [];
        $moduleNames = self::getAvailableModules($permissions);

        foreach ($permissions as $permission) {
            $module = $permission->module ?? 'other';
            $moduleName = $moduleNames[$module];
            $groups[$moduleName][] = $permission;
        }

        return $groups;
    }
}
