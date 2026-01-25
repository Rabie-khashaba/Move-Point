<?php

namespace App\Services;

use App\Repositories\CompanyRepository;
use Illuminate\Support\Facades\Storage;

class CompanyService
{
    protected $repository;

    public function __construct(CompanyRepository $repository)
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
        // ✅ Handle logo upload if present
        if (isset($data['logo']) && $data['logo'] instanceof \Illuminate\Http\UploadedFile) {
            $data['logo'] = $this->uploadLogo($data['logo']);
        }

        return $this->repository->create($data);
    }

    public function update($id, array $data)
    {
        $company = $this->repository->find($id);

        // ✅ Handle logo upload
        if (isset($data['logo']) && $data['logo'] instanceof \Illuminate\Http\UploadedFile) {
            // Delete old logo if exists
            if ($company->logo) {
                $this->deleteLogo($company->logo);
            }
            $data['logo'] = $this->uploadLogo($data['logo']);
        }

        return $this->repository->update($company, $data);
    }

    public function delete($id)
    {
        $company = $this->repository->find($id);

        // ✅ If company has employees/leads/etc, protect delete
        if (method_exists($company, 'employees') && $company->employees()->count() > 0) {
            throw new \Exception("Cannot delete company that has employees.");
        }

        if (method_exists($company, 'departments') && $company->departments()->count() > 0) {
            throw new \Exception("Cannot delete company that has departments.");
        }

        // ✅ Delete logo file if exists
        if ($company->logo) {
            $this->deleteLogo($company->logo);
        }

        return $this->repository->delete($company);
    }

    /**
     * Upload company logo
     */
    private function uploadLogo(\Illuminate\Http\UploadedFile $file)
    {
        $filename = 'company_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('companies/logos', $filename, 'public');
    }

    /**
     * Delete company logo
     */
    private function deleteLogo($logoPath)
    {
        if (Storage::disk('public')->exists($logoPath)) {
            Storage::disk('public')->delete($logoPath);
        }
    }
}
