<?php

namespace App\Http\Controllers;

use App\Models\SalaryRecord;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SalaryRecordController extends Controller
{
    public function index()
    {
        $this->authorize('view_salary_records');
        
        $currentYear = request('year', now()->year);
        $currentMonth = request('month', now()->month);
        
        $salaries = SalaryRecord::with(['employee.department', 'representative', 'supervisor'])
            ->where('year', $currentYear)
            ->where('month', $currentMonth)
            ->when(request('search'), function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->whereHas('employee', function($subQ) use ($search) {
                        $subQ->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('representative', function($subQ) use ($search) {
                        $subQ->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('supervisor', function($subQ) use ($search) {
                        $subQ->where('name', 'like', "%{$search}%");
                    });
                });
            })
            ->when(request('user_type'), function($query, $userType) {
                $query->where('user_type', $userType);
            })
            ->when(request('payment_status'), function($query, $status) {
                if ($status === 'paid') {
                    $query->where('is_paid', true);
                } elseif ($status === 'unpaid') {
                    $query->where('is_paid', false);
                }
            })
            ->get();

        // Get all users to show those without salary records
        $allEmployees = Employee::active()
            ->when(request('search'), function($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->with('department')
            ->get();

        $allRepresentatives = \App\Models\Representative::where('is_active', true)
            ->when(request('search'), function($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->get();

        $allSupervisors = \App\Models\Supervisor::where('is_active', true)
            ->when(request('search'), function($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->get();

        // Create salary records for employees who don't have one for this month
        foreach ($allEmployees as $employee) {
            $existingSalary = $salaries->where('employee_id', $employee->id)->where('user_type', 'employee')->first();
            if (!$existingSalary) {
                $salaries->push(new SalaryRecord([
                    'employee_id' => $employee->id,
                    'user_type' => 'employee',
                    'year' => $currentYear,
                    'month' => $currentMonth,
                    'base_salary' => $employee->salary,
                    'advances' => 0,
                    'deductions' => 0,
                    'lost_orders_penalty' => 0,
                    'delivery_penalty' => 0,
                    'commissions' => 0,
                    'cashback' => 0,
                    'net_salary' => $employee->salary
                ]));
            }
        }

        // Create salary records for representatives who don't have one for this month
        foreach ($allRepresentatives as $representative) {
            $existingSalary = $salaries->where('representative_id', $representative->id)->where('user_type', 'representative')->first();
            if (!$existingSalary) {
                $salaries->push(new SalaryRecord([
                    'employee_id' => null, // Representatives don't have employee_id
                    'supervisor_id' => null, // Representatives don't have supervisor_id
                    'representative_id' => $representative->id,
                    'user_type' => 'representative',
                    'user_code' => $representative->code, // Company code for representatives
                    'year' => $currentYear,
                    'month' => $currentMonth,
                    'base_salary' => $representative->salary,
                    'advances' => 0,
                    'deductions' => 0,
                    'lost_orders_penalty' => 0,
                    'delivery_penalty' => 0,
                    'commissions' => 0,
                    'cashback' => 0,
                    'net_salary' => $representative->salary
                ]));
            }
        }

        // Create salary records for supervisors who don't have one for this month
        foreach ($allSupervisors as $supervisor) {
            $existingSalary = $salaries->where('supervisor_id', $supervisor->id)->where('user_type', 'supervisor')->first();
            if (!$existingSalary) {
                $salaries->push(new SalaryRecord([
                    'employee_id' => null, // Supervisors don't have employee_id
                    'representative_id' => null, // Supervisors don't have representative_id
                    'supervisor_id' => $supervisor->id,
                    'user_type' => 'supervisor',
                    'year' => $currentYear,
                    'month' => $currentMonth,
                    'base_salary' => $supervisor->salary,
                    'advances' => 0,
                    'deductions' => 0,
                    'lost_orders_penalty' => 0,
                    'delivery_penalty' => 0,
                    'commissions' => 0,
                    'cashback' => 0,
                    'net_salary' => $supervisor->salary
                ]));
            }
        }

        $departments = \App\Models\Department::all();
        
        return view('salary-records.index', compact('salaries', 'departments', 'currentYear', 'currentMonth'));
    }

    public function show($id)
    {
        $this->authorize('view_salary_records');
        
        $salary = SalaryRecord::with('employee.department')->findOrFail($id);
        return view('salary-records.show', compact('salary'));
    }

    public function edit($id)
    {
        $this->authorize('edit_salary_records');
        
        $salary = SalaryRecord::findOrFail($id);
        return view('salary-records.edit', compact('salary'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit_salary_records');
        
        $salary = SalaryRecord::findOrFail($id);
        
        $validated = $request->validate([
            'base_salary' => 'required|numeric|min:0',
            'advances' => 'required|numeric|min:0',
            'deductions' => 'required|numeric|min:0',
            'lost_orders_penalty' => 'required|numeric|min:0',
            'delivery_penalty' => 'required|numeric|min:0',
            'commissions' => 'required|numeric|min:0',
            'cashback' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500'
        ]);

        // Calculate net salary
        $totalDeductions = $validated['advances'] + $validated['deductions'] + 
                          $validated['lost_orders_penalty'] + $validated['delivery_penalty'];
        $totalAdditions = $validated['commissions'] + $validated['cashback'];
        $netSalary = $validated['base_salary'] - $totalDeductions + $totalAdditions;

        $validated['net_salary'] = max(0, $netSalary); // Ensure net salary is not negative

        $salary->update($validated);

        return redirect()->route('salary-records.index')
            ->with('success', 'تم تحديث سجل المرتب بنجاح!');
    }

    public function bulkUpdate(Request $request)
    {
        $this->authorize('edit_salary_records');
        
        $validated = $request->validate([
            'salaries' => 'required|array',
            'salaries.*.id' => 'required|exists:salary_records,id',
            'salaries.*.base_salary' => 'required|numeric|min:0',
            'salaries.*.advances' => 'required|numeric|min:0',
            'salaries.*.deductions' => 'required|numeric|min:0',
            'salaries.*.lost_orders_penalty' => 'required|numeric|min:0',
            'salaries.*.delivery_penalty' => 'required|numeric|min:0',
            'salaries.*.commissions' => 'required|numeric|min:0',
            'salaries.*.cashback' => 'required|numeric|min:0'
        ]);

        foreach ($validated['salaries'] as $salaryData) {
            $salary = SalaryRecord::find($salaryData['id']);
            if ($salary) {
                $totalDeductions = $salaryData['advances'] + $salaryData['deductions'] + 
                                  $salaryData['lost_orders_penalty'] + $salaryData['delivery_penalty'];
                $totalAdditions = $salaryData['commissions'] + $salaryData['cashback'];
                $netSalary = $salaryData['base_salary'] - $totalDeductions + $totalAdditions;

                $salary->update([
                    'base_salary' => $salaryData['base_salary'],
                    'advances' => $salaryData['advances'],
                    'deductions' => $salaryData['deductions'],
                    'lost_orders_penalty' => $salaryData['lost_orders_penalty'],
                    'delivery_penalty' => $salaryData['delivery_penalty'],
                    'commissions' => $salaryData['commissions'],
                    'cashback' => $salaryData['cashback'],
                    'net_salary' => max(0, $netSalary)
                ]);
            }
        }

        return back()->with('success', 'تم تحديث جميع سجلات المرتبات بنجاح!');
    }

    public function generateMonthlySalaries()
    {
        $this->authorize('create_salary_records');
        
        $employees = Employee::active()->get();
        $representatives = \App\Models\Representative::where('is_active', true)->get();
        $supervisors = \App\Models\Supervisor::where('is_active', true)->get();
        $currentYear = now()->year;
        $currentMonth = now()->month;

        // Create salary records for employees
        foreach ($employees as $employee) {
            SalaryRecord::firstOrCreate(
                [
                    'employee_id' => $employee->id,
                    'user_type' => 'employee',
                    'year' => $currentYear,
                    'month' => $currentMonth
                ],
                [
                    'base_salary' => $employee->salary,
                    'advances' => 0,
                    'deductions' => 0,
                    'lost_orders_penalty' => 0,
                    'delivery_penalty' => 0,
                    'commissions' => 0,
                    'cashback' => 0,
                    'net_salary' => $employee->salary
                ]
            );
        }

        // Create salary records for representatives
        foreach ($representatives as $representative) {
            SalaryRecord::firstOrCreate(
                [
                    'representative_id' => $representative->id,
                    'user_type' => 'representative',
                    'year' => $currentYear,
                    'month' => $currentMonth
                ],
                [
                    'employee_id' => null, // Representatives don't have employee_id
                    'supervisor_id' => null, // Representatives don't have supervisor_id
                    'user_code' => $representative->code, // Company code for representatives
                    'base_salary' => $representative->salary,
                    'advances' => 0,
                    'deductions' => 0,
                    'lost_orders_penalty' => 0,
                    'delivery_penalty' => 0,
                    'commissions' => 0,
                    'cashback' => 0,
                    'net_salary' => $representative->salary
                ]
            );
        }

        // Create salary records for supervisors
        foreach ($supervisors as $supervisor) {
            SalaryRecord::firstOrCreate(
                [
                    'supervisor_id' => $supervisor->id,
                    'user_type' => 'supervisor',
                    'year' => $currentYear,
                    'month' => $currentMonth
                ],
                [
                    'employee_id' => null, // Supervisors don't have employee_id
                    'representative_id' => null, // Supervisors don't have representative_id
                    'base_salary' => $supervisor->salary,
                    'advances' => 0,
                    'deductions' => 0,
                    'lost_orders_penalty' => 0,
                    'delivery_penalty' => 0,
                    'commissions' => 0,
                    'cashback' => 0,
                    'net_salary' => $supervisor->salary
                ]
            );
        }

        $totalCreated = $employees->count() + $representatives->count() + $supervisors->count();
        return back()->with('success', "تم إنشاء سجلات المرتبات للشهر الحالي بنجاح! ({$totalCreated} سجل)");
    }

    public function markAsPaid($id)
    {
        $this->authorize('edit_salary_records');
        
        $salary = SalaryRecord::findOrFail($id);
        
        if ($salary->is_paid) {
            return back()->with('error', 'تم دفع هذا المرتب مسبقاً');
        }

        $salary->update([
            'is_paid' => true,
            'paid_at' => now()
        ]);

        return redirect()->route('salary-records.index')
            ->with('success', 'تم تحديث حالة المرتب إلى "تم الدفع" بنجاح!');
    }

    public function markAsUnpaid($id)
    {
        $this->authorize('edit_salary_records');
        
        $salary = SalaryRecord::findOrFail($id);
        
        $salary->update([
            'is_paid' => false,
            'paid_at' => null
        ]);

        return redirect()->route('salary-records.index')
            ->with('success', 'تم تحديث حالة المرتب إلى "لم يدفع" بنجاح!');
    }

    public function export(Request $request)
    {
        $this->authorize('view_salary_records');
        
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        
        // Apply the same filters as the index method
        $salaries = SalaryRecord::with(['employee.department', 'representative', 'supervisor'])
            ->where('year', $year)
            ->where('month', $month)
            ->when(request('search'), function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->whereHas('employee', function($subQ) use ($search) {
                        $subQ->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('representative', function($subQ) use ($search) {
                        $subQ->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('supervisor', function($subQ) use ($search) {
                        $subQ->where('name', 'like', "%{$search}%");
                    });
                });
            })
            ->when(request('user_type'), function($query, $userType) {
                $query->where('user_type', $userType);
            })
            ->when(request('payment_status'), function($query, $status) {
                if ($status === 'paid') {
                    $query->where('is_paid', true);
                } elseif ($status === 'unpaid') {
                    $query->where('is_paid', false);
                }
            })
            ->get();

        // Create filename with filter information
        $filename = "salary_records_{$year}_{$month}";
        if (request('user_type')) {
            $filename .= "_" . request('user_type');
        }
        if (request('payment_status')) {
            $filename .= "_" . request('payment_status');
        }
        if (request('search')) {
            $filename .= "_search";
        }
        $filename .= ".csv";
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function() use ($salaries) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Arabic text
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, [
                'اسم المستخدم', 'الكود', 'النوع', 'المرتب الأساسي', 'السلف', 'الخصومات',
                'غرامة الأوردر الضائع', 'غرامة الإيداع', 'العمولات', 'الكاش باك', 'صافي المرتب', 'حالة الدفع'
            ]);
            
            // Data rows
            foreach ($salaries as $salary) {
                fputcsv($file, [
                    $salary->user_name,
                    $salary->user_code,
                    $salary->user_type === 'employee' ? 'موظف' : ($salary->user_type === 'representative' ? 'مندوب' : 'مشرف'),
                    $salary->base_salary,
                    $salary->advances,
                    $salary->deductions,
                    $salary->lost_orders_penalty,
                    $salary->delivery_penalty,
                    $salary->commissions,
                    $salary->cashback,
                    $salary->net_salary,
                    $salary->is_paid ? 'تم الدفع' : 'لم يدفع'
                ]);
            }
            
            // Add totals row
            fputcsv($file, [
                'الإجمالي', '', '', 
                $salaries->sum('base_salary'),
                $salaries->sum('advances'),
                $salaries->sum('deductions'),
                $salaries->sum('lost_orders_penalty'),
                $salaries->sum('delivery_penalty'),
                $salaries->sum('commissions'),
                $salaries->sum('cashback'),
                $salaries->sum('net_salary'),
                ''
            ]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
