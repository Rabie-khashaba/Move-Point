<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\RepresentativeNoService;
use Illuminate\Support\Facades\Log;

class RepresentativeController extends Controller
{
    protected $service;

    public function __construct(RepresentativeNoService $service)
    {
        $this->service = $service;
    }

    public function store(Request $request)
    {
        $this->authorize('create_representatives_no');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|digits:11|unique:users,phone',
            'address' => 'required|string|max:500',
            'contact' => 'required|string|max:255',
            'national_id' => 'required|digits:14|unique:representatives,national_id',
            'salary' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'company_id' => 'required|exists:companies,id',
            'bank_account' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:255',
            'attachments.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'inquiry_checkbox' => 'boolean',
            'inquiry_data' => 'nullable|string|max:1000',
            'governorate_id' => 'required|exists:governorates,id',
            'location_id' => 'nullable|exists:locations,id',
            'home_location' => 'nullable|url|max:500',
            'is_active' => 'boolean',
            'employee_id' => 'nullable|exists:users,id',
            'lead_id' => 'nullable|exists:leads,id',
        ]);

        try {
            $representative = $this->service->create($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء المندوب بنجاح!',
                'data' => $representative
            ], 201);
        } catch (\Exception $e) {
            Log::error('API Representative creation failed: '.$e->getMessage(), [
                'request_data' => $validated
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء المندوب.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
