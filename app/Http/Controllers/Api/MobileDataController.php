<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkSchedule;
use App\Models\EmployeeTarget;
use App\Models\Lead;
use App\Models\Governorate;
use App\Models\Location;
use App\Services\LeadService;

class MobileDataController extends Controller
{
    protected $leadService;

    public function __construct(LeadService $leadService)
    {
        $this->leadService = $leadService;
    }
    public function currentWorkSchedule(Request $request)
    {
        $user = $request->user();

        if ($user->type !== 'employee' || !$user->employee) {
            return response()->json(['message' => 'Only employees have schedules'], 403);
        }

        $schedule = WorkSchedule::where('employee_id', $user->employee->id)
            ->active()
            ->latest('effective_date')
            ->first();

        return response()->json(['data' => $schedule]);
    }

    public function currentTarget(Request $request)
    {
        $user = $request->user();
        if ($user->type !== 'employee' || !$user->employee) {
            return response()->json(['message' => 'Only employees have targets'], 403);
        }

        $year = now()->year;
        $month = now()->month;

        $target = EmployeeTarget::where('employee_id', $user->employee->id)
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        return response()->json(['data' => $target]);
    }

    public function trainingStatus(Request $request)
    {
        $user = $request->user();
        if ($user->type !== 'representative' || !$user->representative) {
            return response()->json(['message' => 'يسمح فقط للمندوبين'], 403);
        }
        $training = \App\Models\RepresentativeTraining::firstOrCreate(
            ['representative_id' => $user->representative->id],
            ['is_completed' => false]
        );
        return response()->json(['is_completed' => (bool) $training->is_completed, 'completed_at' => $training->completed_at]);
    }

    public function completeTraining(Request $request)
    {
        $user = $request->user();
        if ($user->type !== 'representative' || !$user->representative) {
            return response()->json(['message' => 'يسمح فقط للمندوبين'], 403);
        }
        $training = \App\Models\RepresentativeTraining::firstOrCreate(
            ['representative_id' => $user->representative->id]
        );
        if ($training->is_completed) {
            return response()->json(['message' => 'تم إكمال التدريب مسبقاً', 'is_completed' => true, 'completed_at' => $training->completed_at]);
        }
        $training->update(['is_completed' => true, 'completed_at' => now()]);
        return response()->json(['message' => 'تم إكمال التدريب بنجاح', 'is_completed' => true, 'completed_at' => $training->completed_at]);
    }

