<?php

use App\Http\Controllers\Admin\DTRController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;

// Route::get('/', [AttendanceController::class, 'index'])->name('attendance.index');
// Route::post('/attendance/add', [AttendanceController::class, 'store'])->name('attendance.store');

// // auth (login and logout)
// Route::get('/signin', [AuthenticatedSessionController::class, 'index'])->name('auth.login');
// Route::post('/signin/authenticate', [AuthenticatedSessionController::class, 'store'])->name('auth.store');

// Public routes (hindi dapat makita kapag logged in na)
Route::middleware('guest')->group(function () {
    Route::get('/', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/add', [AttendanceController::class, 'store'])->name('attendance.store');

    // Login routes
    Route::get('/signin', [AuthenticatedSessionController::class, 'index'])->name('auth.login');
    Route::post('/signin/authenticate', [AuthenticatedSessionController::class, 'store'])->name('auth.store');
});


Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('auth.logout');

// employee route
Route::get('/employee/dashboard/', function() {
    return view('employee.dashboard');
})->name('employee.dashboard');


// PROTECTED ADMIN ROUTES (Dapat naka-login)
Route::middleware(['auth'])->group(function () {
    
    // Dito mo ilalagay lahat ng pang-admin
    Route::prefix('admin')->group(function () {
        
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');

        // Employee Management
        Route::get('/employee/all', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/employee/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/employee/store', [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/employee/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('/employee/{employee}', [EmployeeController::class, 'update'])->name('employees.update');

        // timekeeping management
        Route::get('/dtr/view', [DTRController::class, 'index'])->name('dtr.view'); // - daily time record
        Route::get('/dtr/edit/{employee}/{date}', [DTRController::class, 'edit'])->name('dtr.edit'); // - edit the daily time record
        Route::put('/dtr/edit/{employee}/{date}/update', [DTRController::class, 'update'])->name('dtr.update');
    });
});

