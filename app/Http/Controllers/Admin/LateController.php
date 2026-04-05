<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $cutoffTime = '08:15:00';
        $selectedDate = $request->date ?? now()->toDateString();

        $query = Attendance::with(['employee'])
            ->whereDate('attendance_date', $selectedDate) // Filter by specific date
            ->whereNotNull('am_in')
            ->whereTime('am_in', '>', $cutoffTime)
            ->orderBy('am_in', 'asc');

        $lateRecords = $query->get()->map(function ($record) use ($cutoffTime) {
            $tempAmIn = Carbon::parse($record->am_in);
            $cutoff = Carbon::parse($record->attendance_date)->setTime(8, 15, 0);
            $actualAmIn = Carbon::parse($record->attendance_date)
                ->setTime($tempAmIn->hour, $tempAmIn->minute, $tempAmIn->second);
            
            $totalMinutesLate = $cutoff->diffInMinutes($actualAmIn, false);
            $record->computed_late = ($totalMinutesLate > 0) ? floor($totalMinutesLate / 60) . "h " . ($totalMinutesLate % 60) . "m" : "0m";
            
            return $record;
        });

        // Kapag AJAX ang request, yung partial table lang ang ibabalik natin
        if ($request->ajax()) {
            return view('partials.admin.timekeeping._late_arrivals_table', compact('lateRecords'))->render();
        }

        return view('admin.timekeeping.late-arrivals', compact('lateRecords'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
