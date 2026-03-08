<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        // Kunin ang kasalukuyang petsa sa Manila
        $todayInManila = now()->timezone('Asia/Manila')->toDateString();

        $employees = Employee::whereHas('attendances', function ($query) use ($todayInManila) {
            $query->where('attendance_date', $todayInManila);
        })
        ->with(['attendances' => function ($query) use ($todayInManila) {
            $query->where('attendance_date', $todayInManila);
        }])
        ->get();

        

        // dd($employees);

        return view('index', compact('employees'));
    }

    public function store(Request $request)
    {
        $employee_id = 'BPDA-' . $request->employee_id;
        // dd($employee_id);
        $employee = Employee::where('employee_id', $employee_id)->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'Employee ID not found.');
        }

        // I-set ang Manila timezone dito
        $nowInManila = now()->timezone('Asia/Manila');

        $dateToday = $nowInManila->toDateString();
        $mode = strtolower(str_replace(' ', '_', $request->attendance_mode)); 

        // Hanapin o gumawa ng bagong record
        $attendance = Attendance::firstOrNew([
            'employee_id' => $employee_id,
            'attendance_date' => $dateToday
        ]);

        // I-save ang oras base sa Manila time
        $attendance->$mode = $nowInManila->toTimeString();
        $attendance->save();

        return redirect()->route('attendance.index')
            ->with('success', "Logged $request->attendance_mode for $employee->first_name at " . $nowInManila->format('h:i A'));
    }
}
