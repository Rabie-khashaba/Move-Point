<?php
namespace App\Repositories;
use App\Models\Company;

class CompanyRepository
{
    public function all()
    {
        return Company::all();
    }

    public function query()
    {
        return Company::query();
    }

    public function create(array $data)
    {
        return Company::create($data);
    }

    public function find($id)
    {
        return Company::findOrFail($id);
    }

    public function update(Company $company, array $data)
    {
        $company->update($data);
        return $company;
    }

    public function delete(Company $company)
    {
        $company->delete();
    }
}