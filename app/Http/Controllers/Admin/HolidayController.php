<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHolidayRequest;
use App\Models\Holiday;

class HolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Kinukuha lahat ng holidays, naka-sort by date (latest first)
        $holidays = Holiday::orderBy('date', 'asc')->get();

        return view('admin.holidays.index', compact('holidays'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.holidays.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreHolidayRequest $request)
    {
        //
        $data = $request->validated();

        Holiday::create($data);

        return redirect()->route('holiday.index')
            ->with('success', 'New holiday has been successfully registered.');
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
        $holiday = Holiday::where('id', $id)->firstOrFail();

        return view('admin.holidays.edit', compact('holiday'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreHolidayRequest $request, string $id)
    {
        //
        $holiday = Holiday::where('id', $id)->firstOrFail();
        $validated = $request->validated();

        $holiday->update($validated);

        return redirect()->route('holiday.index')
            ->with('success', 'Holiday has been successfully updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $holiday = Holiday::where('id', $id);

        $holiday->delete();

        return redirect()->route('holiday.index')
            ->with('success', 'Holiday has been successfully deleted');
    }
}
