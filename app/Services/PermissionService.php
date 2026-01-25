<?php
namespace App\Services;
use App\Repositories\PermissionRepository;

class PermissionService
{
    protected $repository;

    public function __construct(PermissionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function all()
    {
        return $this->repository->all();
    }

    public function paginated($perPage = 20)
    {
        return $this->repository->query()->paginate($perPage);
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }

    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function update($id, array $data)
    {
        $permission = $this->repository->find($id);
        return $this->repository->update($permission, $data);
    }

    public function delete($id)
    {
        $permission = $this->repository->find($id);
        $this->repository->delete($permission);
    }

    /**
     * Sync permissions with predefined list
     */
    public function syncPermissions()
    {
        $permissions = $this->getDefaultPermissions();
        $synced = [];
        $created = 0;
        $updated = 0;

        foreach ($permissions as $permissionData) {
            $permission = $this->repository->findBy('name', $permissionData['name']);
            
            if ($permission) {
                // Update existing permission
                $this->repository->update($permission, $permissionData);
                $updated++;
            } else {
                // Create new permission
                $this->repository->create($permissionData);
                $created++;
            }
            
            $synced[] = $permissionData['name'];
        }

        return [
            'created' => $created,
            'updated' => $updated,
            'total_synced' => count($synced),
            'synced_permissions' => $synced
        ];
    }

    /**
     * Get default permissions list
     */
    public function getDefaultPermissions()
    {
        return [
            // Users Management
            ['name' => 'view_users', 'display_name' => 'عرض المستخدمين', 'description' => 'إمكانية عرض قائمة المستخدمين', 'module' => 'users', 'guard_name' => 'web'],
            ['name' => 'create_users', 'display_name' => 'إنشاء مستخدمين', 'description' => 'إمكانية إنشاء مستخدمين جدد', 'module' => 'users', 'guard_name' => 'web'],
            ['name' => 'edit_users', 'display_name' => 'تعديل المستخدمين', 'description' => 'إمكانية تعديل بيانات المستخدمين', 'module' => 'users', 'guard_name' => 'web'],
            ['name' => 'delete_users', 'display_name' => 'حذف المستخدمين', 'description' => 'إمكانية حذف المستخدمين', 'module' => 'users', 'guard_name' => 'web'],
            ['name' => 'manage_users', 'display_name' => 'إدارة المستخدمين', 'description' => 'إدارة كاملة للمستخدمين', 'module' => 'users', 'guard_name' => 'web'],

            // Roles Management
            ['name' => 'view_roles', 'display_name' => 'عرض الأدوار', 'description' => 'إمكانية عرض قائمة الأدوار', 'module' => 'roles', 'guard_name' => 'web'],
            ['name' => 'create_roles', 'display_name' => 'إنشاء أدوار', 'description' => 'إمكانية إنشاء أدوار جديدة', 'module' => 'roles', 'guard_name' => 'web'],
            ['name' => 'edit_roles', 'display_name' => 'تعديل الأدوار', 'description' => 'إمكانية تعديل الأدوار', 'module' => 'roles', 'guard_name' => 'web'],
            ['name' => 'delete_roles', 'display_name' => 'حذف الأدوار', 'description' => 'إمكانية حذف الأدوار', 'module' => 'roles', 'guard_name' => 'web'],
            ['name' => 'manage_roles', 'display_name' => 'إدارة الأدوار', 'description' => 'إدارة كاملة للأدوار', 'module' => 'roles', 'guard_name' => 'web'],

            // Permissions Management
            ['name' => 'view_permissions', 'display_name' => 'عرض الصلاحيات', 'description' => 'إمكانية عرض قائمة الصلاحيات', 'module' => 'permissions', 'guard_name' => 'web'],
            ['name' => 'create_permissions', 'display_name' => 'إنشاء صلاحيات', 'description' => 'إمكانية إنشاء صلاحيات جديدة', 'module' => 'permissions', 'guard_name' => 'web'],
            ['name' => 'edit_permissions', 'display_name' => 'تعديل الصلاحيات', 'description' => 'إمكانية تعديل الصلاحيات', 'module' => 'permissions', 'guard_name' => 'web'],
            ['name' => 'delete_permissions', 'display_name' => 'حذف الصلاحيات', 'description' => 'إمكانية حذف الصلاحيات', 'module' => 'permissions', 'guard_name' => 'web'],
            ['name' => 'manage_permissions', 'display_name' => 'إدارة الصلاحيات', 'description' => 'إدارة كاملة للصلاحيات', 'module' => 'permissions', 'guard_name' => 'web'],

            // Leads Management
            ['name' => 'view_leads', 'display_name' => 'عرض العملاء المحتملين', 'description' => 'إمكانية عرض قائمة العملاء المحتملين', 'module' => 'leads', 'guard_name' => 'web'],
            ['name' => 'create_leads', 'display_name' => 'إنشاء عملاء محتملين', 'description' => 'إمكانية إنشاء عملاء محتملين جدد', 'module' => 'leads', 'guard_name' => 'web'],
            ['name' => 'edit_leads', 'display_name' => 'تعديل العملاء المحتملين', 'description' => 'إمكانية تعديل بيانات العملاء المحتملين', 'module' => 'leads', 'guard_name' => 'web'],
            ['name' => 'delete_leads', 'display_name' => 'حذف العملاء المحتملين', 'description' => 'إمكانية حذف العملاء المحتملين', 'module' => 'leads', 'guard_name' => 'web'],
            ['name' => 'manage_leads', 'display_name' => 'إدارة العملاء المحتملين', 'description' => 'إدارة كاملة للعملاء المحتملين', 'module' => 'leads', 'guard_name' => 'web'],

            // Representatives Management
            ['name' => 'view_representatives', 'display_name' => 'عرض الممثلين', 'description' => 'إمكانية عرض قائمة الممثلين', 'module' => 'representatives', 'guard_name' => 'web'],
            ['name' => 'create_representatives', 'display_name' => 'إنشاء ممثلين', 'description' => 'إمكانية إنشاء ممثلين جدد', 'module' => 'representatives', 'guard_name' => 'web'],
            ['name' => 'edit_representatives', 'display_name' => 'تعديل الممثلين', 'description' => 'إمكانية تعديل بيانات الممثلين', 'module' => 'representatives', 'guard_name' => 'web'],
            ['name' => 'delete_representatives', 'display_name' => 'حذف الممثلين', 'description' => 'إمكانية حذف الممثلين', 'module' => 'representatives', 'guard_name' => 'web'],
            ['name' => 'manage_representatives', 'display_name' => 'إدارة الممثلين', 'description' => 'إدارة كاملة للممثلين', 'module' => 'representatives', 'guard_name' => 'web'],

            // Interviews Management
            ['name' => 'view_interviews', 'display_name' => 'عرض المقابلات', 'description' => 'إمكانية عرض قائمة المقابلات', 'module' => 'interviews', 'guard_name' => 'web'],
            ['name' => 'create_interviews', 'display_name' => 'إنشاء مقابلات', 'description' => 'إمكانية إنشاء مقابلات جديدة', 'module' => 'interviews', 'guard_name' => 'web'],
            ['name' => 'edit_interviews', 'display_name' => 'تعديل المقابلات', 'description' => 'إمكانية تعديل المقابلات', 'module' => 'interviews', 'guard_name' => 'web'],
            ['name' => 'delete_interviews', 'display_name' => 'حذف المقابلات', 'description' => 'إمكانية حذف المقابلات', 'module' => 'interviews', 'guard_name' => 'web'],
            ['name' => 'manage_interviews', 'display_name' => 'إدارة المقابلات', 'description' => 'إدارة كاملة للمقابلات', 'module' => 'interviews', 'guard_name' => 'web'],

            // Messages Management
            ['name' => 'view_messages', 'display_name' => 'عرض الرسائل', 'description' => 'إمكانية عرض قائمة الرسائل', 'module' => 'messages', 'guard_name' => 'web'],
            ['name' => 'create_messages', 'display_name' => 'إنشاء رسائل', 'description' => 'إمكانية إنشاء رسائل جديدة', 'module' => 'messages', 'guard_name' => 'web'],
            ['name' => 'edit_messages', 'display_name' => 'تعديل الرسائل', 'description' => 'إمكانية تعديل الرسائل', 'module' => 'messages', 'guard_name' => 'web'],
            ['name' => 'delete_messages', 'display_name' => 'حذف الرسائل', 'description' => 'إمكانية حذف الرسائل', 'module' => 'messages', 'guard_name' => 'web'],
            ['name' => 'manage_messages', 'display_name' => 'إدارة الرسائل', 'description' => 'إدارة كاملة للرسائل', 'module' => 'messages', 'guard_name' => 'web'],

            // System Settings
            ['name' => 'view_settings', 'display_name' => 'عرض الإعدادات', 'description' => 'إمكانية عرض إعدادات النظام', 'module' => 'settings', 'guard_name' => 'web'],
            ['name' => 'edit_settings', 'display_name' => 'تعديل الإعدادات', 'description' => 'إمكانية تعديل إعدادات النظام', 'module' => 'settings', 'guard_name' => 'web'],
            ['name' => 'manage_settings', 'display_name' => 'إدارة الإعدادات', 'description' => 'إدارة كاملة لإعدادات النظام', 'module' => 'settings', 'guard_name' => 'web'],

            // Reports
            ['name' => 'view_reports', 'display_name' => 'عرض التقارير', 'description' => 'إمكانية عرض التقارير', 'module' => 'reports', 'guard_name' => 'web'],
            ['name' => 'generate_reports', 'display_name' => 'إنشاء تقارير', 'description' => 'إمكانية إنشاء تقارير جديدة', 'module' => 'reports', 'guard_name' => 'web'],
            ['name' => 'export_reports', 'display_name' => 'تصدير التقارير', 'description' => 'إمكانية تصدير التقارير', 'module' => 'reports', 'guard_name' => 'web'],

            // Dashboard
            ['name' => 'view_dashboard', 'display_name' => 'عرض لوحة التحكم', 'description' => 'إمكانية عرض لوحة التحكم', 'module' => 'dashboard', 'guard_name' => 'web'],
            ['name' => 'manage_dashboard', 'display_name' => 'إدارة لوحة التحكم', 'description' => 'إدارة كاملة للوحة التحكم', 'module' => 'dashboard', 'guard_name' => 'web'],
            // Sales Dashboards
            ['name' => 'view_sales_dashboards', 'display_name' => 'عرض لوحات المبيعات', 'description' => 'إمكانية عرض لوحات المبيعات (القسم ولوحتي)', 'module' => 'dashboard', 'guard_name' => 'web'],

            // Companies Management
            ['name' => 'view_companies', 'display_name' => 'عرض الشركات', 'description' => 'إمكانية عرض قائمة الشركات', 'module' => 'companies', 'guard_name' => 'web'],
            ['name' => 'create_companies', 'display_name' => 'إنشاء شركات', 'description' => 'إمكانية إنشاء شركات جديدة', 'module' => 'companies', 'guard_name' => 'web'],
            ['name' => 'edit_companies', 'display_name' => 'تعديل الشركات', 'description' => 'إمكانية تعديل بيانات الشركات', 'module' => 'companies', 'guard_name' => 'web'],
            ['name' => 'delete_companies', 'display_name' => 'حذف الشركات', 'description' => 'إمكانية حذف الشركات', 'module' => 'companies', 'guard_name' => 'web'],

            // Departments Management
            ['name' => 'view_departments', 'display_name' => 'عرض الأقسام', 'description' => 'إمكانية عرض قائمة الأقسام', 'module' => 'departments', 'guard_name' => 'web'],
            ['name' => 'create_departments', 'display_name' => 'إنشاء أقسام', 'description' => 'إمكانية إنشاء أقسام جديدة', 'module' => 'departments', 'guard_name' => 'web'],
            ['name' => 'edit_departments', 'display_name' => 'تعديل الأقسام', 'description' => 'إمكانية تعديل بيانات الأقسام', 'module' => 'departments', 'guard_name' => 'web'],
            ['name' => 'delete_departments', 'display_name' => 'حذف الأقسام', 'description' => 'إمكانية حذف الأقسام', 'module' => 'departments', 'guard_name' => 'web'],

            // Governorates Management
            ['name' => 'view_governorates', 'display_name' => 'عرض المحافظات', 'description' => 'إمكانية عرض قائمة المحافظات', 'module' => 'governorates', 'guard_name' => 'web'],
            ['name' => 'create_governorates', 'display_name' => 'إنشاء محافظات', 'description' => 'إمكانية إنشاء محافظات جديدة', 'module' => 'governorates', 'guard_name' => 'web'],
            ['name' => 'edit_governorates', 'display_name' => 'تعديل المحافظات', 'description' => 'إمكانية تعديل بيانات المحافظات', 'module' => 'governorates', 'guard_name' => 'web'],
            ['name' => 'delete_governorates', 'display_name' => 'حذف المحافظات', 'description' => 'إمكانية حذف المحافظات', 'module' => 'governorates', 'guard_name' => 'web'],

            // Locations Management
            ['name' => 'view_locations', 'display_name' => 'عرض المواقع', 'description' => 'إمكانية عرض قائمة المواقع', 'module' => 'locations', 'guard_name' => 'web'],
            ['name' => 'create_locations', 'display_name' => 'إنشاء مواقع', 'description' => 'إمكانية إنشاء مواقع جديدة', 'module' => 'locations', 'guard_name' => 'web'],
            ['name' => 'edit_locations', 'display_name' => 'تعديل المواقع', 'description' => 'إمكانية تعديل بيانات المواقع', 'module' => 'locations', 'guard_name' => 'web'],
            ['name' => 'delete_locations', 'display_name' => 'حذف المواقع', 'description' => 'إمكانية حذف المواقع', 'module' => 'locations', 'guard_name' => 'web'],

            // Supervisors Management
            ['name' => 'view_supervisors', 'display_name' => 'عرض المشرفين', 'description' => 'إمكانية عرض قائمة المشرفين', 'module' => 'supervisors', 'guard_name' => 'web'],
            ['name' => 'create_supervisors', 'display_name' => 'إنشاء مشرفين', 'description' => 'إمكانية إنشاء مشرفين جدد', 'module' => 'supervisors', 'guard_name' => 'web'],
            ['name' => 'edit_supervisors', 'display_name' => 'تعديل المشرفين', 'description' => 'إمكانية تعديل بيانات المشرفين', 'module' => 'supervisors', 'guard_name' => 'web'],
            ['name' => 'delete_supervisors', 'display_name' => 'حذف المشرفين', 'description' => 'إمكانية حذف المشرفين', 'module' => 'supervisors', 'guard_name' => 'web'],

            // Employees Management
            ['name' => 'view_employees', 'display_name' => 'عرض الموظفين', 'description' => 'إمكانية عرض قائمة الموظفين', 'module' => 'employees', 'guard_name' => 'web'],
            ['name' => 'create_employees', 'display_name' => 'إنشاء موظفين', 'description' => 'إمكانية إنشاء موظفين جدد', 'module' => 'employees', 'guard_name' => 'web'],
            ['name' => 'edit_employees', 'display_name' => 'تعديل الموظفين', 'description' => 'إمكانية تعديل بيانات الموظفين', 'module' => 'employees', 'guard_name' => 'web'],
            ['name' => 'delete_employees', 'display_name' => 'حذف الموظفين', 'description' => 'إمكانية حذف الموظفين', 'module' => 'employees', 'guard_name' => 'web'],

            // Sources Management
            ['name' => 'view_sources', 'display_name' => 'عرض المصادر', 'description' => 'إمكانية عرض قائمة المصادر', 'module' => 'sources', 'guard_name' => 'web'],
            ['name' => 'create_sources', 'display_name' => 'إنشاء مصادر', 'description' => 'إمكانية إنشاء مصادر جديدة', 'module' => 'sources', 'guard_name' => 'web'],
            ['name' => 'edit_sources', 'display_name' => 'تعديل المصادر', 'description' => 'إمكانية تعديل بيانات المصادر', 'module' => 'sources', 'guard_name' => 'web'],
            ['name' => 'delete_sources', 'display_name' => 'حذف المصادر', 'description' => 'إمكانية حذف المصادر', 'module' => 'sources', 'guard_name' => 'web'],

            // Lead Followup
            ['name' => 'add_followup', 'display_name' => 'إضافة متابعة', 'description' => 'إمكانية إضافة متابعة للعملاء المحتملين', 'module' => 'leads', 'guard_name' => 'web'],

            // Data Export
            ['name' => 'export_data', 'display_name' => 'تصدير البيانات', 'description' => 'إمكانية تصدير البيانات من النظام', 'module' => 'reports', 'guard_name' => 'web'],

            // System Logs
            ['name' => 'view_logs', 'display_name' => 'عرض السجلات', 'description' => 'إمكانية عرض سجلات النظام', 'module' => 'system', 'guard_name' => 'web'],

            // System Backups
            ['name' => 'manage_backups', 'display_name' => 'إدارة النسخ الاحتياطية', 'description' => 'إمكانية إدارة النسخ الاحتياطية للنظام', 'module' => 'system', 'guard_name' => 'web'],

            // HR Management - Leave Requests
            ['name' => 'view_leave_requests', 'display_name' => 'عرض طلبات الإجازة', 'description' => 'إمكانية عرض قائمة طلبات الإجازة', 'module' => 'hr', 'guard_name' => 'web'],
            ['name' => 'create_leave_requests', 'display_name' => 'إنشاء طلبات إجازة', 'description' => 'إمكانية إنشاء طلبات إجازة جديدة', 'module' => 'hr', 'guard_name' => 'web'],
            ['name' => 'edit_leave_requests', 'display_name' => 'تعديل طلبات الإجازة', 'description' => 'إمكانية تعديل طلبات الإجازة', 'module' => 'hr', 'guard_name' => 'web'],
            ['name' => 'delete_leave_requests', 'display_name' => 'حذف طلبات الإجازة', 'description' => 'إمكانية حذف طلبات الإجازة', 'module' => 'hr', 'guard_name' => 'web'],
            ['name' => 'approve_leave_requests', 'display_name' => 'الموافقة على طلبات الإجازة', 'description' => 'إمكانية الموافقة أو رفض طلبات الإجازة', 'module' => 'hr', 'guard_name' => 'web'],

            // HR Management - Work Schedules
            ['name' => 'view_work_schedules', 'display_name' => 'عرض مواعيد العمل', 'description' => 'إمكانية عرض قائمة مواعيد العمل', 'module' => 'hr', 'guard_name' => 'web'],
            ['name' => 'create_work_schedules', 'display_name' => 'إنشاء مواعيد عمل', 'description' => 'إمكانية إنشاء مواعيد عمل جديدة', 'module' => 'hr', 'guard_name' => 'web'],
            ['name' => 'edit_work_schedules', 'display_name' => 'تعديل مواعيد العمل', 'description' => 'إمكانية تعديل مواعيد العمل', 'module' => 'hr', 'guard_name' => 'web'],
            ['name' => 'delete_work_schedules', 'display_name' => 'حذف مواعيد العمل', 'description' => 'إمكانية حذف مواعيد العمل', 'module' => 'hr', 'guard_name' => 'web'],

            // HR Management - Employee Targets
            ['name' => 'view_employee_targets', 'display_name' => 'عرض أهداف الموظفين', 'description' => 'إمكانية عرض أهداف الموظفين', 'module' => 'hr', 'guard_name' => 'web'],
            ['name' => 'create_employee_targets', 'display_name' => 'إنشاء أهداف موظفين', 'description' => 'إمكانية إنشاء أهداف جديدة للموظفين', 'module' => 'hr', 'guard_name' => 'web'],
            ['name' => 'edit_employee_targets', 'display_name' => 'تعديل أهداف الموظفين', 'description' => 'إمكانية تعديل أهداف الموظفين', 'module' => 'hr', 'guard_name' => 'web'],
            ['name' => 'delete_employee_targets', 'display_name' => 'حذف أهداف الموظفين', 'description' => 'إمكانية حذف أهداف الموظفين', 'module' => 'hr', 'guard_name' => 'web'],

            // HR Management - Representative Targets
            ['name' => 'view_representative_targets', 'display_name' => 'عرض أهداف المندوبين', 'description' => 'إمكانية عرض أهداف المندوبين', 'module' => 'Representative Targets', 'guard_name' => 'web'],
            ['name' => 'create_representative_targets', 'display_name' => 'إنشاء أهداف مندوبين', 'description' => 'إمكانية إنشاء أهداف جديدة للمندوبين', 'module' => 'Representative Targets', 'guard_name' => 'web'],
            ['name' => 'edit_representative_targets', 'display_name' => 'تعديل أهداف المندوبين', 'description' => 'إمكانية تعديل أهداف المندوبين', 'module' => 'Representative Targets', 'guard_name' => 'web'],
            ['name' => 'delete_representative_targets', 'display_name' => 'حذف أهداف المندوبين', 'description' => 'إمكانية حذف أهداف المندوبين', 'module' => 'Representative Targets', 'guard_name' => 'web'],

            // HR Management - Salary Records
            ['name' => 'view_salary_records', 'display_name' => 'عرض سجلات المرتبات', 'description' => 'إمكانية عرض سجلات المرتبات', 'module' => 'hr', 'guard_name' => 'web'],
            ['name' => 'create_salary_records', 'display_name' => 'إنشاء سجلات مرتبات', 'description' => 'إمكانية إنشاء سجلات مرتبات جديدة', 'module' => 'hr', 'guard_name' => 'web'],
            ['name' => 'edit_salary_records', 'display_name' => 'تعديل سجلات المرتبات', 'description' => 'إمكانية تعديل سجلات المرتبات', 'module' => 'hr', 'guard_name' => 'web'],
            ['name' => 'delete_salary_records', 'display_name' => 'حذف سجلات المرتبات', 'description' => 'إمكانية حذف سجلات المرتبات', 'module' => 'hr', 'guard_name' => 'web'],

            // HR Management - Advance Requests
            ['name' => 'view_advance_requests', 'display_name' => 'عرض طلبات السلف', 'description' => 'إمكانية عرض قائمة طلبات السلف', 'module' => 'hr', 'guard_name' => 'web'],
            ['name' => 'create_advance_requests', 'display_name' => 'إنشاء طلبات سلف', 'description' => 'إمكانية إنشاء طلبات سلف جديدة', 'module' => 'hr', 'guard_name' => 'web'],
            ['name' => 'edit_advance_requests', 'display_name' => 'تعديل طلبات السلف', 'description' => 'إمكانية تعديل طلبات السلف', 'module' => 'hr', 'guard_name' => 'web'],
            ['name' => 'delete_advance_requests', 'display_name' => 'حذف طلبات السلف', 'description' => 'إمكانية حذف طلبات السلف', 'module' => 'hr', 'guard_name' => 'web'],
            ['name' => 'approve_advance_requests', 'display_name' => 'الموافقة على طلبات السلف', 'description' => 'إمكانية الموافقة أو رفض طلبات السلف', 'module' => 'hr', 'guard_name' => 'web'],

            // HR Management - Delivery Deposits
            ['name' => 'view_delivery_deposits', 'display_name' => 'عرض إيداعات التسليمات', 'description' => 'إمكانية عرض قائمة إيداعات التسليمات', 'module' => 'hr', 'guard_name' => 'web'],
            ['name' => 'create_delivery_deposits', 'display_name' => 'إنشاء إيداعات تسليم', 'description' => 'إمكانية إنشاء إيداعات تسليم جديدة', 'module' => 'hr', 'guard_name' => 'web'],
            ['name' => 'edit_delivery_deposits', 'display_name' => 'تعديل إيداعات التسليمات', 'description' => 'إمكانية تعديل إيداعات التسليمات', 'module' => 'hr', 'guard_name' => 'web'],
            ['name' => 'delete_delivery_deposits', 'display_name' => 'حذف إيداعات التسليمات', 'description' => 'إمكانية حذف إيداعات التسليمات', 'module' => 'hr', 'guard_name' => 'web'],

            // HR Management - Resignation Requests
            ['name' => 'view_resignation_requests', 'display_name' => 'عرض طلبات الاستقالة', 'description' => 'إمكانية عرض قائمة طلبات الاستقالة', 'module' => 'hr', 'guard_name' => 'web'],
            ['name' => 'create_resignation_requests', 'display_name' => 'إنشاء طلبات استقالة', 'description' => 'إمكانية إنشاء طلبات استقالة جديدة', 'module' => 'hr', 'guard_name' => 'web'],
            ['name' => 'edit_resignation_requests', 'display_name' => 'تعديل طلبات الاستقالة', 'description' => 'إمكانية تعديل طلبات الاستقالة', 'module' => 'hr', 'guard_name' => 'web'],
            ['name' => 'delete_resignation_requests', 'display_name' => 'حذف طلبات الاستقالة', 'description' => 'إمكانية حذف طلبات الاستقالة', 'module' => 'hr', 'guard_name' => 'web'],
            ['name' => 'approve_resignation_requests', 'display_name' => 'الموافقة على طلبات الاستقالة', 'description' => 'إمكانية الموافقة أو رفض طلبات الاستقالة', 'module' => 'hr', 'guard_name' => 'web'],

            // Password Management
            ['name' => 'view_passwords', 'display_name' => 'عرض كلمات المرور', 'description' => 'إمكانية عرض قائمة كلمات المرور', 'module' => 'passwords', 'guard_name' => 'web'],
            ['name' => 'create_passwords', 'display_name' => 'إنشاء كلمات مرور', 'description' => 'إمكانية إنشاء كلمات مرور جديدة', 'module' => 'passwords', 'guard_name' => 'web'],
            ['name' => 'edit_passwords', 'display_name' => 'تعديل كلمات المرور', 'description' => 'إمكانية تعديل كلمات المرور', 'module' => 'passwords', 'guard_name' => 'web'],
            ['name' => 'delete_passwords', 'display_name' => 'حذف كلمات المرور', 'description' => 'إمكانية حذف كلمات المرور', 'module' => 'passwords', 'guard_name' => 'web'],
            ['name' => 'manage_passwords', 'display_name' => 'إدارة كلمات المرور', 'description' => 'إدارة كاملة لكلمات المرور', 'module' => 'passwords', 'guard_name' => 'web'],

            // Admin Management
            ['name' => 'view_admins', 'display_name' => 'عرض المسؤولين', 'description' => 'عرض قائمة المسؤولين', 'module' => 'users', 'guard_name' => 'web'],
            ['name' => 'create_admins', 'display_name' => 'إنشاء مسؤولين', 'description' => 'إنشاء مسؤول جديد', 'module' => 'users', 'guard_name' => 'web'],
            ['name' => 'edit_admins', 'display_name' => 'تعديل المسؤولين', 'description' => 'تعديل بيانات المسؤول', 'module' => 'users', 'guard_name' => 'web'],
            ['name' => 'delete_admins', 'display_name' => 'حذف المسؤولين', 'description' => 'حذف مسؤول', 'module' => 'users', 'guard_name' => 'web'],

            // Finance - Expense Types
            ['name' => 'view_expense_types', 'display_name' => 'عرض أنواع المصروفات', 'description' => 'عرض قائمة أنواع المصروفات', 'module' => 'finance', 'guard_name' => 'web'],
            ['name' => 'create_expense_types', 'display_name' => 'إنشاء أنواع مصروفات', 'description' => 'إنشاء نوع مصروف', 'module' => 'finance', 'guard_name' => 'web'],
            ['name' => 'edit_expense_types', 'display_name' => 'تعديل أنواع المصروفات', 'description' => 'تعديل نوع مصروف', 'module' => 'finance', 'guard_name' => 'web'],
            ['name' => 'delete_expense_types', 'display_name' => 'حذف أنواع المصروفات', 'description' => 'حذف نوع مصروف', 'module' => 'finance', 'guard_name' => 'web'],

            // Finance - Safes
            ['name' => 'view_safes', 'display_name' => 'عرض الخزن', 'description' => 'عرض الخزن', 'module' => 'finance', 'guard_name' => 'web'],
            ['name' => 'manage_safes', 'display_name' => 'إدارة الخزن', 'description' => 'إيداع وسحب وإنشاء خزنة', 'module' => 'finance', 'guard_name' => 'web'],

            // Finance - Expenses
            ['name' => 'view_expenses', 'display_name' => 'عرض المصروفات', 'description' => 'عرض المصروفات', 'module' => 'finance', 'guard_name' => 'web'],
            ['name' => 'create_expenses', 'display_name' => 'إنشاء مصروف', 'description' => 'إنشاء مصروف', 'module' => 'finance', 'guard_name' => 'web'],
            ['name' => 'delete_expenses', 'display_name' => 'حذف مصروف', 'description' => 'حذف مصروف', 'module' => 'finance', 'guard_name' => 'web'],

            // Finance - Reports
            ['name' => 'view_revenue_reports', 'display_name' => 'عرض تقرير الإيرادات', 'description' => 'عرض تقرير الإيرادات والتحركات', 'module' => 'finance', 'guard_name' => 'web'],

            // Trainings
            ['name' => 'view_trainings', 'display_name' => 'عرض التدريبات', 'description' => 'عرض حالة تدريبات المندوبين', 'module' => 'trainings', 'guard_name' => 'web'],
            ['name' => 'edit_trainings', 'display_name' => 'تعديل التدريبات', 'description' => 'تحديث حالة التدريب', 'module' => 'trainings', 'guard_name' => 'web'],

            // Sliders
            ['name' => 'view_sliders', 'display_name' => 'عرض السلايدر', 'description' => 'عرض صور السلايدر', 'module' => 'content', 'guard_name' => 'web'],
            ['name' => 'create_sliders', 'display_name' => 'إنشاء سلايدر', 'description' => 'إضافة صور للسلايدر', 'module' => 'content', 'guard_name' => 'web'],
            ['name' => 'edit_sliders', 'display_name' => 'تعديل السلايدر', 'description' => 'تعديل ترتيب السلايدر', 'module' => 'content', 'guard_name' => 'web'],
            ['name' => 'delete_sliders', 'display_name' => 'حذف السلايدر', 'description' => 'حذف صور السلايدر', 'module' => 'content', 'guard_name' => 'web'],
            // Password Reset Requests
            ['name' => 'view_password_resets', 'display_name' => 'عرض طلبات إعادة تعيين كلمة المرور', 'description' => 'عرض لوحة طلبات إعادة تعيين كلمة المرور', 'module' => 'passwords', 'guard_name' => 'web'],
            ['name' => 'edit_password_resets', 'display_name' => 'تعديل طلبات إعادة تعيين كلمة المرور', 'description' => 'تعديل وإتمام طلبات إعادة تعيين كلمة المرور', 'module' => 'passwords', 'guard_name' => 'web'],

            // Notifications Management
            ['name' => 'view_notifications', 'display_name' => 'عرض الإشعارات', 'description' => 'إمكانية عرض صفحة إدارة الإشعارات', 'module' => 'notifications', 'guard_name' => 'web'],
            ['name' => 'create_notifications', 'display_name' => 'إنشاء الإشعارات', 'description' => 'إمكانية إنشاء إشعارات جديدة للمستخدمين', 'module' => 'notifications', 'guard_name' => 'web'],
            ['name' => 'send_notifications', 'display_name' => 'إرسال الإشعارات', 'description' => 'إمكانية إرسال إشعارات للمستخدمين', 'module' => 'notifications', 'guard_name' => 'web'],
            ['name' => 'edit_notifications', 'display_name' => 'تعديل الإشعارات', 'description' => 'إمكانية تعديل الإشعارات الموجودة', 'module' => 'notifications', 'guard_name' => 'web'],
            ['name' => 'delete_notifications', 'display_name' => 'حذف الإشعارات', 'description' => 'إمكانية حذف الإشعارات', 'module' => 'notifications', 'guard_name' => 'web'],
            ['name' => 'manage_notifications', 'display_name' => 'إدارة الإشعارات', 'description' => 'إدارة كاملة للإشعارات', 'module' => 'notifications', 'guard_name' => 'web'],
            ['name' => 'schedule_notifications', 'display_name' => 'جدولة الإشعارات', 'description' => 'إمكانية جدولة الإشعارات للإرسال لاحقاً', 'module' => 'notifications', 'guard_name' => 'web'],
            ['name' => 'view_notification_templates', 'display_name' => 'عرض قوالب الإشعارات', 'description' => 'إمكانية عرض واستخدام قوالب الإشعارات', 'module' => 'notifications', 'guard_name' => 'web'],
            ['name' => 'manage_notification_templates', 'display_name' => 'إدارة قوالب الإشعارات', 'description' => 'إمكانية إنشاء وتعديل قوالب الإشعارات', 'module' => 'notifications', 'guard_name' => 'web'],

            // Missing Permissions - Interview Management
            ['name' => 'view_interviews', 'display_name' => 'عرض المقابلات', 'description' => 'إمكانية عرض قائمة المقابلات', 'module' => 'interviews', 'guard_name' => 'web'],
            ['name' => 'create_interviews', 'display_name' => 'إنشاء مقابلات', 'description' => 'إمكانية إنشاء مقابلات جديدة', 'module' => 'interviews', 'guard_name' => 'web'],
            ['name' => 'edit_interviews', 'display_name' => 'تعديل المقابلات', 'description' => 'إمكانية تعديل المقابلات', 'module' => 'interviews', 'guard_name' => 'web'],
            ['name' => 'delete_interviews', 'display_name' => 'حذف المقابلات', 'description' => 'إمكانية حذف المقابلات', 'module' => 'interviews', 'guard_name' => 'web'],
            ['name' => 'manage_interviews', 'display_name' => 'إدارة المقابلات', 'description' => 'إدارة كاملة للمقابلات', 'module' => 'interviews', 'guard_name' => 'web'],

            // Missing Permissions - Message Management (without authorization)
            ['name' => 'view_messages', 'display_name' => 'عرض الرسائل', 'description' => 'إمكانية عرض قائمة الرسائل', 'module' => 'messages', 'guard_name' => 'web'],
            ['name' => 'create_messages', 'display_name' => 'إنشاء رسائل', 'description' => 'إمكانية إنشاء رسائل جديدة', 'module' => 'messages', 'guard_name' => 'web'],
            ['name' => 'edit_messages', 'display_name' => 'تعديل الرسائل', 'description' => 'إمكانية تعديل الرسائل', 'module' => 'messages', 'guard_name' => 'web'],
            ['name' => 'delete_messages', 'display_name' => 'حذف الرسائل', 'description' => 'إمكانية حذف الرسائل', 'module' => 'messages', 'guard_name' => 'web'],
            ['name' => 'manage_messages', 'display_name' => 'إدارة الرسائل', 'description' => 'إدارة كاملة للرسائل', 'module' => 'messages', 'guard_name' => 'web'],

            // Missing Permissions - Salary Components
            ['name' => 'view_salary_components', 'display_name' => 'عرض مكونات الراتب', 'description' => 'إمكانية عرض قائمة مكونات الراتب', 'module' => 'hr', 'guard_name' => 'web'],
            ['name' => 'create_salary_components', 'display_name' => 'إنشاء مكونات راتب', 'description' => 'إمكانية إنشاء مكونات راتب جديدة', 'module' => 'hr', 'guard_name' => 'web'],
            ['name' => 'edit_salary_components', 'display_name' => 'تعديل مكونات الراتب', 'description' => 'إمكانية تعديل مكونات الراتب', 'module' => 'hr', 'guard_name' => 'web'],
            ['name' => 'delete_salary_components', 'display_name' => 'حذف مكونات الراتب', 'description' => 'إمكانية حذف مكونات الراتب', 'module' => 'hr', 'guard_name' => 'web'],

            // Missing Permissions - Work Schedules
            ['name' => 'view_work_schedules', 'display_name' => 'عرض مواعيد العمل', 'description' => 'إمكانية عرض قائمة مواعيد العمل', 'module' => 'hr', 'guard_name' => 'web'],
            ['name' => 'create_work_schedules', 'display_name' => 'إنشاء مواعيد عمل', 'description' => 'إمكانية إنشاء مواعيد عمل جديدة', 'module' => 'hr', 'guard_name' => 'web'],
            ['name' => 'edit_work_schedules', 'display_name' => 'تعديل مواعيد العمل', 'description' => 'إمكانية تعديل مواعيد العمل', 'module' => 'hr', 'guard_name' => 'web'],
            ['name' => 'delete_work_schedules', 'display_name' => 'حذف مواعيد العمل', 'description' => 'إمكانية حذف مواعيد العمل', 'module' => 'hr', 'guard_name' => 'web'],

            // Missing Permissions - Profile Management
            ['name' => 'view_profile', 'display_name' => 'عرض الملف الشخصي', 'description' => 'إمكانية عرض الملف الشخصي', 'module' => 'profile', 'guard_name' => 'web'],
            ['name' => 'edit_profile', 'display_name' => 'تعديل الملف الشخصي', 'description' => 'إمكانية تعديل الملف الشخصي', 'module' => 'profile', 'guard_name' => 'web'],
            ['name' => 'update_password', 'display_name' => 'تحديث كلمة المرور', 'description' => 'إمكانية تحديث كلمة المرور', 'module' => 'profile', 'guard_name' => 'web'],

            // Missing Permissions - Public Account Management
            ['name' => 'view_public_accounts', 'display_name' => 'عرض الحسابات العامة', 'description' => 'إمكانية عرض الحسابات العامة', 'module' => 'public', 'guard_name' => 'web'],
            ['name' => 'manage_public_accounts', 'display_name' => 'إدارة الحسابات العامة', 'description' => 'إدارة كاملة للحسابات العامة', 'module' => 'public', 'guard_name' => 'web'],

            // Missing Permissions - Landing Pages
            ['name' => 'view_landing_pages', 'display_name' => 'عرض الصفحات المقصودة', 'description' => 'إمكانية عرض الصفحات المقصودة', 'module' => 'content', 'guard_name' => 'web'],
            ['name' => 'create_landing_pages', 'display_name' => 'إنشاء صفحات مقصودة', 'description' => 'إمكانية إنشاء صفحات مقصودة جديدة', 'module' => 'content', 'guard_name' => 'web'],
            ['name' => 'edit_landing_pages', 'display_name' => 'تعديل الصفحات المقصودة', 'description' => 'إمكانية تعديل الصفحات المقصودة', 'module' => 'content', 'guard_name' => 'web'],
            ['name' => 'delete_landing_pages', 'display_name' => 'حذف الصفحات المقصودة', 'description' => 'إمكانية حذف الصفحات المقصودة', 'module' => 'content', 'guard_name' => 'web'],

            // Missing Permissions - Reasons
            ['name' => 'view_reasons', 'display_name' => 'عرض الأسباب', 'description' => 'إمكانية عرض قائمة الأسباب', 'module' => 'system', 'guard_name' => 'web'],
            ['name' => 'create_reasons', 'display_name' => 'إنشاء أسباب', 'description' => 'إمكانية إنشاء أسباب جديدة', 'module' => 'system', 'guard_name' => 'web'],
            ['name' => 'edit_reasons', 'display_name' => 'تعديل الأسباب', 'description' => 'إمكانية تعديل الأسباب', 'module' => 'system', 'guard_name' => 'web'],
            ['name' => 'delete_reasons', 'display_name' => 'حذف الأسباب', 'description' => 'إمكانية حذف الأسباب', 'module' => 'system', 'guard_name' => 'web'],

            // Missing Permissions - Safe Management
            ['name' => 'view_safes', 'display_name' => 'عرض الخزن', 'description' => 'عرض الخزن', 'module' => 'finance', 'guard_name' => 'web'],
            ['name' => 'manage_safes', 'display_name' => 'إدارة الخزن', 'description' => 'إيداع وسحب وإنشاء خزنة', 'module' => 'finance', 'guard_name' => 'web'],

            // Missing Permissions - Supervisor Transfer Logs
            ['name' => 'view_supervisor_transfer_logs', 'display_name' => 'عرض سجلات نقل المشرفين', 'description' => 'إمكانية عرض سجلات نقل المشرفين', 'module' => 'supervisors', 'guard_name' => 'web'],
            ['name' => 'create_supervisor_transfer_logs', 'display_name' => 'إنشاء سجلات نقل مشرفين', 'description' => 'إمكانية إنشاء سجلات نقل مشرفين جديدة', 'module' => 'supervisors', 'guard_name' => 'web'],
            ['name' => 'edit_supervisor_transfer_logs', 'display_name' => 'تعديل سجلات نقل المشرفين', 'description' => 'إمكانية تعديل سجلات نقل المشرفين', 'module' => 'supervisors', 'guard_name' => 'web'],
            ['name' => 'delete_supervisor_transfer_logs', 'display_name' => 'حذف سجلات نقل المشرفين', 'description' => 'إمكانية حذف سجلات نقل المشرفين', 'module' => 'supervisors', 'guard_name' => 'web'],

            // WhatsApp Management
            ['name' => 'view_whatsapp_messages', 'display_name' => 'عرض رسائل الواتساب', 'description' => 'إمكانية عرض رسائل الواتساب والإحصائيات', 'module' => 'whatsapp', 'guard_name' => 'web'],
            ['name' => 'manage_whatsapp_messages', 'display_name' => 'إدارة رسائل الواتساب', 'description' => 'إدارة كاملة لرسائل الواتساب', 'module' => 'whatsapp', 'guard_name' => 'web'],
            ['name' => 'resend_whatsapp_messages', 'display_name' => 'إعادة إرسال رسائل الواتساب', 'description' => 'إمكانية إعادة إرسال الرسائل الفاشلة', 'module' => 'whatsapp', 'guard_name' => 'web'],
            ['name' => 'view_whatsapp_logs', 'display_name' => 'عرض سجلات الواتساب', 'description' => 'إمكانية عرض سجلات رسائل الواتساب', 'module' => 'whatsapp', 'guard_name' => 'web'],
            ['name' => 'manage_whatsapp_logs', 'display_name' => 'إدارة سجلات الواتساب', 'description' => 'إدارة سجلات رسائل الواتساب ومسح القديمة', 'module' => 'whatsapp', 'guard_name' => 'web'],
        ];
    }

    /**
     * Get permissions grouped by module
     */
    public function getPermissionsByModule()
    {
        $permissions = $this->all();
        $grouped = [];

        foreach ($permissions as $permission) {
            $module = $permission->module ?? 'other';
            $grouped[$module][] = $permission;
        }

        return $grouped;
    }

    /**
     * Get permissions by action type
     */
    public function getPermissionsByActionType()
    {
        $permissions = $this->all();
        $grouped = [];

        foreach ($permissions as $permission) {
            $actionType = $permission->action_type;
            $grouped[$actionType][] = $permission;
        }

        return $grouped;
    }
}
