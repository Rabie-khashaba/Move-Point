<?php
namespace App\Services;

use App\Repositories\DepartmentRepository;

class DepartmentService
{
    protected $repository;

    public function __construct(DepartmentRepository $repository)
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
        $department = $this->repository->find($id);
        return $this->repository->update($department, $data);
    }

    public function delete($id)
    {
        $department = $this->repository->find($id);

        // 🔎 If department has related employees, leads, etc.
        if (method_exists($department, 'employees') && $department->employees()->count() > 0) {
            throw new \Exception("Cannot delete department with employees.");
        }

        return $this->repository->delete($department);
    }
}
