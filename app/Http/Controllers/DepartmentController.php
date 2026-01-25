<?php
namespace App\Http\Controllers;

use App\Services\DepartmentService;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    protected $service;

    public function __construct(DepartmentService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $this->authorize('view_departments');

        $query = Department::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $departments = $query->paginate(20)->withQueryString();

        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        $this->authorize('create_departments');
        return view('departments.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create_departments');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $this->service->create($validated);
        return redirect()->route('departments.index')->with('success', 'تم إنشاء القسم بنجاح!');
    }

    public function show($id)
    {
        $this->authorize('view_departments');
        $department = $this->service->find($id);
        return view('departments.show', compact('department'));
    }

    public function edit($id)
    {
        $this->authorize('edit_departments');
        $department = $this->service->find($id);
        return view('departments.edit', compact('department'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit_departments');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $this->service->update($id, $validated);
        return redirect()->route('departments.index')->with('success', 'تم تحديث القسم بنجاح!');
    }

    public function destroy($id)
    {
        $this->authorize('delete_departments');

        try {
            $this->service->delete($id);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم حذف القسم بنجاح'
                ]);
            }

            return redirect()->route('departments.index')
                             ->with('success', 'تم حذف القسم بنجاح!');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء الحذف: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('departments.index')
                             ->with('error', 'حدث خطأ أثناء الحذف: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        $this->authorize('edit_departments');
        $department = $this->service->find($id);
        $department->update(['is_active' => !$department->is_active]);

        $status = $department->is_active ? 'تم تفعيل القسم بنجاح!' : 'تم تعطيل القسم بنجاح!';

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $status,
                'department' => $department
            ]);
        }

        return redirect()->route('departments.index')->with('success', $status);
    }
}
