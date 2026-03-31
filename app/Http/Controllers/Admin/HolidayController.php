<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHolidayRequest;
use App\Http\Requests\UpdateHolidayRequest;
use App\Models\AuditTrail;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $data = $request->validated();

        // Gamitin ang Transaction para sigurado
        return DB::transaction(function () use ($request, $data) {
            
            $holiday = Holiday::create($data);

            AuditTrail::create([
                'user_id'        => Auth::getUser()->employee_id, 
                'event'          => 'Created',
                'auditable_type' => get_class($holiday),
                'auditable_id'   => $holiday->id,
                'old_values'     => json_encode([]), 
                'new_values'     => $data, 
                'ip_address'     => $request->ip(),
                'remarks'        => "Created new holiday: " . $holiday->name,
            ]);

            return redirect()->route('holiday.index')
                ->with('success', 'New holiday has been successfully registered.');
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
    public function edit(string $id)
    {
        $holiday = Holiday::where('id', $id)->firstOrFail();

        return view('admin.holidays.edit', compact('holiday'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateHolidayRequest $request, string $id)
    {
        $holiday = Holiday::findOrFail($id);
        
        // Kunin ang original data bago i-update para sa "old_values"
        $oldValues = $holiday->getRawOriginal(); 
        
        $validated = $request->validated();

        return DB::transaction(function () use ($request, $holiday, $validated, $oldValues) {
            
            $result = $holiday->update($validated);

            // Kunin lang ang mga fields na actual na nagbago
            $changes = $holiday->getChanges();

            // Mag-create lang ng log kung may totoong binago ang user
            if (!empty($changes)) {
                AuditTrail::create([
                    'user_id'        => Auth::getUser()->employee_id,
                    'event'          => 'Updated',
                    'auditable_type' => get_class($holiday),
                    'auditable_id'   => $holiday->id,
                    'old_values'     => $oldValues, // Array/JSON format na ito
                    'new_values'     => $changes,   // I-save lang ang mga nagbago para malinis
                    'ip_address'     => $request->ip(),
                    'remarks' => "Updated holiday: " . $holiday->name . ". Reason: " . ($validated['remarks'] ?? 'No specific remarks.'),
                ]);
            }

            return redirect()->route('holiday.index')
                ->with('success', 'Holiday has been successfully updated.');
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        // 1. Hanapin ang holiday o mag-throw ng 404 kung wala
        $holiday = Holiday::findOrFail($id);

        // 2. Kunin ang input mula sa SweetAlert textarea (naka-map sa 'remarks' name sa hidden input)
        $userReason = $request->input('remarks');

        // 3. I-capture ang snapshot ng data bago i-delete (para sa old_values)
        $oldValues = $holiday->toArray();

        try {
            // 4. I-record sa Audit Trail Table
            \App\Models\AuditTrail::create([
                'user_id'        => Auth::user()->employee_id, // Base sa iyong user schema
                'event'          => 'deleted',
                'auditable_type' => get_class($holiday),
                'auditable_id'   => $holiday->id,
                'old_values'     => json_encode($oldValues), 
                'new_values'     => json_encode([]), // Laging empty array sa delete
                'ip_address'     => $request->ip(),
                'user_agent'     => $request->userAgent(),
                // Professional formatting para sa remarks:
                'remarks'        => "DELETED HOLIDAY: " . $oldValues['name'] . " | REASON: " . ($userReason ?? 'No reason provided')
            ]);

            // 5. Tuluyan nang i-delete ang record
            $holiday->delete();

            return redirect()->route('holiday.index')
                ->with('success', "Holiday '{$oldValues['name']}' has been successfully removed from the system.");

        } catch (\Exception $e) {
            // Sakaling mag-fail ang database transaction
            return redirect()->route('holiday.index')
                ->with('error', "Failed to delete holiday. Please try again.");
        }
    }
}
