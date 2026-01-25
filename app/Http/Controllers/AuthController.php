<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Employee;
use App\Models\Company;
use App\Models\Department;
use App\Models\Lead;
use Carbon\Carbon; // Add this line


class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function index(Request $request)
    {
        try {
//            // Cache dashboard statistics for 5 minutes
//            $stats = cache()->remember('dashboard.stats', 300, function () {
//                return [
//                    'employeeCount' => Employee::count(),
//                    'departmentCount' => Department::count(),
//                    'recentHires' => Company::count(),
//                ];
//            });


            // استقبل الفلتر من الريكوست
            $dateFrom = $request->input('date_from')
                ? Carbon::parse($request->input('date_from'))->startOfDay()
                : null;

            $dateTo = $request->input('date_to')
                ? Carbon::parse($request->input('date_to'))->endOfDay()
                : null;

// cache key
            $cacheKey = 'dashboard.stats.'
                . ($dateFrom ? $dateFrom->format('Ymd') : 'all')
                . '.'
                . ($dateTo ? $dateTo->format('Ymd') : 'all');

            $stats = cache()->remember($cacheKey, 300, function () use ($dateFrom, $dateTo) {

                $employeeQuery   = Employee::query();
                $departmentQuery = Department::query();
                $companyQuery    = Company::query();
                $leadQuery       = Lead::query();

                if ($dateFrom && $dateTo) {
                    $employeeQuery->whereBetween('created_at', [$dateFrom, $dateTo]);
                    $departmentQuery->whereBetween('created_at', [$dateFrom, $dateTo]);
                    $companyQuery->whereBetween('created_at', [$dateFrom, $dateTo]);
                    $leadQuery->whereBetween('created_at', [$dateFrom, $dateTo]);
                }

                return [
                    'employeeCount'   => $employeeQuery->count(),
                    'departmentCount' => $departmentQuery->count(),
                    'recentHires'     => $companyQuery->count(),
                    'leadCount'       => $leadQuery->count(),
                ];
            });


            // Get recent leads with eager loading for better performance
            $user = auth()->user();
            $leads = Lead::with(['governorate', 'source', 'employee.employee', 'referredBy'])
                ->when($user && $user->type === 'employee', function($query) use ($user) {
                    return $query->where('assigned_to', $user->id);
                })
                ->orderBy('created_at', 'desc')
                ->limit(4)
                ->get();



            // Restrict dashboard target summary to Sales employees only
            $salesDepartment = Department::where('name', 'like', '%Sales%')->orWhere('name', 'like', '%المبيعات%')->first();
            $salesEmployees = collect();
            if ($salesDepartment) {
                $salesEmployees = Employee::where('department_id', $salesDepartment->id)->get();
            }

            // Get employees for bulk assign dropdown (only for users with 'view_roles' permission)
            $employees = auth()->user()->can('view_roles')
                ? User::where('type', 'employee')->with('employee')->get()
                : collect();


            // lead by governorate
            $leadsByGovernorateQuery = Lead::with('governorate')
                ->selectRaw('governorate_id, COUNT(*) as total');

// فلترة حسب التاريخ (مرن)
            if ($dateFrom && $dateTo) {
                $leadsByGovernorateQuery->whereBetween('created_at', [$dateFrom, $dateTo]);
            } elseif ($dateFrom) {
                $leadsByGovernorateQuery->where('created_at', '>=', $dateFrom);
            } elseif ($dateTo) {
                $leadsByGovernorateQuery->where('created_at', '<=', $dateTo);
            }

            $leadsByGovernorate = $leadsByGovernorateQuery
                ->groupBy('governorate_id')
                ->get()
                ->map(function($row) {
                    return [
                        'governorate' => $row->governorate ? $row->governorate->name : 'غير محدد',
                        'total'       => $row->total,
                    ];
                });

            return view('dashboard', array_merge($stats, [
                'leads' => $leads,
                'employees' => $employees,
                'salesEmployees' => $salesEmployees,
                'leadsByGovernorate' => $leadsByGovernorate,
            ]));
        } catch (\Exception $e) {
            Log::error('Dashboard error: ' . $e->getMessage());

            return view('dashboard', [
                'employeeCount' => 0,
                'departmentCount' => 0,
                'recentHires' => 0,
                'leadCount' => 0,
                'leads' => collect(),
                'employees' => collect(),
                'salesEmployees' => collect(),
            ]);
        }
    }

  /*   public function login(Request $request)
    {
        try {
            $credentials = $request->only('phone', 'password');

            // Validate input
            $request->validate([
                'phone' => 'required|string',
                'password' => 'required|string',
            ]);

            // Check if user exists
            $user = User::where('phone', $credentials['phone'])->first();

            if (!$user) {
                Log::warning('Login attempt with non-existent phone: ' . $credentials['phone']);
                return back()->withErrors([
                    'phone' => 'Invalid credentials.',
                ])->withInput($request->only('phone'));
            }

            // Block inactive accounts
            if ($user && isset($user->is_active) && $user->is_active === 0) {
                Log::warning('Login blocked for inactive user: ' . $user->phone);
                return back()->withErrors([
                    'phone' => 'تم إيقاف هذا الحساب. يرجى التواصل مع الإدارة.',
                ])->withInput($request->only('phone'));
            }

            // Attempt authentication
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();

                Log::info('User logged in successfully: ' . $user->phone);
                $user->update([
                    'last_login_at' => Carbon::now(),
                ]);
                // Redirect based on user type
                switch ($user->type) {
                    case 'admin':
                        return redirect()->intended('/');
                    case 'employee':
                    case 'supervisor':
                    case 'representative':
                        return redirect()->intended('/dashboards/my');
                    default:
                        return redirect()->intended('/');
                }
            }

            Log::warning('Failed login attempt for phone: ' . $credentials['phone']);
            return back()->withErrors([
                'phone' => 'Invalid credentials.',
            ])->withInput($request->only('phone'));

        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return back()->withErrors([
                'phone' => 'An error occurred during login. Please try again.',
            ])->withInput($request->only('phone'));
        }
    }
 */


    public function login(Request $request)
    {
        try {
            $credentials = $request->only('phone', 'password');

            // Validate input
            $request->validate([
                'phone' => 'required|string',
                'password' => 'required|string',
            ]);

            // 🔎 تحقق من وجود المستخدم
            $user = User::where('phone', $credentials['phone'])->first();

            if (!$user) {
                Log::warning('Login attempt with non-existent phone: ' . $credentials['phone']);
                return back()->withErrors([
                    'phone' => 'بيانات الدخول غير صحيحة.',
                ])->withInput($request->only('phone'));
            }

            // 👨‍💼 لو المستخدم من نوع employee، تحقق من جدول employees
            if ($user->type === 'employee') {
                $employee = \App\Models\Employee::where('user_id', $user->id)->first();

                if (!$employee) {
                    Log::warning('Login blocked: no employee record for user ' . $user->phone);
                    return back()->withErrors([
                        'phone' => 'بيانات الموظف غير موجودة.',
                    ])->withInput($request->only('phone'));
                }

                // 🚫 لو الحساب غير نشط في جدول employees
                if ($employee->is_active == 0) {
                    Log::warning('Login blocked: inactive employee ' . $user->phone);
                    return back()->withErrors([
                        'phone' => 'تم إيقاف هذا الحساب. يرجى التواصل مع الإدارة.',
                    ])->withInput($request->only('phone'));
                }
            }

            // ✅ حاول تسجيل الدخول
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();

                $user->update(['last_login_at' => now()]);

                Log::info('User logged in successfully: ' . $user->phone);

                // التوجيه حسب نوع المستخدم
                return match ($user->type) {
                    'admin' => redirect()->intended('/'),
                    'employee', 'supervisor', 'representative' => redirect()->intended('/dashboards/my'),
                    default => redirect()->intended('/'),
                };
            }

            Log::warning('Failed login attempt for phone: ' . $credentials['phone']);
            return back()->withErrors([
                'phone' => 'بيانات الدخول غير صحيحة.',
            ])->withInput($request->only('phone'));

        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return back()->withErrors([
                'phone' => 'حدث خطأ أثناء تسجيل الدخول. يرجى المحاولة لاحقًا.',
            ])->withInput($request->only('phone'));
        }
    }

    public function logout(Request $request)
    {
        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            Log::info('User logged out successfully');
            return redirect('/login');

        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            return redirect('/login');
        }
    }
}
