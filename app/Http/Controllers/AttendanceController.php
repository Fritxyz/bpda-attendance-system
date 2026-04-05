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
        if($request->employee_id == null) {
            return redirect()->back()
                ->withInput($request->only('attendance_mode'))
                ->with('error', 'Please enter your Employee ID Number to proceed.');
        }

        if(strlen($request->employee_id) != 8) {
            return redirect()->back()
                ->withInput($request->only('attendance_mode'))
                ->with('error', 'Invalid Input: Employee ID must be exactly 8 digits.');
        }

        $employee_id = 'BPDA-' . $request->employee_id;
        $employee = Employee::where('employee_id', $employee_id)->first();

        if (!$employee) {
            return redirect()->back()
                ->withInput($request->only('attendance_mode'))
                ->with('error', 'Record Not Found: The Employee ID entered does not exist in our database.');
        }

        if($employee) {
            if($employee->is_active == 0) {
                return redirect()->back()
                    ->withInput($request->only('attendance_mode'))
                    ->with('error', 'Access Denied: Your employee account is currently inactive. Please contact the HR for assistance.');
            }
        } 

        $nowInManila = now()->timezone('Asia/Manila');

        $dateToday = $nowInManila->toDateString();
        $mode = strtolower(str_replace(' ', '_', $request->attendance_mode)); 

        $holiday = Holiday::whereDate('date', $dateToday)->first();

        if ($holiday) {
            return redirect()->back()
                ->withInput($request->only('attendance_mode'))
                ->with('error', "Attendance Restricted: Today is an official holiday ($holiday->name). Please contact HR if you are required to report for duty.");
        }

        $has_a_record = Attendance::where('employee_id', $employee_id)
            ->where('attendance_date', $dateToday)
            ->whereNotNull($mode)
            ->exists();

        if($has_a_record) {
            return redirect()->back()
                ->withInput($request->only('attendance_mode'))
                ->with('error', "Transaction Duplicate: You have already recorded a $request->attendance_mode log for today..");
        }
            
        $attendance = Attendance::firstOrNew([
            'employee_id' => $employee_id,
            'attendance_date' => $dateToday
        ]);

        $attendance->$mode = $nowInManila->toTimeString();
        $attendance->save();

        return redirect()->route('attendance.index')
            ->withInput($request->only('attendance_mode'))
            ->with('success', "Attendance Verified: {$request->attendance_mode} recorded for {$employee->first_name} at " . $nowInManila->format('h:i A') . ". Have a productive day!");
    }
}
