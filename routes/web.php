<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientSearchController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\UserController;
use App\Models\Project;
use App\Http\Controllers\ProjectLocationController;
use App\Http\Controllers\Admin\DepartmentReportController as AdminDepartmentReportController;
use App\Http\Controllers\Admin\DepartmentOversightController;
use App\Http\Controllers\Admin\AdminSystemReportController;
use App\Http\Controllers\Admin\AutoPenaltyController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\JournalEntryController;
use App\Http\Controllers\FinancialReportController;
use App\Http\Controllers\FinancialInvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\SystemSettingsController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\LoginActivityController;
use App\Http\Controllers\SystemMonitoringController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\ClientPortalController;
use App\Http\Controllers\ClientServiceReportController;
use App\Http\Controllers\ClientSupportTicketController;
use App\Http\Controllers\ClientWebsiteIssueController;
use App\Http\Controllers\ClientMeetingRequestController;
use App\Http\Controllers\ClientAuthController;
use App\Http\Controllers\Admin\ClientAccountController;
use App\Http\Controllers\Admin\ClientWebsiteIssueController as AdminClientWebsiteIssueController;
use App\Http\Controllers\Admin\ClientMeetingRequestController as AdminClientMeetingRequestController;
use App\Http\Controllers\Admin\ClientSharedDocumentController as AdminClientSharedDocumentController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\Support\ContactRequestController as SupportContactRequestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route مخصص لعرض الملفات من storage/app/public (صور، PDF، إلخ)
| يجب أن يكون قبل أي routes أخرى لتفادي اعتراض الطلبات
|--------------------------------------------------------------------------
*/
Route::get('/storage/{path}', function ($path) {
    try {
        $path = str_replace('..', '', $path);
        $path = ltrim($path, '/');

        $basePath = storage_path('app/public');
        $filePath = $basePath . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        $filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);

        \Log::info('Storage route accessed', [
            'requested_path' => $path,
            'file_path' => $filePath,
            'file_exists' => @file_exists($filePath),
            'is_file' => @is_file($filePath),
            'storage_path' => $basePath,
        ]);

        if (!@file_exists($filePath)) {
            \Log::warning('Storage file not found', ['requested_path' => $path, 'file_path' => $filePath]);
            abort(404, 'File not found');
        }

        if (!@is_file($filePath)) {
            \Log::warning('Storage path is not a file', ['requested_path' => $path, 'file_path' => $filePath]);
            abort(404, 'Not a file');
        }

        $realPath = @realpath($filePath) ?: $filePath;
        $allowedPath = @realpath($basePath) ?: $basePath;
        $normalizedRealPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $realPath);
        $normalizedAllowedPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $allowedPath);

        if (strpos($normalizedRealPath, $normalizedAllowedPath) !== 0) {
            \Log::warning('Storage access denied - path outside allowed directory', [
                'requested_path' => $path, 'file_path' => $filePath, 'real_path' => $realPath, 'allowed_path' => $allowedPath,
            ]);
            abort(404, 'Access denied');
        }

        if (!@is_readable($realPath)) {
            \Log::warning('Storage file is not readable', ['requested_path' => $path, 'real_path' => $realPath]);
            abort(403, 'File not readable');
        }

        $mimeType = @mime_content_type($realPath);
        if (!$mimeType) {
            $extension = strtolower(pathinfo($realPath, PATHINFO_EXTENSION));
            $mimeTypes = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                'svg' => 'image/svg+xml',
                'pdf' => 'application/pdf',
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ];
            $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
        }

        $headers = [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=31536000',
        ];
        if ($mimeType === 'application/pdf') {
            $headers['Content-Disposition'] = 'inline; filename="' . basename($realPath) . '"';
        }

        \Log::info('Storage file served successfully', [
            'requested_path' => $path, 'real_path' => $realPath, 'mime_type' => $mimeType,
        ]);

        return response()->file($realPath, $headers);
    } catch (\Exception $e) {
        \Log::error('Storage route error', [
            'path' => $path ?? 'unknown',
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        abort(404, 'File not found');
    }
})->where('path', '.*')->name('storage.file')->middleware('web');

// مشاركة موقع المشروع — صفحة عامة بدون تسجيل دخول
Route::get('/locate/project/{project}', [ProjectLocationController::class, 'show'])->name('public.project.locate');
Route::get('/locate/project/{project}/viewer', [ProjectLocationController::class, 'viewer'])->name('public.project.locate.viewer');

// الصفحة الرئيسية = تسجيل الدخول مباشرة (لا موقع خارجي)
Route::get('/', function () {
    if (auth()->check() && !session('verification_pending')) {
        return redirect(auth()->user()->homeRoute());
    }
    return redirect()->route('login');
})->name('home');

// =========================
// Client Auth (تسجيل دخول العملاء)
// =========================
Route::prefix('client')->name('client.')->group(function () {
    Route::get('login', [ClientAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [ClientAuthController::class, 'login'])->name('login.submit');
    Route::post('logout', [ClientAuthController::class, 'logout'])->name('logout');
});

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'verified.code', 'redirect.crm'])
    ->name('dashboard');

