<?php

namespace App\Http\Controllers;

use App\Services\EmployeeService;
use App\Models\Department;
use App\Models\Interview;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Role;

class EmployeeController extends Controller
{
    protected $service;

    public function __construct(EmployeeService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $this->authorize('view_employees');
        $employees = $this->service->paginated(100);
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $this->authorize('create_employees');
        $departments = Department::get();
        $roles = Role::all(); // لو شغال بـ Spatie Permission

        return view('employees.create', compact('departments', 'roles'));
    }

    public function store(Request $request)
    {
        $this->authorize('create_employees');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|digits:11|unique:users,phone',
            'whatsapp_phone' => 'required|digits:11',
            'address' => 'required|string|max:255',
            'contact' => ['nullable', 'digits:11'], // صياغة صحيحة
            'national_id' => 'required|digits:14|unique:employees,national_id',
            'salary' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'department_id' => 'required|exists:departments,id',
            'attachments.*' => 'file',
            'role' => 'required|exists:roles,id',
            'password' => 'required|string|min:8',
            'is_active' => 'boolean',
            'shift' => 'nullable|in:مسائي,صباحي',
            'days_off' => 'nullable|numeric|min:0',
        ]);

        $this->service->create($validated);
        return redirect()->route('employees.index')->with('success', 'تم إنشاء الموظف بنجاح!');
    }

    public function show($id)
    {
        $this->authorize('view_employees');
        $employee = $this->service->find($id);

        // Process attachments to include proper URLs
        if ($employee->attachments) {
            // Check if attachments is already an array or needs to be decoded
            if (is_string($employee->attachments)) {
                $attachments = json_decode($employee->attachments, true);
            } else {
                $attachments = $employee->attachments;
            }

            if (is_array($attachments)) {
                $employee->attachments_with_urls = array_map(function($path) {
                    // Check if path is already a full URL
                    if (filter_var($path, FILTER_VALIDATE_URL)) {
                        return [
                            'path' => $path,
                            'url' => $path,
                            'src' => $path
                        ];
                    } else {
                        return [
                            'path' => $path,
                            'url' => asset('storage/app/public/' . $path),
                            'src' => asset('storage/app/public/' . $path)
                        ];
                    }
                }, $attachments);
            }
        }

        return view('employees.show', compact('employee'));
    }

    public function edit($id)
    {
        $this->authorize('edit_employees');
        $employee = $this->service->find($id);
        $roles = Role::all();
        $departments = Department::get();
        return view('employees.edit', compact('employee', 'departments', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit_employees');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['required', 'digits:11', Rule::unique('users')->ignore($this->service->find($id)->user_id)],
            'whatsapp_phone' => 'required|digits:11',
            'address' => 'required|string|max:255',
            'contact' => ['nullable', 'digits:11'], // صياغة صحيحة
            'national_id' => 'required|digits:14|unique:employees,national_id,' . $id,
            'salary' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'password' => 'nullable|string|min:8',
            'department_id' => 'required|exists:departments,id',
            'attachments.*' => 'file|mimes:pdf,jpg,png|max:2048',
            'role' => 'required|exists:roles,id',
            'is_active' => 'boolean',
            'shift' => 'nullable|in:مسائي,صباحي',
            'days_off' => 'nullable|numeric|min:0',
        ]);

        $this->service->update($id, $validated);
        return redirect()->route('employees.index')->with('success', 'تم تحديث الموظف بنجاح!');
    }

    public function changePassword(Request $request, $id)
    {
        $this->authorize('edit_employees');
        $validated = $request->validate([
            'password' => 'required|string|min:8',
        ]);

        $this->service->changePassword($id, $validated['password']);
        return redirect()->route('employees.index')->with('success', 'تم تحديث كلمة المرور بنجاح!');
    }

    public function destroy($id)
    {
        $this->authorize('delete_employees');
        $this->service->delete($id);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم حذف الموظف بنجاح'
            ]);
        }

        return redirect()->route('employees.index')->with('success', 'تم حذف الموظف بنجاح!');
    }

    public function toggleStatus($id)
    {
        $this->authorize('edit_employees');

        $employee = $this->service->find($id);

        $employee = $this->service->update($id, [
            'is_active' => !$employee->is_active
        ]);

        // Sync user table status
        try {
            if ($employee->user) {
                $employee->user->update(['is_active' => $employee->is_active]);
            }
        } catch (\Throwable $e) { /* ignore */ }

        $status = $employee->is_active ? 'نشط' : 'غير نشط';

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "تم تغيير حالة الموظف إلى {$status} بنجاح",
                'employee' => $employee
            ]);
        }

        return redirect()->route('employees.index')->with('success', "تم تغيير حالة الموظف إلى {$status} بنجاح");
    }

    public function downloadAttachment($id, $index)
    {
        try {
            $this->authorize('view_employees');
            $employee = $this->service->find($id);

            $attachments = null;
            if ($employee->attachments) {
                if (is_string($employee->attachments)) {
                    $attachments = json_decode($employee->attachments, true);
                } elseif (is_array($employee->attachments)) {
                    $attachments = $employee->attachments;
                }
            }

            if (!$attachments || !isset($attachments[$index])) {
                abort(404, 'الملف غير موجود');
            }

        $attachment = $attachments[$index];

        // Extract filename from URL
        $urlParts = parse_url($attachment);
        $path = $urlParts['path'] ?? '';

        // Remove /storage/ from the beginning if present
        $path = preg_replace('/^\/storage\//', '', $path);

        // Build the correct file path for public_html/storage/app/public/
        $filePath = public_path('storage/' . $path);

        // Additional security check to prevent directory traversal
        $realPath = realpath($filePath);
        $storagePath = realpath(public_path('storage'));

        if (!$realPath || strpos($realPath, $storagePath) !== 0) {
            abort(404, 'الملف غير موجود على الخادم');
        }

        if (!file_exists($filePath)) {
            abort(404, 'الملف غير موجود على الخادم');
        }

        $fileName = basename($path);
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $contentType = 'application/octet-stream';
        switch ($extension) {
            case 'pdf':
                $contentType = 'application/pdf';
                break;
            case 'jpg':
            case 'jpeg':
                $contentType = 'image/jpeg';
                break;
            case 'png':
                $contentType = 'image/png';
                break;
        }

        return response()->download($filePath, $fileName, [
            'Content-Type' => $contentType
        ]);
        } catch (\Exception $e) {
            \Log::error('Error downloading attachment: ' . $e->getMessage(), [
                'employee_id' => $id,
                'attachment_index' => $index,
                'error' => $e->getMessage()
            ]);
            abort(500, 'حدث خطأ أثناء تحميل الملف');
        }
    }

    public function viewAttachment($id, $index)
    {
        try {
            $this->authorize('view_employees');
            $employee = $this->service->find($id);

            $attachments = null;
            if ($employee->attachments) {
                if (is_string($employee->attachments)) {
                    $attachments = json_decode($employee->attachments, true);
                } elseif (is_array($employee->attachments)) {
                    $attachments = $employee->attachments;
                }
            }

            if (!$attachments || !isset($attachments[$index])) {
                abort(404, 'الملف غير موجود');
            }

            $attachment = $attachments[$index];

            // Extract filename from URL or use the path directly
            if (filter_var($attachment, FILTER_VALIDATE_URL)) {
                $urlParts = parse_url($attachment);
                $path = $urlParts['path'] ?? '';
                // Remove /storage/ from the beginning if present
                $path = preg_replace('/^\/storage\//', '', $path);
            } else {
                // It's already a path, not a URL
                $path = $attachment;
            }

            // Basic security check to prevent directory traversal
            if (strpos($path, '..') !== false || strpos($path, '/') === 0) {
                abort(404, 'الملف غير موجود على الخادم');
            }

            // Return a redirect to the proper storage URL
            $storageUrl = asset('storage/app/public/' . $path);
            return redirect($storageUrl);
        } catch (\Exception $e) {
            \Log::error('Error viewing attachment: ' . $e->getMessage(), [
                'employee_id' => $id,
                'attachment_index' => $index,
                'error' => $e->getMessage()
            ]);
            abort(500, 'حدث خطأ أثناء عرض الملف');
        }
    }

    public function transferLeads(Request $request, $id)
    {
        $this->authorize('edit_employees');

        $validated = $request->validate([
            'new_employee_id' => 'required|exists:employees,id',
        ]);

        $oldEmployee = $this->service->find($id);
        $newEmployee = $this->service->find($validated['new_employee_id']);

        // جلب جميع الـ leads المخصصة للموظف القديم
        $leadsCount = \App\Models\Lead::where('assigned_to', $oldEmployee->user_id)->count();

        // جلب جميع الـ interviews المخصصة للموظف القديم
        $interviewsCount = Interview::where('assigned_to', $oldEmployee->user_id)->count();

        if ($leadsCount > 0) {
            // تحديث assigned_to لجميع الـ leads
            \App\Models\Lead::where('assigned_to', $oldEmployee->user_id)
                ->update(['assigned_to' => $newEmployee->user_id]);
        }

        if ($interviewsCount > 0) {
            // تحديث assigned_to لجميع الـ interviews
            Interview::where('assigned_to', $oldEmployee->user_id)
                ->update(['assigned_to' => $newEmployee->user_id]);
        }

        // إلغاء تفعيل الموظف القديم
        if ($oldEmployee->is_active) {
            $oldEmployee = $this->service->update($id, [
                'is_active' => false
            ]);
            
            // Sync user table status
            try {
                if ($oldEmployee->user) {
                    $oldEmployee->user->update(['is_active' => false]);
                }
            } catch (\Throwable $e) { /* ignore */ }
        }

        $messageParts = [];
        if ($leadsCount > 0) {
            $messageParts[] = "{$leadsCount} عميل محتمل";
        }
        if ($interviewsCount > 0) {
            $messageParts[] = "{$interviewsCount} مقابلة";
        }

        $message = "تم إلغاء تفعيل {$oldEmployee->name}";
        if (!empty($messageParts)) {
            $message .= " ونقل " . implode(' و ', $messageParts) . " إلى {$newEmployee->name} بنجاح";
        } else {
            $message .= " بنجاح (لا توجد عملاء محتملين أو مقابلات مخصصة)";
        }

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'leads_count' => $leadsCount,
                'interviews_count' => $interviewsCount
            ]);
        }

        return redirect()->route('employees.index')->with('success', $message);
    }
}
