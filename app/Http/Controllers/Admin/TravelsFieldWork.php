<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\TravelOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TravelsFieldWork extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {
        $today = Carbon::today();

        // 1. Kunin lahat ng Travel Orders na may kasamang Employee data
        $query = TravelOrder::with('employee');

        // Search Filter (kung may search input)
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('to_number', 'like', "%{$search}%")
                  ->orWhereHas('employee', function($e) use ($search) {
                      $e->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        $travelOrders = $query->latest()->paginate(10);

        // 2. Calculate Stats para sa Cards sa taas
        // Active Travels = Approved at pasok ang date ngayon sa date range
        $activeTravelsCount = TravelOrder::whereDate('date_from', '<=', $today)
            ->whereDate('date_to', '>=', $today)
            ->count();

        // Para sa "Create" modal, kailangan natin ng listahan ng employees
        $employees = Employee::where('is_active', true)
            ->orderBy('last_name')
            ->get();

        return view('admin.obm.index', compact(
            'travelOrders', 
            'activeTravelsCount', 
            'employees'
        ));
    }

    public function create() {
        return view('admin.obm.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'to_number' => 'required|unique:travel_orders,to_number',
            'destination' => 'required|string',
            'purpose' => 'required|string',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'fund_source' => 'nullable|string',
        ]);

        TravelOrder::create($validated);

        return redirect()->back()->with('success', 'Travel Order created successfully!');
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
