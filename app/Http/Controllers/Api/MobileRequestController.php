<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Slider;
use App\Models\LeaveRequest;
use App\Models\AdvanceRequest;
use App\Models\ResignationRequest;
use App\Models\DeliveryDeposit;
use App\Services\FirebaseNotificationService;
use App\Services\NotificationService;

class MobileRequestController extends Controller
{
    /**
     * Convert old storage URLs to new format
     */
    private function convertStorageUrl($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            // If it's an old storage URL format, convert it to the new format
            if (strpos($url, '/storage/attachments/') !== false) {
                return str_replace('/storage/attachments/', '/storage/app/public/attachments/', $url);
            } elseif (strpos($url, '/storage/representatives/attachments/') !== false) {
                return str_replace('/storage/representatives/attachments/', '/storage/app/public/representatives/attachments/', $url);
            } elseif (strpos($url, '/storage/delivery-receipts/') !== false) {
                return str_replace('/storage/delivery-receipts/', '/storage/app/public/delivery-receipts/', $url);
            } elseif (strpos($url, '/storage/sliders/') !== false) {
                return str_replace('/storage/sliders/', '/storage/app/public/sliders/', $url);
            } else {
                return $url; // Keep as-is if it's already correct
            }
        } else {
            return asset('storage/app/public/' . $url); // Add prefix for relative paths
        }
    }
    protected $firebaseService;
    protected $notificationService;

    public function __construct(FirebaseNotificationService $firebaseService, NotificationService $notificationService)
    {
        $this->firebaseService = $firebaseService;
        $this->notificationService = $notificationService;
    }

    /**
     * Supervisors: list representatives who have no attachments uploaded.
     */
    public function getSupervisorRepsWithoutAttachments(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->type !== 'supervisor' || !$user->supervisor) {
            return response()->json(['message' => 'غير مصرح'], 403);
        }

        $query = \App\Models\Representative::where('is_active', true)
            ->whereHas('supervisors', function($q) use ($user) {
                $q->where('supervisors.id', $user->supervisor->id);
            })
            ->where(function($q){
                $q->whereNull('attachments')
                  ->orWhere('attachments', '[]')
                  ->orWhere('attachments', '=','"[]"');
            });

        // Add search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        $reps = $query->select(['id','name','phone','code','company_id','attachments'])
            ->with('company:id,name')
            ->orderBy('name')
            ->get();

        return response()->json(['data' => $reps]);
    }

    /**
     * Supervisors: list representatives who have uploaded attachments.
     */
    public function getSupervisorRepsWithAttachments(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->type !== 'supervisor' || !$user->supervisor) {
            return response()->json(['message' => 'غير مصرح'], 403);
        }

        $query = \App\Models\Representative::where('is_active', true)
            ->whereHas('supervisors', function($q) use ($user) {
                $q->where('supervisors.id', $user->supervisor->id);
            });

        // Add search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        $reps = $query->select(['id','name','phone','code','company_id','attachments'])
            ->with('company:id,name')
            ->orderBy('name')
            ->get()
            ->filter(function($rep){
                $raw = is_string($rep->attachments) ? json_decode($rep->attachments, true) : $rep->attachments;
                $raw = is_array($raw) ? array_values($raw) : [];
                $nonEmpty = array_filter($raw, function($v){ return is_string($v) && trim($v) !== ''; });
                return count($nonEmpty) > 0;
            })
            ->map(function($rep){
                $raw = is_string($rep->attachments) ? json_decode($rep->attachments, true) : $rep->attachments;
                $raw = is_array($raw) ? array_values($raw) : [];
                $rep->attachments = $raw;

                // Check if all 8 attachments are present
                $allAttachmentsPresent = true;
                for ($i = 0; $i < 8; $i++) {
                    if (!isset($raw[$i]) || empty($raw[$i])) {
                        $allAttachmentsPresent = false;
                        break;
                    }
                }
                $rep->all_attachments_present = $allAttachmentsPresent;

                // Build full 0..7 list
                $rep->attachment_items = collect(range(0,7))->map(function($i) use ($raw){
                    $label = match($i){
                        0 => 'البطاقة (وجه أول)',
                        1 => 'البطاقة (خلف)',
                        2 => 'فيش',
                        3 => 'شهادة ميلاد',
                        4 => 'إيصال الأمانة',
                        5 => 'رخصة القيادة',
                        6 => 'رخصة السيارة',
                        7 => 'إيصال مرافق',
                        default => 'مرفق ' . ($i + 1),
                    };
                    return [
                        'index' => $i,
                        'label' => $label,
                        'has_image' => isset($raw[$i]) && !empty($raw[$i]),
                        'url' => isset($raw[$i]) && !empty($raw[$i]) ? $this->convertStorageUrl($raw[$i]) : null,
                        'src' => isset($raw[$i]) && !empty($raw[$i]) ? $this->convertStorageUrl($raw[$i]) : null,
                        'path' => $raw[$i] ?? null
                    ];
                });
                return $rep;
            });

        return response()->json(['data' => $reps]);
    }

    /**
     * Supervisors: search representatives with or without attachments
     * Query parameters:
     * - search: optional search by name or phone
     * Returns both representatives with and without attachments
     */
    public function searchSupervisorReps(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->type !== 'supervisor' || !$user->supervisor) {
            return response()->json(['message' => 'غير مصرح'], 403);
        }

        $request->validate([
            'search' => 'nullable|string|max:255'
        ]);

        $search = $request->search;

        $query = \App\Models\Representative::where('is_active', true)
            ->whereHas('supervisors', function($q) use ($user) {
                $q->where('supervisors.id', $user->supervisor->id);
            });

        // Add search functionality
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        $reps = $query->select(['id','name','phone','code','company_id','attachments'])
            ->with('company:id,name')
            ->orderBy('name')
            ->get();

        // Separate representatives with and without attachments
        $repsWithAttachments = $reps->filter(function($rep){
            $raw = is_string($rep->attachments) ? json_decode($rep->attachments, true) : $rep->attachments;
            $raw = is_array($raw) ? array_values($raw) : [];
            $nonEmpty = array_filter($raw, function($v){ return is_string($v) && trim($v) !== ''; });
            return count($nonEmpty) > 0;
        })->map(function($rep){
            $raw = is_string($rep->attachments) ? json_decode($rep->attachments, true) : $rep->attachments;
            $raw = is_array($raw) ? array_values($raw) : [];
            $rep->attachments = $raw;

            // Check if all 8 attachments are present
            $allAttachmentsPresent = true;
            for ($i = 0; $i < 8; $i++) {
                if (!isset($raw[$i]) || empty($raw[$i])) {
                    $allAttachmentsPresent = false;
                    break;
                }
            }
            $rep->all_attachments_present = $allAttachmentsPresent;

            // Build full 0..7 list
            $rep->attachment_items = collect(range(0,7))->map(function($i) use ($raw){
                $label = match($i){
                    0 => 'البطاقة (وجه أول)',
                    1 => 'البطاقة (خلف)',
                    2 => 'فيش',
                    3 => 'شهادة ميلاد',
                    4 => 'إيصال الأمانة',
                    5 => 'رخصة القيادة',
                    6 => 'رخصة السيارة',
                    7 => 'إيصال مرافق',
                    default => 'مرفق ' . ($i + 1),
                };
                return [
                    'index' => $i,
                    'label' => $label,
                    'has_image' => isset($raw[$i]) && !empty($raw[$i]),
                    'url' => isset($raw[$i]) && !empty($raw[$i]) ? $this->convertStorageUrl($raw[$i]) : null,
                    'src' => isset($raw[$i]) && !empty($raw[$i]) ? $this->convertStorageUrl($raw[$i]) : null,
                    'path' => $raw[$i] ?? null
                ];
            });
            return $rep;
        });

        $repsWithoutAttachments = $reps->filter(function($rep){
            $raw = is_string($rep->attachments) ? json_decode($rep->attachments, true) : $rep->attachments;
            $raw = is_array($raw) ? array_values($raw) : [];
            $nonEmpty = array_filter($raw, function($v){ return is_string($v) && trim($v) !== ''; });
            return count($nonEmpty) === 0;
        });

        return response()->json([
            'data' => [
                'with_attachments' => $repsWithAttachments->values(),
                'without_attachments' => $repsWithoutAttachments->values()
            ],
            'meta' => [
                'search' => $search,
                'total_count' => $reps->count(),
                'with_attachments_count' => $repsWithAttachments->count(),
                'without_attachments_count' => $repsWithoutAttachments->count()
            ]
        ]);
    }

    /**
     * Supervisors: show attachments for a specific representative under them.
     */
    public function getRepresentativeAttachments(Request $request, $id)
    {
        $user = $request->user();
        if (!$user || $user->type !== 'supervisor' || !$user->supervisor) {
            return response()->json(['message' => 'غير مصرح'], 403);
        }

        $rep = \App\Models\Representative::where('id', $id)
            ->whereHas('supervisors', function($q) use ($user) {
                $q->where('supervisors.id', $user->supervisor->id);
            })
            ->with('company:id,name')
            ->firstOrFail(['id','name','attachments','company_id']);

        $attachments = $rep->attachments;
        if (is_string($attachments)) {
            $attachments = json_decode($attachments, true) ?: [];
        }

        // Check if all 8 attachments are present
        $allAttachmentsPresent = true;
        for ($i = 0; $i < 8; $i++) {
            if (!isset($attachments[$i]) || empty($attachments[$i])) {
                $allAttachmentsPresent = false;
                break;
            }
        }

        // Always build full 0..7 set
        $attachmentItems = collect(range(0,7))->map(function($i) use ($attachments){
            $label = match($i){
                0 => 'البطاقة (وجه أول)',
                1 => 'البطاقة (خلف)',
                2 => 'فيش',
                3 => 'شهادة ميلاد',
                4 => 'إيصال الأمانة',
                5 => 'رخصة القيادة',
                6 => 'رخصة السيارة',
                7 => 'إيصال مرافق',
                default => 'مرفق ' . ($i + 1),
            };
            return [
                'index' => $i,
                'label' => $label,
                'has_image' => isset($attachments[$i]) && !empty($attachments[$i]),
                'url' => isset($attachments[$i]) && !empty($attachments[$i]) ? $this->convertStorageUrl($attachments[$i]) : null,
                'src' => isset($attachments[$i]) && !empty($attachments[$i]) ? $this->convertStorageUrl($attachments[$i]) : null,
                'path' => $attachments[$i] ?? null
            ];
        });

        return response()->json(['data' => [
            'representative_id' => $rep->id,
            'name' => $rep->name,
            'company_name' => $rep->company?->name,
            'attachments' => $attachments,
            'attachment_items' => $attachmentItems,
            'all_attachments_present' => $allAttachmentsPresent,
        ]]);
    }

    /**
     * Supervisors: update attachments array for a representative.
     * Accepts: attachments[] as array of URLs or storage paths.
     */
    public function updateRepresentativeAttachments(Request $request, $id)
    {
        $user = $request->user();
        if (!$user || $user->type !== 'supervisor' || !$user->supervisor) {
            return response()->json(['message' => 'غير مصرح'], 403);
        }

        // Base64-only, update-by-index API
        $validated = $request->validate([
            'updates' => 'required_without:remove_indices|array',
            'updates.*.index' => 'required|integer|min:0|max:7',
            'updates.*.file_base64' => 'required|string',
            'remove_indices' => 'sometimes|array',
            'remove_indices.*' => 'integer|min:0|max:7',
        ]);

        $rep = \App\Models\Representative::where('id', $id)
            ->whereHas('supervisors', function($q) use ($user) {
                $q->where('supervisors.id', $user->supervisor->id);
            })
            ->firstOrFail();

        // Load current attachments as array
        $current = $rep->attachments;
        if (is_string($current)) {
            $current = json_decode($current, true) ?: [];
        }
        if (!is_array($current)) { $current = []; }

        // Ensure fixed 8-slot structure (0..7)
        $new = array_values($current);
        for ($i = 0; $i < 8; $i++) {
            if (!array_key_exists($i, $new)) { $new[$i] = null; }
        }

        // Apply removals first (set to null)
        if (!empty($validated['remove_indices'])) {
            foreach ($validated['remove_indices'] as $ri) {
                $idx = (int) $ri;
                if ($idx >= 0 && $idx <= 7) {
                    $new[$idx] = null;
                }
            }
        }

        // Apply updates (decode base64 and write to exact index)
        $debug = [];
        $savedCount = 0;
        if (!empty($validated['updates'])) {
            foreach ($validated['updates'] as $u) {
                $idx = (int) $u['index'];
                $raw = (string) $u['file_base64'];
                // Strip data URI prefix if present
                if (preg_match('/^data:.*?;base64,/', $raw)) {
                    $raw = substr($raw, strpos($raw, ',') + 1);
                }
                // Remove whitespace/newlines
                $raw = preg_replace('/\s+/', '', $raw);
                // Support URL-safe base64 (- and _)
                $raw = strtr($raw, '-_', '+/');
                // Pad to length multiple of 4
                $mod = strlen($raw) % 4;
                if ($mod) { $raw .= str_repeat('=', 4 - $mod); }

                $decoded = base64_decode($raw, true);
                if ($decoded === false) {
                    $debug[] = ['index' => $idx, 'reason' => 'invalid_base64'];
                    continue;
                }
                if (strlen($decoded) > 15 * 1024 * 1024) {
                    // skip too large (>15MB approx)
                    $debug[] = ['index' => $idx, 'reason' => 'too_large', 'size' => strlen($decoded)];
                    continue;
                }
                $filename = 'rep_' . now()->timestamp . '_' . uniqid() . '.png';
                $path = 'representatives/attachments/' . $filename;
                $ok = \Storage::disk('public')->put($path, $decoded);
                if (!$ok) {
                    $debug[] = ['index' => $idx, 'reason' => 'write_failed'];
                    continue;
                }
                $url = asset('storage/app/public/' . $path);
                if ($idx < 0) { $idx = 0; }
                if ($idx > 7) { $idx = 7; }
                $new[$idx] = $url;
                $savedCount++;
            }
        }

        // Normalize to sequential 8-length array [0..7]
        $new = array_values($new);
        for ($i = 0; $i < 8; $i++) {
            if (!array_key_exists($i, $new)) { $new[$i] = null; }
        }

        // Persist normalized 8-element array
        $rep->attachments = $new;
        $rep->save();

        // Check if all 8 attachments are present
        $allAttachmentsPresent = true;
        for ($i = 0; $i < 8; $i++) {
            if (!isset($new[$i]) || empty($new[$i])) {
                $allAttachmentsPresent = false;
                break;
            }
        }

        // Build labeled response
        $items = collect(range(0,7))->map(function($index) use ($new){
            $label = match($index){
                0 => 'البطاقة (وجه أول)',
                1 => 'البطاقة (خلف)',
                2 => 'فيش',
                3 => 'شهادة ميلاد',
                4 => 'إيصال الأمانة',
                5 => 'رخصة القيادة',
                6 => 'رخصة السيارة',
                7 => 'إيصال مرافق',
                default => 'مرفق ' . ($index + 1),
            };
            return [
                'index' => $index,
                'label' => $label,
                'has_image' => !empty($new[$index]),
                'url' => $new[$index],
            ];
        })->values()->all();

        return response()->json([
            'message' => 'تم تحديث المرفقات بنجاح',
            'data' => [
                'attachments' => $new,
                'attachment_items' => $items,
                'all_attachments_present' => $allAttachmentsPresent,
            ],
            'meta' => [
                'saved' => $savedCount,
                'removed' => !empty($validated['remove_indices']) ? count($validated['remove_indices']) : 0,
                'debug' => $debug,
            ]
        ]);
    }
    public function getProfile(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->type, ['representative', 'supervisor'])) {
            return response()->json(['message' => 'يسمح فقط للمندوبين والمشرفين'], 403);
        }

        // Get current year and month for default salary
        $currentYear = now()->year;
        $currentMonth = now()->month;
        $monthNames = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
        ];

        // Fixed salary values for all months
        $baseSalary = 5632;
        $advance = 1000;
        $bonus = 150;
        $deduction = 0;
        $missingGoods = 124;
        $total = $baseSalary + $advance + $bonus - $deduction - $missingGoods;

        // Generate salary data for all 12 months of current year
        $salaryByYear = [];
        for ($month = 1; $month <= 12; $month++) {
            $salaryByYear[$month] = [
                'month' => $month,
                'month_name' => $monthNames[$month],
                'month_display' => "شهر {$monthNames[$month]} {$currentYear}",
                'base_salary' => $baseSalary,
                'advance' => $advance,
                'bonus' => $bonus,
                'deduction' => $deduction,
                'missing_goods' => $missingGoods,
                'total' => $total,
                'breakdown' => [
                    [
                        'type' => 'المرتب الاساسي',
                        'amount' => $baseSalary,
                        'description' => 'المرتب الاساسي'
                    ],
                    [
                        'type' => 'سلفة',
                        'amount' => $advance,
                        'description' => 'سلفة'
                    ],
                    [
                        'type' => 'مكافأة',
                        'amount' => $bonus,
                        'description' => 'مكافأة'
                    ],
                    [
                        'type' => 'خصم',
                        'amount' => $deduction,
                        'description' => 'خصم'
                    ],
                    [
                        'type' => 'بضاعة مفقودة',
                        'amount' => $missingGoods,
                        'description' => 'بضاعة مفقودة'
                    ],
                    [
                        'type' => 'الاجمالي',
                        'amount' => $total,
                        'description' => 'الاجمالي'
                    ]
                ]
            ];
        }

        $data = [
            'id' => $user->id,
            'name' => $user->name ?? $user->employee->name ?? $user->representative->name ?? $user->supervisor->name ?? null,
            'phone' => $user->phone,
            'type' => $user->type,
            'avatar_url' => $user->avatar_url,
            // Salary grouped by year and month
            'salary' => [
                'amount' => $baseSalary,
                'currency' => 'EGP',
                'formatted' => number_format($baseSalary, 2) . ' جنيه',
                'monthly' => true,
                'current_year' => $currentYear,
                'current_month' => $currentMonth,
                'current_month_name' => $monthNames[$currentMonth] ?? 'غير محدد',
                'by_year' => [
                    $currentYear => [
                        'year' => $currentYear,
                        'months' => $salaryByYear
                    ]
                ]
            ]
        ];

        if ($user->type === 'representative' && $user->representative) {
            $rep = $user->representative;
            $data['bank_account'] = $rep->bank_account ?? null;
            $data['company_id'] = $rep->company?->id;
            $data['company_name'] = $rep->company?->name;

            // Address and location data
            $data['address'] = $rep->address ?? null;
            $data['home_location'] = $rep->home_location ?? null;
            $data['national_id'] = $rep->national_id ?? null;
            $data['contact'] = $rep->contact ?? null;
            $data['start_date'] = $rep->start_date ?? null;
            $data['code'] = $rep->code ?? null;

            // Generate salary data for all 12 months of current year (same structure as default)
            $repSalaryByYear = [];
            for ($month = 1; $month <= 12; $month++) {
                $repSalaryByYear[$month] = [
                    'month' => $month,
                    'month_name' => $monthNames[$month],
                    'month_display' => "شهر {$monthNames[$month]} {$currentYear}",
                    'base_salary' => $baseSalary,
                    'advance' => $advance,
                    'bonus' => $bonus,
                    'deduction' => $deduction,
                    'missing_goods' => $missingGoods,
                    'total' => $total,
                    'breakdown' => [
                        [
                            'type' => 'المرتب الاساسي',
                            'amount' => $baseSalary,
                            'description' => 'المرتب الاساسي'
                        ],
                        [
                            'type' => 'سلفة',
                            'amount' => $advance,
                            'description' => 'سلفة'
                        ],
                        [
                            'type' => 'مكافأة',
                            'amount' => $bonus,
                            'description' => 'مكافأة'
                        ],
                        [
                            'type' => 'خصم',
                            'amount' => $deduction,
                            'description' => 'خصم'
                        ],
                        [
                            'type' => 'بضاعة مفقودة',
                            'amount' => $missingGoods,
                            'description' => 'بضاعة مفقودة'
                        ],
                        [
                            'type' => 'الاجمالي',
                            'amount' => $total,
                            'description' => 'الاجمالي'
                        ]
                    ]
                ];
            }

            $data['salary'] = [
                'amount' => $baseSalary,
                'currency' => 'EGP',
                'formatted' => number_format($baseSalary, 2) . ' جنيه',
                'monthly' => true,
                'current_year' => $currentYear,
                'current_month' => $currentMonth,
                'current_month_name' => $monthNames[$currentMonth] ?? 'غير محدد',
                'by_year' => [
                    $currentYear => [
                        'year' => $currentYear,
                        'months' => $repSalaryByYear
                    ]
                ]
            ];

            // Governorate data
            if ($rep->governorate) {
                $data['governorate'] = [
                    'id' => $rep->governorate->id,
                    'name' => $rep->governorate->name,
                ];
            } else {
                $data['governorate'] = null;
            }

            // Location data
            if ($rep->location) {
                $data['location'] = [
                    'id' => $rep->location->id,
                    'name' => $rep->location->name,
                    'address' => $rep->location->address,
                    'governorate_id' => $rep->location->governorate_id,
                    'governorate_name' => $rep->location->governorate?->name,
                ];
            } else {
                $data['location'] = null;
            }
        }

        if ($user->type === 'supervisor' && $user->supervisor) {
            $sup = $user->supervisor;
            $data['bank_account'] = $sup->bank_account ?? null;
            $data['company_id'] = $sup->company?->id;
            $data['company_name'] = $sup->company?->name;

            // Address and location data
            $data['address'] = $sup->address ?? null;
            $data['home_location'] = $sup->home_location ?? null;
            $data['national_id'] = $sup->national_id ?? null;
            $data['contact'] = $sup->contact ?? null;
            $data['start_date'] = $sup->start_date ?? null;
            $data['code'] = $sup->code ?? null;

            // Generate salary data for all 12 months of current year (same structure as default)
            $supSalaryByYear = [];
            for ($month = 1; $month <= 12; $month++) {
                $supSalaryByYear[$month] = [
                    'month' => $month,
                    'month_name' => $monthNames[$month],
                    'month_display' => "شهر {$monthNames[$month]} {$currentYear}",
                    'base_salary' => $baseSalary,
                    'advance' => $advance,
                    'bonus' => $bonus,
                    'deduction' => $deduction,
                    'missing_goods' => $missingGoods,
                    'total' => $total,
                    'breakdown' => [
                        [
                            'type' => 'المرتب الاساسي',
                            'amount' => $baseSalary,
                            'description' => 'المرتب الاساسي'
                        ],
                        [
                            'type' => 'سلفة',
                            'amount' => $advance,
                            'description' => 'سلفة'
                        ],
                        [
                            'type' => 'مكافأة',
                            'amount' => $bonus,
                            'description' => 'مكافأة'
                        ],
                        [
                            'type' => 'خصم',
                            'amount' => $deduction,
                            'description' => 'خصم'
                        ],
                        [
                            'type' => 'بضاعة مفقودة',
                            'amount' => $missingGoods,
                            'description' => 'بضاعة مفقودة'
                        ],
                        [
                            'type' => 'الاجمالي',
                            'amount' => $total,
                            'description' => 'الاجمالي'
                        ]
                    ]
                ];
            }

            $data['salary'] = [
                'amount' => $baseSalary,
                'currency' => 'EGP',
                'formatted' => number_format($baseSalary, 2) . ' جنيه',
                'monthly' => true,
                'current_year' => $currentYear,
                'current_month' => $currentMonth,
                'current_month_name' => $monthNames[$currentMonth] ?? 'غير محدد',
                'by_year' => [
                    $currentYear => [
                        'year' => $currentYear,
                        'months' => $supSalaryByYear
                    ]
                ]
            ];

            // Governorate data
            if ($sup->governorate) {
                $data['governorate'] = [
                    'id' => $sup->governorate->id,
                    'name' => $sup->governorate->name,
                ];
            } else {
                $data['governorate'] = null;
            }

            // Location data
            if ($sup->location) {
                $data['location'] = [
                    'id' => $sup->location->id,
                    'name' => $sup->location->name,
                    'address' => $sup->location->address,
                    'governorate_id' => $sup->location->governorate_id,
                    'governorate_name' => $sup->location->governorate?->name,
                ];
            } else {
                $data['location'] = null;
            }
        }

        return response()->json(['data' => $data]);
    }

    /**
     * Create bank account for representative
     */
    public function createBankAccount(Request $request)
    {
        try {
            $user = $request->user();

            // Check if user is representative
            /* if ($user->type !== 'representative' || !$user->representative) {
                return response()->json([
                    'message' => 'يسمح فقط للمندوبين بإنشاء حساب بنكي',
                    'status' => 'error'
                ], 403);
            } */

            $representative = $user->representative;

            // التحقق من وجود حساب بنكي للمندوب
            $existingBankAccount = \App\Models\BankAccount::where('representative_id', $representative->id)
                ->with('bank')
                ->first();

            $isUpdate = false; // متغير لتحديد إذا كان التحديث أم الإنشاء

            // إذا كان يوجد حساب وحالته "يمتلك حساب"، لا نسمح بإنشاء حساب جديد
            if ($existingBankAccount && $existingBankAccount->status === 'يمتلك حساب') {
                return response()->json([
                    'message' => 'لديك حساب بنكي مسجل بالفعل',
                    'status' => 'error',
                    'data' => [
                        'id' => $existingBankAccount->id,
                        'bank_id' => $existingBankAccount->bank_id,
                        'bank_name' => $existingBankAccount->bank->name ?? null,
                        'status' => $existingBankAccount->status,
                        'account_owner_name' => $existingBankAccount->account_owner_name,
                        'account_number' => $existingBankAccount->account_number,
                    ]
                ], 409); // 409 Conflict status code
            }

            // إذا كانت الحالة "لا يمتلك حساب"، نقبل البيانات بدون الحقول الأخرى
            if ($request->status === 'لا يمتلك حساب') {
                $request->validate([
                    'status' => 'required|in:لا يمتلك حساب',
                ]);

                // إذا كان يوجد حساب بحالة "لا يمتلك حساب"، نحدثه بدلاً من إنشاء حساب جديد
                if ($existingBankAccount) {
                    $isUpdate = true;
                    $bankAccount = $existingBankAccount;
                    $bankAccount->update([
                        'status' => 'لا يمتلك حساب',
                        'bank_id' => null,
                        'account_owner_name' => null,
                        'account_number' => null,
                    ]);
                } else {
                    $bankAccount = \App\Models\BankAccount::create([
                        'representative_id' => $representative->id,
                        'bank_id' => null, // لا يوجد بنك
                        'status' => 'لا يمتلك حساب',
                        'account_owner_name' => null, // فارغ
                        'account_number' => null, // فارغ
                    ]);
                }
            } else {
                // إذا كانت الحالة "يمتلك حساب"، نطلب جميع الحقول
                $request->validate([
                    'bank_id' => 'required|exists:banks,id',
                    'status' => 'required|in:يمتلك حساب',
                    'account_owner_name' => 'required|string|max:255',
                    'account_number' => 'required|string|max:255',
                ]);

                // إذا كان يوجد حساب بحالة "لا يمتلك حساب"، نحدثه بدلاً من إنشاء حساب جديد
                if ($existingBankAccount) {
                    $isUpdate = true;
                    $bankAccount = $existingBankAccount;
                    $bankAccount->update([
                        'bank_id' => $request->bank_id,
                        'status' => $request->status,
                        'account_owner_name' => $request->account_owner_name,
                        'account_number' => $request->account_number,
                    ]);
                } else {
                    $bankAccount = \App\Models\BankAccount::create([
                        'representative_id' => $representative->id,
                        'bank_id' => $request->bank_id,
                        'status' => $request->status,
                        'account_owner_name' => $request->account_owner_name,
                        'account_number' => $request->account_number,
                    ]);
                }
            }

            // Load bank relationship if exists
            if ($bankAccount->bank_id) {
                $bankAccount->load('bank');
            }

            // تحديد الرسالة بناءً على العملية (تحديث أم إنشاء) والحالة
            $message = '';
            if ($isUpdate) {
                $message = $bankAccount->status === 'لا يمتلك حساب'
                    ? 'تم تحديث الحالة بنجاح'
                    : 'تم تحديث الحساب البنكي بنجاح';
            } else {
                $message = $bankAccount->status === 'لا يمتلك حساب'
                    ? 'تم تسجيل الحالة بنجاح'
                    : 'تم إنشاء الحساب البنكي بنجاح';
            }

            return response()->json([
                'message' => $message,
                'status' => 'success',
                'data' => [
                    'id' => $bankAccount->id,
                    'bank_id' => $bankAccount->bank_id,
                    'bank_name' => $bankAccount->bank->name ?? null,
                    'status' => $bankAccount->status,
                    'account_owner_name' => $bankAccount->account_owner_name,
                    'account_number' => $bankAccount->account_number,
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'بيانات غير صالحة',
                'status' => 'error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error creating bank account from mobile: ' . $e->getMessage());
            return response()->json([
                'message' => 'حدث خطأ أثناء إنشاء الحساب البنكي',
                'status' => 'error'
            ], 500);
        }
    }
    public function getBankAccount(Request $request)
    {
        $user = $request->user();
        $bankAccount = \App\Models\BankAccount::where('representative_id', $user->representative->id)
            ->with('bank')
            ->first();
        return response()->json(['data' => $bankAccount]);
    }

    public function updateBankAccount(Request $request)
    {

        $request->validate([
            'bank_id' => 'required|exists:banks,id',
            'account_owner_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
        ]);
        $user = $request->user();
        $bankAccount = \App\Models\BankAccount::where('representative_id', $user->representative->id)
            ->with('bank')
            ->first();
        $bankAccount->update($request->all());
        return response()->json(['message' => 'تم تحديث الحساب البنكي بنجاح']);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->type, ['representative', 'supervisor'])) {
            return response()->json(['message' => 'يسمح فقط للمندوبين والمشرفين'], 403);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|digits:11',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
            'national_id' => 'nullable|digits:14',
            'bank_account' => 'nullable|string|max:255',
        ]);

        if (isset($validated['name'])) {
            $user->name = $validated['name'];
        }
        if (isset($validated['phone'])) {
            $user->phone = $validated['phone'];
        }

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        if (isset($validated['bank_account'])) {
            if ($user->type === 'representative' && $user->representative) {
                $user->representative->update(['bank_account' => $validated['bank_account']]);
            } elseif ($user->type === 'supervisor' && $user->supervisor) {
                // Ensure supervisors table has bank_account column; ignore if not
                try {
                    \Illuminate\Support\Facades\DB::table('supervisors')->where('id', $user->supervisor->id)->update(['bank_account' => $validated['bank_account']]);
                } catch (\Throwable $e) {
                    // silently ignore if column missing
                }
            }
        }
        if (isset($validated['national_id'])) {
            if ($user->type === 'representative' && $user->representative) {
                $user->representative->update(['national_id' => $validated['national_id']]);
            } elseif ($user->type === 'supervisor' && $user->supervisor) {
                $user->supervisor->update(['national_id' => $validated['national_id']]);
            }
        }

        return response()->json(['message' => 'تم تحديث الملف الشخصي بنجاح']);
    }
    public function createLeave(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'nullable|in:سنوية,مرضية,طارئة,أخرى',
            'reason' => 'nullable|string|max:500'
        ]);

        $user = $request->user();

        // السماح فقط للمندوبين والمشرفين بطلب الإجازة
        if (!in_array($user->type, ['representative', 'supervisor'])) {
            return response()->json(['message' => 'يسمح فقط للمندوبين والمشرفين بطلب الإجازة'], 403);
        }

        $leaveData = [
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'type' => $request->type,
            'reason' => $request->reason??null,
            'status' => 'pending'
        ];

        // ربط الطلب بنوع المستخدم المناسب
        if ($user->type === 'representative' && $user->representative) {
            $leaveData['representative_id'] = $user->representative->id;
        } elseif ($user->type === 'supervisor' && $user->supervisor) {
            $leaveData['supervisor_id'] = $user->supervisor->id;
        } else {
            return response()->json(['message' => 'لم يتم العثور على ملف المستخدم'], 404);
        }

        $leave = LeaveRequest::create($leaveData);

        // Create notification for admins only (mobile requests)
        try {
            $this->notificationService->notifyLeaveRequest($leave, 'created');
        } catch (\Exception $e) {
            \Log::error('Failed to create leave request notification: ' . $e->getMessage());
        }

        return response()->json(['message' => 'تم إرسال طلب الإجازة بنجاح', 'data' => $leave]);
    }

    public function createAdvance(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0|max:5000',
            'installment_months' => 'nullable|integer|min:1|max:12',
            'reason' => 'nullable|string|max:500',
            'wallet_number' => 'required|string|max:255',
        ], [
            'amount.max' => 'اقصى حد لطلب السلفة هو 5000.',
            'wallet_number.required' => 'رقم المحفظة مطلوب.',
        ]);

        $user = $request->user();

        $dayOfMonth = now()->day;
        if ($dayOfMonth < 15 || $dayOfMonth > 20) {
            return response()->json(['message' => 'موعد السلفة من 15 ل 20 من كل شهر'], 422);
        }

        // السماح فقط للمندوبين والمشرفين بطلب السلفة
        if (!in_array($user->type, ['representative', 'supervisor'])) {
            return response()->json(['message' => 'يسمح فقط للمندوبين والمشرفين بطلب السلفة'], 403);
        }
        if ($user->type === 'representative' && $user->representative) {
            // منع أكثر من سلفة واحدة في نفس الشهر
            $hasAdvanceThisMonth = \App\Models\AdvanceRequest::where('representative_id', $user->representative->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->whereIn('status', ['approved'])
                ->exists();
            if ($hasAdvanceThisMonth) {
                return response()->json(['message' => 'لا يمكن طلب أكثر من سلفة واحدة في نفس الشهر'], 403);
            }
            // $max = 20000 * 0.8;
            // if ($request->amount > $max) {
            //     return response()->json(['message' => 'المبلغ يتجاوز الحد الأقصى (80% من الراتب)'], 403);
            // }
        }
        if ($user->type === 'supervisor' && $user->supervisor) {
            // منع أكثر من سلفة واحدة في نفس الشهر
            $hasAdvanceThisMonth = \App\Models\AdvanceRequest::where('supervisor_id', $user->supervisor->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->whereIn('status', ['pending','approved'])
                ->exists();
            if ($hasAdvanceThisMonth) {
                return response()->json(['message' => 'لا يمكن طلب أكثر من سلفة واحدة في نفس الشهر'], 403);
            }
            // $max = 20000 * 0.8;
            // if ($request->amount > $max) {
            //     return response()->json(['message' => 'المبلغ يتجاوز الحد الأقصى (80% من الراتب)'], 403);
            // }
        }

        // Set default installment months to 1 if not provided
        $installmentMonths = $request->installment_months ?? 1;

        // Calculate monthly installment
        $monthlyInstallment = $request->amount / $installmentMonths;

        $advanceData = [
            'amount' => $request->amount,
            'installment_months' => $installmentMonths,
            'monthly_installment' => $monthlyInstallment,
            'reason' => $request->reason ?? null,
            'status' => 'pending'
        ];

        // ربط الطلب بنوع المستخدم المناسب
        if ($user->type === 'representative' && $user->representative) {
            // Keep representative wallet synced with the wallet entered on advance request
            $user->representative->update([
                'bank_account' => $request->wallet_number,
            ]);
            $advanceData['representative_id'] = $user->representative->id;
        } elseif ($user->type === 'supervisor' && $user->supervisor) {
            $advanceData['supervisor_id'] = $user->supervisor->id;
        } else {
            return response()->json(['message' => 'لم يتم العثور على ملف المستخدم'], 404);
        }

        $advance = AdvanceRequest::create($advanceData);

        // Create notification for admins only (mobile requests)
        try {
            $this->notificationService->notifyAdvanceRequest($advance, 'created');
        } catch (\Exception $e) {
            \Log::error('Failed to create advance request notification: ' . $e->getMessage());
        }

        return response()->json(['message' => 'تم إرسال طلب السلفة بنجاح', 'data' => $advance]);
    }

    public function createResignation(Request $request)
    {
        $request->validate([
            'resignation_date' => 'nullable|date|after_or_equal:today',
            'last_working_day' => 'nullable|date|after_or_equal:resignation_date',
            'reason' => 'nullable|string|max:500'
        ]);

        $user = $request->user();

        // السماح فقط للمندوبين والمشرفين بتقديم الاستقالة
        if (!in_array($user->type, ['representative', 'supervisor'])) {
            return response()->json(['message' => 'يسمح فقط للمندوبين والمشرفين بتقديم الاستقالة'], 403);
        }

        $resignationData = [
            'resignation_date' => $request->resignation_date??now(),
            'last_working_day' => $request->last_working_day??null,
            'reason' => $request->reason??null,
            'status' => 'pending'
        ];

        // ربط الطلب بنوع المستخدم المناسب
        if ($user->type === 'representative' && $user->representative) {
            $resignationData['representative_id'] = $user->representative->id;
        } elseif ($user->type === 'supervisor' && $user->supervisor) {
            $resignationData['supervisor_id'] = $user->supervisor->id;
        } else {
            return response()->json(['message' => 'لم يتم العثور على ملف المستخدم'], 404);
        }

        $resignation = ResignationRequest::create($resignationData);

        // Create notification for admins and supervisors
        try {
            $this->notificationService->notifyResignationRequest($resignation, 'created');
        } catch (\Exception $e) {
            \Log::error('Failed to create resignation request notification: ' . $e->getMessage());
        }

        return response()->json(['message' => 'تم إرسال طلب الاستقالة بنجاح', 'data' => $resignation]);
    }

    public function createDeliveryDeposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500'
        ]);

        $user = $request->user();
        if (!$user || (isset($user->is_active) && !$user->is_active)) {
            return response()->json(['message' => 'الحساب غير نشط'], 403);
        }

        // السماح فقط للمندوبين والمشرفين بإنشاء الإيداعات
        if (!in_array($user->type, ['representative', 'supervisor'])) {
            return response()->json(['message' => 'يسمح فقط للمندوبين والمشرفين بإنشاء الإيداعات'], 403);
        }

        $depositData = [
            'amount' => $request->amount,
            'status' => 'pending',
            'notes' => $request->notes
        ];
        if ($request->filled('orders_count')) {
            $request->validate(['orders_count' => 'nullable|integer|min:0']);
            $depositData['orders_count'] = (int) $request->orders_count;
        }


        // ربط الإيداع بنوع المستخدم المناسب
        if ($user->type === 'representative' && $user->representative) {
            $depositData['representative_id'] = $user->representative->id;
        } elseif ($user->type === 'supervisor' && $user->supervisor) {
            $depositData['supervisor_id'] = $user->supervisor->id;
        } else {
            return response()->json(['message' => 'لم يتم العثور على ملف المستخدم'], 404);
        }

        $deposit = DeliveryDeposit::create($depositData);

        // Create notification for admins and supervisors
        try {
            $this->notificationService->notifyDeliveryDeposit($deposit, 'created');
        } catch (\Exception $e) {
            \Log::error('Failed to create delivery deposit notification: ' . $e->getMessage());
        }

        return response()->json(['message' => 'تم إنشاء الإيداع بنجاح', 'data' => $deposit]);
    }

    public function uploadDepositReceipt(Request $request)
    {
        $request->validate([
            'receipt_image' => 'required|image|mimes:jpeg,png,jpg|max:4096',
            'date' => 'required|string|in:today,yesterday',
            'amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
            'orders_count' => 'nullable|integer|min:0'
        ], [
            'receipt_image.required' => 'صورة الإيصال مطلوبة',
            'receipt_image.image' => 'يجب أن يكون الملف صورة',
            'receipt_image.mimes' => 'يجب أن تكون الصورة بصيغة jpeg, png, أو jpg',
            'receipt_image.max' => 'حجم الصورة يجب أن يكون أقل من 4 ميجابايت',
            'date.required' => 'التاريخ مطلوب',
            'date.in' => 'التاريخ يجب أن يكون "today" أو "yesterday"'
        ]);

        $user = $request->user();

        if (!$user || (isset($user->is_active) && !$user->is_active)) {
            return response()->json(['message' => 'الحساب غير نشط'], 403);
        }

        if (!in_array($user->type, ['representative', 'supervisor'])) {
            return response()->json(['message' => 'يسمح فقط للمندوبين والمشرفين برفع الإيصال'], 403);
        }

        // Debug information for troubleshooting
        if (!$request->hasFile('receipt_image')) {
            return response()->json([
                'message' => 'لم يتم رفع أي ملف',
                'debug' => [
                    'has_file' => $request->hasFile('receipt_image'),
                    'all_files' => $request->allFiles(),
                    'content_type' => $request->header('Content-Type'),
                    'request_data' => $request->all()
                ]
            ], 400);
        }

        $file = $request->file('receipt_image');

        if (!$file->isValid()) {
            return response()->json([
                'message' => 'ملف مرفوع غير صالح',
                'debug' => [
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'file_mime' => $file->getMimeType(),
                    'upload_error' => $file->getError()
                ]
            ], 400);
        }

        // Store new image
        $path = $file->store('delivery-receipts', 'public');

        // Prepare deposit data
        $depositData = [
            'amount'        => $request->amount ?? 0,
            'status'        => 'delivered',
            'notes'         => $request->notes,
            'receipt_image' => $path,
        ];
        if ($request->filled('orders_count')) {
            $depositData['orders_count'] = (int) $request->orders_count;
        }

        // Set delivery date
        if ($request->date === 'today') {
            $depositData['delivered_at'] = now();
        } elseif ($request->date === 'yesterday') {
            $depositData['delivered_at'] = now()->subDay();
        }

        // ربط الإيداع بالمستخدم
        if ($user->type === 'representative' && $user->representative) {
            $depositData['representative_id'] = $user->representative->id;
        } elseif ($user->type === 'supervisor' && $user->supervisor) {
            $depositData['supervisor_id'] = $user->supervisor->id;
        } else {
            return response()->json(['message' => 'لم يتم العثور على ملف المستخدم'], 404);
        }

        // Create new deposit
        $deposit = DeliveryDeposit::create($depositData);

        // Create notification for admins and supervisors
        try {
            $this->notificationService->notifyDeliveryDeposit($deposit, 'created');
        } catch (\Exception $e) {
            \Log::error('Failed to create delivery deposit notification: ' . $e->getMessage());
        }

        return response()->json(['message' => 'تم إنشاء الإيداع مع إيصال بنجاح', 'data' => $deposit]);
    }


    /**
     * بديل رفع الإيصال عبر Base64 لتجاوز مشاكل multipart على بعض السيرفرات
     * الحقول: receipt_base64 (مطلوب)، filename (اختياري)، date (today|yesterday)، amount (اختياري)، notes (اختياري)
     */
    public function uploadDepositReceiptBase64(Request $request)
    {
        $request->validate([
            'receipt_base64' => 'required|string',
            'filename' => 'nullable|string|max:255',
            'date' => 'required|string|in:today,yesterday',
            'amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
            'orders_count' => 'nullable|integer|min:0'
        ]);

        $user = $request->user();
        if (!in_array($user->type, ['representative', 'supervisor'])) {
            return response()->json(['message' => 'يسمح فقط للمندوبين والمشرفين برفع الإيصال'], 403);
        }
        if (!$user || (isset($user->is_active) && !$user->is_active)) {
            return response()->json(['message' => 'الحساب غير نشط'], 403);
        }

        $base64 = $request->receipt_base64;
        if (preg_match('/^data:(.*?);base64,/', $base64, $m)) {
            $base64 = substr($base64, strpos($base64, ',') + 1);
        }

        $decoded = base64_decode($base64, true);
        if ($decoded === false) {
            return response()->json(['message' => 'بيانات الصورة غير صالحة'], 400);
        }

        // حد أعلى تقريبي 8MB
        if (strlen($decoded) > 8 * 1024 * 1024) {
            return response()->json(['message' => 'حجم الصورة كبير جداً (أقصى حد 8MB)'], 413);
        }

        // تحديد الامتداد من اسم الملف إن وجد، وإلا استخدم png
        $filename = $request->filename ?: ('receipt_' . now()->timestamp . '.png');
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION) ?: 'png');
        if (!in_array($extension, ['jpg','jpeg','png','gif','pdf'])) {
            $extension = 'png';
            $filename .= '.png';
        }

        $path = 'delivery-receipts/' . uniqid('rcpt_') . '.' . $extension;
        \Storage::disk('public')->put($path, $decoded);

        $depositData = [
            'amount'        => $request->amount ?? 0,
            'status'        => 'delivered',
            'notes'         => $request->notes,
            'receipt_image' => $path,
        ];
        if ($request->filled('orders_count')) {
            $depositData['orders_count'] = (int) $request->orders_count;
        }

        if ($request->date === 'today') {
            $depositData['delivered_at'] = now();
        } elseif ($request->date === 'yesterday') {
            $depositData['delivered_at'] = now()->subDay();
        }

        if ($user->type === 'representative' && $user->representative) {
            $depositData['representative_id'] = $user->representative->id;
        } elseif ($user->type === 'supervisor' && $user->supervisor) {
            $depositData['supervisor_id'] = $user->supervisor->id;
        } else {
            return response()->json(['message' => 'لم يتم العثور على ملف المستخدم'], 404);
        }

        $deposit = \App\Models\DeliveryDeposit::create($depositData);

        // Create notification for admins and supervisors
        try {
            $this->notificationService->notifyDeliveryDeposit($deposit, 'created');
        } catch (\Exception $e) {
            \Log::error('Failed to create delivery deposit notification: ' . $e->getMessage());
        }

        return response()->json(['message' => 'تم إنشاء الإيداع مع إيصال بنجاح (Base64)', 'data' => $deposit]);
    }

    // Get all leave requests for the authenticated user
    public function getAllLeaveRequests(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->type, ['representative', 'supervisor'])) {
            return response()->json(['message' => 'يسمح فقط للمندوبين والمشرفين بعرض طلبات الإجازة'], 403);
        }

        $leaveRequests = LeaveRequest::where(function($query) use ($user) {
            if ($user->type === 'representative' && $user->representative) {
                $query->where('representative_id', $user->representative->id);
            } elseif ($user->type === 'supervisor' && $user->supervisor) {
                $query->where('supervisor_id', $user->supervisor->id);
            }
        })
        ->select(['id', 'start_date', 'end_date', 'type', 'reason', 'status', 'rejection_reason', 'created_at', 'updated_at'])
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json(['data' => $leaveRequests]);
    }

    // Get all advance requests for the authenticated user
    public function getAllAdvanceRequests(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->type, ['representative', 'supervisor'])) {
            return response()->json(['message' => 'يسمح فقط للمندوبين والمشرفين بعرض طلبات السلف'], 403);
        }

        $advanceRequests = AdvanceRequest::where(function($query) use ($user) {
            if ($user->type === 'representative' && $user->representative) {
                $query->where('representative_id', $user->representative->id);
            } elseif ($user->type === 'supervisor' && $user->supervisor) {
                $query->where('supervisor_id', $user->supervisor->id);
            }
        })
        ->select(['id', 'amount', 'wallet_number', 'installment_months', 'monthly_installment', 'reason', 'status', 'rejection_reason', 'created_at', 'updated_at'])
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json(['data' => $advanceRequests]);
    }

    // Get all resignation requests for the authenticated user
    public function getAllResignationRequests(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->type, ['representative', 'supervisor'])) {
            return response()->json(['message' => 'يسمح فقط للمندوبين والمشرفين بعرض طلبات الاستقالة'], 403);
        }

        $resignationRequests = ResignationRequest::where(function($query) use ($user) {
            if ($user->type === 'representative' && $user->representative) {
                $query->where('representative_id', $user->representative->id);
            } elseif ($user->type === 'supervisor' && $user->supervisor) {
                $query->where('supervisor_id', $user->supervisor->id);
            }
        })
        ->select(['id', 'resignation_date', 'last_working_day', 'reason', 'status', 'rejection_reason', 'created_at', 'updated_at'])
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json(['data' => $resignationRequests]);
    }

    // Get all delivery deposits for the authenticated user
    public function getAllDeliveryDeposits(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->type, ['representative', 'supervisor'])) {
            return response()->json(['message' => 'يسمح فقط للمندوبين والمشرفين بعرض إيداعات التسليم'], 403);
        }

        $deliveryDeposits = DeliveryDeposit::where(function($query) use ($user) {
            if ($user->type === 'representative' && $user->representative) {
                $query->where('representative_id', $user->representative->id);
            } elseif ($user->type === 'supervisor' && $user->supervisor) {
                $query->where('supervisor_id', $user->supervisor->id);
            }
        })
        ->select(['id', 'amount', 'notes', 'status', 'rejection_reason', 'receipt_image', 'delivered_at', 'created_at', 'updated_at'])
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json(['data' => $deliveryDeposits]);
    }

    // Get all requests summary for the authenticated user
    public function getAllRequestsSummary(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->type, ['representative', 'supervisor'])) {
            return response()->json(['message' => 'يسمح فقط للمندوبين والمشرفين بعرض ملخص الطلبات'], 403);
        }

        $whereClause = function($query) use ($user) {
            if ($user->type === 'representative' && $user->representative) {
                $query->where('representative_id', $user->representative->id);
            } elseif ($user->type === 'supervisor' && $user->supervisor) {
                $query->where('supervisor_id', $user->supervisor->id);
            }
        };

        $summary = [
            'leave_requests' => [
                'total' => LeaveRequest::where($whereClause)->count(),
                'pending' => LeaveRequest::where($whereClause)->where('status', 'pending')->count(),
                'approved' => LeaveRequest::where($whereClause)->where('status', 'approved')->count(),
                'rejected' => LeaveRequest::where($whereClause)->where('status', 'rejected')->count(),
            ],
            'advance_requests' => [
                'total' => AdvanceRequest::where($whereClause)->count(),
                'pending' => AdvanceRequest::where($whereClause)->where('status', 'pending')->count(),
                'approved' => AdvanceRequest::where($whereClause)->where('status', 'approved')->count(),
                'rejected' => AdvanceRequest::where($whereClause)->where('status', 'rejected')->count(),
            ],
            'resignation_requests' => [
                'total' => ResignationRequest::where($whereClause)->count(),
                'pending' => ResignationRequest::where($whereClause)->where('status', 'pending')->count(),
                'approved' => ResignationRequest::where($whereClause)->where('status', 'approved')->count(),
                'rejected' => ResignationRequest::where($whereClause)->where('status', 'rejected')->count(),
            ],
            'delivery_deposits' => [
                'total' => DeliveryDeposit::where($whereClause)->count(),
                'pending' => DeliveryDeposit::where($whereClause)->where('status', 'pending')->count(),
                'delivered' => DeliveryDeposit::where($whereClause)->where('status', 'delivered')->count(),
            ]
        ];

        return response()->json(['data' => $summary]);
    }

    // Get the status of the last request of each type
    public function getLastRequestsStatus(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->type, ['representative', 'supervisor'])) {
            return response()->json(['message' => 'يسمح فقط للمندوبين والمشرفين بعرض حالة آخر الطلبات'], 403);
        }

        $whereClause = function($query) use ($user) {
            if ($user->type === 'representative' && $user->representative) {
                $query->where('representative_id', $user->representative->id);
            } elseif ($user->type === 'supervisor' && $user->supervisor) {
                $query->where('supervisor_id', $user->supervisor->id);
            }
        };

        $lastRequests = [
            'last_leave_request' => LeaveRequest::where($whereClause)
                ->orderBy('created_at', 'desc')
                ->first(['id', 'type', 'start_date', 'reason', 'rejection_reason', 'end_date', 'status', 'created_at']),

            'last_advance_request' => AdvanceRequest::where($whereClause)
                ->orderBy('created_at', 'desc')
                ->first(['id', 'amount', 'wallet_number', 'installment_months', 'monthly_installment', 'reason', 'rejection_reason', 'status', 'created_at']),

            'last_resignation_request' => ResignationRequest::where($whereClause)
                ->orderBy('created_at', 'desc')
                ->first(['id', 'resignation_date', 'last_working_day', 'reason', 'rejection_reason', 'status', 'created_at']),

            'last_delivery_deposit' => DeliveryDeposit::where($whereClause)
                ->orderBy('created_at', 'desc')
                ->first(['id', 'amount', 'status', 'delivered_at', 'created_at'])
        ];

        // Add status summary for each type
        $statusSummary = [
            'leave_requests' => [
                'last_status' => $lastRequests['last_leave_request'] ? $lastRequests['last_leave_request']->status : null,
                'last_request_date' => $lastRequests['last_leave_request'] ? $lastRequests['last_leave_request']->created_at : null,
                'last_request_type' => $lastRequests['last_leave_request'] ? $lastRequests['last_leave_request']->type : null
            ],
            'advance_requests' => [
                'last_status' => $lastRequests['last_advance_request'] ? $lastRequests['last_advance_request']->status : null,
                'last_request_date' => $lastRequests['last_advance_request'] ? $lastRequests['last_advance_request']->created_at : null,
                'last_amount' => $lastRequests['last_advance_request'] ? $lastRequests['last_advance_request']->amount : null
            ],
            'resignation_requests' => [
                'last_status' => $lastRequests['last_resignation_request'] ? $lastRequests['last_resignation_request']->status : null,
                'last_request_date' => $lastRequests['last_resignation_request'] ? $lastRequests['last_resignation_request']->created_at : null,
                'resignation_date' => $lastRequests['last_resignation_request'] ? $lastRequests['last_resignation_request']->resignation_date : null
            ],
            'delivery_deposits' => [
                'last_status' => $lastRequests['last_delivery_deposit'] ? $lastRequests['last_delivery_deposit']->status : null,
                'last_request_date' => $lastRequests['last_delivery_deposit'] ? $lastRequests['last_delivery_deposit']->created_at : null,
                'last_amount' => $lastRequests['last_delivery_deposit'] ? $lastRequests['last_delivery_deposit']->amount : null,
                'delivered_at' => $lastRequests['last_delivery_deposit'] ? $lastRequests['last_delivery_deposit']->delivered_at : null
            ]
        ];

        return response()->json([
            'data' => [
                'last_requests' => $lastRequests,
                'status_summary' => $statusSummary
            ]
        ]);
    }

    /**
     * نقطة واحدة لإرجاع جميع الحالات لجميع الطلبات مع سبب الرفض (إن وجد)
     */
    public function getAllStatusesWithReasons(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->type, ['representative', 'supervisor'])) {
            return response()->json(['message' => 'يسمح فقط للمندوبين والمشرفين بعرض البيانات'], 403);
        }

        $filterByUser = function ($query) use ($user) {
            if ($user->type === 'representative' && $user->representative) {
                $query->where('representative_id', $user->representative->id);
            } elseif ($user->type === 'supervisor' && $user->supervisor) {
                $query->where('supervisor_id', $user->supervisor->id);
            }
        };
        $date = now()->toDateString();
        $can_advance = false;
        $allowed_dates = ['2025-09-14','2025-09-15','2025-09-16','2025-09-17','2025-09-18','2025-09-19','2025-09-20'];
        if(in_array($date, $allowed_dates)){
            $can_advance = true;
        }

        $leaveRequest = \App\Models\LeaveRequest::where($filterByUser)
            ->latest('created_at')
            ->first(['id', 'type', 'start_date', 'end_date', 'status', 'rejection_reason', 'created_at']);

        $advanceRequest = \App\Models\AdvanceRequest::where($filterByUser)
            ->latest('created_at')
            ->first(['id', 'amount', 'wallet_number', 'installment_months', 'monthly_installment', 'status', 'rejection_reason', 'created_at']);

        $resignationRequest = \App\Models\ResignationRequest::where($filterByUser)
            ->latest('created_at')
            ->first(['id', 'resignation_date', 'last_working_day', 'status', 'rejection_reason', 'created_at']);

        $deliveryDeposit = \App\Models\DeliveryDeposit::where($filterByUser)
            ->latest('created_at')
            ->first(['id', 'amount', 'status', 'delivered_at', 'created_at']);

        // Today / Yesterday deposit statuses
        $todayDeposit = \App\Models\DeliveryDeposit::where($filterByUser)
            ->whereDate('delivered_at', now()->toDateString())
            ->latest('delivered_at')
            ->first(['id', 'status', 'delivered_at']);

        $yesterdayDeposit = \App\Models\DeliveryDeposit::where($filterByUser)
            ->whereDate('delivered_at', now()->subDay()->toDateString())
            ->latest('delivered_at')
            ->first(['id', 'status', 'delivered_at']);

        // Sliders: first 3 images by sort_order then id (use controller route, no symlink required)
        $sliders = Slider::orderBy('sort_order')
            ->orderBy('id', 'desc')
            ->limit(3)
            ->get(['id', 'image_path', 'sort_order'])
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'image_url' => route('sliders.image', $s),
                    'sort_order' => $s->sort_order,
                ];
            });


        // Training status for representatives
        $trainingData = null;
        if ($user->type === 'representative' && $user->representative) {
            $training = \App\Models\RepresentativeTraining::firstOrCreate(
                ['representative_id' => $user->representative->id],
                ['is_completed' => false]
            );
            $trainingData = [
                'is_completed' => (bool) $training->is_completed,
                'completed_at' => $training->completed_at,
            ];
        }

        return response()->json([
            'data' => [
                'can_advance' => $can_advance,
                'last_leave_request' => $leaveRequest,
                'last_advance_request' => $advanceRequest,
                'last_resignation_request' => $resignationRequest,
                'last_delivery_deposit' => $deliveryDeposit,
                'today_delivery_deposit_status' => $todayDeposit ? $todayDeposit->status : null,
                'yesterday_delivery_deposit_status' => $yesterdayDeposit ? $yesterdayDeposit->status : null,
                'sliders' => $sliders,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name?? $user->employee->name ?? $user->representative->name ?? $user->supervisor->name??null,
                    'phone' => $user->phone,
                    'type' => $user->type,
                ],
                'training' => $trainingData,
            ]
        ]);
    }


}