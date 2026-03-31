<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Holiday;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $todayInManila = now()->timezone('Asia/Manila')->toDateString();

        $employees = Employee::whereHas('attendances', function ($query) use ($todayInManila) {
            $query->where('attendance_date', $todayInManila);
        })
        ->with(['attendances' => function ($query) use ($todayInManila) {
            $query->where('attendance_date', $todayInManila);
        }])
        ->get();

        return view('index', compact('employees'));
    }

    public function store(Request $request)
    {
        $employee_id = 'BPDA-' . $request->employee_id;
        $employee = Employee::where('employee_id', $employee_id)->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'Employee ID not found.');
        }

        $nowInManila = now()->timezone('Asia/Manila');

        $dateToday = $nowInManila->toDateString();
        $mode = strtolower(str_replace(' ', '_', $request->attendance_mode)); 

        $holiday = Holiday::whereDate('date', $dateToday)->first();

        if ($holiday) {
            return redirect()->back()->with('error', "ATTENDANCE BLOCKED: Today is a Holiday ({$holiday->name}). Please contact the HR/Admin if you are on official duty.");
        }

        $has_a_record = Attendance::where('employee_id', $employee_id)
            ->where('attendance_date', $dateToday)
            ->whereNotNull($mode)
            ->exists();

        if($has_a_record) {
            return redirect()->back()->with('error', "Employee {$employee_id} has already logged {$request->attendance_mode}.");
        }
            
        $attendance = Attendance::firstOrNew([
            'employee_id' => $employee_id,
            'attendance_date' => $dateToday
        ]);

        $attendance->$mode = $nowInManila->toTimeString();
        $attendance->save();

        return redirect()->route('attendance.index')
            ->with('success', "Logged $request->attendance_mode for $employee->first_name at " . $nowInManila->format('h:i A'));
    }
}
