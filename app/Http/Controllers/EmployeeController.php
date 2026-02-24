<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Log\Logger;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return view('employees.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('employees.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        $validated = $request->validated();
        // Handle checkbox
        $validated['is_active'] = $request->has('is_active');
        // Prepend BPDA- to the input number
        $validated['employee_id'] = 'BPDA-' . $request->input('employee_id');
        
        $employee = Employee::create($validated);

        // Flash a message and redirect instead of just returning a string
        return redirect()->route('employees.index')
        ->with('success', "Employee saved! Username: {$employee->username}");

        // logger($request->all());

        // return redirect()->route('employees.index')
        // ->with('success', "Employee saved!");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
