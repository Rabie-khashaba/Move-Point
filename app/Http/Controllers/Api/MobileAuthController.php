<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\AppNotification;

class MobileAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
            'device_token' => 'nullable|string',
        ]);

        // Find user by phone number
        $user = User::where('phone', $request->phone)->first();

        // Check if user exists and password is correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'بيانات الدخول غير صحيحة',
                'status' => 'error'
            ], 401);
        }

        // Check if user type is supervisor or representative
        if (!in_array($user->type, ['supervisor', 'representative'])) {
            return response()->json([
                'message' => 'هذا الحساب غير مسموح له بالدخول من التطبيق',
                'status' => 'error'
            ], 403);
        }

        // Handle device token - convert to array if provided
        $deviceTokens = [];
        if ($request->device_token) {
            // If user already has device tokens, merge with existing ones
            $existingTokens = $user->device_tokens ?? [];
            $deviceTokens = array_unique(array_merge($existingTokens, [$request->device_token]));
        } else {
            // Keep existing tokens if no new token provided
            $deviceTokens = $user->device_tokens ?? [];
        }

        // Update last login and device tokens
        $user->update([
            'last_login_at' => now(),
            'device_tokens' => $deviceTokens,
            'notifications_enabled' => true
        ]);

        // Create token
        $token = $user->createToken('Mobile App')->plainTextToken;

        // Get user details based on type
        $userDetails = null;
        switch ($user->type) {
            case 'representative':
                $userDetails = $user->representative;
                break;
            case 'supervisor':
                $userDetails = $user->supervisor;
                break;
        }
        $roleLabel = match($user->type) {
            'representative' => 'مندوب',
            'supervisor'     => 'مشرف',
            default          => 'موظف',
        };
        return response()->json([
            'message' => 'تم تسجيل الدخول بنجاح',
            'status' => 'success',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'type' =>$roleLabel,
                'name' => $userDetails->name ?? null,
                'phone' => $user->phone,
                'details' => $userDetails
            ]
        ]);
    }
    public function forgetPassword(Request $request){
        $request->validate([
            'phone' => 'required|string',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json([
                'message' => 'هذا الحساب غير موجود',
                'status' => 'error'
            ], 404);
        }

        $user->update(['forget_password' => true]);

        return response()->json([
            'message' => 'تم إرسال كلمة المرور الى الرقم المحدد',
            'status' => 'success'
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }


    public function me(Request $request)
    {
        $user = $request->user();

        // Check if user type is supervisor or representative
        if (!in_array($user->type, ['supervisor', 'representative'])) {
            return response()->json([
                'message' => 'هذا الحساب غير مسموح له بالدخول من التطبيق',
                'status' => 'error'
            ], 403);
        }

        // Get user details based on type
        $userDetails = null;
        switch ($user->type) {
            case 'representative':
                $userDetails = $user->representative;
                break;
            case 'supervisor':
                $userDetails = $user->supervisor;
                break;
        }
        $roleLabel = match($user->type) {
            'representative' => 'مندوب',
            'supervisor'     => 'مشرف',
            default          => 'موظف',
        };

        // Build details object based on user type
        $details = [];

        if ($user->type === 'representative' && $user->representative) {
            $rep = $user->representative;
            $details = [
                'bank_account' => $rep->bank_account ?? null,
                'company_id' => $rep->company?->id,
                'company_name' => $rep->company?->name,
                'address' => $rep->address ?? null,
                'home_location' => $rep->home_location ?? null,
                'national_id' => $rep->national_id ?? null,
                'contact' => $rep->contact ?? null,
                'salary' => $rep->salary ?? null,
                'start_date' => $rep->start_date ?? null,
                'code' => $rep->code ?? null,
                'avatar_url' => $user->avatar_url,
            ];

            // Governorate data
            if ($rep->governorate) {
                $details['governorate'] = [
                    'id' => $rep->governorate->id,
                    'name' => $rep->governorate->name,
                ];
            } else {
                $details['governorate'] = null;
            }

            // Location data
            if ($rep->location) {
                $details['location'] = [
                    'id' => $rep->location->id,
                    'name' => $rep->location->name,
                    'address' => $rep->location->address,
                    'governorate_id' => $rep->location->governorate_id,
                    'governorate_name' => $rep->location->governorate?->name,
                ];
            } else {
                $details['location'] = null;
            }
        }

        if ($user->type === 'supervisor' && $user->supervisor) {
            $sup = $user->supervisor;

            // Build address from governorate and location names if they exist
            $addressParts = [];
            if ($sup->governorate && $sup->governorate->name) {
                $addressParts[] = $sup->governorate->name;
            }
            if ($sup->location && $sup->location->name) {
                $addressParts[] = $sup->location->name;
            }

            $details = [
                'bank_account' => $sup->bank_account ?? null,
                'company_id' => null,
                'company_name' => !empty($addressParts) ? implode('/', $addressParts) : null,
                'address' => !empty($addressParts) ? implode('/', $addressParts) : null,
                'contact' => $sup->contact ?? null,
                'national_id' => $sup->national_id ?? null,
                'salary' => $sup->salary ?? null,
                'start_date' => $sup->start_date ?? null,
                'location_name' => $sup->location_name ?? null,
                'avatar_url' => $user->avatar_url,
            ];

            // Governorate data
            if ($sup->governorate) {
                $details['governorate'] = [
                    'id' => $sup->governorate->id,
                    'name' => $sup->governorate->name,
                ];
            } else {
                $details['governorate'] = null;
            }

            // Location data
            if ($sup->location) {
                $details['location'] = [
                    'id' => $sup->location->id,
                    'name' => $sup->location->name,
                    'address' => $sup->location->address,
                    'governorate_id' => $sup->location->governorate_id,
                    'governorate_name' => $sup->location->governorate?->name,
                ];
            } else {
                $details['location'] = null;
            }
        }

        return response()->json([
            'id' => $user->id,
            'type' => $roleLabel,
            'name' => $userDetails->name ?? null,
            'phone' => $user->phone,
            'details' => $details
        ]);
    }

    /**
     * Delete user account
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'المستخدم غير موجود'
            ], 401);
        }

        // Validate the request
        $request->validate([
            'password' => 'required|string',
            'confirmation' => 'required|string|in:DELETE',
        ], [
            'password.required' => 'كلمة المرور مطلوبة',
            'confirmation.required' => 'تأكيد الحذف مطلوب',
            'confirmation.in' => 'يجب كتابة DELETE للتأكيد',
        ]);

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'كلمة المرور غير صحيحة'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Get user type for cleanup
            $userType = $user->type;
            $userId = $user->id;

            // Delete related data based on user type
            switch ($userType) {
                case 'employee':
                    // Delete employee record
                    if ($user->employee) {
                        $user->employee->delete();
                    }
                    break;

                case 'representative':
                    // Delete representative record
                    if ($user->representative) {
                        $user->representative->delete();
                    }
                    break;

                case 'supervisor':
                    // Delete supervisor record
                    if ($user->supervisor) {
                        $user->supervisor->delete();
                    }
                    break;
            }

            // Delete user's notifications
            AppNotification::where('user_id', $userId)->delete();

            // Delete user's device tokens (set to null)
            $user->update([
                'device_tokens' => null,
                'notifications_enabled' => false
            ]);

            // Delete the user account
            $user->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الحساب بنجاح'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error deleting user account', [
                'user_id' => $userId ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف الحساب. يرجى المحاولة مرة أخرى.'
            ], 500);
        }
    }
}
