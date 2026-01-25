<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $this->authorize('view_admins');
        $admins = User::where('type', 'admin')->orderByDesc('id')->paginate(20);
        return view('admins.index', compact('admins'));
    }

    public function store(Request $request)
    {
        $this->authorize('create_admins');
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'required|digits:11|unique:users,phone',
            'password' => 'required|string|min:6',
        ]);
        $user = User::create([
            'type' => 'admin',
            'name' => $data['name'] ?? null,
            'phone' => $data['phone'],
            'password' => bcrypt($data['password']),
        ]);
        if (!$user->hasRole('المشرف العام')) {
            $user->assignRole('المشرف العام');
        }
        return back()->with('success', 'تم إنشاء مسؤول');
    }

    public function update(Request $request, User $admin)
    {
        $this->authorize('edit_admins');
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'required|digits:11|unique:users,phone,' . $admin->id,
            'password' => 'nullable|string|min:6',
        ]);
        $update = [
            'name' => $data['name'] ?? null,
            'phone' => $data['phone'],
        ];
        if (!empty($data['password'])) {
            $update['password'] = bcrypt($data['password']);
        }
        $admin->update($update);
        return back()->with('success', 'تم تحديث المسؤول');
    }

    public function destroy(User $admin)
    {
        $this->authorize('delete_admins');
        $admin->delete();
        return back()->with('success', 'تم حذف المسؤول');
    }
}


