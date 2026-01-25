<?php
namespace App\Repositories;
use App\Models\Representative;

class RepresentativeRepository
{
    public function all()
    {
        return Representative::all();
    }

    public function query()
    {
        return Representative::query();
    }

    public function create(array $data)
    {
        return Representative::create($data);
    }

    public function find($id)
    {
        return Representative::with(['company', 'user', 'governorate', 'location'])->findOrFail($id);
    }

    public function update(Representative $representative, array $data)
    {
        $representative->update($data);
        return $representative;
    }

    public function delete(Representative $representative)
    {
        return $representative->delete();
    }
}