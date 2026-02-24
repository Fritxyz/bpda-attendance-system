<?php

use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});


// employee route
Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');

Route::get('/employee/all', [EmployeeController::class, 'index'])->name('employees.index');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

