<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [AttendanceController::class, 'index'])->name('attendance.index');
Route::post('/attendance/add', [AttendanceController::class, 'store'])->name('attendance.store');

// auth (login and logout)
Route::get('/signin', [AuthenticatedSessionController::class, 'index'])->name('auth.login');
Route::post('/signin/authenticate', [AuthenticatedSessionController::class, 'store'])->name('auth.store');


Route::middleware('auth')->group(function () {
    // Pansamantalang ganito muna para ma-test kung gagana
    Route::post('/logout', function (Illuminate\Http\Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('auth.login');
    })->name('logout');
});

// // ADMIN ROUTES
// // dashboard route
// Route::get('/admin/dashboard', function () {
//     return view('admin.dashboard');
// })->name('admin.dashboard');

// // employee section/route
// Route::get('/admin/employee/create', [EmployeeController::class, 'create'])->name('employees.create');
// Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
// Route::get('/admin/employee/all', [EmployeeController::class, 'index'])->name('employees.index');
// Route::get('/admin/dashboard/employee/{employee}', [EmployeeController::class, 'edit'])->name('employees.edit');


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
    });
});

