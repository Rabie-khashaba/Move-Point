<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employee;
use App\Models\Lead;
use App\Models\LeadFollowup;
use App\Models\Supervisor;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class DashboardController extends Controller
{
    public function departmentSeven(Request $request)
    {
        $this->authorize('view_sales_dashboards');

        // Date filters (default to today)
        $dateFrom = $request->input('date_from');
        $dateTo   = $request->input('date_to');

        if (!$dateFrom && !$dateTo) {
            $dateFrom = now()->toDateString();
            $dateTo   = now()->toDateString();
        }

        // Employees in department 7
        $employees = Employee::with('user')
            ->where('department_id', 7)
            ->where('is_active', true)
            ->get();

        $statusList = ['جديد','متابعة','مقابلة','قديم','لم يرد','غير مهتم' , 'شفت مسائي','بدون وسيلة نقل'];

        $employeeStats = $employees->map(function (Employee $employee) use ($statusList, $dateFrom, $dateTo) {
            $userId = optional($employee->user)->id;
            $displayName = $employee->name ?? optional($employee->user)->name ?? 'غير معروف';
            $initial = mb_substr($displayName, 0, 1);

            if (!$userId) {
                return [
                    'display_name' => $displayName,
                    'initial'      => $initial,
                    'total'        => 0,
                    'by_status'    => array_fill_keys($statusList, 0),
                    'followed'     => 0,
                    'today_follow_ups' => 0,
                    'activeCount' => 0,
                    'disactiveCount' => 0,

                ];
            }


             $activeCount = \App\Models\Representative::where('employee_id', $userId ?? 0)
            ->where('status', 1)
            ->when($dateFrom, fn($q) => $q->whereDate('converted_to_active_date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('converted_to_active_date', '<=', $dateTo))
            ->count();


            $disactiveCount = \App\Models\Representative::where('employee_id', $userId ?? 0)
            ->where('status', 0)
            ->when($dateFrom, fn($q) => $q->whereDate('converted_to_active_date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('converted_to_active_date', '<=', $dateTo))
            ->count();

            // Base query with date filter
            $leadQuery = Lead::where('assigned_to', $userId);
            if ($dateFrom) {
                $leadQuery->whereDate('created_at', '>=', $dateFrom);
            }
            if ($dateTo) {
                $leadQuery->whereDate('created_at', '<=', $dateTo);
            }

            // Total count
            $total = (clone $leadQuery)->count();

            // Group by status in one query
            $statusCounts = (clone $leadQuery)
                ->select('status', DB::raw('COUNT(*) as cnt'))
                ->groupBy('status')
                ->pluck('cnt', 'status')
                ->toArray();

            // Fill all statuses with 0 if missing
            $byStatus = [];
            foreach ($statusList as $status) {
                $byStatus[$status] = $statusCounts[$status] ?? 0;
            }

            // Leads with followups in date window
            /* $followed = Lead::where('assigned_to', $userId)
                ->whereHas('followUps', function ($q) use ($dateFrom, $dateTo) {
                    if ($dateFrom) {
                        $q->whereDate('created_at', '>=', $dateFrom);
                    }
                    if ($dateTo) {
                        $q->whereDate('created_at', '<=', $dateTo);
                    }
                })
                ->count(); */

            $followed = \App\Models\LeadFollowup::whereHas('lead', function ($q) use ($userId) {
            $q->where('assigned_to', $userId);
            })
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->count();



            $leadCountfollowups = Lead::where('assigned_to', $userId)
            ->where('status', 'مقابلة')
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->count();

            // Follow-ups from today's leads only
            // $todayFollowUps = Lead::where('assigned_to', $userId)
            //     ->whereDate('created_at', now()->toDateString())
            //     ->whereHas('followUps', function ($q) {
            //         $q->whereDate('created_at', now()->toDateString());
            //     })
            //     ->count();


            $todayFollowUps = array_sum($statusCounts) - ($statusCounts['جديد'] ?? 0);




            return [
                'userId' => $userId,
                'display_name' => $displayName,
                'initial'      => $initial,
                'total'        => $total,
                'by_status'    => $byStatus,
                'followed'     => $followed,
                'today_follow_ups' => $todayFollowUps,
                'leadCountfollowups' => $leadCountfollowups,
                'activeCount' => $activeCount,
                'disactiveCount' => $disactiveCount,
            ];
        });

        // Calculate summary statistics for all employees
        $userIds = $employees->pluck('user.id')->filter();
        $totalLeads = 0;
        $totalFollowUps = 0;
        $totalTodayFollowUps = 0;
        $summaryByStatus = array_fill_keys($statusList, 0);

        foreach ($employeeStats as $stat) {
            $totalLeads += $stat['total'];
            $totalFollowUps += $stat['followed'];
            $totalTodayFollowUps += $stat['today_follow_ups'];

            foreach ($stat['by_status'] as $status => $count) {
                $summaryByStatus[$status] += $count;
            }
        }

        $summaryStats = [
            'total_leads' => $totalLeads,
            'total_follow_ups' => $totalFollowUps,
            'total_today_follow_ups' => $totalTodayFollowUps,
            'by_status' => $summaryByStatus,
            'employee_count' => $employees->count()
        ];

        return view('dashboards.department7', [
            'employeeStats' => $employeeStats,
            'statusList'    => $statusList,
            'dateFrom'      => $dateFrom,
            'dateTo'        => $dateTo,
            'summaryStats'  => $summaryStats,
        ]);
    }

    public function myDashboard(Request $request)
    {
        $user = $request->user();
        $statusList = ['جديد','مقابلة','قديم','لم يرد','متابعة','غير مهتم'];

        // Base query for user
        $leadQuery = Lead::where('assigned_to', $user->id);

        $total = (clone $leadQuery)->count();

        // Leads with followups
        $followedLeads = (clone $leadQuery)
            ->whereHas('followUps')
            ->count();

        // Group by status
        $statusCounts = (clone $leadQuery)
            ->select('status', DB::raw('COUNT(*) as cnt'))
            ->groupBy('status')
            ->pluck('cnt', 'status')
            ->toArray();

        $byStatus = [];
        foreach ($statusList as $status) {
            $byStatus[$status] = $statusCounts[$status] ?? 0;
        }

        // Latest leads
        $leads = (clone $leadQuery)
            ->with(['governorate', 'source'])
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        return view('dashboards.my', [
            'user'          => $user,
            'total'         => $total,
            'followedLeads' => $followedLeads,
            'byStatus'      => $byStatus,
            'statusList'    => $statusList,
            'leads'         => $leads,
        ]);
    }



    public function moderation(Request $request){


    $dateFrom = $request->input('date_from');
    $dateTo = $request->input('date_to');

    // ✅ لو مفيش تواريخ في الفلتر، استخدم تاريخ اليوم الحالي
    if (!$dateFrom && !$dateTo) {
        $dateFrom = Carbon::today()->startOfDay(); // بداية اليوم
        $dateTo = Carbon::today()->endOfDay();     // نهاية اليوم
    }


    $employees = Employee::where('department_id', 6)
    ->withCount('leads')
    ->get();

    //return $employees;
    /* $employees = Employee::where('is_active', 1)->where('department_id', 6)

        ->withCount([
            'leads as leads_count' => function ($query) use ($dateFrom, $dateTo) {
                if ($dateFrom) {
                    $query->where('created_at', '>=', $dateFrom);
                }
                if ($dateTo) {
                    $query->where('created_at', '<=', $dateTo);
                }
            }
        ])
        ->get(); */


        return view('dashboards.moderation', compact('employees', 'dateFrom', 'dateTo'));
    }
}
