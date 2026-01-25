<?php
namespace App\Repositories;
use App\Models\Supervisor;

class SupervisorRepository
{
    public function all()
    {
        return Supervisor::all();
    }

    public function query()
    {
        return Supervisor::query();
    }

    public function create(array $data)
    {
        return Supervisor::create($data);
    }

    public function find($id)
    {
        return Supervisor::with('location', 'user')->findOrFail($id);
    }

    public function update(Supervisor $supervisor, array $data)
    {
        $supervisor->update($data);
        return $supervisor;
    }

    public function delete(Supervisor $supervisor)
    {
        $supervisor->delete();
    }
}