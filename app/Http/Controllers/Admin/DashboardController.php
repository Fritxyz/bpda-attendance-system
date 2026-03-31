<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;

class DashboardController extends Controller
{
    //
    public function index() {

        $totalEmployeeCount = Employee::count();

        $startOfMonth = Carbon::now('Asia/Manila')->startOfMonth();
        $endOfMonth   = Carbon::now('Asia/Manila')->endOfMonth();
        $newEmployeesThisMonth = Employee::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        
        $presentToday = Attendance::whereDate(
            'attendance_date',
            Carbon::now('Asia/Manila')
        )->get()->count();

        $attendanceRate = round(($presentToday / $totalEmployeeCount) * 100);

        $lateToday = Attendance::whereDate('attendance_date', Carbon::now('Asia/Manila'))
                       ->where(function($query) {
                           $query->where('am_in', '>', '08:15:00')
                                 ->orWhere(function($q) {
                                     
                                     $q->whereNull('am_in')
                                       ->where('pm_in', '>', '08:15:00');
                                 });
                       })->get()->count();

        $todaysAttendance = Attendance::with('employee')
            ->whereDate('attendance_date', '2026-03-11')
            ->orderByRaw("
                GREATEST(
                    COALESCE(am_in, '00:00:00'),
                    COALESCE(am_out, '00:00:00'),
                    COALESCE(pm_in, '00:00:00'),
                    COALESCE(pm_out, '00:00:00'),
                    COALESCE(ot_in, '00:00:00'),
                    COALESCE(ot_out, '00:00:00')
                ) DESC
            ")
            ->get();

        
        
        return view('admin.dashboard', (compact(
            'totalEmployeeCount',
            'newEmployeesThisMonth', 
            'attendanceRate',
            'lateToday',
            'presentToday',
            'todaysAttendance'
            )));
    }
}
