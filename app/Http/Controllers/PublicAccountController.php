<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\AppNotification;

class PublicAccountController extends Controller
{
    /**
     * Show the public account deletion form
     */
    public function showDeleteForm(): View
    {
        return view('public.delete-account');
    }

    /**
     * Delete user account via public form
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        // Validate the request
        $request->validate([
            'phone' => 'required|string|exists:users,phone',
            'password' => 'required|string',
            'confirmation' => 'required|string|in:DELETE',
        ], [
            'phone.required' => 'رقم الهاتف مطلوب',
            'phone.exists' => 'رقم الهاتف غير موجود في النظام',
            'password.required' => 'كلمة المرور مطلوبة',
            'confirmation.required' => 'تأكيد الحذف مطلوب',
            'confirmation.in' => 'يجب كتابة DELETE للتأكيد',
        ]);

        try {
            // Find user by phone
            $user = User::where('phone', $request->phone)->first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'المستخدم غير موجود'
                ], 404);
            }

            // Verify password
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'كلمة المرور غير صحيحة'
                ], 400);
            }

            // Check if user is active
            if (!$user->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'هذا الحساب غير نشط'
                ], 400);
            }

            DB::beginTransaction();

            // Get user type for cleanup
            $userType = $user->type;
            $userId = $user->id;
            $userName = $user->name ?? 'غير محدد';

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

            // Log the successful deletion
            Log::info('Account deleted via public form', [
                'user_id' => $userId,
                'user_name' => $userName,
                'user_type' => $userType,
                'phone' => $request->phone,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الحساب بنجاح'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error deleting user account via public form', [
                'phone' => $request->phone,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف الحساب. يرجى المحاولة مرة أخرى.'
            ], 500);
        }
    }

    /**
     * Check if phone number exists (for AJAX validation)
     */
    public function checkPhone(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string'
        ]);

        $user = User::where('phone', $request->phone)
            ->where('is_active', true)
            ->select('id', 'name', 'type')
            ->first();

        if ($user) {
            return response()->json([
                'exists' => true,
                'name' => $user->name,
                'type' => $user->type
            ]);
        }

        return response()->json([
            'exists' => false
        ]);
    }
}
