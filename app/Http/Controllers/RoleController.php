<?php
namespace App\Http\Controllers;

use App\Services\RoleService;
use App\Models\Department;
use Spatie\Permission\Models\Permission; // <-- Use Spatie's Permission
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    protected $service;

    public function __construct(RoleService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $this->authorize('view_roles');
        $roles = $this->service->paginated(20);
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $this->authorize('create_roles');
        $departments = Department::get();
        $permissions = Permission::all(); // Spatie Permission
        return view('roles.create', compact('departments', 'permissions'));
    }

    public function store(Request $request)
    {
        $this->authorize('create_roles');
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permission_ids' => 'array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $role = $this->service->create($validated);

        return redirect()->route('roles.index')->with('success', 'تم إنشاء الدور بنجاح');
    }

    public function show($id)
    {
        $this->authorize('view_roles');
        $role = $this->service->find($id);
        return view('roles.show', compact('role'));
    }

    public function edit($id)
    {
        $this->authorize('edit_roles');
        $role = $this->service->find($id);
        $departments = Department::get();
        $permissions = Permission::all(); // Spatie Permission
        return view('roles.edit', compact('role', 'departments', 'permissions'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit_roles');
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($id)],
            'permission_ids' => 'array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $role = $this->service->update($id, $validated);

        return redirect()->route('roles.index')->with('success', 'تم تحديث الدور بنجاح');
    }

    public function destroy($id)
    {
        $this->authorize('delete_roles');
        $this->service->delete($id);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم حذف الدور بنجاح'
            ]);
        }

        return redirect()->route('roles.index')->with('success', 'تم حذف الدور بنجاح');
    }
}
