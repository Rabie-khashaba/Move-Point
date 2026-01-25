<?php

namespace App\Services;

use App\Repositories\EmployeeRepository;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class EmployeeService
{
    protected $repository;

    public function __construct(EmployeeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function all()
    {
        return $this->repository->all()->load(['user', 'department']);
    }

    public function paginated($perPage = 20)
    {
        return $this->repository->query()->with(['user', 'department'])->paginate($perPage);
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }

    public function create(array $data)
    {
        // 1️⃣ Create the user
        $user = User::create([
            'name'=>$data['name'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'type' => 'employee',
        ]);

        // 2️⃣ Handle attachments
        $attachments = [];
        if (!empty($data['attachments'])) {
            foreach ($data['attachments'] as $file) {
                $path = $file->store('attachments', 'public');
                $attachments[] = $path;
            }
        }

        // 3️⃣ Create employee
        $employee = $this->repository->create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'contact' => $data['contact'] ?? null,
            'national_id' => $data['national_id'],
            'salary' => $data['salary'],
            'start_date' => $data['start_date'],
            'department_id' => $data['department_id'],
            'attachments' => json_encode($attachments),
            'shift' => $data['shift'] ?? null,
            'days_off' => $data['days_off'] ?? null,
            'whatsapp_phone' => $data['whatsapp_phone'] ?? null,
        ]);

        // 4️⃣ Assign roles

    if (!empty($data['role'])) {
        // ربط الدور
        $role = Role::with('permissions')->findOrFail($data['role']);
        $user->syncRoles([$role->name]);

        // جلب الدور مع صلاحياته

        if ($role) {
            // ربط صلاحيات الدور تلقائي
            $user->syncPermissions($role->permissions);
        } else {
            throw new \Exception("Role not found for ID: " . $data['role']);
        }
    }

        return $employee;
    }

    public function update($id, array $data)
    {
        $employee = $this->repository->find($id);
        $user = $employee->user;
        if (!empty($data['password'])) {
            $user->update([
                'name' => $data['name'],
                'phone'=> $data['phone'],
                'password' => Hash::make($data['password']),
            ]);
        }

        // 1️⃣ Handle attachments
        $attachments = [];
        if ($employee->attachments) {
            if (is_string($employee->attachments)) {
                $attachments = json_decode($employee->attachments, true) ?? [];
            } elseif (is_array($employee->attachments)) {
                $attachments = $employee->attachments;
            }
        }

        if (!empty($data['attachments'])) {
            foreach ($data['attachments'] as $file) {
                $path = $file->store('attachments', 'public');
                $attachments[] = $path;
            }
            $data['attachments'] = json_encode($attachments);
        } else {
            unset($data['attachments']);
        }

        // 2️⃣ Update roles
        if (!empty($data['role'])) {
        // ربط الدور
        $role = Role::with('permissions')->findOrFail($data['role']);
        $user->syncRoles([$role->name]);

        // جلب الدور مع صلاحياته
        if ($role) {

            // ربط صلاحيات الد
            // ور تلقائي
            $user->syncPermissions($role->permissions);
        } else {
            throw new \Exception("Role not found for ID: " . $data['role']);
        }
    }

        // 3️⃣ Update employee data
        return $this->repository->update($employee, $data);
    }

    public function changePassword($id, $password)
    {
        $employee = $this->repository->find($id);
        $employee->user->update([
            'password' => Hash::make($password),
        ]);
    }

    public function delete($id)
    {
        $employee = $this->repository->find($id);
        $this->repository->delete($employee);
        // User and user_roles will be deleted via ON DELETE CASCADE
    }
}
