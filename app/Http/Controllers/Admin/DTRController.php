<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAttendanceRequest;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DTRController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $targetDate = $request->input('date', now()->timezone('Asia/Manila')->toDateString());
        $search = $request->input('search');

        $query = Attendance::with('employee')
            ->whereDate('attendance_date', $targetDate);

        if ($search) {
            $query->whereHas('employee', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        $today_records = $query->get()->map(function ($record) {
            $amIn = $record->am_in ? Carbon::parse($record->am_in) : null;
            $amOut = $record->am_out ? Carbon::parse($record->am_out) : null;
            $pmIn = $record->pm_in ? Carbon::parse($record->pm_in) : null;
            $pmOut = $record->pm_out ? Carbon::parse($record->pm_out) : null;

            $totalMinutes = 0;
            if ($amIn && $amOut) $totalMinutes += $amIn->diffInMinutes($amOut);
            if ($pmIn && $pmOut) $totalMinutes += $pmIn->diffInMinutes($pmOut);

            $hours = floor($totalMinutes / 60);
            $minutes = $totalMinutes % 60;
            $record->computed_total_hours = "{$hours}h {$minutes}m";

            $requiredMinutes = 8 * 60;
            $diffMinutes = abs($totalMinutes - $requiredMinutes);
            $diffH = floor($diffMinutes / 60);
            $diffM = $diffMinutes % 60;
            $record->diff_hours = "{$diffH}h {$diffM}m";

            if ($totalMinutes > $requiredMinutes) {
                $record->attendance_status = 'OVERTIME';
                $record->status_color = 'blue';
            } elseif ($totalMinutes < $requiredMinutes && $totalMinutes > 0) {
                $record->attendance_status = 'UNDERTIME';
                $record->status_color = 'rose';
            } else {
                $record->attendance_status = 'REGULAR';
                $record->status_color = 'emerald';
                $record->diff_hours = "0h 0m";
            }
            return $record;
        });

        return view('admin.timekeeping.view-dtr', compact('today_records'));
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
    public function edit($employee, $date)
    {
        $attendance = Attendance::with('employee')
            ->where('employee_id', $employee)
            ->where('attendance_date', $date)
            ->firstOrFail(); 

        return view('admin.timekeeping.edit-dtr', compact('attendance'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttendanceRequest $request, $employee, $date)
    {
        // dd($request, $employee, $date);
        $attendance = Attendance::where('employee_id', $employee)
                            ->where('attendance_date', $date)
                            ->firstOrFail();

        // dd($attendance);

        $attendance->update($request->validated());

        return redirect()->route('dtr.view')
                     ->with('success', "Attendance record for {$employee} on {$date} has been updated.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
