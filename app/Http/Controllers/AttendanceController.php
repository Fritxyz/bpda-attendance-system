<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['attendances' => function($query) {
            $query->where('attendance_date', now()->toDateString());
        }])->get();

        return view('index', compact('employees'));
    }

    public function store(Request $request)
    {
        $employee_id = 'BPDA-' . $request->employee_id;
        $employee = Employee::where('employee_id', $employee_id)->firstOrFail();
        
        $dateToday = now()->toDateString();
        $mode = strtolower(str_replace(' ', '_', $request->attendance_mode)); // 'am_in', 'pm_out', etc.

        // Hanapin kung may record na siya ngayong araw, kung wala, gawa ng bago
        $attendance = Attendance::firstOrNew([
            'employee_id' => $employee->id,
            'attendance_date' => $dateToday
        ]);

        // I-set ang oras sa column na pinili sa radio button
        $attendance->$mode = now()->toTimeString();
        $attendance->save();

        return back()->with('success', "Logged $request->attendance_mode for $employee->first_name");
    }
}
