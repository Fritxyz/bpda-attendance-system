<?php

use App\Http\Controllers\Admin\AuditTrailController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboardController;
use App\Http\Controllers\Admin\DTRController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\HolidayController;
use App\Http\Controllers\Admin\LateController;
use App\Http\Controllers\Admin\SalaryController;
use App\Http\Controllers\Admin\TravelsFieldWork;
use App\Http\Controllers\Admin\WTRController;
use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/add', [AttendanceController::class, 'store'])->name('attendance.store');

    // Login routes
    Route::get('/signin', [AuthenticatedSessionController::class, 'index'])->name('auth.login');
    Route::post('/signin/authenticate', [AuthenticatedSessionController::class, 'store'])->name('auth.store');
});

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('auth.logout')
    ->middleware('auth');

// PROTECTED ADMIN ROUTES (Dapat naka-login)
Route::middleware(['auth', 'admin'])->group(function () {
    
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
        Route::get('/employee/{employee}/view/printdtr/{month}/{year}', [DTRController::class, 'generateDTR'])->name('dtr.print');

        // timekeeping
        Route::get('/dtr/view', [DTRController::class, 'index'])->name('dtr.view'); // - daily time record
        Route::get('/dtr/edit/{employee}/{date}', [DTRController::class, 'edit'])->name('dtr.edit'); // - edit the daily time record
        Route::put('/dtr/edit/{employee}/{date}/update', [DTRController::class, 'update'])->name('dtr.update');
        Route::get('/dtr/print/daily', [DTRController::class, 'printDailyAttendance'])->name('dtr.print.daily');
        Route::get('/wtr/view/', [WTRController::class, 'index'])->name('wtr.view');
        Route::get('/wtr/print/', [WTRController::class, 'print'])->name('wtr.print');

        // late arrivals
        Route::get('/tardiness', [LateController::class, 'index'])->name('tardiness.index');

        // Holiday management
        Route::get('/holidays/all', [HolidayController::class, 'index'])->name('holiday.index');
        Route::get('/holidays/create', [HolidayController::class, 'create'])->name('holiday.create');
        Route::post('/holidays/create', [HolidayController::class, 'store'])->name('holiday.store');
        Route::get('/holidays/{id}/edit', [HolidayController::class, 'edit'])->name('holiday.edit');
        Route::put('/holidays/{id}/update', [HolidayController::class, 'update'])->name('holiday.update');
        Route::delete('/holidays/{id}/delete', [HolidayController::class, 'destroy'])->name('holiday.destroy');

        // on travel
        Route::get('/travels-field-work', [TravelsFieldWork::class, 'index'])->name('travels.field.index');
        Route::get('/travels-field-work/create', [TravelsFieldWork::class, 'create'])->name('travels.field.create');
        Route::post('/travels-field-work/create', [TravelsFieldWork::class, 'store'])->name('travels.field.store');
        Route::get('/travels-field-work/edit/{id}', [TravelsFieldWork::class, 'edit'])->name('travels.field.edit');
        Route::put('/travels-field-work/edit/{id}', [TravelsFieldWork::class, 'update'])->name('travels.field.update');
        Route::delete('/travels-field-work/delete/{id}', [TravelsFieldWork::class, 'destroy'])->name('travels.field.delete');
        
        Route::get('/audit-trail', [AuditTrailController::class, 'index'])->name('admin.audittrail');
    });
});


Route::middleware(['auth', 'employee'])->group(function () {

    // route::group employee
    Route::prefix('employee')->group(function() {
        Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('employee.dashboard');
        Route::get('/my-profile', [EmployeeDashboardController::class, 'showEmployeeProfile'])->name('employee.profile');
        Route::get('/my-profile/print-dtr/{month}/{year}', [EmployeeDashboardController::class, 'printEmployeeDtr'])->name('employee.printdtr');
    });
});


 

