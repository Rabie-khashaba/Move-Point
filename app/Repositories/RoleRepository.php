<?php
namespace App\Repositories;

use Spatie\Permission\Models\Role;

class RoleRepository
{
    public function all()
    {
        return Role::with('permissions')->get();
    }

    public function query()
    {
        return Role::query()->with('permissions');
    }

    public function create(array $data)
    {
        return Role::create($data);
    }

    public function find($id)
    {
        return Role::with('permissions')->findOrFail($id);
    }

    public function update(Role $role, array $data)
    {
        $role->update($data);
        return $role;
    }

    public function delete(Role $role)
    {
        $role->delete();
    }
}
