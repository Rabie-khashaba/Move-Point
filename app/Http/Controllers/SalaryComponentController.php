<?php

namespace App\Http\Controllers;

use App\Models\SalaryRecord;
use App\Models\Employee;
use App\Models\Representative;
use App\Models\Supervisor;
use App\Models\AdvanceRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SalaryComponentController extends Controller
{
    public function index()
    {
        $this->authorize('view_salary_records');
        
        $currentYear = request('year', now()->year);
        $currentMonth = request('month', now()->month);
        $userType = request('user_type', '');
        $userId = request('user_id', '');
        
        // Get salary records with filters
        $query = SalaryRecord::with(['employee.department', 'representative', 'supervisor'])
            ->where('year', $currentYear)
            ->where('month', $currentMonth);
            
        if ($userType) {
            $query->where('user_type', $userType);
        }
        
        if ($userId) {
            switch ($userType) {
                case 'employee':
                    $query->where('employee_id', $userId);
                    break;
                case 'representative':
                    $query->where('representative_id', $userId);
                    break;
                case 'supervisor':
                    $query->where('supervisor_id', $userId);
                    break;
            }
        }
        
        $salaryRecords = $query->get();
        
        // Update advances for all salary records from advance_requests table
        foreach ($salaryRecords as $record) {
            $userId = $record->employee_id ?? $record->representative_id ?? $record->supervisor_id;
            if ($userId) {
                $record->advances = $this->getAdvancesFromTable($record->user_type, $userId, $record->year, $record->month);
            }
        }
        
        // Get all users for dropdowns
        $employees = Employee::active()->with('department')->get();
        $representatives = Representative::where('is_active', true)->get();
        $supervisors = Supervisor::where('is_active', true)->get();
        
        // Calculate totals with updated advances
        $totals = [
            'advances' => $salaryRecords->sum('advances'),
            'deductions' => $salaryRecords->sum('deductions'),
            'lost_orders_penalty' => $salaryRecords->sum('lost_orders_penalty'),
            'delivery_penalty' => $salaryRecords->sum('delivery_penalty'),
            'commissions' => $salaryRecords->sum('commissions'),
            'cashback' => $salaryRecords->sum('cashback'),
            'total_penalties' => $salaryRecords->sum('lost_orders_penalty') + $salaryRecords->sum('delivery_penalty'),
            'total_deductions' => $salaryRecords->sum('advances') + $salaryRecords->sum('deductions'),
            'total_bonuses' => $salaryRecords->sum('commissions') + $salaryRecords->sum('cashback'),
        ];
        
        // Get detailed advances information for display
        $advancesSummary = $this->getAdvancesSummary($currentYear, $currentMonth, $userType);
        
        return view('salary-components.index', compact(
            'salaryRecords', 
            'employees', 
            'representatives', 
            'supervisors', 
            'currentYear', 
            'currentMonth', 
            'userType', 
            'userId',
            'totals',
            'advancesSummary'
        ));
    }

    public function create()
    {
        $this->authorize('create_salary_records');
        
        $currentYear = request('year', now()->year);
        $currentMonth = request('month', now()->month);
        $userType = request('user_type', '');
        $userId = request('user_id', '');
        
        // Get all users for dropdowns
        $employees = Employee::active()->with('department')->get();
        $representatives = Representative::where('is_active', true)->get();
        $supervisors = Supervisor::where('is_active', true)->get();
        
        return view('salary-components.create', compact(
            'currentYear', 
            'currentMonth', 
            'userType', 
            'userId', 
            'employees',
            'representatives',
            'supervisors'
        ));
    }

    public function store(Request $request)
    {
        $this->authorize('create_salary_records');
        
        $validated = $request->validate([
            'user_type' => 'required|in:employee,representative,supervisor',
            'user_id' => 'required|integer',
            'year' => 'required|integer|min:2020|max:2030',
            'month' => 'required|integer|min:1|max:12',
            'deductions' => 'required|numeric|min:0',
            'lost_orders_penalty' => 'required|numeric|min:0',
            'delivery_penalty' => 'required|numeric|min:0',
            'commissions' => 'required|numeric|min:0',
            'cashback' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500'
        ]);
        
        // Check if salary record exists
        $salaryRecord = SalaryRecord::where('year', $validated['year'])
            ->where('month', $validated['month'])
            ->where('user_type', $validated['user_type']);
            
        switch ($validated['user_type']) {
            case 'employee':
                $salaryRecord->where('employee_id', $validated['user_id']);
                $user = Employee::find($validated['user_id']);
                $baseSalary = $user->salary;
                break;
            case 'representative':
                $salaryRecord->where('representative_id', $validated['user_id']);
                $user = Representative::find($validated['user_id']);
                $baseSalary = $user->salary;
                break;
            case 'supervisor':
                $salaryRecord->where('supervisor_id', $validated['user_id']);
                $user = Supervisor::find($validated['user_id']);
                $baseSalary = $user->salary;
                break;
        }
        
        $salaryRecord = $salaryRecord->first();
        
        if (!$salaryRecord) {
            // Create new salary record
            $salaryRecord = new SalaryRecord();
            $salaryRecord->year = $validated['year'];
            $salaryRecord->month = $validated['month'];
            $salaryRecord->user_type = $validated['user_type'];
            $salaryRecord->base_salary = $baseSalary;
            
            switch ($validated['user_type']) {
                case 'employee':
                    $salaryRecord->employee_id = $validated['user_id'];
                    break;
                case 'representative':
                    $salaryRecord->representative_id = $validated['user_id'];
                    $salaryRecord->user_code = $user->code;
                    break;
                case 'supervisor':
                    $salaryRecord->supervisor_id = $validated['user_id'];
                    break;
            }
        }
        
        // Get advances from advance_requests table
        $advances = $this->getAdvancesFromTable($validated['user_type'], $validated['user_id'], $validated['year'], $validated['month']);
        
        // Update components
        $salaryRecord->advances = $advances;
        $salaryRecord->deductions = $validated['deductions'];
        $salaryRecord->lost_orders_penalty = $validated['lost_orders_penalty'];
        $salaryRecord->delivery_penalty = $validated['delivery_penalty'];
        $salaryRecord->commissions = $validated['commissions'];
        $salaryRecord->cashback = $validated['cashback'];
        $salaryRecord->notes = $validated['notes'];
        
        // Calculate net salary
        $totalDeductions = $advances + $validated['deductions'] + 
                          $validated['lost_orders_penalty'] + $validated['delivery_penalty'];
        $totalAdditions = $validated['commissions'] + $validated['cashback'];
        $salaryRecord->net_salary = max(0, $baseSalary - $totalDeductions + $totalAdditions);
        
        $salaryRecord->save();
        
        return redirect()->route('salary-components.index', [
            'year' => $validated['year'],
            'month' => $validated['month'],
            'user_type' => $validated['user_type']
        ])->with('success', 'تم إضافة مكونات المرتب بنجاح!');
    }

    public function edit($id)
    {
        $this->authorize('edit_salary_records');
        
        $salaryRecord = SalaryRecord::with(['employee.department', 'representative', 'supervisor'])->findOrFail($id);
        
        // Get detailed advances information
        $advancesInfo = $this->getDetailedAdvancesInfo(
            $salaryRecord->user_type,
            $salaryRecord->employee_id ?? $salaryRecord->representative_id ?? $salaryRecord->supervisor_id,
            $salaryRecord->year,
            $salaryRecord->month
        );
        
        return view('salary-components.edit', compact('salaryRecord', 'advancesInfo'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit_salary_records');
        
        $salaryRecord = SalaryRecord::findOrFail($id);
        
        $validated = $request->validate([
            'deductions' => 'required|numeric|min:0',
            'lost_orders_penalty' => 'required|numeric|min:0',
            'delivery_penalty' => 'required|numeric|min:0',
            'commissions' => 'required|numeric|min:0',
            'cashback' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500'
        ]);
        
        // Get advances from advance_requests table
        $advances = $this->getAdvancesFromTable($salaryRecord->user_type, 
            $salaryRecord->employee_id ?? $salaryRecord->representative_id ?? $salaryRecord->supervisor_id, 
            $salaryRecord->year, $salaryRecord->month);
        
        // Update components
        $salaryRecord->advances = $advances;
        $salaryRecord->deductions = $validated['deductions'];
        $salaryRecord->lost_orders_penalty = $validated['lost_orders_penalty'];
        $salaryRecord->delivery_penalty = $validated['delivery_penalty'];
        $salaryRecord->commissions = $validated['commissions'];
        $salaryRecord->cashback = $validated['cashback'];
        $salaryRecord->notes = $validated['notes'];
        
        // Calculate net salary
        $totalDeductions = $advances + $validated['deductions'] + 
                          $validated['lost_orders_penalty'] + $validated['delivery_penalty'];
        $totalAdditions = $validated['commissions'] + $validated['cashback'];
        $salaryRecord->net_salary = max(0, $salaryRecord->base_salary - $totalDeductions + $totalAdditions);
        
        $salaryRecord->save();
        
        return redirect()->route('salary-components.index', [
            'year' => $salaryRecord->year,
            'month' => $salaryRecord->month,
            'user_type' => $salaryRecord->user_type
        ])->with('success', 'تم تحديث مكونات المرتب بنجاح!');
    }

    public function bulkUpdate(Request $request)
    {
        $this->authorize('edit_salary_records');
        
        $validated = $request->validate([
            'salaries' => 'required|array',
            'salaries.*.id' => 'required|exists:salary_records,id',
            'salaries.*.deductions' => 'required|numeric|min:0',
            'salaries.*.lost_orders_penalty' => 'required|numeric|min:0',
            'salaries.*.delivery_penalty' => 'required|numeric|min:0',
            'salaries.*.commissions' => 'required|numeric|min:0',
            'salaries.*.cashback' => 'required|numeric|min:0'
        ]);
        
        foreach ($validated['salaries'] as $salaryData) {
            $salary = SalaryRecord::find($salaryData['id']);
            if ($salary) {
                // Get advances from advance_requests table
                $advances = $this->getAdvancesFromTable($salary->user_type, 
                    $salary->employee_id ?? $salary->representative_id ?? $salary->supervisor_id, 
                    $salary->year, $salary->month);
                
                // Update components
                $salary->advances = $advances;
                $salary->deductions = $salaryData['deductions'];
                $salary->lost_orders_penalty = $salaryData['lost_orders_penalty'];
                $salary->delivery_penalty = $salaryData['delivery_penalty'];
                $salary->commissions = $salaryData['commissions'];
                $salary->cashback = $salaryData['cashback'];
                
                // Calculate net salary
                $totalDeductions = $advances + $salaryData['deductions'] + 
                                  $salaryData['lost_orders_penalty'] + $salaryData['delivery_penalty'];
                $totalAdditions = $salaryData['commissions'] + $salaryData['cashback'];
                $salary->net_salary = max(0, $salary->base_salary - $totalDeductions + $totalAdditions);
                
                $salary->save();
            }
        }
        
        return back()->with('success', 'تم تحديث جميع مكونات المرتبات بنجاح!');
    }

    public function export(Request $request)
    {
        $this->authorize('view_salary_records');
        
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        $userType = $request->get('user_type', '');
        
        $query = SalaryRecord::with(['employee.department', 'representative', 'supervisor'])
            ->where('year', $year)
            ->where('month', $month);
            
        if ($userType) {
            $query->where('user_type', $userType);
        }
        
        $salaryRecords = $query->get();
        
        // Create filename
        $filename = "salary_components_{$year}_{$month}";
        if ($userType) {
            $filename .= "_" . $userType;
        }
        $filename .= ".csv";
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function() use ($salaryRecords) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Arabic text
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, [
                'م', 'اسم المستخدم', 'النوع', 'المرتب الأساسي', 'السلف', 'الخصومات',
                'غرامة الأوردر الضائع', 'غرامة إيصال التسليم', 'العمولات', 'كاش باك', 'صافي المرتب'
            ]);
            
            // Data rows
            foreach ($salaryRecords as $index => $record) {
                fputcsv($file, [
                    $index + 1,
                    $record->user_name,
                    $record->user_type === 'employee' ? 'موظف' : ($record->user_type === 'representative' ? 'مندوب' : 'مشرف'),
                    $record->base_salary,
                    $record->advances,
                    $record->deductions,
                    $record->lost_orders_penalty,
                    $record->delivery_penalty,
                    $record->commissions,
                    $record->cashback,
                    $record->net_salary
                ]);
            }
            
            // Add totals row
            fputcsv($file, [
                'الإجمالي', '', '', 
                $salaryRecords->sum('base_salary'),
                $salaryRecords->sum('advances'),
                $salaryRecords->sum('deductions'),
                $salaryRecords->sum('lost_orders_penalty'),
                $salaryRecords->sum('delivery_penalty'),
                $salaryRecords->sum('commissions'),
                $salaryRecords->sum('cashback'),
                $salaryRecords->sum('net_salary')
            ]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    
        /**
     * Get advances from advance_requests table for a specific user and month
     * Handles both one-time advances and monthly installments
     */
    private function getAdvancesFromTable($userType, $userId, $year, $month)
    {
        $totalAdvances = 0;
        
        // Get all approved advance requests for this user
        $query = AdvanceRequest::where('status', 'approved');
        
        switch ($userType) {
            case 'employee':
                $query->where('employee_id', $userId);
                break;
            case 'representative':
                $query->where('representative_id', $userId);
                break;
            case 'supervisor':
                $query->where('supervisor_id', $userId);
                break;
        }
        
        $advanceRequests = $query->get();
        
        foreach ($advanceRequests as $advance) {
            if ($advance->installment_months && $advance->installment_months > 1) {
                // This is an installment advance
                $advanceDate = $advance->created_at;
                $advanceYear = $advanceDate->year;
                $advanceMonth = $advanceDate->month;
                
                // Calculate how many months have passed since the advance was created
                $monthsPassed = (($year - $advanceYear) * 12) + ($month - $advanceMonth);
                
                // If we're within the installment period, add the monthly installment
                if ($monthsPassed >= 0 && $monthsPassed < $advance->installment_months) {
                    $totalAdvances += $advance->monthly_installment;
                    
                    // Log for debugging
                    \Log::info("Installment advance added", [
                        'advance_id' => $advance->id,
                        'total_amount' => $advance->amount,
                        'monthly_installment' => $advance->monthly_installment,
                        'installment_months' => $advance->installment_months,
                        'months_passed' => $monthsPassed,
                        'current_month' => $month,
                        'current_year' => $year,
                        'advance_month' => $advanceMonth,
                        'advance_year' => $advanceYear
                    ]);
                }
            } else {
                // This is a one-time advance - only add if it's in the same month
                if ($advance->created_at->year == $year && $advance->created_at->month == $month) {
                    $totalAdvances += $advance->amount;
                    
                    // Log for debugging
                    \Log::info("One-time advance added", [
                        'advance_id' => $advance->id,
                        'amount' => $advance->amount,
                        'current_month' => $month,
                        'current_year' => $year
                    ]);
                }
            }
        }
        
        // Log total advances for this month
        \Log::info("Total advances calculated", [
            'user_type' => $userType,
            'user_id' => $userId,
            'year' => $year,
            'month' => $month,
            'total_advances' => $totalAdvances
        ]);
        
        return $totalAdvances;
    }
    
    /**
     * Get detailed advances information for display purposes
     */
    private function getDetailedAdvancesInfo($userType, $userId, $year, $month)
    {
        $advancesInfo = [
            'total' => 0,
            'one_time' => 0,
            'installments' => 0,
            'details' => []
        ];
        
        // Get all approved advance requests for this user
        $query = AdvanceRequest::where('status', 'approved');
        
        switch ($userType) {
            case 'employee':
                $query->where('employee_id', $userId);
                break;
            case 'representative':
                $query->where('representative_id', $userId);
                break;
            case 'supervisor':
                $query->where('supervisor_id', $userId);
                break;
        }
        
        $advanceRequests = $query->get();
        
        foreach ($advanceRequests as $advance) {
            if ($advance->installment_months && $advance->installment_months > 1) {
                // This is an installment advance
                $advanceDate = $advance->created_at;
                $advanceYear = $advanceDate->year;
                $advanceMonth = $advanceDate->month;
                
                // Calculate how many months have passed since the advance was created
                $monthsPassed = (($year - $advanceYear) * 12) + ($month - $advanceMonth);
                
                // If we're within the installment period, add the monthly installment
                if ($monthsPassed >= 0 && $monthsPassed < $advance->installment_months) {
                    $advancesInfo['total'] += $advance->monthly_installment;
                    $advancesInfo['installments'] += $advance->monthly_installment;
                    
                    $advancesInfo['details'][] = [
                        'type' => 'installment',
                        'amount' => $advance->monthly_installment,
                        'total_amount' => $advance->amount,
                        'installment_number' => $monthsPassed + 1,
                        'total_installments' => $advance->installment_months,
                        'reason' => $advance->reason,
                        'created_at' => $advance->created_at->format('Y-m-d'),
                        'remaining_installments' => $advance->installment_months - ($monthsPassed + 1)
                    ];
                }
            } else {
                // This is a one-time advance - only add if it's in the same month
                if ($advance->created_at->year == $year && $advance->created_at->month == $month) {
                    $advancesInfo['total'] += $advance->amount;
                    $advancesInfo['one_time'] += $advance->amount;
                    
                    $advancesInfo['details'][] = [
                        'type' => 'one_time',
                        'amount' => $advance->amount,
                        'reason' => $advance->reason,
                        'created_at' => $advance->created_at->format('Y-m-d')
                    ];
                }
            }
        }
        
        return $advancesInfo;
    }
    
    /**
     * Get summary of advances for all users in a specific month
     */
    private function getAdvancesSummary($year, $month, $userType = '')
    {
        $summary = [
            'total_advances' => 0,
            'total_installments' => 0,
            'total_one_time' => 0,
            'advances_by_type' => [
                'employee' => 0,
                'representative' => 0,
                'supervisor' => 0
            ],
            'installment_details' => [],
            'one_time_details' => []
        ];
        
        // Get all approved advance requests for this month
        $query = AdvanceRequest::where('status', 'approved');
        
        if ($userType) {
            // Filter by specific user type
            switch ($userType) {
                case 'employee':
                    $query->whereNotNull('employee_id');
                    break;
                case 'representative':
                    $query->whereNotNull('representative_id');
                    break;
                case 'supervisor':
                    $query->whereNotNull('supervisor_id');
                    break;
            }
        }
        
        $advanceRequests = $query->get();
        
        foreach ($advanceRequests as $advance) {
            if ($advance->installment_months && $advance->installment_months > 1) {
                // This is an installment advance
                $advanceDate = $advance->created_at;
                $advanceYear = $advanceDate->year;
                $advanceMonth = $advanceDate->month;
                
                // Calculate how many months have passed since the advance was created
                $monthsPassed = (($year - $advanceYear) * 12) + ($month - $advanceMonth);
                
                // If we're within the installment period, add the monthly installment
                if ($monthsPassed >= 0 && $monthsPassed < $advance->installment_months) {
                    $summary['total_advances'] += $advance->monthly_installment;
                    $summary['total_installments'] += $advance->monthly_installment;
                    
                    // Determine user type for this advance
                    $userTypeForAdvance = '';
                    if ($advance->employee_id) $userTypeForAdvance = 'employee';
                    elseif ($advance->representative_id) $userTypeForAdvance = 'representative';
                    elseif ($advance->supervisor_id) $userTypeForAdvance = 'supervisor';
                    
                    if ($userTypeForAdvance) {
                        $summary['advances_by_type'][$userTypeForAdvance] += $advance->monthly_installment;
                    }
                    
                    $summary['installment_details'][] = [
                        'amount' => $advance->monthly_installment,
                        'total_amount' => $advance->amount,
                        'installment_number' => $monthsPassed + 1,
                        'total_installments' => $advance->installment_months,
                        'reason' => $advance->reason,
                        'user_type' => $userTypeForAdvance
                    ];
                }
            } else {
                // This is a one-time advance - only add if it's in the same month
                if ($advance->created_at->year == $year && $advance->created_at->month == $month) {
                    $summary['total_advances'] += $advance->amount;
                    $summary['total_one_time'] += $advance->amount;
                    
                    // Determine user type for this advance
                    $userTypeForAdvance = '';
                    if ($advance->employee_id) $userTypeForAdvance = 'employee';
                    elseif ($advance->representative_id) $userTypeForAdvance = 'representative';
                    elseif ($advance->supervisor_id) $userTypeForAdvance = 'supervisor';
                    
                    if ($userTypeForAdvance) {
                        $summary['advances_by_type'][$userTypeForAdvance] += $advance->amount;
                    }
                    
                    $summary['one_time_details'][] = [
                        'amount' => $advance->amount,
                        'reason' => $advance->reason,
                        'user_type' => $userTypeForAdvance
                    ];
                }
            }
        }
        
        return $summary;
    }
}
