<?php
namespace App\Services;

use App\Repositories\RoleRepository;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleService
{
    protected $repository;

    public function __construct(RoleRepository $repository)
    {
        $this->repository = $repository;
    }

    public function all()
    {
        return $this->repository->all();
    }

    public function paginated($perPage = 20)
    {
        return $this->repository->query()->with('permissions')->paginate($perPage);
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }

    public function create(array $data)
    {
        $data['guard_name'] = $data['guard_name'] ?? 'web';

        // إنشاء role
        $role = $this->repository->create([
            'name' => $data['name'],
            'guard_name' => $data['guard_name']
        ]);

        // ربط permissions إذا وُجدت
        if (!empty($data['permission_ids'])) {
            $permissions = Permission::whereIn('id', $data['permission_ids'])
                                     ->where('guard_name', $role->guard_name)
                                     ->get();
            $role->syncPermissions($permissions);
        }

        return $role;
    }

    public function update($id, array $data)
    {
        $role = $this->repository->find($id);

        $role = $this->repository->update($role, ['name' => $data['name']]);

        // تحديث permissions
        if (!empty($data['permission_ids'])) {
            $permissions = Permission::whereIn('id', $data['permission_ids'])->get();
            $role->syncPermissions($permissions);
        } else {
            $role->syncPermissions([]); // إزالة جميع permissions إذا لم يتم تمرير شيء
        }

        return $role;
    }

    public function delete($id)
    {
        $role = $this->repository->find($id);
        $this->repository->delete($role);
    }
}
