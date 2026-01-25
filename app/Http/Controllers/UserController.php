<?php
namespace App\Http\Controllers;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    protected $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }


    public function index()
    {
        $this->authorize('view_users');
        $users = $this->service->paginated(20);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $this->authorize('create_users');
        return view('users.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create_users');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|digits:11|unique:users,phone',
            'email' => 'nullable|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'type' => ['required', Rule::in(['employee', 'supervisor', 'representative', 'admin'])],
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'role_ids' => 'array',
            'role_ids.*' => 'exists:roles,id',
        ]);

        $user = $this->service->create($validated);
        return redirect()->route('users.index')->with('success', 'User created successfully!');
    }

    public function show($id)
    {
        $this->authorize('view_users');
        $user = $this->service->find($id);
        
        // Process avatar to include proper URL
        if ($user->avatar) {
            $user->avatar_url = asset('storage/app/public/' . $user->avatar);
            $user->avatar_src = asset('storage/app/public/' . $user->avatar);
        }
        
        return view('users.show', compact('user'));
    }

    public function edit($id)
    {
        $this->authorize('edit_users');
        $user = $this->service->find($id);
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit_users');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['required', 'digits:11', Rule::unique('users')->ignore($id)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users')->ignore($id)],
            'password' => 'nullable|string|min:8|confirmed',
            'type' => ['required', Rule::in(['employee', 'supervisor', 'representative', 'admin'])],
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'role_ids' => 'array',
            'role_ids.*' => 'exists:roles,id',
        ]);

        $user = $this->service->update($id, $validated);
        return redirect()->route('users.index')->with('success', 'User updated successfully!');
    }

    public function changePassword(Request $request, $id)
    {
        $this->authorize('edit_users');
        $validated = $request->validate([
            'password' => 'required|string|min:8',
        ]);

        $this->service->changePassword($id, $validated['password']);
        return redirect()->route('users.index')->with('success', 'Password updated successfully!');
    }

    public function toggleStatus($id)
    {
        $this->authorize('edit_users');
        $user = $this->service->toggleStatusSync((int) $id);
        
        $status = $user->is_active ? 'activated' : 'deactivated';
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "User {$status} successfully!",
                'user' => $user
            ]);
        }
        
        return redirect()->route('users.index')->with('success', "User {$status} successfully!");
    }

    public function destroy($id)
    {
        $this->authorize('delete_users');
        $this->service->delete($id);
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        }
        
        return redirect()->route('users.index')->with('success', 'User deleted successfully!');
    }
}
