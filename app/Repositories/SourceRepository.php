<?php
namespace App\Repositories;
use App\Models\Source;

class SourceRepository
{
    public function all()
    {
        return Source::all();
    }

    public function query()
    {
        return Source::query();
    }

    public function create(array $data)
    {
        return Source::create($data);
    }

    public function find($id)
    {
        return Source::findOrFail($id);
    }

    public function update(Source $source, array $data)
    {
        $source->update($data);
        return $source;
    }

    public function delete(Source $source)
    {
        $source->delete();
    }
}
