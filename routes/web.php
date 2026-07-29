<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\UserController;
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
});
