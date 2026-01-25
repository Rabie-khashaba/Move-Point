<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // Show the profile
    public function show()
    {
        $user = Auth::user();  // Get the logged-in user
        $employee = $user->employee;  // Fetch employee details related to the user
        
        // Process avatar to include proper URL
        if ($user->avatar) {
            $user->avatar_url = asset('storage/app/public/' . $user->avatar);
            $user->avatar_src = asset('storage/app/public/' . $user->avatar);
        }
        
        return view('profile.show', compact('user', 'employee'));
    }

    // Update the profile
    public function update(Request $request)
    {
        // Validate the input data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255',
            'salary' => 'nullable|numeric',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = Auth::user();
        $employee = $user->employee;

        // Update user details
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->save();

        // Update employee details
        if ($employee) {
            $employee->name = $request->employee_name;
            $employee->phone = $request->employee_phone;
            $employee->address = $request->address;
            $employee->salary = $request->salary;
            $employee->save();
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            
            // Delete old avatar if exists
            if ($user->avatar && Storage::exists('public/' . $user->avatar)) {
                Storage::delete('public/' . $user->avatar);
            }
            $user->avatar = $avatarPath;
            $user->save();
        }

        return back()->with('status', 'Profile updated successfully.');
    }
}
