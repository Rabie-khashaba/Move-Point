<?php
namespace App\Services;
use App\Repositories\LocationRepository;

class LocationService
{
    protected $repository;

    public function __construct(LocationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function all()
    {
        return $this->repository->all()->load(['governorate']);
    }

    public function paginated($perPage = 20)
    {
        return $this->repository->query()->with(['governorate'])->paginate($perPage);
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
        $location = $this->repository->find($id);
        return $this->repository->update($location, $data);
    }

    public function delete($id)
    {
        $location = $this->repository->find($id);
        $this->repository->delete($location);
    }
}
