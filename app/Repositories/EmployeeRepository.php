<?php

namespace App\Repositories;

use App\Models\Employee;

class EmployeeRepository
{
    public function all()
    {
        return Employee::all();
    }

    public function query()
    {
        return Employee::query();
    }

    public function create(array $data)
    {
        return Employee::create($data);
    }

    public function find($id)
    {
        return Employee::with('department', 'user')->findOrFail($id);
    }

    public function update(Employee $employee, array $data)
    {
        $employee->update($data);
        return $employee;
    }

    public function delete(Employee $employee)
    {
        $employee->delete();
    }
}