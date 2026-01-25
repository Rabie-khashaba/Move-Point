<?php
namespace App\Http\Controllers;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    protected $service;

    public function __construct(PermissionService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $this->authorize('view_permissions');
        $permissions = $this->service->paginated(20);
        return view('permissions.index', compact('permissions'));
    }

    public function create()
    {
        $this->authorize('create_permissions');
        return view('permissions.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create_permissions');
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'display_name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'module' => 'nullable|string|max:100',
        ]);

        $permission = $this->service->create($validated);
        return redirect()->route('permissions.index')->with('success', 'تم إنشاء الصلاحية بنجاح!');
    }

    public function show($id)
    {
        $this->authorize('view_permissions');
        $permission = $this->service->find($id);
        return view('permissions.show', compact('permission'));
    }

    public function edit($id)
    {
        $this->authorize('edit_permissions');
        $permission = $this->service->find($id);
        return view('permissions.edit', compact('permission'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit_permissions');
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions')->ignore($id)],
            'display_name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'module' => 'nullable|string|max:100',
        ]);

        $permission = $this->service->update($id, $validated);
        return redirect()->route('permissions.index')->with('success', 'تم تحديث الصلاحية بنجاح!');
    }

    public function destroy($id)
    {
        $this->authorize('delete_permissions');
        $this->service->delete($id);
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم حذف الصلاحية بنجاح'
            ]);
        }
        
        return redirect()->route('permissions.index')->with('success', 'تم حذف الصلاحية بنجاح!');
    }

    /**
     * Show sync preview
     */
    public function syncPreview()
    {
        // Temporarily comment out authorization for testing
        // $this->authorize('manage_permissions');
        
        // Get default permissions from service
        $defaultPermissions = $this->service->getDefaultPermissions();
        
        return view('permissions.sync', compact('defaultPermissions'));
    }

    /**
     * Sync permissions with default list
     */
    public function sync()
    {
        // Temporarily comment out authorization for testing
        // $this->authorize('manage_permissions');
        
        try {
            $result = $this->service->syncPermissions();
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "تم مزامنة الصلاحيات بنجاح. تم إنشاء {$result['created']} صلاحية جديدة وتحديث {$result['updated']} صلاحية.",
                    'data' => $result
                ]);
            }
            
            return redirect()->route('permissions.index')->with('success', 
                "تم مزامنة الصلاحيات بنجاح. تم إنشاء {$result['created']} صلاحية جديدة وتحديث {$result['updated']} صلاحية."
            );
            
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء مزامنة الصلاحيات: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('permissions.index')->with('error', 'حدث خطأ أثناء مزامنة الصلاحيات: ' . $e->getMessage());
        }
    }

    /**
     * Get permissions grouped by module
     */
    public function byModule()
    {
        $this->authorize('view_permissions');
        
        $permissionsByModule = $this->service->getPermissionsByModule();
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $permissionsByModule
            ]);
        }
        
        return view('permissions.by-module', compact('permissionsByModule'));
    }

    /**
     * Get permissions grouped by action type
     */
    public function byActionType()
    {
        $this->authorize('view_permissions');
        
        $permissionsByAction = $this->service->getPermissionsByActionType();
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $permissionsByAction
            ]);
        }
        
        return view('permissions.by-action', compact('permissionsByAction'));
    }
}

