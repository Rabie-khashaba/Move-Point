<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Governorate;
use App\Models\Source;
use App\Models\Location;
use App\Services\LeadService;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LandingPageController extends Controller
{
    protected LeadService $service;

    public function __construct(LeadService $service)
    {
        $this->service = $service;
    }
    public function show($slug = null)
    {
        // Get available governorates for the form
        $governorates = Governorate::all();
        $locations = Location::all();
        
        // Get any tracking parameters from the URL
        $utmSource = request('utm_source');
        $utmMedium = request('utm_medium');
        $utmCampaign = request('utm_campaign');
        $referrer = request('ref');
        
        return view('landing-pages.lead', compact(
            'governorates', 
            'locations',
            'slug', 
            'utmSource', 
            'utmMedium', 
            'utmCampaign', 
            'referrer'
        ));
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:leads,phone',
            'governorate_id' => 'required|exists:governorates,id',
            'location_id' => 'nullable|exists:locations,id',
            'message' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $slug = $request->landing_page_slug ?? null;

            // ✅ source_id logic
            $sourceId = match (strtolower($slug)) {
                'facebook'  => 16,
                'instagram' => 17,
                'tiktok'    => 19,
                default     => null,
            };

            $validated = [
                'name'           => $request->name,
                'phone'          => $request->phone,
                'governorate_id' => $request->governorate_id,
                'location_id'    => $request->location_id,
                'notes'          => $request->message,
                'status'         => 'جديد',
                'is_active'      => true,
                'source_id'      => $sourceId,
                // ⚠️ لا نمرر assigned_to → الخدمة ستوزعه أوتوماتيكياً
            ];

            // ✅ create via service
            $lead = $this->service->create($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم إرسال طلبك بنجاح! سنتواصل معك قريباً لتحديد موعد المقابلة.',
                'redirect' => route('landing-page.success'),
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in LandingPageController@store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إرسال الطلب. يرجى المحاولة مرة أخرى أو التواصل معنا عبر الهاتف.',
            ], 500);
        }
    }




    public function success()
    {
        return view('landing-pages.success');
    }
}
