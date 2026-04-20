<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTravelOrderRequest;
use App\Http\Requests\UpdateTravelOrderRequest;
use App\Models\AuditTrail;
use App\Models\Employee;
use App\Models\TravelOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TravelsFieldWork extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {
        $today = Carbon::today();
        $search = $request->input('search');

        // 1. Kunin lahat ng Travel Orders na may kasamang Employee data
        $travelOrders = TravelOrder::with('employee') // Siguraduhin na may relationship sa Model
            ->when($search, function ($query, $search) {
                return $query->where('to_number', 'LIKE', "%{$search}%")
                    ->orWhereHas('employee', function ($q) use ($search) {
                        $q->where('first_name', 'LIKE', "%{$search}%")
                        ->orWhere('last_name', 'LIKE', "%{$search}%");
                    });
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        if ($request->ajax()) {
            return view('partials.admin.obm._travel_orders_table', compact('travelOrders'))->render();
        }
        return view('admin.obm.index', compact(
            'travelOrders',
        ));
    }

    public function create() {
        // 1. Kunin natin lahat ng employees para sa dropdown
        $employees = Employee::where('is_active', 1)->orderBy('last_name', 'asc')->get();
        return view('admin.obm.create', compact('employees'));
    }

    public function store(StoreTravelOrderRequest $request)
    {
        // Kapag nakarating dito, ibig sabihin pasado na sa validation
        $validated = $request->validated();

        return DB::transaction(function () use ($request, $validated) {
            $travelOrder = TravelOrder::create($validated);

            AuditTrail::create([
                'user_id'        => Auth::user()->employee_id,
                'event'          => 'Created',
                'auditable_type' => get_class($travelOrder),
                'auditable_id'   => $travelOrder->id,
                'old_values'     => json_encode([]),
                'new_values'     => json_encode($validated),
                'ip_address'     => $request->ip(),
                'remarks'        => "Created Travel Order: " . $travelOrder->to_number . " for employee ID: " . $travelOrder->employee->first_name . ' ' . $travelOrder->employee->last_name,
            ]);

            return redirect()->route('travels.field.index')->with('success', 'Travel Order successfully created!');
        });
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
        $employees = Employee::where('is_active', 1)->orderBy('last_name', 'asc')->get();
        $travelOrder = TravelOrder::where('id', $id)->firstOrFail();
        return view('admin.obm.edit', compact('employees', 'travelOrder'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTravelOrderRequest $request, $id)
    {
        $travelOrder = TravelOrder::findOrFail($id);
        $oldValues = $travelOrder->getRawOriginal();
        $validated = $request->validated();

        return DB::transaction(function () use ($request, $travelOrder, $validated, $oldValues) {
            $travelOrder->update($validated);
            $changes = $travelOrder->getChanges();


            if (!empty($changes)) {
                AuditTrail::create([
                    'user_id'        => Auth::user()->employee_id,
                    'event'          => 'Updated',
                    'auditable_type' => get_class($travelOrder),
                    'auditable_id'   => $travelOrder->id,
                    'old_values'     => json_encode($oldValues),
                    'new_values'     => json_encode($changes),
                    'ip_address'     => $request->ip(),
                    'remarks'        => "Updated Travel Order: " . $travelOrder->to_number . ". Reason: " . ($request->remarks ?? 'No specific remarks.'),
                ]);
            } else {
                return redirect()->route('travels.field.index')
                    ->with('info', "No changes were detected for Travel Order {$travelOrder->to_number}.");
            }

            return redirect()->route('travels.field.index')->with('success', 'Travel Order updated successfully!');
        });;

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        //
        $travelOrder = TravelOrder::findOrFail($id);
        $oldValues = $travelOrder->toArray();
        $userReason = $request->input('remarks');

        try {
            DB::transaction(function () use ($request, $travelOrder, $oldValues, $userReason) {
                AuditTrail::create([
                    'user_id'        => Auth::user()->employee_id,
                    'event'          => 'Deleted',
                    'auditable_type' => get_class($travelOrder),
                    'auditable_id'   => $travelOrder->id,
                    'old_values'     => json_encode($oldValues),
                    'new_values'     => json_encode([]),
                    'ip_address'     => $request->ip(),
                    'user_agent'     => $request->userAgent(),
                    'remarks'        => "DELETED TRAVEL ORDER: " . $travelOrder->to_number . " | REASON: " . ($userReason ?? 'No reason provided')
                ]);

                $travelOrder->delete();
            });

            return redirect()->route('travels.field.index')
                ->with('success', 'Travel Order deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->route('travels.field.index')
                ->with('error', "Failed to delete travel order. Please try again.");
        }
    }
}
