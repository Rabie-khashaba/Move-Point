<?php

namespace App\Http\Controllers;

use App\Services\PasswordService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PasswordController extends Controller
{
    protected $passwordService;

    public function __construct(PasswordService $passwordService)
    {
        $this->passwordService = $passwordService;
    }

    /**
     * Show password reset requests dashboard
     */
    public function index(Request $request)
    {
        $this->authorize('view_password_resets');
        
        $status = $request->get('status', 'all');
        
        if ($status === 'all') {
            $resetRequests = $this->passwordService->getAllResetRequests();
        } else {
            $resetRequests = $this->passwordService->getResetRequestsByStatus($status);
        }
        
        return view('passwords.index', compact('resetRequests', 'status'));
    }

    /**
     * API endpoint to reset password by phone number
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|digits:11'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 400);
        }

        $result = $this->passwordService->resetPassword($request->phone);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message']
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }
    }

    /**
     * Trigger initial reset for a specific user id (dashboard button)
     */
    public function resetUser($id)
    {
        $this->authorize('create_passwords');
        
        $user = \App\Models\User::findOrFail($id);
        $result = $this->passwordService->resetPassword($user->phone);
        
        if ($result['success']) {
            return redirect()->route('passwords.index')->with('success', 'تم إرسال كلمة المرور عبر واتساب');
        }
        
        return redirect()->route('passwords.index')->with('error', $result['message'] ?? 'فشل في الإرسال');
    }

    /**
     * Mark password reset as completed
     */
    public function markAsCompleted($id)
    {
        $this->authorize('edit_password_resets');
        
        $result = $this->passwordService->markAsCompleted($id);
        
        if ($result) {
            return redirect()->route('passwords.index')->with('success', 'تم تحديث حالة طلب إعادة تعيين كلمة المرور');
        }
        
        return redirect()->route('passwords.index')->with('error', 'حدث خطأ أثناء تحديث الحالة');
    }

    /**
     * Resend password reset via WhatsApp
     */
    public function resend($id)
    {
        $this->authorize('edit_password_resets');
        
        $result = $this->passwordService->resendPasswordReset($id);
        
        if ($result) {
            return redirect()->route('passwords.index')->with('success', 'تم إعادة إرسال كلمة المرور عبر WhatsApp');
        }
        
        return redirect()->route('passwords.index')->with('error', 'فشل في إعادة إرسال كلمة المرور');
    }

    /**
     * Show password reset request details
     */
    public function show($id)
    {
        $this->authorize('view_password_resets');
        
        $resetRequest = \App\Models\PasswordResetRequest::with(['user'])->findOrFail($id);
        
        return view('passwords.show', compact('resetRequest'));
    }

    /**
     * Get password reset statistics
     */
    public function statistics()
    {
        $this->authorize('view_passwords');
        
        $stats = [
            'total' => \App\Models\PasswordResetRequest::count(),
            'pending' => \App\Models\PasswordResetRequest::where('status', 'pending')->count(),
            'sent' => \App\Models\PasswordResetRequest::where('status', 'sent')->count(),
            'completed' => \App\Models\PasswordResetRequest::where('status', 'completed')->count(),
            'failed' => \App\Models\PasswordResetRequest::where('status', 'failed')->count(),
        ];
        
        return view('passwords.statistics', compact('stats'));
    }

    /**
     * Show the form for creating a new password reset request
     */
    public function create()
    {
        $this->authorize('create_passwords');
        
        return view('passwords.create');
    }

    /**
     * Store a newly created password reset request
     */
    public function store(Request $request)
    {
        $this->authorize('create_passwords');
        
        $validator = Validator::make($request->all(), [
            'phone' => 'required|digits:11',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $result = $this->passwordService->resetPassword($request->phone);
        
        if ($result['success']) {
            return redirect()->route('passwords.index')
                ->with('success', 'تم إنشاء طلب إعادة تعيين كلمة المرور بنجاح');
        }
        
        return redirect()->back()
            ->with('error', $result['message'])
            ->withInput();
    }

    /**
     * Show the form for editing the specified password reset request
     */
    public function edit($id)
    {
        $this->authorize('edit_passwords');
        
        $resetRequest = \App\Models\PasswordResetRequest::with(['user'])->findOrFail($id);
        
        return view('passwords.edit', compact('resetRequest'));
    }

    /**
     * Update the specified password reset request
     */
    public function update(Request $request, $id)
    {
        $this->authorize('edit_passwords');
        
        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string|max:500',
            'status' => 'required|in:pending,sent,completed,failed'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $resetRequest = \App\Models\PasswordResetRequest::findOrFail($id);
        $resetRequest->update($request->only(['notes', 'status']));
        
        return redirect()->route('passwords.index')
            ->with('success', 'تم تحديث طلب إعادة تعيين كلمة المرور بنجاح');
    }

    /**
     * Remove the specified password reset request
     */
    public function destroy($id)
    {
        $this->authorize('delete_passwords');
        
        $resetRequest = \App\Models\PasswordResetRequest::findOrFail($id);
        $resetRequest->delete();
        
        return redirect()->route('passwords.index')
            ->with('success', 'تم حذف طلب إعادة تعيين كلمة المرور بنجاح');
    }
}
