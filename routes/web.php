<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\EmployeeSalaryController;
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
    Route::resource('companies', CompanyController::class);
    Route::prefix('company')->name('company.')->group(function () {
        Route::resource('{company}/branches', BranchController::class)->names('branches');
    });

    // Department 
    Route::resource('departments', DepartmentController::class);

    // Employee 
    Route::get('companies/{company}/get-branches', [EmployeeController::class, 'getBranches'])->name('companies.get-branches');
    Route::resource('employees', EmployeeController::class);
    Route::resource('employees.salaries', EmployeeSalaryController::class);

    // Attendance
    Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('attendance/create', [AttendanceController::class, 'create'])->name('attendance.create');
    Route::post('attendance/load-employees', [AttendanceController::class, 'loadEmployees'])->name('attendance.loadEmployees');
    Route::post('attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('attendance/{attendance}/edit', [AttendanceController::class, 'edit'])->name('attendance.edit');
    Route::put('attendance/{attendance}', [AttendanceController::class, 'update'])->name('attendance.update');
    Route::delete('attendance/{attendance}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');
});


