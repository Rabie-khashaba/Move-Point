<?php

namespace App\Repositories;

use App\Models\Lead;
use Illuminate\Support\Str;


class LeadRepository
{
    public function all()
    {
        return Lead::all();
    }

    public function query()
    {
        return Lead::with(['governorate', 'source','assignedTo']); // add assigned_to if needed
    }

    public function create(array $data)
    {
        return Lead::create($data);
    }

    public function find($id)
    {
        return Lead::with('governorate', 'source')->findOrFail($id);
    }

    public function update(Lead $lead, array $data)
    {
        $lead->update($data);
        return $lead;
    }

    public function delete(Lead $lead)
    {
        $lead->delete();
    }
}