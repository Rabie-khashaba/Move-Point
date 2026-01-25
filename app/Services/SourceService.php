<?php
namespace App\Services;
use App\Repositories\SourceRepository;

class SourceService
{
    protected $repository;

    public function __construct(SourceRepository $repository)
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
        $source = $this->repository->find($id);
        return $this->repository->update($source, $data);
    }

    public function delete($id)
    {
        $source = $this->repository->find($id);
        $this->repository->delete($source);
    }
}
