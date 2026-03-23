<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboardController;
use App\Http\Controllers\Admin\DTRController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\HolidayController;
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
// // Route::get('/employee/dashboard/', function() {
// //     return view('employee.dashboard');
// // })->name('employee.dashboard');


// PROTECTED ADMIN ROUTES (Dapat naka-login)
Route::middleware(['auth'])->group(function () {
    
    // route group: admin
    Route::prefix('admin')->group(function () {
        
        // dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

        // Employee Management
        Route::get('/employee/all', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/employee/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/employee/store', [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/employee/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('/employee/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::get('/employee/{employee}/view', [EmployeeController::class, 'show'])->name('employees.show');

        // timekeeping management
        Route::get('/dtr/view', [DTRController::class, 'index'])->name('dtr.view'); // - daily time record
        Route::get('/dtr/edit/{employee}/{date}', [DTRController::class, 'edit'])->name('dtr.edit'); // - edit the daily time record
        Route::put('/dtr/edit/{employee}/{date}/update', [DTRController::class, 'update'])->name('dtr.update');

        // holiday management
        Route::get('/holidays/all', [HolidayController::class, 'index'])->name('holiday.index');
        Route::get('/holidays/create', [HolidayController::class, 'create'])->name('holiday.create');
        Route::post('/holidays/create', [HolidayController::class, 'store'])->name('holiday.store');
        Route::get('/holidays/{id}/edit', [HolidayController::class, 'edit'])->name('holiday.edit');
        Route::put('/holidays/{id}/update', [HolidayController::class, 'update'])->name('holiday.update');
        Route::delete('/holidays/{id}/delete', [HolidayController::class, 'destroy'])->name('holiday.destroy');
    });

    Route::prefix('employee')->group(function() {
        Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('employee.dashboard');
    });
});