require __DIR__.'/crm.php';
require __DIR__.'/marketing.php';

// Profile routes
Route::middleware(['auth', 'verified', 'verified.code'])->group(function () {
    Route::get('api/clients/search', ClientSearchController::class)->name('clients.search');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/picture', [ProfileController::class, 'deleteProfilePicture'])->name('profile.delete-picture');
});

// All authenticated routes
Route::middleware(['auth', 'verified', 'verified.code', 'crm.only', 'marketing.only'])->group(function () {
    // =========================
    // Client Portal (بوابة العميل)
    // =========================
    // (تم نقل بوابة العميل إلى auth:client بدلاً من role:client)

    // Administration - Users
    Route::get('users', [UserController::class, 'index'])->name('users.index')->middleware('permission:view-users');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create')->middleware('permission:create-users');
    Route::post('users', [UserController::class, 'store'])->name('users.store')->middleware('permission:create-users');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show')->middleware('permission:view-users');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('permission:edit-users');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('permission:edit-users');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:delete-users');
    
    // Administration - Departments
    Route::get('departments', [DepartmentController::class, 'index'])->name('departments.index')->middleware('permission:view-departments');
    Route::get('departments/create', [DepartmentController::class, 'create'])->name('departments.create')->middleware('permission:create-departments');
    Route::post('departments', [DepartmentController::class, 'store'])->name('departments.store')->middleware('permission:create-departments');
    Route::get('departments/{department}', [DepartmentController::class, 'show'])->name('departments.show')->middleware('permission:view-departments');
    Route::get('departments/{department}/edit', [DepartmentController::class, 'edit'])->name('departments.edit')->middleware('permission:edit-departments');
    Route::put('departments/{department}', [DepartmentController::class, 'update'])->name('departments.update')->middleware('permission:edit-departments');
    Route::delete('departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy')->middleware('permission:delete-departments');
    
    // Login Activity Logs
    Route::get('login-activity', [LoginActivityController::class, 'index'])->name('login-activity.index')->middleware('permission:view-users|manage-roles');
    
    // System Monitoring
    Route::get('system-monitoring', [SystemMonitoringController::class, 'index'])->name('system-monitoring.index')->middleware('permission:view-users|manage-roles');
    
    // الأدوار والصلاحيات
    Route::prefix('roles')->name('roles.')->middleware('permission:manage-roles')->group(function () {
        Route::get('/', [App\Http\Controllers\RoleController::class, 'index'])->name('index');
        Route::get('/user/{user}/permissions', [App\Http\Controllers\RoleController::class, 'userPermissions'])->name('user-permissions');
        Route::post('/user/{user}/assign-role', [App\Http\Controllers\RoleController::class, 'assignRole'])->name('assign-role');
        Route::post('/user/{user}/assign-permissions', [App\Http\Controllers\RoleController::class, 'assignCustomPermissions'])->name('assign-permissions');
        Route::post('/role/{role}/update-permissions', [App\Http\Controllers\RoleController::class, 'updateRolePermissions'])->name('update-permissions');
    });
    
    // System Settings
    Route::prefix('system-settings')->name('system-settings.')->middleware('permission:view-settings')->group(function () {
        Route::get('/', [SystemSettingsController::class, 'index'])->name('index');
        Route::put('/update', [SystemSettingsController::class, 'update'])->name('update')->middleware('permission:edit-settings');
        Route::post('/reset', [SystemSettingsController::class, 'reset'])->name('reset')->middleware('permission:edit-settings');
    });
    
    // Reports
    Route::prefix('reports')->name('reports.')->middleware('permission:view-reports')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/employees', [ReportController::class, 'employees'])->name('employees')->middleware('permission:view-employees');
        Route::get('/employees/print', [ReportController::class, 'employeesPrint'])->name('employees.print')->middleware('permission:view-employees');
        Route::get('/attendance', [ReportController::class, 'attendance'])->name('attendance')->middleware('permission:view-attendance');
        Route::get('/attendance/print', [ReportController::class, 'attendancePrint'])->name('attendance.print')->middleware('permission:view-attendance');
        Route::get('/sales', [ReportController::class, 'sales'])->name('sales')->middleware('permission:view-sales');
        Route::get('/sales/print', [ReportController::class, 'salesPrint'])->name('sales.print')->middleware('permission:view-sales');
        Route::get('/salaries', [ReportController::class, 'salaries'])->name('salaries')->middleware('permission:view-salaries');
        Route::get('/salaries/print', [ReportController::class, 'salariesPrint'])->name('salaries.print')->middleware('permission:view-salaries');
        Route::get('/departments', [ReportController::class, 'departments'])->name('departments')->middleware('permission:view-departments');
        Route::get('/departments/print', [ReportController::class, 'departmentsPrint'])->name('departments.print')->middleware('permission:view-departments');
        Route::get('/performance', [ReportController::class, 'performance'])->name('performance');
    });
    
    // HR Department - Employees
    Route::get('employees', [EmployeeController::class, 'index'])->name('employees.index')->middleware('permission:view-employees');
    Route::get('employees/create', [EmployeeController::class, 'create'])->name('employees.create')->middleware('permission:create-employees');
    Route::post('employees', [EmployeeController::class, 'store'])->name('employees.store')->middleware('permission:create-employees');
    Route::get('employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show')->middleware('permission:view-employees');
    Route::get('employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit')->middleware('permission:edit-employees');
    Route::put('employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update')->middleware('permission:edit-employees');
    Route::delete('employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy')->middleware('permission:delete-employees');
    
    // Attendance routes
    Route::get('attendances', [AttendanceController::class, 'index'])->name('attendances.index')->middleware('permission:view-attendance');
    Route::get('attendances/create', [AttendanceController::class, 'create'])->name('attendances.create')->middleware('permission:create-attendance');
    Route::post('attendances', [AttendanceController::class, 'store'])->name('attendances.store')->middleware('permission:create-attendance');
    Route::get('attendances/{attendance}', [AttendanceController::class, 'show'])->name('attendances.show')->middleware('permission:view-attendance');
    Route::get('attendances/{attendance}/edit', [AttendanceController::class, 'edit'])->name('attendances.edit')->middleware('permission:edit-attendance');
    Route::put('attendances/{attendance}', [AttendanceController::class, 'update'])->name('attendances.update')->middleware('permission:edit-attendance');
    Route::delete('attendances/{attendance}', [AttendanceController::class, 'destroy'])->name('attendances.destroy')->middleware('permission:delete-attendance');
    Route::post('attendances/check-in', [AttendanceController::class, 'checkIn'])->name('attendances.check-in');
    Route::post('attendances/check-out', [AttendanceController::class, 'checkOut'])->name('attendances.check-out');
    Route::post('attendances/start-break', [AttendanceController::class, 'startBreak'])->name('attendances.start-break');
    Route::post('attendances/end-break', [AttendanceController::class, 'endBreak'])->name('attendances.end-break');
    Route::get('attendances/current-work-time', [AttendanceController::class, 'getCurrentWorkTime'])->name('attendances.current-work-time');
    Route::post('attendances/auto-check-out', [AttendanceController::class, 'autoCheckOut'])->name('attendances.auto-check-out');
    
    // Leave routes
    Route::get('leaves', [LeaveController::class, 'index'])->name('leaves.index')->middleware('permission:view-leaves');
    Route::get('leaves/create', [LeaveController::class, 'create'])->name('leaves.create')->middleware('permission:create-leaves');
    Route::post('leaves', [LeaveController::class, 'store'])->name('leaves.store')->middleware('permission:create-leaves');
    Route::get('leaves/{leave}', [LeaveController::class, 'show'])->name('leaves.show')->middleware('permission:view-leaves');
    Route::get('leaves/{leave}/edit', [LeaveController::class, 'edit'])->name('leaves.edit')->middleware('permission:edit-leaves');
    Route::put('leaves/{leave}', [LeaveController::class, 'update'])->name('leaves.update')->middleware('permission:edit-leaves');
    Route::delete('leaves/{leave}', [LeaveController::class, 'destroy'])->name('leaves.destroy')->middleware('permission:delete-leaves');
    Route::post('leaves/{leave}/approve', [LeaveController::class, 'approve'])->name('leaves.approve')->middleware('permission:approve-leaves');
    Route::post('leaves/{leave}/reject', [LeaveController::class, 'reject'])->name('leaves.reject')->middleware('permission:approve-leaves');
    
    // Salary routes
    Route::get('salaries', [SalaryController::class, 'index'])->name('salaries.index')->middleware('permission:view-salaries');
    Route::get('salaries/create', [SalaryController::class, 'create'])->name('salaries.create')->middleware('permission:create-salaries');
    Route::post('salaries', [SalaryController::class, 'store'])->name('salaries.store')->middleware('permission:create-salaries');
    Route::get('salaries/{salary}', [SalaryController::class, 'show'])->name('salaries.show')->middleware('permission:view-salaries');
    Route::get('salaries/{salary}/edit', [SalaryController::class, 'edit'])->name('salaries.edit')->middleware('permission:edit-salaries');
    Route::put('salaries/{salary}', [SalaryController::class, 'update'])->name('salaries.update')->middleware('permission:edit-salaries');
    Route::delete('salaries/{salary}', [SalaryController::class, 'destroy'])->name('salaries.destroy')->middleware('permission:delete-salaries');
    Route::post('salaries/generate', [SalaryController::class, 'generateSalaries'])->name('salaries.generate')->middleware('permission:edit-salaries');
    Route::post('salaries/{salary}/approve', [SalaryController::class, 'approve'])->name('salaries.approve')->middleware('permission:approve-salaries');
    Route::post('salaries/{salary}/mark-paid', [SalaryController::class, 'markAsPaid'])->name('salaries.mark-paid')->middleware('permission:edit-salaries');
    
    // المشاريع العقارية — مسار موحّد عبر CRM (إعادة توجيه الروابط القديمة)
    Route::middleware('permission:view-own-projects|view-all-projects')->group(function () {
        Route::get('projects', fn () => redirect()->route('crm.projects.index'))->name('projects.index');
        Route::get('projects/create', fn () => redirect()->route('crm.projects.create'))->name('projects.create')->middleware('permission:create-projects');
        Route::get('projects/{project}', fn (Project $project) => redirect()->route('crm.projects.show', $project))->name('projects.show');
        Route::get('projects/{project}/edit', fn (Project $project) => redirect()->route('crm.projects.edit', $project))->name('projects.edit')->middleware('permission:edit-projects');
    });
    
    // Admin: System reports hub (CRM + HR + operations)
    Route::prefix('admin/system-reports')->name('admin.system-reports.')->middleware('permission:view-reports')->group(function () {
        Route::get('/', [AdminSystemReportController::class, 'index'])->name('index');
        Route::get('/{report}/export', [AdminSystemReportController::class, 'export'])->name('export')->where('report', '[a-z_]+');
        Route::get('/{report}', [AdminSystemReportController::class, 'show'])->name('show')->where('report', '[a-z_]+');
    });

    // Admin oversight: Department reports
    Route::get('admin/department-reports', [AdminDepartmentReportController::class, 'index'])
        ->name('admin.department-reports.index')
        ->middleware('permission:view-reports');
    Route::get('admin/department-reports/{departmentReport}', [AdminDepartmentReportController::class, 'show'])
        ->name('admin.department-reports.show')
        ->middleware('permission:view-reports');

    Route::get('admin/department-oversight', [DepartmentOversightController::class, 'index'])
        ->name('admin.department-oversight.index')
        ->middleware('permission:view-reports|view-departments');

    Route::prefix('admin/auto-penalties')->name('admin.auto-penalties.')->middleware('permission:view-reports')->group(function () {
        Route::get('/', [AutoPenaltyController::class, 'index'])->name('index');
        Route::post('/process', [AutoPenaltyController::class, 'processNow'])
            ->name('process')
            ->middleware('permission:edit-settings');
        Route::post('/', [AutoPenaltyController::class, 'store'])
            ->name('store')
            ->middleware('permission:edit-settings');
        Route::put('/{autoPenaltyRule}', [AutoPenaltyController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit-settings');
        Route::patch('/{autoPenaltyRule}/toggle', [AutoPenaltyController::class, 'toggle'])
            ->name('toggle')
            ->middleware('permission:edit-settings');
        Route::delete('/{autoPenaltyRule}', [AutoPenaltyController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:edit-settings');
    });
    
    // Sales & Marketing - Clients
    Route::get('clients', [ClientController::class, 'index'])->name('clients.index')->middleware('permission:view-clients');
    Route::get('clients/create', [ClientController::class, 'create'])->name('clients.create')->middleware('permission:create-clients');
    Route::post('clients', [ClientController::class, 'store'])->name('clients.store')->middleware('permission:create-clients');
    Route::get('clients/{client}', [ClientController::class, 'show'])->name('clients.show')->middleware('permission:view-clients');
    Route::get('clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit')->middleware('permission:edit-clients');
    Route::put('clients/{client}', [ClientController::class, 'update'])->name('clients.update')->middleware('permission:edit-clients');
    Route::delete('clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy')->middleware('permission:delete-clients');
    Route::post('clients/{client}/service-reports', [ClientServiceReportController::class, 'store'])->name('clients.service-reports.store')->middleware('permission:edit-clients');
    Route::get('clients/{client}/service-reports/{serviceReport}/download', [ClientServiceReportController::class, 'download'])->name('clients.service-reports.download')->middleware('permission:view-clients');
    Route::delete('clients/{client}/service-reports/{serviceReport}', [ClientServiceReportController::class, 'destroy'])->name('clients.service-reports.destroy')->middleware('permission:edit-clients');

    Route::get('client-service-reports', [ClientServiceReportController::class, 'index'])->name('client-service-reports.index')->middleware('permission:view-clients');
    Route::get('client-service-reports/create', [ClientServiceReportController::class, 'create'])->name('client-service-reports.create')->middleware('permission:edit-clients');
    Route::post('client-service-reports', [ClientServiceReportController::class, 'storeFromAdmin'])->name('client-service-reports.store')->middleware('permission:edit-clients');
    Route::delete('client-service-reports/{serviceReport}', [ClientServiceReportController::class, 'destroyStandalone'])->name('client-service-reports.destroy')->middleware('permission:edit-clients');

    Route::get('client-shared-documents', [AdminClientSharedDocumentController::class, 'index'])->name('client-shared-documents.index')->middleware('permission:view-clients');
    Route::get('client-shared-documents/create', [AdminClientSharedDocumentController::class, 'create'])->name('client-shared-documents.create')->middleware('permission:edit-clients');
    Route::post('client-shared-documents', [AdminClientSharedDocumentController::class, 'store'])->name('client-shared-documents.store')->middleware('permission:edit-clients');
    Route::get('client-shared-documents/{sharedDocument}/download', [AdminClientSharedDocumentController::class, 'download'])->name('client-shared-documents.download')->middleware('permission:view-clients');
    Route::delete('client-shared-documents/{sharedDocument}', [AdminClientSharedDocumentController::class, 'destroy'])->name('client-shared-documents.destroy')->middleware('permission:edit-clients');

    // Client Accounts (بوابة العملاء) - داخل لوحة الأدمن
    Route::prefix('client-accounts')->name('client-accounts.')->middleware('permission:view-clients')->group(function () {
        Route::get('/', [ClientAccountController::class, 'index'])->name('index');
        Route::get('/create', [ClientAccountController::class, 'create'])->name('create')->middleware('permission:create-clients');
        Route::post('/', [ClientAccountController::class, 'store'])->name('store')->middleware('permission:create-clients');
        Route::get('/{clientAccount}/edit', [ClientAccountController::class, 'edit'])->name('edit')->middleware('permission:edit-clients');
        Route::put('/{clientAccount}', [ClientAccountController::class, 'update'])->name('update')->middleware('permission:edit-clients');
        Route::delete('/{clientAccount}', [ClientAccountController::class, 'destroy'])->name('destroy')->middleware('permission:delete-clients');
    });
    
    // Sales & Marketing - Sales
    Route::get('sales', [SaleController::class, 'index'])->name('sales.index')->middleware('permission:view-sales');
    Route::get('sales/create', [SaleController::class, 'create'])->name('sales.create')->middleware('permission:create-sales');
    Route::post('sales', [SaleController::class, 'store'])->name('sales.store')->middleware('permission:create-sales');
    Route::get('sales/{sale}', [SaleController::class, 'show'])->name('sales.show')->middleware('permission:view-sales');
    Route::get('sales/{sale}/edit', [SaleController::class, 'edit'])->name('sales.edit')->middleware('permission:edit-sales');
    Route::put('sales/{sale}', [SaleController::class, 'update'])->name('sales.update')->middleware('permission:edit-sales');
    Route::delete('sales/{sale}', [SaleController::class, 'destroy'])->name('sales.destroy')->middleware('permission:delete-sales');
    Route::post('sales/{sale}/update-stage', [SaleController::class, 'updateStage'])->name('sales.update-stage')->middleware('permission:edit-sales');
    Route::post('sales/{sale}/generate-invoice', [SaleController::class, 'generateInvoice'])->name('sales.generate-invoice')->middleware('permission:edit-sales');
    Route::get('sales/statistics/data', [SaleController::class, 'getStatistics'])->name('sales.statistics')->middleware('permission:view-sales');
    
    // Support - Tickets
    Route::get('tickets', [TicketController::class, 'index'])->name('tickets.index')->middleware('permission:view-tickets');
    Route::get('tickets/create', [TicketController::class, 'create'])->name('tickets.create')->middleware('permission:create-tickets');
    Route::post('tickets', [TicketController::class, 'store'])->name('tickets.store')->middleware('permission:create-tickets');
    Route::get('tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show')->middleware('permission:view-tickets');
    Route::get('tickets/{ticket}/edit', [TicketController::class, 'edit'])->name('tickets.edit')->middleware('permission:edit-tickets');
    Route::put('tickets/{ticket}', [TicketController::class, 'update'])->name('tickets.update')->middleware('permission:edit-tickets');
    Route::delete('tickets/{ticket}', [TicketController::class, 'destroy'])->name('tickets.destroy')->middleware('permission:delete-tickets');

    // Support - Contact requests / Consultation bookings
    Route::get('support/contact-requests', [SupportContactRequestController::class, 'index'])->name('support.contact-requests.index')->middleware('permission:view-tickets');
    Route::get('support/contact-requests/{contactRequest}', [SupportContactRequestController::class, 'show'])->name('support.contact-requests.show')->middleware('permission:view-tickets');
    Route::patch('support/contact-requests/{contactRequest}/status', [SupportContactRequestController::class, 'updateStatus'])->name('support.contact-requests.status')->middleware('permission:edit-tickets');

    Route::get('client-website-issues', [AdminClientWebsiteIssueController::class, 'index'])->name('client-website-issues.index')->middleware('permission:view-tickets');
    Route::get('client-website-issues/{websiteIssue}/files/{index}', [AdminClientWebsiteIssueController::class, 'file'])->name('client-website-issues.file')->middleware('permission:view-tickets')->whereNumber('index');
    Route::get('client-website-issues/{websiteIssue}', [AdminClientWebsiteIssueController::class, 'show'])->name('client-website-issues.show')->middleware('permission:view-tickets');
    Route::put('client-website-issues/{websiteIssue}', [AdminClientWebsiteIssueController::class, 'update'])->name('client-website-issues.update')->middleware('permission:edit-tickets');

    Route::get('client-meeting-requests', [AdminClientMeetingRequestController::class, 'index'])->name('client-meeting-requests.index')->middleware('permission:view-tickets');
    Route::get('client-meeting-requests/{meetingRequest}', [AdminClientMeetingRequestController::class, 'show'])->name('client-meeting-requests.show')->middleware('permission:view-tickets');
    Route::put('client-meeting-requests/{meetingRequest}', [AdminClientMeetingRequestController::class, 'update'])->name('client-meeting-requests.update')->middleware('permission:edit-tickets');
    
    // Finance & Accounting
    Route::prefix('accounting')->name('accounting.')->middleware('permission:view-finance')->group(function () {
        // لوحة التحكم المالية
        Route::get('/', [AccountingController::class, 'index'])->name('index');
        
        // إدارة الحسابات
        Route::get('/accounts', [AccountingController::class, 'accounts'])->name('accounts');
        Route::post('/accounts', [AccountingController::class, 'createAccount'])->name('accounts.create')->middleware('permission:edit-finance');
        Route::get('/accounts/{account}/edit', [AccountingController::class, 'editAccount'])->name('accounts.edit')->middleware('permission:edit-finance');
        Route::put('/accounts/{account}', [AccountingController::class, 'updateAccount'])->name('accounts.update')->middleware('permission:edit-finance');
        Route::delete('/accounts/{account}', [AccountingController::class, 'deleteAccount'])->name('accounts.delete')->middleware('permission:edit-finance');
        
        // القيود المحاسبية
        Route::get('/journal-entries', [JournalEntryController::class, 'index'])->name('journal-entries');
        Route::get('/journal-entries/create', [JournalEntryController::class, 'create'])->name('journal-entries.create')->middleware('permission:edit-finance');
        Route::post('/journal-entries', [JournalEntryController::class, 'store'])->name('journal-entries.store')->middleware('permission:edit-finance');
        Route::get('/journal-entries/{journalEntry}', [JournalEntryController::class, 'show'])->name('journal-entries.show');
        Route::get('/journal-entries/{journalEntry}/edit', [JournalEntryController::class, 'edit'])->name('journal-entries.edit')->middleware('permission:edit-finance');
        Route::put('/journal-entries/{journalEntry}', [JournalEntryController::class, 'update'])->name('journal-entries.update')->middleware('permission:edit-finance');
        Route::delete('/journal-entries/{journalEntry}', [JournalEntryController::class, 'destroy'])->name('journal-entries.destroy')->middleware('permission:edit-finance');
        Route::post('/journal-entries/{journalEntry}/approve', [JournalEntryController::class, 'approve'])->name('journal-entries.approve')->middleware('permission:edit-finance');
        Route::post('/journal-entries/{journalEntry}/post', [JournalEntryController::class, 'post'])->name('journal-entries.post')->middleware('permission:edit-finance');
        
        // التقارير المالية
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [FinancialReportController::class, 'index'])->name('index');
            Route::get('/balance-sheet', [FinancialReportController::class, 'balanceSheet'])->name('balance-sheet');
            Route::get('/income-statement', [FinancialReportController::class, 'incomeStatement'])->name('income-statement');
            Route::get('/cash-flow', [FinancialReportController::class, 'cashFlow'])->name('cash-flow');
            Route::get('/trial-balance', [FinancialReportController::class, 'trialBalance'])->name('trial-balance');
        });
    });
    
    // الفواتير والمدفوعات والمصروفات - Financial Invoices
    Route::get('financial-invoices', [FinancialInvoiceController::class, 'index'])->name('financial-invoices.index')->middleware('permission:view-finance');
    Route::get('financial-invoices/create', [FinancialInvoiceController::class, 'create'])->name('financial-invoices.create')->middleware('permission:create-finance');
    Route::post('financial-invoices', [FinancialInvoiceController::class, 'store'])->name('financial-invoices.store')->middleware('permission:create-finance');
    Route::get('financial-invoices/{financialInvoice}', [FinancialInvoiceController::class, 'show'])->name('financial-invoices.show')->middleware('permission:view-finance');
    Route::get('financial-invoices/{financialInvoice}/edit', [FinancialInvoiceController::class, 'edit'])->name('financial-invoices.edit')->middleware('permission:edit-finance');
    Route::put('financial-invoices/{financialInvoice}', [FinancialInvoiceController::class, 'update'])->name('financial-invoices.update')->middleware('permission:edit-finance');
    Route::delete('financial-invoices/{financialInvoice}', [FinancialInvoiceController::class, 'destroy'])->name('financial-invoices.destroy')->middleware('permission:delete-finance');
    Route::post('financial-invoices/{invoice}/mark-as-sent', [FinancialInvoiceController::class, 'markAsSent'])->name('financial-invoices.mark-as-sent')->middleware('permission:edit-finance');
    Route::post('financial-invoices/{invoice}/mark-as-paid', [FinancialInvoiceController::class, 'markAsPaid'])->name('financial-invoices.mark-as-paid')->middleware('permission:edit-finance');
    
    // Payments
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index')->middleware('permission:view-finance');
    Route::get('payments/create', [PaymentController::class, 'create'])->name('payments.create')->middleware('permission:create-finance');
    Route::post('payments', [PaymentController::class, 'store'])->name('payments.store')->middleware('permission:create-finance');
    Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show')->middleware('permission:view-finance');
    Route::get('payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit')->middleware('permission:edit-finance');
    Route::put('payments/{payment}', [PaymentController::class, 'update'])->name('payments.update')->middleware('permission:edit-finance');
    Route::delete('payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy')->middleware('permission:delete-finance');
    Route::post('payments/{payment}/mark-as-completed', [PaymentController::class, 'markAsCompleted'])->name('payments.mark-as-completed')->middleware('permission:edit-finance');
    
    // Expenses
    Route::get('expenses', [ExpenseController::class, 'index'])->name('expenses.index')->middleware('permission:view-finance');
    Route::get('expenses/create', [ExpenseController::class, 'create'])->name('expenses.create')->middleware('permission:create-finance');
    Route::post('expenses', [ExpenseController::class, 'store'])->name('expenses.store')->middleware('permission:create-finance');
    Route::get('expenses/{expense}', [ExpenseController::class, 'show'])->name('expenses.show')->middleware('permission:view-finance');
    Route::get('expenses/{expense}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit')->middleware('permission:edit-finance');
    Route::put('expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update')->middleware('permission:edit-finance');
    Route::delete('expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy')->middleware('permission:delete-finance');
    
    // Invoices
    Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index')->middleware('permission:view-invoices');
    Route::get('invoices/create', [InvoiceController::class, 'create'])->name('invoices.create')->middleware('permission:create-invoices');
    Route::post('invoices', [InvoiceController::class, 'store'])->name('invoices.store')->middleware('permission:create-invoices');
    Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show')->middleware('permission:view-invoices');
    Route::get('invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit')->middleware('permission:edit-invoices');
    Route::put('invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update')->middleware('permission:edit-invoices');
    Route::delete('invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy')->middleware('permission:delete-invoices');
    Route::post('invoices/{invoice}/mark-as-sent', [InvoiceController::class, 'markAsSent'])->name('invoices.mark-as-sent')->middleware('permission:edit-invoices');
    Route::post('invoices/{invoice}/mark-as-paid', [InvoiceController::class, 'markAsPaid'])->name('invoices.mark-as-paid')->middleware('permission:edit-invoices');
    
    // Legal - Contracts
    Route::get('contracts', [ContractController::class, 'index'])->name('contracts.index')->middleware('permission:view-contracts');
    Route::get('contracts/create', [ContractController::class, 'create'])->name('contracts.create')->middleware('permission:create-contracts');
    Route::post('contracts', [ContractController::class, 'store'])->name('contracts.store')->middleware('permission:create-contracts');
    Route::get('contracts/{contract}', [ContractController::class, 'show'])->name('contracts.show')->middleware('permission:view-contracts');
    Route::get('contracts/{contract}/edit', [ContractController::class, 'edit'])->name('contracts.edit')->middleware('permission:edit-contracts');
    Route::put('contracts/{contract}', [ContractController::class, 'update'])->name('contracts.update')->middleware('permission:edit-contracts');
    Route::delete('contracts/{contract}', [ContractController::class, 'destroy'])->name('contracts.destroy')->middleware('permission:delete-contracts');
    Route::post('contracts/{contract}/generate-invoice', [ContractController::class, 'generateInvoice'])->name('contracts.generate-invoice')->middleware('permission:edit-contracts');
    Route::post('contracts/{contract}/renew', [ContractController::class, 'renew'])->name('contracts.renew')->middleware('permission:edit-contracts');
    Route::get('contracts/statistics/data', [ContractController::class, 'getStatistics'])->name('contracts.statistics')->middleware('permission:view-contracts');
    
    // Notifications routes
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::post('notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::post('notifications/clear-read', [NotificationController::class, 'clearRead'])->name('notifications.clear-read');
    Route::delete('notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Messages routes
    // Messages Routes
    Route::get('messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('messages/create', [MessageController::class, 'create'])->name('messages.create');
    Route::post('messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('messages/{message}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('messages/{message}/reply', [MessageController::class, 'reply'])->name('messages.reply');
    Route::post('messages/{message}/mark-read', [MessageController::class, 'markAsRead'])->name('messages.mark-read');
    Route::post('messages/{message}/mark-important', [MessageController::class, 'markAsImportant'])->name('messages.mark-important');
    Route::get('messages/unread-count', [MessageController::class, 'unreadCount'])->name('messages.unread-count');
    Route::get('messages/recent', [MessageController::class, 'getRecentMessages'])->name('messages.recent');
    Route::delete('messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
    
    // Training & Development
    Route::get('training', [TrainingController::class, 'index'])->name('training.index')->middleware('permission:view-training');
    Route::get('training/create', [TrainingController::class, 'create'])->name('training.create')->middleware('permission:create-training');
    Route::post('training/seed-demos', [TrainingController::class, 'storeDemo'])->name('training.seed-demos')->middleware('permission:create-training');
    Route::post('training', [TrainingController::class, 'store'])->name('training.store')->middleware('permission:create-training');
    Route::get('training/{training}', [TrainingController::class, 'show'])->name('training.show')->middleware('permission:view-training');
    Route::get('training/{training}/edit', [TrainingController::class, 'edit'])->name('training.edit')->middleware('permission:edit-training');
    Route::put('training/{training}', [TrainingController::class, 'update'])->name('training.update')->middleware('permission:edit-training');
    Route::delete('training/{training}', [TrainingController::class, 'destroy'])->name('training.destroy')->middleware('permission:delete-training');
    Route::post('training/{training}/participants', [TrainingController::class, 'addParticipant'])->name('training.add-participant')->middleware('permission:edit-training');
    Route::delete('training/{training}/participants/{participant}', [TrainingController::class, 'removeParticipant'])->name('training.remove-participant')->middleware('permission:edit-training');
    Route::get('training/{training}/applications', [TrainingController::class, 'applications'])->name('training.applications')->middleware('permission:view-training');
    Route::patch('training/applications/{application}/status', [TrainingController::class, 'updateApplicationStatus'])->name('training.applications.status')->middleware('permission:edit-training');
    
    // Meetings & Conferences
    Route::get('meetings', [MeetingController::class, 'index'])->name('meetings.index')->middleware('permission:view-meetings');
    Route::get('meetings/create', [MeetingController::class, 'create'])->name('meetings.create')->middleware('permission:create-meetings');
    Route::post('meetings', [MeetingController::class, 'store'])->name('meetings.store')->middleware('permission:create-meetings');
    Route::get('meetings/{meeting}', [MeetingController::class, 'show'])->name('meetings.show')->middleware('permission:view-meetings');
    Route::get('meetings/{meeting}/edit', [MeetingController::class, 'edit'])->name('meetings.edit')->middleware('permission:edit-meetings');
    Route::put('meetings/{meeting}', [MeetingController::class, 'update'])->name('meetings.update')->middleware('permission:edit-meetings');
    Route::delete('meetings/{meeting}', [MeetingController::class, 'destroy'])->name('meetings.destroy')->middleware('permission:delete-meetings');
    Route::post('meetings/{meeting}/participants', [MeetingController::class, 'addParticipant'])->name('meetings.add-participant')->middleware('permission:edit-meetings');
    Route::delete('meetings/{meeting}/participants/{participant}', [MeetingController::class, 'removeParticipant'])->name('meetings.remove-participant')->middleware('permission:edit-meetings');
    
    // Assets & Properties
    Route::get('assets', function () {
        return view('assets.index');
    })->name('assets.index')->middleware('permission:view-assets');
});

// =========================
// Client Portal (بوابة العميل) - Guard مستقل
// =========================
Route::prefix('client')->name('client.')->middleware('auth:client')->group(function () {
    Route::get('/', [ClientPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/projects', [ClientPortalController::class, 'projects'])->name('projects');
    Route::get('/service-reports', [ClientPortalController::class, 'serviceReports'])->name('service-reports');
    Route::get('/service-reports/{serviceReport}/download', [ClientPortalController::class, 'downloadServiceReport'])->name('service-reports.download');

    Route::get('/notifications', [ClientPortalController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/{clientNotification}/read', [ClientPortalController::class, 'markNotificationRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [ClientPortalController::class, 'markAllNotificationsRead'])->name('notifications.read-all');

    Route::get('/documents', [ClientPortalController::class, 'documents'])->name('documents');
    Route::get('/documents/{sharedDocument}/download', [ClientPortalController::class, 'downloadSharedDocument'])->name('documents.download');

    Route::get('/calendar', [ClientPortalController::class, 'calendar'])->name('calendar');

    Route::get('/help', function () {
        return view('client-portal.help');
    })->name('help');

    Route::middleware('client.section:billing')->group(function () {
        Route::get('/invoices', [ClientPortalController::class, 'invoices'])->name('invoices');
    });

    Route::middleware('client.section:technical_requests')->group(function () {
        Route::prefix('website-issues')->name('website-issues.')->group(function () {
            Route::get('/', [ClientWebsiteIssueController::class, 'index'])->name('index');
            Route::get('/create', [ClientWebsiteIssueController::class, 'create'])->name('create');
            Route::post('/', [ClientWebsiteIssueController::class, 'store'])->name('store');
            Route::get('/{websiteIssue}/files/{index}', [ClientWebsiteIssueController::class, 'file'])->name('file')->whereNumber('index');
            Route::delete('/{websiteIssue}', [ClientWebsiteIssueController::class, 'destroy'])->name('destroy');
            Route::get('/{websiteIssue}', [ClientWebsiteIssueController::class, 'show'])->name('show');
        });

        Route::prefix('meeting-requests')->name('meeting-requests.')->group(function () {
            Route::get('/', [ClientMeetingRequestController::class, 'index'])->name('index');
            Route::get('/create', [ClientMeetingRequestController::class, 'create'])->name('create');
            Route::post('/', [ClientMeetingRequestController::class, 'store'])->name('store');
            Route::post('/{meetingRequest}/feedback', [ClientMeetingRequestController::class, 'storeFeedback'])->name('feedback');
            Route::get('/{meetingRequest}', [ClientMeetingRequestController::class, 'show'])->name('show');
        });
    });

    Route::prefix('support')->name('support.')->group(function () {
        Route::get('/tickets', [ClientSupportTicketController::class, 'index'])->name('tickets');
        Route::get('/tickets/create', [ClientSupportTicketController::class, 'create'])->name('tickets.create');
        Route::post('/tickets', [ClientSupportTicketController::class, 'store'])->name('tickets.store');
        Route::get('/tickets/{ticket}', [ClientSupportTicketController::class, 'show'])->name('tickets.show');
        Route::post('/tickets/{ticket}/feedback', [ClientSupportTicketController::class, 'storeFeedback'])->name('tickets.feedback');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';