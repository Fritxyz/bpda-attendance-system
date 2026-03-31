<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return view('employee.dashboard');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function showEmployeeProfile(Request $request)
    {
        //
        $employee = Auth::user()->employee;

        $selectedMonth = $request->get('month', now()->timezone('Asia/Manila')->format('Y-m'));
        $parsedMonth = Carbon::parse($selectedMonth);
        $now = now()->timezone('Asia/Manila');

        $hireDate = Carbon::parse($employee->created_at)->startOfDay();

        $attendanceRecords = Attendance::where('employee_id', $employee->employee_id)
            ->whereYear('attendance_date', $parsedMonth->year)
            ->whereMonth('attendance_date', $parsedMonth->month)
            ->get()
            ->map(function ($record) {
                $tz = 'Asia/Manila';
                $amIn  = $record->am_in  ? Carbon::parse($record->am_in, $tz) : null;
                $amOut = $record->am_out ? Carbon::parse($record->am_out, $tz) : null;
                $pmIn  = $record->pm_in  ? Carbon::parse($record->pm_in, $tz) : null;
                $pmOut = $record->pm_out ? Carbon::parse($record->pm_out, $tz) : null;
                $otIn  = $record->ot_in  ? Carbon::parse($record->ot_in, $tz) : null;
                $otOut = $record->ot_out ? Carbon::parse($record->ot_out, $tz) : null;

                $regularMinutes = 0;
                if ($amIn && $pmOut && !$amOut) {
                    if ($pmOut->gt($amIn)) { $regularMinutes = $amIn->diffInMinutes($pmOut); }
                } else {
                    if ($amIn && $amOut && $amOut->gt($amIn)) { $regularMinutes += $amIn->diffInMinutes($amOut); }
                    if ($pmIn && $pmOut && $pmOut->gt($pmIn)) { $regularMinutes += $pmIn->diffInMinutes($pmOut); }
                }

                if ($otIn && $otOut && $otOut->gt($otIn)) { $regularMinutes += $otIn->diffInMinutes($otOut); }

                $hours = floor($regularMinutes / 60);
                $minutes = $regularMinutes % 60;
                $record->computed_total_hours = "{$hours}h {$minutes}m";

                $requiredMinutes = 480;
                $diffMinutes = $regularMinutes - $requiredMinutes;

                if ($diffMinutes > 0) {
                    $h = floor($diffMinutes / 60); $m = $diffMinutes % 60;
                    $record->diff_ut_ot = "+{$h}h {$m}m";
                    $record->attendance_status = 'OVERTIME';
                    $record->status_color = 'emerald';
                } elseif ($diffMinutes < 0 && $regularMinutes > 0) {
                    $absDiff = abs($diffMinutes); $h = floor($absDiff / 60); $m = $absDiff % 60;
                    $record->diff_ut_ot = "-{$h}h {$m}m";
                    $record->attendance_status = 'UNDERTIME';
                    $record->status_color = 'red';
                } else {
                    $record->diff_ut_ot = '0h 0m';
                    $record->attendance_status = $regularMinutes > 0 ? 'REGULAR' : 'ABSENT';
                    $record->status_color = $regularMinutes > 0 ? 'emerald' : 'red';
                }
                return $record;
            })
            ->keyBy(function($item) {
                return Carbon::parse($item->attendance_date)->format('Y-m-d');
            });

        $daysInMonth = $parsedMonth->daysInMonth;
        $attendance = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentIterationDate = Carbon::create($parsedMonth->year, $parsedMonth->month, $day)->startOfDay();
            $dateStr = $currentIterationDate->format('Y-m-d');

            // Logic Flags
            $isFuture = $currentIterationDate->gt($now->startOfDay());
            $isBeforeHire = $currentIterationDate->lt($hireDate);
            
            $attendance[$day] = [
                'day_name'      => $currentIterationDate->format('D'),
                'date_str'      => $dateStr,
                'is_future'     => $isFuture,      // New flag
                'is_before_hire' => $isBeforeHire, // New flag
                'is_holiday'    => false,          // Replace with your holiday logic
                'is_leave'      => false,          // Replace with your leave logic
                'record'        => $attendanceRecords->get($dateStr) ?? null
            ];
        }

        if ($request->ajax()) {
            return view('partials.employees._monthly_attendance_table', compact('attendance', 'employee'))->render();
        }

        return view('employee.profile', compact('employee', 'attendance', 'selectedMonth'));

    }

    public function printEmployeeDtr($month, $year) 
    {
        $authEmployee = Auth::user()->employee;

        $employee = Employee::where('employee_id', $authEmployee->employee_id)->firstOrFail();
        
        $start = Carbon::create($year, $month, 1);
        $end = $start->copy()->endOfMonth();
        $period = CarbonPeriod::create($start, $end);

        $attendances = Attendance::where('employee_id', $authEmployee->employee_id)
            ->whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year)
            ->get()
            ->keyBy(function($item) {
                return Carbon::parse($item->attendance_date)->day;
            });

        $pdf = Pdf::loadView('dtr.print.blade', compact('employee', 'period', 'attendances', 'start'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream("DTR_{$employee->last_name}.pdf");
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
