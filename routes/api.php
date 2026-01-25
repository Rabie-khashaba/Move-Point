<?php

use App\Http\Controllers\Api\GeneralData_RepresentativeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\MobileRequestController;
use App\Http\Controllers\Api\MobileDataController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\SupportController;
use App\Http\Controllers\Api\SalaryController;
use App\Http\Controllers\Api\Supervisor_InterviewController;
use App\Http\Controllers\Api\RepresentativeController;
use App\Http\Controllers\Api\SupervisorRepresentativeController;

Route::post('login', [MobileAuthController::class, 'login']);

// Password reset endpoint (no authentication required)
Route::post('reset-password', [App\Http\Controllers\PasswordController::class, 'resetPassword']);
Route::post('forget-password', [MobileAuthController::class, 'forgetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [MobileAuthController::class, 'logout']);
    Route::get('me', [MobileAuthController::class, 'me']);
    Route::get('profile', [MobileRequestController::class, 'getProfile']);
    Route::post('profile', [MobileRequestController::class, 'updateProfile']);
    Route::delete('account', [MobileAuthController::class, 'deleteAccount']);

    // Requests from mobile app (employee/supervisor/representative)
    Route::post('leave-requests', [MobileRequestController::class, 'createLeave']);
    Route::post('advance-requests', [MobileRequestController::class, 'createAdvance']);
    Route::post('resignation-requests', [MobileRequestController::class, 'createResignation']);
    Route::post('delivery-deposits', [MobileRequestController::class, 'createDeliveryDeposit']);
    Route::post('delivery-deposits/receipt', [MobileRequestController::class, 'uploadDepositReceipt']);
    Route::post('delivery-deposits/receipt-base64', [MobileRequestController::class, 'uploadDepositReceiptBase64']);

    // Bank accounts (for representatives)
    Route::post('bank-accounts', [MobileRequestController::class, 'createBankAccount']);
    Route::get('get/bank-accounts', [MobileRequestController::class, 'getBankAccount']);
    Route::put('update/bank-accounts', [MobileRequestController::class, 'updateBankAccount']);

    // Get all requests data
    Route::get('leave-requests', [MobileRequestController::class, 'getAllLeaveRequests']);
    Route::get('advance-requests', [MobileRequestController::class, 'getAllAdvanceRequests']);
    Route::get('resignation-requests', [MobileRequestController::class, 'getAllResignationRequests']);
    Route::get('delivery-deposits', [MobileRequestController::class, 'getAllDeliveryDeposits']);
    Route::get('requests-summary', [MobileRequestController::class, 'getAllRequestsSummary']);
    Route::get('last-requests-status', [MobileRequestController::class, 'getLastRequestsStatus']);
    Route::get('all-requests-statuses', [MobileRequestController::class, 'getAllStatusesWithReasons']);

    // Data fetch for mobile app
    Route::get('work-schedule', [MobileDataController::class, 'currentWorkSchedule']);
    Route::get('targets/current', [MobileDataController::class, 'currentTarget']);
    Route::get('training/status', [MobileDataController::class, 'trainingStatus']);
    Route::post('training/complete', [MobileDataController::class, 'completeTraining']);

    // Lead referral endpoints (for representatives) - must come before parameterized routes
    Route::post('leads', [MobileDataController::class, 'createLead']);
    Route::get('leads/referred', [MobileDataController::class, 'getReferredLeads']);

    // Leads endpoints (for employees/sales)
    Route::get('leads', [MobileDataController::class, 'getLeads']);
    Route::get('leads/{id}', [MobileDataController::class, 'getLead']);
    Route::put('leads/{id}/status', [MobileDataController::class, 'updateLeadStatus']);

    // Government and locations endpoints
    Route::get('governments', [MobileDataController::class, 'getGovernments']);
    Route::get('governments/with-locations', [MobileDataController::class, 'getGovernmentsWithLocations']);
    Route::get('governments/{id}/locations', [MobileDataController::class, 'getLocationsByGovernment']);
    Route::get('locations', [MobileDataController::class, 'getLocations']);

    // Banks endpoint
    Route::get('banks', [MobileDataController::class, 'getBanks']);

    // Salary endpoints
    Route::get('salary/summary', [MobileDataController::class, 'getSalarySummary']);
    Route::get('salary/details/{month}', [MobileDataController::class, 'getSalaryDetails']);

    // Supervisor representative attachments
    Route::get('supervisor/representatives/no-attachments', [MobileRequestController::class, 'getSupervisorRepsWithoutAttachments']);
    Route::get('supervisor/representatives/with-attachments', [MobileRequestController::class, 'getSupervisorRepsWithAttachments']);
    Route::get('supervisor/representatives/search', [MobileRequestController::class, 'searchSupervisorReps']);
    Route::get('supervisor/representatives/{id}/attachments', [MobileRequestController::class, 'getRepresentativeAttachments']);
    Route::post('supervisor/representatives/{id}/attachments', [MobileRequestController::class, 'updateRepresentativeAttachments']);

    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('{id}', [NotificationController::class, 'destroy']);
        Route::post('register-token', [NotificationController::class, 'registerToken']);
        Route::post('unregister-token', [NotificationController::class, 'unregisterToken']);
        Route::post('toggle', [NotificationController::class, 'toggleNotifications']);
        Route::get('settings', [NotificationController::class, 'settings']);
    });



    //support
    Route::post('/supports', [SupportController::class, 'store']);
    Route::get('/support/{id}', [SupportController::class, 'show']);
    Route::post('supports/{support}/reply', [SupportController::class, 'reply']);
    Route::post('/supports/{id}/close', [SupportController::class, 'close']);


    //salary
    Route::get('salary/{star_id}', [SalaryController::class, 'getSalaryByStarId']);


    // get interviews by supervisor
    Route::get('supervisor/{id}/interviews', [Supervisor_InterviewController::class, 'supervisorInterviews']);
    //change status to interview by supervisor
    Route::post('interview/{id}/status', [Supervisor_InterviewController::class, 'updateInterviewStatus']);


    //create representatives by supervisor
    Route::post('/representatives', [RepresentativeController::class, 'store']);

    //get representatives active and not completed related to supervisor
    Route::get('/supervisors/{id}/representatives/active', [SupervisorRepresentativeController::class, 'active']);
    Route::get('/supervisors/{id}/representatives/incomplete', [SupervisorRepresentativeController::class, 'incomplete']);

    Route::get('/companies', [GeneralData_RepresentativeController::class, 'companies']);
    Route::get('/locations', [GeneralData_RepresentativeController::class, 'locations']);
    Route::get('/governorates', [GeneralData_RepresentativeController::class, 'governorates']);

});
