<?php
namespace App\Repositories;
use App\Models\Permission;

class PermissionRepository
{
    public function all()
    {
        return Permission::all();
    }

    public function query()
    {
        return Permission::query();
    }

    public function create(array $data)
    {
        return Permission::create($data);
    }

    public function find($id)
    {
        return Permission::findOrFail($id);
    }

    public function findBy($column, $value)
    {
        return Permission::where($column, $value)->first();
    }

    public function update(Permission $permission, array $data)
    {
        $permission->update($data);
        return $permission;
    }

    public function delete(Permission $permission)
    {
        $permission->delete();
    }
}
