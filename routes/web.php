<?php

use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});


// employee route
Route::get('/admin/employee/create', [EmployeeController::class, 'create'])->name('employees.create');
Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
Route::get('/admin/employee/all', [EmployeeController::class, 'index'])->name('employees.index');


// dashboard route (admin)
Route::get('/admin/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

