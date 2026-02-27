<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     // 1. Simulan ang query
    //     $query = Employee::query();
        
    //     // fetch all employee records
    //     $employees = Employee::select(
    //         'employee_id',
    //         'first_name',
    //         'middle_name',
    //         'last_name',
    //         'bureau',
    //         'division',
    //         'position',
    //         'salary',
    //         'employment_type',
    //         'role',
    //         'is_active',
    //     )->get();

    //     return view('employees.index', compact('employees'));
    // }

    /**
     * Display a flitered listing of the resource.
     */
    public function index(Request $request)
    {
        // 1. Simulan ang query
        $query = Employee::query();
        
        // 2. Search Filter (Name o ID)
        if ($request->filled('search-input')) {
            $search = $request->input('search-input');
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('bureau')) {
            $query->where('bureau', $request->bureau);
        }

        if ($request->filled('division')) {
            $query->where('division', $request->division);
        }

        if ($request->filled('type')) {
            $query->where('employment_type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('is_Active', $request->status);
        }

        $employees = $query->latest()->paginate(10);

        // Eto ang trick: Kapag AJAX, table partial lang ang ibabalik
        if ($request->ajax() || $request->has('ajax')) {
            return view('employees.partials.table', compact('employees'))->render();
        }

        return view('employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
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
