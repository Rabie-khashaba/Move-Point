    <?php

    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\UserController;
    use App\Http\Controllers\CompanyController;
    use App\Http\Controllers\AuthController;
    use App\Http\Controllers\DepartmentController;
    use App\Http\Controllers\GovernorateController;
    use App\Http\Controllers\LocationController;
    use App\Http\Controllers\RepresentativeController;
    use App\Http\Controllers\SupervisorController;
    use App\Http\Controllers\SupervisorTransferLogController;
    use App\Http\Controllers\PermissionController;
    use App\Http\Controllers\RoleController;
    use App\Http\Controllers\SourceController;
    use App\Http\Controllers\EmployeeController;
    use App\Http\Controllers\LeadController;
    use App\Http\Controllers\DashboardController;
    use App\Http\Controllers\RepresentativeTargetController;
    use App\Http\Controllers\LeadsTargetController;
    use Spatie\Permission\Models\Permission;
    use Spatie\Permission\Models\Role;
    use App\Http\Controllers\ReasonController;
    use App\Http\Controllers\ProfileController;
    use App\Http\Controllers\MessageController;
    use App\Http\Controllers\InterviewController;
    use App\Http\Controllers\WorkScheduleController;
    use App\Http\Controllers\LeaveRequestController;
    use App\Http\Controllers\EmployeeTargetController;
    use App\Http\Controllers\AdvanceRequestController;
    use App\Http\Controllers\DeliveryDepositController;
    use App\Http\Controllers\SalaryRecordController;
    use App\Http\Controllers\SalaryComponentController;
    use App\Http\Controllers\ResignationRequestController;
    use App\Http\Controllers\PasswordController;
    use App\Http\Controllers\Admin\NotificationController;
    use App\Http\Controllers\PublicAccountController;
    use App\Http\Controllers\PublicRepresentativeController;
    use App\Http\Controllers\WhatsAppLogController;
    use App\Http\Controllers\MessageTrainingController;
    use App\Http\Controllers\MessageStartWorkingController;
    use App\Http\Controllers\RepresentativeNotCompletedController;
    use App\Http\Controllers\SupportController;
    use App\Http\Controllers\SupportReplyController;
    use App\Services\WhatsAppService;
    use App\Http\Controllers\SalaryRecord1Controller;
    use App\Http\Controllers\DeviceController;
    use App\Http\Controllers\TrainingSessionController;
    use App\Http\Controllers\WorkStartController;
    use App\Http\Controllers\WaitingRepresentativeController;
    use App\Http\Controllers\DebtController;
    use App\Http\Controllers\BankController;






    // Public routes (no authentication required)
    Route::get('/move-point', function () {
        return view('public.move-point');
    })->name('public.move-point');

    // WhatsApp routes using controller
    Route::get('/whatsapp-dashboard', [WhatsAppLogController::class, 'dashboard'])->name('whatsapp.dashboard');
    Route::get('/whatsapp-logs', [WhatsAppLogController::class, 'getLogs'])->name('whatsapp.logs');
    Route::get('/whatsapp-stats', [WhatsAppLogController::class, 'getStats'])->name('whatsapp.stats');
    Route::get('/whatsapp-clear-logs', [WhatsAppLogController::class, 'clearLogs'])->name('whatsapp.clear-logs');
    Route::get('/whatsapp-send-test', [WhatsAppLogController::class, 'sendTest'])->name('whatsapp.send-test');
    Route::get('/whatsapp-link', [WhatsAppLogController::class, 'generateLink'])->name('whatsapp.link');

    // WhatsApp management routes
    Route::get('/whatsapp-messages', [WhatsAppLogController::class, 'index'])->name('whatsapp.messages.index');
    Route::get('/whatsapp-messages-simple', [WhatsAppLogController::class, 'simpleIndex'])->name('whatsapp.messages.simple');
    Route::get('/whatsapp-pending-messages', [WhatsAppLogController::class, 'getPendingMessages'])->name('whatsapp.pending-messages');
    Route::get('/whatsapp-dashboard-stats', [WhatsAppLogController::class, 'getDashboardStats'])->name('whatsapp.dashboard-stats');
    Route::post('/whatsapp-resend-message', [WhatsAppLogController::class, 'resendMessage'])->name('whatsapp.resend-message');

    // WhatsApp Logs Management Routes
    Route::prefix('whatsapp-logs')->name('whatsapp.logs.')->group(function () {
        Route::get('/', [App\Http\Controllers\WhatsAppLogController::class, 'index'])->name('index');
        Route::get('/{log}', [App\Http\Controllers\WhatsAppLogController::class, 'show'])->name('show');
        Route::post('/{log}/resend', [App\Http\Controllers\WhatsAppLogController::class, 'resend'])->name('resend');
        Route::post('/resend-all', [App\Http\Controllers\WhatsAppLogController::class, 'resendAll'])->name('resend-all');
        Route::delete('/{log}', [App\Http\Controllers\WhatsAppLogController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-delete', [App\Http\Controllers\WhatsAppLogController::class, 'bulkDelete'])->name('bulk-delete');
        Route::post('/clear-old', [App\Http\Controllers\WhatsAppLogController::class, 'clearOld'])->name('clear-old');
        Route::get('/statistics', [App\Http\Controllers\WhatsAppLogController::class, 'statistics'])->name('statistics');
        Route::get('/export', [App\Http\Controllers\WhatsAppLogController::class, 'export'])->name('export');
    });

    Route::get('/delete-account', [PublicAccountController::class, 'showDeleteForm'])->name('public.delete-account');
    Route::post('/delete-account', [PublicAccountController::class, 'deleteAccount']);
    Route::post('/check-phone', [PublicAccountController::class, 'checkPhone'])->name('public.check-phone');

    // Public representative registration routes
    Route::get('/representative-registration', [PublicRepresentativeController::class, 'create'])->name('public.representative.create');
    Route::post('/representative-registration', [PublicRepresentativeController::class, 'store'])->name('public.representative.store');
    Route::get('/representative-success', function () {
        return view('public.representative-success');
    })->name('public.representative.success');

    // Authentication routes (Sanctum or Laravel default)
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('landing-page/{slug?}', [App\Http\Controllers\LandingPageController::class, 'show'])
            ->name('landing-page.show');
        Route::post('landing-page/store', [App\Http\Controllers\LandingPageController::class, 'store'])
            ->name('landing-page.store');
        Route::get('success', [App\Http\Controllers\LandingPageController::class, 'success'])
            ->name('landing-page.success');
            Route::get('sliders/{slider}/image', [\App\Http\Controllers\SliderController::class, 'image'])->name('sliders.image');




    // Protected routes (require authentication)
    Route::middleware(['auth'])->group(function () {

        // Dashboard route
        Route::get('/', [AuthController::class, 'index'])->name('dashboard');

        // User Notifications (user-specific)
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/unread-count', [App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('unread-count');
            Route::get('/recent', [App\Http\Controllers\NotificationController::class, 'getRecentNotifications'])->name('recent');
            Route::post('/{id}/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('mark-read');
            Route::post('/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
            Route::get('/stats', [App\Http\Controllers\NotificationController::class, 'getStats'])->name('stats');
            Route::get('/', [App\Http\Controllers\NotificationController::class, 'index'])->name('index');

            // Admin-only notification creation
            Route::get('/create', [App\Http\Controllers\NotificationController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\NotificationController::class, 'store'])->name('store');
        });

        Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::post('profile/update', [ProfileController::class, 'update'])->name('profile.update');

        // Users
        Route::resource('users', UserController::class);
        Route::post('users/{id}/password', [UserController::class, 'changePassword']);
        Route::post('users/{id}/toggle-status', [UserController::class, 'toggleStatus']);
        Route::post('leads/bulk-assign', [LeadController::class, 'bulkAssign'])->name('leads.bulkAssign');

        // Companies
        Route::resource('companies', CompanyController::class);
        Route::post('companies/{id}/toggle-status', [CompanyController::class, 'toggleStatus'])
            ->name('companies.toggle-status');

        // Departments
        Route::resource('departments', DepartmentController::class);
        Route::post('departments/{id}/toggle-status', [DepartmentController::class, 'toggleStatus'])
            ->name('departments.toggle-status');
        Route::resource('messages', MessageController::class);
        Route::resource('messagesTraining', MessageTrainingController::class);
        Route::resource('messagesWorking', MessageStartWorkingController::class);
        Route::resource('banks', BankController::class);
        
        Route::get('bank-accounts/export', [\App\Http\Controllers\BankAccountController::class, 'export'])
        ->name('bank-accounts.export');
        
        Route::post('bank-accounts/import', [\App\Http\Controllers\BankAccountController::class, 'import'])
        ->name('bank-accounts.import');
        Route::resource('bank-accounts', \App\Http\Controllers\BankAccountController::class);
        
        Route::get('wallet-accounts', [\App\Http\Controllers\WalletAccountController::class, 'index'])
        ->name('wallet-accounts.index');
        
            Route::get('wallet-accounts/export', [\App\Http\Controllers\WalletAccountController::class, 'export'])
        ->name('wallet-accounts.export');

        
        
        Route::get('/interviews/export', [InterviewController::class, 'export'])->name('interviews.export');
        Route::resource('interviews', InterviewController::class);
        Route::post('interviews/{id}/note', [InterviewController::class, 'saveNote'])->name('interviews.save-note');
        Route::post('interviews/{id}/date', [InterviewController::class, 'updateDate'])->name('interviews.update-date');
        Route::post('interviews/{id}/resend-whatsapp', [InterviewController::class, 'resendWhatsApp'])->name('interviews.resend-whatsapp');
        Route::get('getmessages', [MessageController::class, 'getMessagesByLocation'])->name('messages.byLocation');
        Route::get('getmessage/{id}', [MessageController::class, 'getMessage'])->name('messages.getMessage');
        Route::get('test-whatsapp/{phone}', function($phone) {
            $whatsappService = app(\App\Services\WhatsAppService::class);
            $result = $whatsappService->testMessage($phone);
            return response()->json(['success' => $result, 'phone' => $phone]);
        })->name('test.whatsapp');
        Route::get('delivery-deposits/{id}/receipt', [DeliveryDepositController::class, 'showReceipt'])
    ->name('delivery-deposits.receipt');
    
        Route::post('interview/bulk-assign', [InterviewController::class, 'bulkAssign'])->name('interview.bulkAssign');
        Route::get('get-supervisors', [App\Http\Controllers\InterviewController::class, 'getSupervisors']);




        // Governorates
        Route::resource('governorates', GovernorateController::class);
        Route::post('governorates/{id}/toggle-status', [GovernorateController::class, 'toggleStatus'])
            ->name('governorates.toggle-status');

        // Locations
        Route::resource('locations', LocationController::class);
        Route::post('locations/{id}/toggle-status', [LocationController::class, 'toggleStatus'])
            ->name('locations.toggle-status');
        Route::get('getlocations/{governorate}', [LeadController::class, 'getLocations'])->name('locations.byGovernorate');

        // Representatives
        
        
        Route::get('/representatives/export', [RepresentativeController::class, 'export'])
            ->name('representatives.export');
        Route::resource('representatives', RepresentativeController::class);
        Route::post('representatives/{id}/password', [RepresentativeController::class, 'changePassword'])
            ->name('representatives.change-password');
        Route::post('representatives/{id}/toggle-status', [RepresentativeController::class, 'toggleStatus'])
            ->name('representatives.toggle-status');
        Route::post('representatives/{id}/mark-not-completed', [RepresentativeController::class, 'markNotCompleted'])
        ->name('representatives.mark-not-completed');
        Route::get('representatives/locations/{governorateId}', [RepresentativeController::class, 'getLocationsByGovernorate'])
            ->name('representatives.locations-by-governorate');
        Route::get('representatives/{id}/attachment/{index}/view', [RepresentativeController::class, 'viewAttachment'])
            ->name('representatives.attachment.view');
        Route::get('representatives/{id}/attachment/{index}/download', [RepresentativeController::class, 'downloadAttachment'])
            ->name('representatives.attachment.download');




        //Representatives Not Completed
        Route::get('/representatives-not-completed/export', [RepresentativeNotCompletedController::class, 'export'])
            ->name('representatives-not-completed.export');


        Route::resource('representatives-not-completed', RepresentativeNotCompletedController::class);
        Route::get('representatives-not-completed/{id}/attachment/{index}/view', [RepresentativeNotCompletedController::class, 'viewAttachment'])
            ->name('representatives_no.attachment.view');
        Route::get('representatives-not-completed/{id}/attachment/{index}/download', [RepresentativeNotCompletedController::class, 'downloadAttachment'])
            ->name('representatives_no.attachment.download');
        Route::get('representatives-not-completed/{id}/inquiry-attachment/{type}/{index}/view', [RepresentativeNotCompletedController::class, 'viewInquiryAttachment'])
            ->name('representatives_no.inquiry_attachment.view');

        Route::post('representatives-not-completed/{id}/toggle-status', [RepresentativeNotCompletedController::class, 'toggleStatus'])
            ->name('representatives-not-completed.toggle-status');

        Route::post('representatives-not-completed/{id}/start-representative', [RepresentativeNotCompletedController::class, 'StartRealRepresentative'])
            ->name('representatives-not-completed.startRealRepresentative');

        Route::get('getmessagesStartWork', [MessageStartWorkingController::class, 'getMessagesByLocation'])->name('messagesStartWork.byLocation');
        Route::get('getmessageStartWork/{id}', [MessageStartWorkingController::class, 'getMessage'])->name('messagesStartWork.getMessage');
        Route::post('representatives-not-completed/{id}/transfer_to_representative', [RepresentativeNotCompletedController::class, 'representative'])
            ->name('representatives-not-completed.transferToActive');

        Route::get('getmessagesTraining', [MessageTrainingController::class, 'getMessagesByLocation'])->name('getmessagesTraining.byLocation');
        Route::get('getmessageTraining/{id}', [MessageTrainingController::class, 'getMessage'])->name('getmessageTraining.getMessage');
        Route::post('representatives-not-completed/{id}/send_message_training', [RepresentativeNotCompletedController::class, 'send_message_training'])
            ->name('representatives-not-completed.send_message_training');

        Route::put('/representatives-not-completed/{id}/toggle-training', [RepresentativeNotCompletedController::class, 'toggleTraining'])
            ->name('representatives-not-completed.toggleTraining');


        Route::post('representatives-not-completed/{id}/note', [RepresentativeNotCompletedController::class, 'saveNote'])->name('representatives-not-completed.save-note');
        Route::post('representatives-not-completed/{id}/transfer_to_representative2', [RepresentativeNotCompletedController::class, 'representative2'])
            ->name('representatives-not-completed.transferToActive2');
            
        Route::PUT('representatives-not-completed/{id}/resignation', [RepresentativeNotCompletedController::class, 'resignation'])
        ->name('representatives-not-completed.resignation');
        
        Route::put(
        '/representatives/{id}/toggle-documents-status',
        [RepresentativeNotCompletedController::class, 'toggleDocumentsStatus']
    )->name('representatives.toggleDocumentsStatus');

        
        
        //resignation representative
        
        Route::get('/resignation-representatives/export', [\App\Http\Controllers\RepresentativeResignContorller::class, 'export'])
            ->name('resignation-representatives.export');
        Route::resource('resignation-representatives', \App\Http\Controllers\RepresentativeResignContorller::class);
        Route::post('resignation-representatives/{id}/toggle-status', [\App\Http\Controllers\RepresentativeResignContorller::class, 'toggleStatus'])
            ->name('resignation-representatives.toggle-status');







        // Employees
        Route::resource('employees', EmployeeController::class);
        Route::post('employees/{id}/password', [EmployeeController::class, 'changePassword'])
            ->name('employees.change-password');
        Route::post('employees/{id}/toggle-status', [EmployeeController::class, 'toggleStatus'])
            ->name('employees.toggle-status');
        Route::get('employees/{id}/attachment/{index}/view', [EmployeeController::class, 'viewAttachment'])
            ->name('employees.attachment.view');
        Route::get('employees/{id}/attachment/{index}/download', [EmployeeController::class, 'downloadAttachment'])
            ->name('employees.attachment.download');
            
        Route::post('employees/{id}/transfer-leads', [EmployeeController::class, 'transferLeads'])
        ->name('employees.transfer-leads');

        // Supervisors
        Route::resource('supervisors', SupervisorController::class);
        Route::post('supervisors/{id}/password', [SupervisorController::class, 'changePassword'])
            ->name('supervisors.change-password');
        Route::post('supervisors/{id}/toggle-status', [SupervisorController::class, 'toggleStatus'])
            ->name('supervisors.toggle-status');
        Route::post('supervisors/transfer-representative', [SupervisorController::class, 'transferRepresentative'])
        ->name('supervisors.transfer-representative');
    Route::get('supervisors/{id}/representatives', [SupervisorController::class, 'getRepresentatives'])
        ->name('supervisors.representatives');
    Route::get('representatives/by-governorate/{governorateId}', [SupervisorController::class, 'getRepresentativesByGovernorate'])
        ->name('supervisors.representatives-by-governorate');
    Route::get('representatives/by-location/{locationId}', [SupervisorController::class, 'getRepresentativesByLocation'])
        ->name('supervisors.representatives-by-location');
    Route::get('supervisors/by-location/{locationId}', [SupervisorController::class, 'getSupervisorsByLocation'])
        ->name('supervisors.by-location');
    Route::get('supervisors/by-governorate/{governorateId}', [SupervisorController::class, 'getSupervisorsByGovernorate'])
        ->name('supervisors.by-governorate');


        // Supervisor Transfer Logs
        Route::get('supervisor-transfer-logs', [SupervisorTransferLogController::class, 'index'])
            ->name('supervisor-transfer-logs.index');

        // Permissions
        Route::resource('permissions', PermissionController::class);
    Route::get('permissions/sync/preview', [PermissionController::class, 'syncPreview'])->name('permissions.sync.preview');
    Route::post('permissions/sync', [PermissionController::class, 'sync'])->name('permissions.sync');
    Route::get('permissions/sync/test', [PermissionController::class, 'sync'])->name('permissions.sync.test');

    // Simple test route without authorization
    Route::get('test-sync', function() {
        $permissionService = app(\App\Services\PermissionService::class);
        $result = $permissionService->syncPermissions();
        return response()->json([
            'success' => true,
            'message' => "تم مزامنة الصلاحيات بنجاح. تم إنشاء {$result['created']} صلاحية جديدة وتحديث {$result['updated']} صلاحية.",
            'data' => $result
        ]);
    })->name('test.sync');
    Route::get('permissions/by-module', [PermissionController::class, 'byModule'])->name('permissions.by-module');
    Route::get('permissions/by-action', [PermissionController::class, 'byActionType'])->name('permissions.by-action');

        // Roles
        Route::resource('roles', RoleController::class);

        // Sources
        Route::resource('sources', SourceController::class);
        Route::post('sources/{id}/toggle-status', [SourceController::class, 'toggleStatus'])
            ->name('sources.toggle-status');

        // Employees
        Route::resource('employees', EmployeeController::class);
        Route::post('employees/{id}/password', [EmployeeController::class, 'changePassword'])
            ->name('employees.change-password');
        Route::post('employees/{id}/toggle-status', [EmployeeController::class, 'toggleStatus'])
            ->name('employees.toggle-status');
        Route::resource('reasons', ReasonController::class);

        // HR Management Routes
        Route::resource('work-schedules', WorkScheduleController::class);
        Route::post('work-schedules/{id}/toggle-status', [WorkScheduleController::class, 'toggleStatus'])
            ->name('work-schedules.toggle-status');

        Route::resource('leave-requests', LeaveRequestController::class);
        Route::post('leave-requests/{id}/approve', [LeaveRequestController::class, 'approve'])
            ->name('leave-requests.approve');
        Route::post('leave-requests/{id}/reject', [LeaveRequestController::class, 'reject'])
            ->name('leave-requests.reject');

    // Employee Targets - Specific routes must come BEFORE resource route
    Route::post('employee-targets/bulk-update', [EmployeeTargetController::class, 'bulkUpdate'])
        ->name('employee-targets.bulk-update');
    Route::post('employee-targets/generate-monthly', [EmployeeTargetController::class, 'generateMonthlyTargets'])
        ->name('employee-targets.generate-monthly');
    Route::post('employee-targets/refresh-achieved', [EmployeeTargetController::class, 'refreshAchievedFollowUps'])
        ->name('employee-targets.refresh-achieved');
    Route::get('employee-targets/export', [EmployeeTargetController::class, 'export'])
        ->name('employee-targets.export');
    Route::post('employee-targets/{id}/update-converted-leads', [EmployeeTargetController::class, 'updateConvertedLeads'])
        ->name('employee-targets.update-converted-leads');
        
        
    Route::get('employee-targets/salary', [EmployeeTargetController::class, 'showSalaryForm'])
    ->name('employee-targets.salary');

    Route::post('employee-targets/storeSalary', [EmployeeTargetController::class, 'storeSalary'])
    ->name('employee-targets.storeSalary');

    Route::resource('employee-targets', EmployeeTargetController::class);

    // Representative Targets Routes
    Route::post('representative-targets/bulk-update', [RepresentativeTargetController::class, 'bulkUpdate'])
        ->name('representative-targets.bulk-update');
    Route::post('representative-targets/process-bonuses', [RepresentativeTargetController::class, 'processBonuses'])
        ->name('representative-targets.process-bonuses');
    Route::resource('representative-targets', RepresentativeTargetController::class);
    
    
    
    Route::post('lead-targets/bulk-update', [LeadsTargetController::class, 'bulkUpdate'])
        ->name('lead-targets.bulk-update');
    Route::post('lead-targets/process-bonuses', [LeadsTargetController::class, 'processBonuses'])
        ->name('lead-targets.process-bonuses');
    Route::resource('lead-targets', LeadsTargetController::class);
    
    
    
    Route::get('advance-requests/export', [AdvanceRequestController::class, 'export'])->name('advance-requests.export');
    Route::get('/advance-requests/export-excel', [AdvanceRequestController::class, 'exportExcel'])
    ->name('advance-requests.export.excel');


        Route::resource('advance-requests', AdvanceRequestController::class);
        Route::post('advance-requests/{id}/approve', [AdvanceRequestController::class, 'approve'])
            ->name('advance-requests.approve');
        Route::post('advance-requests/{id}/reject', [AdvanceRequestController::class, 'reject'])
            ->name('advance-requests.reject');
        Route::post('advance-requests/calculate-installment', [AdvanceRequestController::class, 'calculateInstallment'])
            ->name('advance-requests.calculate-installment');
Route::get('advance-requests/{id}/receipt', [AdvanceRequestController::class, 'showReceipt'])
            ->name('advance-requests.receipt');
            
             Route::put('advance-requests/{id}/updateCode', [AdvanceRequestController::class, 'updateCode'])
            ->name('advance-requests.updateCode');

        Route::resource('delivery-deposits', DeliveryDepositController::class);
        Route::post('delivery-deposits/{id}/update-receipt', [DeliveryDepositController::class, 'updateReceipt'])
            ->name('delivery-deposits.update-receipt');
        Route::post('delivery-deposits/{id}/mark-delivered', [DeliveryDepositController::class, 'markAsDelivered'])
            ->name('delivery-deposits.mark-delivered');
        Route::post('delivery-deposits/{id}/mark-not-delivered', [DeliveryDepositController::class, 'markAsNotDelivered'])
            ->name('delivery-deposits.mark-not-delivered');
        Route::get('delivery-deposits/export', [DeliveryDepositController::class, 'export'])
            ->name('delivery-deposits.export');

        // Salary Records - Specific routes must come BEFORE resource route
        Route::post('salary-records/bulk-update', [SalaryRecordController::class, 'bulkUpdate'])
            ->name('salary-records.bulk-update');
        Route::post('salary-records/generate-monthly', [SalaryRecordController::class, 'generateMonthlySalaries'])
            ->name('salary-records.generate-monthly');
        Route::post('salary-records/{id}/mark-paid', [SalaryRecordController::class, 'markAsPaid'])
            ->name('salary-records.mark-paid');
        Route::post('salary-records/{id}/mark-unpaid', [SalaryRecordController::class, 'markAsUnpaid'])
            ->name('salary-records.mark-unpaid');
        Route::get('salary-records/export', [SalaryRecordController::class, 'export'])
            ->name('salary-records.export');

        Route::resource('salary-records', SalaryRecordController::class);

        // Salary Components - Specific routes must come BEFORE resource route
        Route::get('salary-components/export', [SalaryComponentController::class, 'export'])
            ->name('salary-components.export');
        Route::post('salary-components/bulk-update', [SalaryComponentController::class, 'bulkUpdate'])
            ->name('salary-components.bulk-update');
        Route::resource('salary-components', SalaryComponentController::class);
        
        
        
        
        //salary
        Route::get('salary-record1', [SalaryRecord1Controller::class,'index'])->name('salary-record1.index');
        Route::get('salary-record1/import', [SalaryRecord1Controller::class,'showImportForm'])->name('salary-record1.import.form');
        Route::post('salary-record1/import', [SalaryRecord1Controller::class,'import'])->name('salary-record1.import');
        Route::post('salary-record1/import-server', [SalaryRecord1Controller::class,'importFromServer'])->name('salary-record1.import.server');
        Route::delete('salary-record1/bulk-delete', [SalaryRecord1Controller::class, 'bulkDelete'])->name('salary-record1.bulk-delete');
        Route::delete('salary-record1/{record}', [SalaryRecord1Controller::class,'destroy'])->name('salary-record1.destroy');



        Route::get('resignation-requests/reports', [ResignationRequestController::class, 'report'])
        ->name('resignation-requests.reports');

        Route::resource('resignation-requests', ResignationRequestController::class);
        Route::post('resignation-requests/{id}/approve', [ResignationRequestController::class, 'approve'])
            ->name('resignation-requests.approve');
        Route::post('resignation-requests/{id}/reject', [ResignationRequestController::class, 'reject'])
            ->name('resignation-requests.reject');
        Route::get('resignation-requests/export', [ResignationRequestController::class, 'export'])
            ->name('resignation-requests.export');
        Route::post('resignation-requests/{id}/toggle-status', [ResignationRequestController::class, 'toggleStatus'])
        ->name('resignation-requests.toggle-status');
        Route::get('resignation-requests/{id}/notes', [ResignationRequestController::class, 'getNotes'])
        ->name('resignation-requests.notes');
        Route::post('resignation-requests/{id}/notes', [ResignationRequestController::class, 'storeNote'])
            ->name('resignation-requests.notes.store');
        Route::post('resignation-requests/{id}/update-status', [ResignationRequestController::class, 'updateStatus'])
        ->name('resignation-requests.update-status');

        // Leads
        Route::get('leads/import', [LeadController::class, 'showImportForm'])->name('leads.import.form');
        Route::post('leads/import', [LeadController::class, 'import'])->name('leads.import');
        Route::resource('leads', LeadController::class);
        Route::post('leads/{id}/followup', [LeadController::class, 'addFollowup'])
            ->name('leads.addFollowup');
        Route::post('leads/{id}/status', [LeadController::class, 'updateStatus'])
            ->name('leads.updateStatus');
        Route::get('leads-waiting', [LeadController::class, 'waiting'])->name('leads.waiting');
        Route::get('leads-search', [LeadController::class, 'search'])->name('leads.search');

        // Dashboards
        Route::get('dashboards/department-7', [DashboardController::class, 'departmentSeven'])
            ->name('dashboards.department7');
        Route::get('dashboards/my', [DashboardController::class, 'myDashboard'])
            ->name('dashboards.my');
        Route::get('dashboards/moderation', [DashboardController::class, 'moderation'])
            ->name('dashboards.moderation');    
            

        // Password Management
        Route::resource('passwords', PasswordController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);
        Route::post('passwords/{id}/reset', [PasswordController::class, 'resetUser'])->name('passwords.reset');
        Route::post('passwords/{id}/complete', [PasswordController::class, 'markAsCompleted'])->name('passwords.complete');
        Route::post('passwords/{id}/resend', [PasswordController::class, 'resend'])->name('passwords.resend');
        Route::get('passwords/statistics', [PasswordController::class, 'statistics'])->name('passwords.statistics');
        Route::get('passwords/test', function() {
            return view('passwords.test');
        })->name('passwords.test');

        // Expense Types
        Route::resource('expense-types', \App\Http\Controllers\ExpenseTypeController::class)->except(['show']);

        // Safes
        Route::get('safes', [\App\Http\Controllers\SafeController::class, 'index'])->name('safes.index');
        Route::post('safes', [\App\Http\Controllers\SafeController::class, 'store'])->name('safes.store');
        Route::post('safes/{safe}/deposit', [\App\Http\Controllers\SafeController::class, 'deposit'])->name('safes.deposit');
        Route::post('safes/{safe}/withdraw', [\App\Http\Controllers\SafeController::class, 'withdraw'])->name('safes.withdraw');

        // Expenses
        Route::get('expenses', [\App\Http\Controllers\ExpenseController::class, 'index'])->name('expenses.index');
        Route::post('expenses', [\App\Http\Controllers\ExpenseController::class, 'store'])->name('expenses.store');

        // Reports
        Route::get('reports/revenue', [\App\Http\Controllers\ReportController::class, 'revenue'])->name('reports.revenue');

        // Trainings
        Route::get('trainings', [\App\Http\Controllers\TrainingController::class, 'index'])->name('trainings.index');
        Route::put('trainings/{training}', [\App\Http\Controllers\TrainingController::class, 'update'])->name('trainings.update');

        // Sliders
        Route::get('sliders', [\App\Http\Controllers\SliderController::class, 'index'])->name('sliders.index');
        Route::post('sliders', [\App\Http\Controllers\SliderController::class, 'store'])->name('sliders.store');
        Route::put('sliders/{slider}', [\App\Http\Controllers\SliderController::class, 'update'])->name('sliders.update');
        Route::delete('sliders/{slider}', [\App\Http\Controllers\SliderController::class, 'destroy'])->name('sliders.destroy');

        // Admins
        Route::get('admins', [\App\Http\Controllers\AdminController::class, 'index'])->name('admins.index');
        Route::post('admins', [\App\Http\Controllers\AdminController::class, 'store'])->name('admins.store');
        Route::put('admins/{admin}', [\App\Http\Controllers\AdminController::class, 'update'])->name('admins.update');
        Route::delete('admins/{admin}', [\App\Http\Controllers\AdminController::class, 'destroy'])->name('admins.destroy');
        
         //support
        Route::resource('supports', SupportController::class);
        Route::post('supports/{support}/reply', [SupportController::class, 'reply'])->name('supports.reply');
        Route::post('supports/{support}/close', [SupportController::class, 'close'])->name('supports.close');
        Route::post('supports/{support}/replies', [SupportReplyController::class, 'store'])
        ->name('supports.replies.store');
        
        
        //advertisers
        Route::resource('advertisers', \App\Http\Controllers\AdvertiserController::class);
        
        
        //devices phone
        Route::resource('devices', DeviceController::class);
        
        //training sessions
        Route::resource('training_sessions', TrainingSessionController::class);
        Route::post('training_sessions/{id}/toggle-status', [TrainingSessionController::class, 'toggleStatus'])
            ->name('training_sessions.toggle-status');
            Route::post('training_sessions/{id}/activeResign', [TrainingSessionController::class, 'activeResigne'])
        ->name('training_sessions.activeResigne');
        
        Route::post('training_sessions/{id}/postpone', [TrainingSessionController::class, 'postpone'])
        ->name('training_sessions.postpone');
        Route::get('training_sessions/{id}/postpone-history', [TrainingSessionController::class, 'postponeHistory'])
        ->name('training_sessions.postpone-history');
            
        Route::post('training_sessions/{id}/start-representative', [TrainingSessionController::class, 'startRealRepresentative'])
        ->name('training_sessions.startRealRepresentative');
        Route::post('training_sessions/{id}/noLocation', [TrainingSessionController::class, 'noLocation'])
        ->name('training_sessions.noLocation');
        
        
        Route::get('work_starts', [WorkStartController::class, 'index'])->name('work_starts.index');
        Route::post('work_starts/{id}/toggle-status', [WorkStartController::class, 'toggleStatus'])
        ->name('work_starts.toggle-status');
        Route::post('work_starts/{id}/start-representative', [WorkStartController::class, 'startRealRepresentative'])
            ->name('work_starts.startRealRepresentative');
        Route::post('work_starts/{id}/followup', [WorkStartController::class, 'followup'])
        ->name('work_starts.followup');
        Route::post('work_starts/{id}/postpone', [WorkStartController::class, 'postpone'])
        ->name('work_starts.postpone');
        Route::get('work_starts/{id}/postpone-history', [WorkStartController::class, 'postponeHistory'])
        ->name('work_starts.postpone-history');
        

         Route::get('/get-locations/{governorate_id}', function($governorate_id) {
            return \App\Models\Location::where('governorate_id', $governorate_id)->get();
        });
        
        
        Route::get('/waiting-representatives', [WaitingRepresentativeController::class, 'index'])
        ->name('waiting-representatives.index');

        Route::post('waiting-representatives/{id}/start-representative', [WaitingRepresentativeController::class, 'startRealRepresentative'])
            ->name('waiting-representatives.startRealRepresentative');
    
        Route::post('waiting-representatives/{id}/change-location', [WaitingRepresentativeController::class, 'changeLocation'])
            ->name('waiting-representatives.changeLocation');
            
        Route::post('waiting-representatives/{id}/resign', [WaitingRepresentativeController::class, 'resign'])
            ->name('waiting-representatives.resign');
        Route::post('waiting-representatives/{id}/followup', [WaitingRepresentativeController::class, 'followupStore'])
          ->name('waiting-representatives.followup');
        Route::get('waiting-representatives/{id}/followup-history', [WaitingRepresentativeController::class, 'followupHistory'])
         ->name('waiting-representatives.followup-history');



        Route::get('debts', [DebtController::class, 'index'])->name('debts.index');
        Route::get('debts/index2', [DebtController::class, 'index2'])->name('debts.index2');
        Route::post('debts/{debt}/toggle-status', [DebtController::class, 'toggleStatus'])
            ->name('debts.toggle-status');
        Route::post('debts-sheets', [DebtController::class, 'storeSheet'])->name('debts-sheets.store');
        Route::put('debts-sheets/{debtSheet}', [DebtController::class, 'updateSheet'])->name('debts-sheets.update');
        Route::delete('debts-sheets/{debtSheet}', [DebtController::class, 'destroySheet'])->name('debts-sheets.destroy');
        Route::post('debts-sheets/import', [DebtController::class, 'importSheet'])->name('debts-sheets.import');
            
            
            
        Route::get('/representatives/all', function() {
            return Representative::where('is_active', 1)
                ->with(['company','governorate','location'])
                ->get();
        });



            
        
        // Settings - Mapped to existing controllers for submenu items
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('company', [CompanyController::class, 'index'])->name('company');
            Route::get('department', [DepartmentController::class, 'index'])->name('department');
            Route::get('roles', [RoleController::class, 'index'])->name('roles');
            Route::get('location', [LocationController::class, 'index'])->name('location');
            Route::get('government', [GovernorateController::class, 'index'])->name('government');
        });

        // Admin Notifications Management
        Route::prefix('admin-notifications')->name('admin-notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::post('send-to-all', [NotificationController::class, 'sendToAll'])->name('send-to-all');
            Route::post('send-to-user-type', [NotificationController::class, 'sendToUserType'])->name('send-to-user-type');
            Route::post('send-to-users', [NotificationController::class, 'sendToUsers'])->name('send-to-users');
            Route::get('users', [NotificationController::class, 'getUsers'])->name('users');
            Route::get('stats', [NotificationController::class, 'getStats'])->name('stats');
            Route::delete('{id}', [NotificationController::class, 'destroy'])->name('destroy');
            Route::post('bulk-delete', [NotificationController::class, 'bulkDelete'])->name('bulk-delete');
        });
    });



