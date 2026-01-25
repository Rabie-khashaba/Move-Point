<?php

namespace App\Services;

use App\Repositories\GovernorateRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Models\Governorate;

class GovernorateService
{
    protected GovernorateRepository $repository;

    public function __construct(GovernorateRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all governorates.
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return $this->repository->all();
    }

    /**
     * Get paginated governorates.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
   public function paginated($perPage = 15, $callback = null) : LengthAwarePaginator
    {
        $query = $this->model->query();

        if ($callback) {
            $callback($query);
        }

        return $query->paginate($perPage)->withQueryString();
    }


    /**
     * Find governorate by ID.
     *
     * @param int $id
     * @return Governorate
     *
     * @throws ModelNotFoundException
     */
    public function find(int $id): Governorate
    {
        return $this->repository->Find($id);
    }

    /**
     * Create a new governorate.
     *
     * @param array $data
     * @return Governorate
     */
    public function create(array $data): Governorate
    {
        return $this->repository->create($data);
    }

    /**
     * Update an existing governorate.
     *
     * @param int $id
     * @param array $data
     * @return Governorate
     */
    public function update(int $id, array $data): Governorate
    {
        $governorate = $this->find($id);
        return $this->repository->update($governorate, $data);
    }

    /**
     * Delete a governorate.
     *
     * @param int $id
     * @return bool|null
     */
    public function delete(int $id): ?bool
    {
        $governorate = $this->find($id);
        return $this->repository->delete($governorate);
    }
}
