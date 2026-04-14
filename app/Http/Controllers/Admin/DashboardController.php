<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index() {
        $today = Carbon::now('Asia/Manila')->format('Y-m-d');
        $totalEmployeeCount = Employee::count();

        // Stats Cards
        $startOfMonth = Carbon::now('Asia/Manila')->startOfMonth();
        $endOfMonth   = Carbon::now('Asia/Manila')->endOfMonth();
        $newEmployeesThisMonth = Employee::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        
        $presentToday = Attendance::whereDate('attendance_date', $today)->count();
        $attendanceRate = $totalEmployeeCount > 0 ? round(($presentToday / $totalEmployeeCount) * 100) : 0;

        $lateToday = Attendance::whereDate('attendance_date', $today)
            ->where(function($query) {
                $query->where('am_in', '>', '08:15:00')
                      ->orWhere(function($q) {
                          $q->whereNull('am_in')->where('pm_in', '>', '08:15:00'); // Assuming 1PM for PM shift
                      });
            })->count();

        // Recent Activity Table (Last 10 logs for today)
        $todaysAttendance = Attendance::with('employee')
            ->whereDate('attendance_date', $today)
            ->orderByRaw("GREATEST(COALESCE(am_in, '00:00'), COALESCE(pm_in, '00:00'), COALESCE(ot_in, '00:00')) DESC")
            ->get();

        $onTimeToday = max(0, $presentToday - $lateToday);
        $punctualityRate = $presentToday > 0 ? round(($onTimeToday / $presentToday) * 100) : 0;

        // Logic for Charts (Last 7 Days Trend)
        $days = collect(range(6, 0))->map(function($i) {
            return Carbon::now('Asia/Manila')->subDays($i)->format('Y-m-d');
        });

        $attendanceTrend = $days->map(function($date) {
            return Attendance::whereDate('attendance_date', $date)->count();
        });

        $chartLabels = $days->map(fn($date) => Carbon::parse($date)->format('D'));

        return view('admin.dashboard', compact(
            'totalEmployeeCount',
            'newEmployeesThisMonth', 
            'attendanceRate',
            'lateToday',
            'presentToday',
            'onTimeToday',      // Add this
            'punctualityRate',  // Add this
            'todaysAttendance',
            'attendanceTrend',
            'chartLabels'
        ));
    }
}