    /**
     * Create a new lead (for representatives to refer leads)
     */
    public function createLead(Request $request)
    {
        $user = $request->user();

        // Check if user is a representative or supervisor
        if (!in_array($user->type, ['representative', 'supervisor'])) {
            return response()->json(['message' => 'يسمح فقط للمندوبين والمشرفين بإحالة العملاء المحتملين'], 403);
        }

        // Check if user has the required relationship
        if ($user->type === 'representative' && !$user->representative) {
            return response()->json(['message' => 'لم يتم العثور على ملف المندوب'], 404);
        }

        if ($user->type === 'supervisor' && !$user->supervisor) {
            return response()->json(['message' => 'لم يتم العثور على ملف المشرف'], 404);
        }



        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'governorate_id' => 'required|exists:governorates,id',
            'location_id' => 'nullable|exists:locations,id',
            'notes' => 'nullable|string|max:1000'
        ], [
            'name.required' => 'اسم العميل المحتمل مطلوب',
            'name.max' => 'اسم العميل المحتمل يجب أن يكون أقل من 255 حرف',
            'phone.required' => 'رقم الهاتف مطلوب',
            'phone.max' => 'رقم الهاتف يجب أن يكون أقل من 20 رقم',
            'governorate_id.required' => 'المحافظة مطلوبة',
            'governorate_id.exists' => 'المحافظة غير موجودة',
            'location_id.exists' => 'الموقع غير موجود',

            'notes.max' => 'الملاحظات يجب أن تكون أقل من 1000 حرف'
        ]);

        // Check if lead with same phone already exists
        $existingLead = Lead::where('phone', $request->phone)->first();
        if ($existingLead) {
            return response()->json([
                'message' => 'يوجد عميل محتمل بنفس رقم الهاتف مسبقاً',
                'existing_lead' => [
                    'id' => $existingLead->id,
                    'name' => $existingLead->name,
                    'phone' => $existingLead->phone,
                ]
            ], 200);
        }

        // Prepare lead data
        $leadData = [
            'name' => $request->name,
            'phone' => $request->phone,
            'governorate_id' => $request->governorate_id,
            'source_id' => 22,
            'notes' => $request->notes ?? 'تم الإحالة من ' . ($user->type === 'representative' ? 'المندوب' : 'المشرف') . ': ' . $user->name,
            'status' => 'جديد'
        ];

        // Set referral information - refer by the user who created the lead
        $leadData['referred_by'] = $user->id;
        $leadData['referred_by_type'] = $user->type;

        // No need to validate referral data since we're using the authenticated user

        // Create the lead using LeadService for automatic assignment
        try {
            $lead = $this->leadService->create($leadData);
        } catch (\Illuminate\Database\QueryException $e) {
            // Log the error for debugging
            \Log::error('Lead creation failed', [
                'lead_data' => $leadData,
                'user_id' => $user->id,
                'user_type' => $user->type,
                'error' => $e->getMessage()
            ]);

            // Handle foreign key constraint violation
            if ($e->getCode() == 23000) {
                return response()->json([
                    'message' => 'خطأ في قاعدة البيانات: المراجع غير موجود',
                    'error' => 'Foreign key constraint violation - referenced record not found',
                    'details' => 'The referenced representative or supervisor does not exist'
                ], 400);
            }

            // Handle other database errors
            return response()->json([
                'message' => 'خطأ في قاعدة البيانات',
                'error' => 'Database error: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Lead creation failed with LeadService', [
                'lead_data' => $leadData,
                'user_id' => $user->id,
                'user_type' => $user->type,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'خطأ في إنشاء العميل المحتمل',
                'error' => 'Lead creation error: ' . $e->getMessage()
            ], 500);
        }

        // Load relationships for response
        $lead->load(['governorate:id,name', 'source:id,name', 'employee:id,name', 'referredBy:id,name,type']);

        $message = 'تم إحالة العميل المحتمل بنجاح';
        if ($lead->assigned_to) {
            $assignedEmployee = $lead->employee;
            if ($assignedEmployee) {
                $message .= ' وتم تعيينه تلقائياً لـ ' . $assignedEmployee->name;
            }
        }

        return response()->json([
            'message' => $message,
            'data' => $lead,
            'assigned_to' => $lead->assigned_to ? [
                'id' => $lead->assigned_to,
                'name' => $lead->employee?->name ?? 'غير محدد'
            ] : null,
            'referred_by' => $lead->referred_by ? [
                'id' => $lead->referred_by,
                'name' => $lead->referredBy?->name ?? 'غير محدد',
                'type' => $lead->referred_by_type
            ] : null
        ], 201);
    }

    /**
     * Get leads for the authenticated user
     */
    public function getLeads(Request $request)
    {
        $user = $request->user();

        // Check if user is an employee (sales)
        if ($user->type !== 'employee' || !$user->employee) {
            return response()->json(['message' => 'يسمح فقط للموظفين بعرض العملاء المحتملين'], 403);
        }

        // Get leads assigned to this employee
        $leads = Lead::where('employee_id', $user->employee->id)
            ->with(['governorate:id,name', 'source:id,name'])
            ->select([
                'id', 'name', 'phone', 'governorate_id', 'source_id',
                'status', 'notes', 'created_at', 'updated_at'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $leads]);
    }

    /**
     * Get leads referred by the authenticated representative or supervisor
     */
    public function getReferredLeads(Request $request)
    {
        $user = $request->user();

        // Check if user is a representative or supervisor
        if (!in_array($user->type, ['representative', 'supervisor'])) {
            return response()->json(['message' => 'يسمح فقط للمندوبين والمشرفين بعرض الإحالات'], 403);
        }

        // Check if user has the required relationship
        if ($user->type === 'representative' && !$user->representative) {
            return response()->json(['message' => 'لم يتم العثور على ملف المندوب'], 404);
        }

        if ($user->type === 'supervisor' && !$user->supervisor) {
            return response()->json(['message' => 'لم يتم العثور على ملف المشرف'], 404);
        }

        // Get leads referred by this user
        $referredBy = $user->type === 'representative' ? $user->representative->id : $user->supervisor->id;
        $referredByType = $user->type;

        $leads = Lead::where('referred_by', $referredBy)
            ->where('referred_by_type', $referredByType)
            ->with(['governorate:id,name', 'source:id,name', 'employee:id,name'])
            ->select([
                'id', 'name', 'phone', 'governorate_id', 'source_id', 'employee_id',
                'status', 'notes', 'referred_by', 'referred_by_type', 'created_at', 'updated_at'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $leads]);
    }

    /**
     * Get a specific lead by ID
     */
    public function getLead(Request $request, $id)
    {
        $user = $request->user();

        // Check if user is an employee (sales)
        if ($user->type !== 'employee' || !$user->employee) {
            return response()->json(['message' => 'يسمح فقط للموظفين بعرض العملاء المحتملين'], 403);
        }

        // Get lead assigned to this employee
        $lead = Lead::where('id', $id)
            ->where('employee_id', $user->employee->id)
            ->with(['governorate:id,name', 'source:id,name'])
            ->first();

        if (!$lead) {
            return response()->json(['message' => 'العميل المحتمل غير موجود'], 404);
        }

        return response()->json(['data' => $lead]);
    }

    /**
     * Update lead status
     */
    public function updateLeadStatus(Request $request, $id)
    {
        $user = $request->user();

        // Check if user is an employee (sales)
        if ($user->type !== 'employee' || !$user->employee) {
            return response()->json(['message' => 'يسمح فقط للموظفين بتحديث العملاء المحتملين'], 403);
        }

        $request->validate([
            'status' => 'required|in:new,contacted,qualified,proposal,negotiation,closed_won,closed_lost',
            'notes' => 'nullable|string|max:1000'
        ], [
            'status.required' => 'حالة العميل المحتمل مطلوبة',
            'status.in' => 'حالة العميل المحتمل غير صحيحة',
            'notes.max' => 'الملاحظات يجب أن تكون أقل من 1000 حرف'
        ]);

        // Get lead assigned to this employee
        $lead = Lead::where('id', $id)
            ->where('employee_id', $user->employee->id)
            ->first();

        if (!$lead) {
            return response()->json(['message' => 'العميل المحتمل غير موجود'], 404);
        }

        // Update lead
        $lead->update([
            'status' => $request->status,
            'notes' => $request->notes ?? $lead->notes
        ]);

        return response()->json([
            'message' => 'تم تحديث حالة العميل المحتمل بنجاح',
            'data' => $lead->fresh(['governorate:id,name', 'source:id,name'])
        ]);
    }

    /**
     * Get all governments with their locations
     */
    public function getGovernmentsWithLocations(Request $request)
    {
        $governments = Governorate::with(['locations' => function($query) {
            $query->select('id', 'name', 'address', 'governorate_id')
                ->orderBy('name');
        }])
        ->select('id', 'name')
        ->orderBy('name')
        ->get();

        return response()->json(['data' => $governments]);
    }

    /**
     * Get locations for a specific government
     */
    public function getLocationsByGovernment(Request $request, $governmentId)
    {
        $government = Governorate::find($governmentId);

        if (!$government) {
            return response()->json(['message' => 'المحافظة غير موجودة'], 404);
        }

        $locations = Location::where('governorate_id', $governmentId)
            ->select('id', 'name', 'address', 'governorate_id', 'is_active')
            ->orderBy('name')
            ->get();

        // Add waiting list indicator to location names if inactive
        $locations = $locations->map(function ($location) {
            if (!$location->is_active) {
                $location->name = $location->name . ' - قائمة الانتظار';
            }
            return $location;
        });

        // Add waiting list indicator to government name if inactive
        $governmentName = $government->name;
        if (!$government->is_active) {
            $governmentName = $government->name . ' - قائمة الانتظار';
        }

        return response()->json([
            'data' => [
                'government' => [
                    'id' => $government->id,
                    'name' => $governmentName,
                    'is_active' => $government->is_active
                ],
                'locations' => $locations
            ]
        ]);
    }

    /**
     * Get all governments (simple list)
     */
    public function getGovernments(Request $request)
    {
        $governments = Governorate::select('id', 'name', 'is_active')
            ->orderBy('name')
            ->get();

        // Add waiting list indicator to government names if inactive
        $governments = $governments->map(function ($government) {
            if (!$government->is_active) {
                $government->name = $government->name . ' - قائمة الانتظار';
            }
            return $government;
        });

        return response()->json(['data' => $governments]);
    }

    /**
     * Get all locations (simple list)
     */
    public function getLocations(Request $request)
    {
        $locations = Location::with('governorate:id,name,is_active')
            ->select('id', 'name', 'address', 'governorate_id', 'is_active')
            ->orderBy('name')
            ->get();

        // Add waiting list indicator to location names if inactive
        $locations = $locations->map(function ($location) {
            if (!$location->is_active) {
                $location->name = $location->name . ' - قائمة الانتظار';
            }

            // Also add waiting list indicator to governorate name if inactive
            if ($location->governorate && !$location->governorate->is_active) {
                $location->governorate->name = $location->governorate->name . ' - قائمة الانتظار';
            }

            return $location;
        });

        return response()->json(['data' => $locations]);
    }

    /**
     * Get all banks (for bank account creation)
     */
    public function getBanks(Request $request)
    {
        $banks = \App\Models\Bank::select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json(['data' => $banks]);
    }

    /**
     * Get salary summary for the authenticated user
     */
    public function getSalarySummary(Request $request)
    {
        $user = $request->user();

        // Check if user is a representative or supervisor
        if (!in_array($user->type, ['representative', 'supervisor'])) {
            return response()->json(['message' => 'يسمح فقط للمندوبين والمشرفين بعرض بيانات المرتبات'], 403);
        }

        // Static salary data for 2025
        $salaryData = [
            'year' => 2025,
            'title' => 'المرتبات',
            'total_salary' => 5632,
            'months' => [
                [
                    'month' => 'يناير',
                    'month_name' => 'شهر يناير',
                    'base_salary' => 5632,
                    'total' => 5632,
                    'details' => [
                        [
                            'type' => 'المرتب الاساسي',
                            'amount' => 5632,
                            'description' => 'المرتب الاساسي'
                        ]
                    ]
                ],
                [
                    'month' => 'فبراير',
                    'month_name' => 'شهر فبراير',
                    'base_salary' => 5632,
                    'total' => 6632,
                    'details' => [
                        [
                            'type' => 'المرتب الاساسي',
                            'amount' => 5632,
                            'description' => 'المرتب الاساسي'
                        ],
                        [
                            'type' => 'سلفة',
                            'amount' => 1000,
                            'description' => 'سلفة'
                        ]
                    ]
                ],
                [
                    'month' => 'مارس',
                    'month_name' => 'شهر مارس',
                    'base_salary' => 5632,
                    'total' => 5782,
                    'details' => [
                        [
                            'type' => 'المرتب الاساسي',
                            'amount' => 5632,
                            'description' => 'المرتب الاساسي'
                        ],
                        [
                            'type' => 'مكافأة',
                            'amount' => 150,
                            'description' => 'مكافأة'
                        ]
                    ]
                ]
            ],
            'summary' => [
                'total_base_salary' => 16896, // 5632 * 3 months
                'total_bonuses' => 1150, // 1000 + 150
                'total_deductions' => 0,
                'grand_total' => 18046
            ]
        ];

        return response()->json(['data' => $salaryData]);
    }

    /**
     * Get detailed salary breakdown for a specific month
     */
    public function getSalaryDetails(Request $request, $month)
    {
        $user = $request->user();

        // Check if user is a representative or supervisor
        if (!in_array($user->type, ['representative', 'supervisor'])) {
            return response()->json(['message' => 'يسمح فقط للمندوبين والمشرفين بعرض تفاصيل المرتبات'], 403);
        }

        // Validate month parameter
        $validMonths = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
        if (!in_array($month, $validMonths)) {
            return response()->json(['message' => 'الشهر غير صحيح'], 400);
        }

        // Static detailed salary data
        $salaryDetails = [
            'year' => 2025,
            'month' => $month,
            'month_name' => 'شهر ' . $month,
            'base_salary' => 5632,
            'components' => [
                [
                    'type' => 'المرتب الاساسي',
                    'amount' => 5632,
                    'description' => 'المرتب الاساسي',
                    'category' => 'base'
                ]
            ],
            'deductions' => [],
            'bonuses' => [],
            'total' => 5632
        ];


        // Add specific data based on month
        switch ($month) {
            case 'فبراير':
                $salaryDetails['components'][] = [
                    'type' => 'سلفة',
                    'amount' => 1000,
                    'description' => 'سلفة',
                    'category' => 'bonus'
                ];
                $salaryDetails['bonuses'] = [
                    [
                        'type' => 'سلفة',
                        'amount' => 1000,
                        'description' => 'سلفة'
                    ]
                ];
                $salaryDetails['total'] = 6632;
                break;

            case 'مارس':
                $salaryDetails['components'][] = [
                    'type' => 'مكافأة',
                    'amount' => 150,
                    'description' => 'مكافأة',
                    'category' => 'bonus'
                ];
                $salaryDetails['bonuses'] = [
                    [
                        'type' => 'مكافأة',
                        'amount' => 150,
                        'description' => 'مكافأة'
                    ]
                ];
                $salaryDetails['total'] = 5782;
                break;

            case 'يناير':
                // Add deduction example for January
                $salaryDetails['components'][] = [
                    'type' => 'خصم',
                    'amount' => -124,
                    'description' => 'بضاعة مفقودة',
                    'category' => 'deduction'
                ];
                $salaryDetails['deductions'] = [
                    [
                        'type' => 'خصم',
                        'amount' => 124,
                        'description' => 'بضاعة مفقودة'
                    ]
                ];
                $salaryDetails['total'] = 5508; // 5632 - 124
                break;
        }

        return response()->json(['data' => $salaryDetails]);
    }
}
