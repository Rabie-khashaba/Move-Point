<?php
namespace App\Repositories;
use App\Models\Department;

class DepartmentRepository
{
    public function all()
    {
        return Department::all();
    }


    public function query()
    {
        return Department::query();
    }

    public function create(array $data)
    {
        return Department::create($data);
    }

    public function find($id)
    {
        return Department::findOrFail($id);
    }

    public function update(Department $department, array $data)
    {
        $department->update($data);
        return $department;
    }

    public function delete(Department $department)
    {
        $department->delete();
    }
}
