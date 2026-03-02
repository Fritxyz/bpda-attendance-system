<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

// auth (login and logout)
Route::get('/signin', [AuthenticatedSessionController::class, 'create'])->name('auth.login');

// ADMIN ROUTES
// dashboard route
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

// employee section/route
Route::get('/admin/employee/create', [EmployeeController::class, 'create'])->name('employees.create');
Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
Route::get('/admin/employee/all', [EmployeeController::class, 'index'])->name('employees.index');
Route::get('/admin/dashboard/employee/{employee}', [EmployeeController::class, 'edit'])->name('employees.edit');


