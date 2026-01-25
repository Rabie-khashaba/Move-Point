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
            'phone' => 'required|string|max:20',
            'governorate_id' => 'required|exists:governorates,id',
            'location_id' => 'nullable|exists:locations,id',
            'message' => 'nullable|string|max:1000',
            'transportation' => 'nullable|string',
            'landing_page_slug' => 'sometimes|string|max:100',
        ], [
            'name.required' => 'الاسم مطلوب.',
            'name.string' => 'الاسم يجب أن يكون نصاً.',
            'name.max' => 'الاسم يجب ألا يتجاوز 255 حرفاً.',

            'phone.required' => 'رقم الهاتف مطلوب.',
            'phone.string' => 'رقم الهاتف يجب أن يكون نصاً.',
            'phone.max' => 'رقم الهاتف يجب ألا يتجاوز 20 رقماً.',

            'governorate_id.required' => 'اختيار المحافظة مطلوب.',
            'governorate_id.exists' => 'المحافظة المختارة غير صحيحة.',

            'location_id.exists' => 'المنطقة المختارة غير صحيحة.',

            'message.max' => 'الرسالة يجب ألا تتجاوز 1000 حرف.',
        ]);
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        $validated = $validator->validated();
        $validated['phone'] = preg_replace('/[^0-9]/', '', $validated['phone']);
        // Keep name/message; just trim
        if (isset($validated['name'])) { $validated['name'] = trim($validated['name']); }
        if (isset($validated['message'])) { $validated['message'] = trim($validated['message']); }
        // Derive slug safely from input or route param
        $incomingSlug = $request->input('landing_page_slug', $request->route('slug'));
        $slug = is_string($incomingSlug) ? strtolower(trim($incomingSlug)) : null;

        try {
            // Check if phone already exists
            $existingLead = Lead::where('phone', $validated['phone'])->first();

            if ($existingLead) {
                // Phone exists - return error response
                return response()->json([
                    'success' => false,
                    'message' => 'هذا الهاتف مستخدم من قبل',
                    'errors' => [
                        'phone' => ['هذا الهاتف مستخدم من قبل']
                    ]
                ], 422);
            }

            // ✅ source_id logic for new leads
            if($slug){
                $sourceId = match ($slug) {
                    'facebook'  => 16,
                    'instagram' => 17,
                    'tiktok'    => 19,
                    'old-data'  => 21,
                    default     => null,
                };
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'الرابط غير صحيح.'
                ], 400);
            }
            $validated = [
                'name'           => $validated['name'],
                'phone'          => $validated['phone'],
                'governorate_id' => $validated['governorate_id'],
                'location_id'    => $validated['location_id'] ?? null,
                'notes'          => $validated['message'] ?? null,
                'status'         => 'جديد',
                'is_active'      => true,
                'source_id'      => $sourceId,
                'transportation' => $validated['transportation'] ?? null, // 👈 تمت الإضافة

                // ⚠️ لا نمرر assigned_to → الخدمة ستوزعه أوتوماتيكياً
            ];

            // ✅ create via service for new leads
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
