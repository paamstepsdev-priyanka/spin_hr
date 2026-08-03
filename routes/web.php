<?php

use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\AttendanceReportController;
use App\Http\Controllers\Admin\EmployeeSalaryController;
use App\Http\Controllers\Admin\PayrollController;
use App\Http\Controllers\Admin\PayrollProcessingController;
use App\Http\Controllers\EmployeePortal\EmployeeDashboardController;
use App\Http\Controllers\EmployeePortal\EmployeeProfileController;
use App\Http\Controllers\EmployeePortal\EmployeeAttendanceController;
use App\Http\Controllers\EmployeePortal\EmployeeSalaryHistoryController;
use App\Http\Controllers\EmployeePortal\EmployeePayrollHistoryController;
use App\Http\Controllers\EmployeePortal\EmployeePayslipController;
use App\Http\Controllers\EmployeePortal\EmployeeDocumentController;
use App\Http\Middleware\EmployeeMiddleware;
use Illuminate\Support\Facades\Route;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Protected Admin & User Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/logout', [AuthController::class, 'logout']); // Fallback GET logout for convenience

    // User 
    Route::resource('users', UserController::class);

    // Company 
    Route::post('company/switch', [CompanyController::class, 'switchCompany'])->name('company.switch');
    Route::resource('companies', CompanyController::class);
    Route::prefix('company')->name('company.')->group(function () {
        Route::resource('{company}/branches', BranchController::class)->names('branches');
    });

    // Department 
    Route::resource('departments', DepartmentController::class);

    // Employee 
    Route::get('companies/{company}/get-branches', [EmployeeController::class, 'getBranches'])->name('companies.get-branches');
    Route::get('employees/{employee}/pdf', [EmployeeController::class, 'exportPdf'])->name('employees.pdf');
    Route::post('employees/{employee}/status', [EmployeeController::class, 'updateStatus'])->name('employees.update-status');
    Route::resource('employees', EmployeeController::class);
    Route::resource('employees.salaries', EmployeeSalaryController::class);

    // Attendance
    Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('attendance/create', [AttendanceController::class, 'create'])->name('attendance.create');
    Route::post('attendance/load-employees', [AttendanceController::class, 'loadEmployees'])->name('attendance.loadEmployees');
    Route::post('attendance/load-summary', [AttendanceController::class, 'loadAttendanceSummary'])->name('attendance.loadAttendanceSummary');
    Route::post('attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('attendance/{attendance}', [AttendanceController::class, 'show'])->name('attendance.show');

    // Attendance Edit/Update/Delete protected by EnsureAttendanceUnlocked middleware
    Route::middleware([\App\Http\Middleware\EnsureAttendanceUnlocked::class])->group(function () {
        Route::get('attendance/{attendance}/edit', [AttendanceController::class, 'edit'])->name('attendance.edit');
        Route::put('attendance/{attendance}', [AttendanceController::class, 'update'])->name('attendance.update');
        Route::delete('attendance/{attendance}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');
    });

    // Attendance Report
    Route::get('attendance-report', [AttendanceReportController::class, 'index'])->name('attendance-report.index');
    Route::get('attendance-report/get-branches/{company}', [AttendanceReportController::class, 'getBranches'])->name('attendance-report.get-branches');
    Route::get('attendance-report/get-employees', [AttendanceReportController::class, 'getEmployees'])->name('attendance-report.get-employees');
    Route::post('attendance-report/report', [AttendanceReportController::class, 'report'])->name('attendance-report.report');
    Route::get('attendance-report/export-pdf', [AttendanceReportController::class, 'exportPdf'])->name('attendance-report.export-pdf');
    Route::get('attendance-report/export-excel', [AttendanceReportController::class, 'exportExcel'])->name('attendance-report.export-excel');

    // Payroll & Payroll Processing
    Route::get('payroll-processing', [PayrollProcessingController::class, 'index'])->name('payroll-processing.index');
    Route::get('payroll-processing/details/{year}/{month}', [PayrollProcessingController::class, 'show'])->name('payroll-processing.show');
    Route::post('payroll-processing/generate-payslips/{year}/{month}', [PayrollProcessingController::class, 'generatePayslips'])->name('payroll-processing.generate-payslips');

    Route::get('payrolls', [PayrollController::class, 'index'])->name('payrolls.index');
    Route::get('payrolls/create', [PayrollController::class, 'create'])->name('payrolls.create');
    Route::post('payrolls/load-employees', [PayrollController::class, 'loadEmployees'])->name('payrolls.loadEmployees');
    Route::post('payrolls', [PayrollController::class, 'store'])->name('payrolls.store');
    Route::get('payrolls/{payroll}', [PayrollController::class, 'show'])->name('payrolls.show');
    Route::get('payrolls/{payroll}/edit', [PayrollController::class, 'edit'])->name('payrolls.edit');
    Route::put('payrolls/{payroll}', [PayrollController::class, 'update'])->name('payrolls.update');
    Route::delete('payrolls/{payroll}', [PayrollController::class, 'destroy'])->name('payrolls.destroy');
    Route::get('payrolls/salary-slip/{detail}', [PayrollController::class, 'salarySlip'])->name('payrolls.salary-slip');
    Route::get('payrolls/salary-slip/{detail}/pdf', [PayrollController::class, 'salarySlipPdf'])->name('payrolls.salary-slip.pdf');
});

// Employee Self-Service (ESS) Portal Routes
Route::middleware(['auth', EmployeeMiddleware::class])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [EmployeeProfileController::class, 'show'])->name('profile');
    Route::get('/profile/pdf', [EmployeeProfileController::class, 'downloadPdf'])->name('profile.pdf');
    Route::get('/attendance', [EmployeeAttendanceController::class, 'index'])->name('attendance');
    Route::get('/salary-history', [EmployeeSalaryHistoryController::class, 'index'])->name('salary-history');
    Route::get('/payroll-history', [EmployeePayrollHistoryController::class, 'index'])->name('payroll-history');
    Route::get('/payslips', [EmployeePayslipController::class, 'index'])->name('payslips.index');
    Route::get('/payslips/{detail}', [EmployeePayslipController::class, 'show'])->name('payslips.show');
    Route::get('/payslips/{detail}/pdf', [EmployeePayslipController::class, 'downloadPdf'])->name('payslips.pdf');
    Route::get('/documents', [EmployeeDocumentController::class, 'index'])->name('documents');
    Route::get('/documents/download/{type}', [EmployeeDocumentController::class, 'download'])->name('documents.download');

    /*
    | Future Modules Reserved Routing Structure (Hidden / Inactive for now):
    | Route::get('/leave', [EmployeeLeaveController::class, 'index'])->name('leave.index');
    | Route::get('/holidays', [EmployeeHolidayController::class, 'index'])->name('holidays.index');
    | Route::get('/assets', [EmployeeAssetController::class, 'index'])->name('assets.index');
    | Route::get('/helpdesk', [EmployeeHelpdeskController::class, 'index'])->name('helpdesk.index');
    | Route::get('/training', [EmployeeTrainingController::class, 'index'])->name('training.index');
    | Route::get('/performance', [EmployeePerformanceController::class, 'index'])->name('performance.index');
    */
});
