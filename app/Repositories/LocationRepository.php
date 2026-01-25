<?php
namespace App\Repositories;
use App\Models\Location;

class LocationRepository
{
    public function all()
    {
        return Location::all();
    }

    public function query()
    {
        return Location::query();
    }

    public function create(array $data)
    {
        return Location::create($data);
    }

    public function find($id)
    {
        return Location::with('governorate')->findOrFail($id);
    }

    public function update(Location $location, array $data)
    {
        $location->update($data);
        return $location;
    }

    public function delete(Location $location)
    {
        $location->delete();
    }
}