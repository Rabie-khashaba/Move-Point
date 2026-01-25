<?php
namespace App\Repositories;
use App\Models\Governorate;

class GovernorateRepository
{
    public function all()
    {
        return Governorate::all();
    }

    public function query()
    {
        return Governorate::query();
    }

    public function create(array $data)
    {
        return Governorate::create($data);
    }

    public function find($id)
    {
        return Governorate::findOrFail($id);
    }

    public function update(Governorate $governorate, array $data)
    {
        $governorate->update($data);
        return $governorate;
    }

    public function delete(Governorate $governorate)
    {
        $governorate->delete();
    }
}
