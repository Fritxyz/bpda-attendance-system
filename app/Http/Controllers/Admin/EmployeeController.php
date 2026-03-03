<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    /**
     * Display a (filtered/unfiltered) listing of the resource.
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

        $employees = $query->latest()->paginate(10)->appends($request->all( ));

        // Eto ang trick: Kapag AJAX, table partial lang ang ibabalik
        if ($request->ajax() || $request->has('ajax')) {
            return view('partials.admin.employees.table', compact('employees'))->render();
        }

        return view('admin.employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.employees.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        $validated = $request->validated();

        return DB::transaction(function () use ($request, $validated) {
            
            // 1. I-prepare ang basic data
            $validated['is_active'] = $request->has('is_active');
            $validated['employee_id'] = 'BPDA-' . $request->input('employee_id');

            // 2. Handle Image Upload
            if ($request->hasFile('profile_picture')) {
                $extension = $request->file('profile_picture')->getClientOriginalExtension();
                // Mas maganda kung ang ID ang filename para unique
                $fileName = 'BPDA-' . $request->input('employee_id') . '.' . $extension;
                
                // I-store ang file
                $imagePath = $request->file('profile_picture')->storeAs('profiles', $fileName, 'public');
                
                // Eto ang kulang mo: I-update ang validated array para ang path ang ma-save, hindi yung file object
                $validated['profile_picture'] = $imagePath; 
            }

            // 3. I-save ang Employee Profile
            $employee = Employee::create($validated);

            // 4. I-create ang User Account
            $user = User::create([
                'employee_id' => $employee->employee_id,
                'password'    => $request->input('password'),
                'role'        => $request->input('role', 'Employee'),
            ]);

            return redirect()->route('employees.index')
                ->with('success', "Employee and Account created! \nEmployee ID: {$user->employee_id}");
        });
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
    public function edit(Employee $employee)
    {
        // Mas maikli ito (ganun din ang resulta):
        $employee->employee_id = substr($employee->employee_id, 5);

        return view('admin.employees.edit', [
            'employee' => $employee
        ]);
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
