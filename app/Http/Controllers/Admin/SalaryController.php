<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Holiday;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate = Carbon::parse($month)->endOfMonth();

        $holidays = Holiday::whereBetween('date', [$startDate, $endDate])
            ->get()
            ->map(function ($item) {
                return $item->date->format('Y-m-d');
            })
        ->toArray();
        

        // 2. Alamin kung aling mga araw ang "Working Days" (Mon-Fri, excluding holidays)
        $workingDays = [];
        $period = CarbonPeriod::create($startDate, $endDate);
        foreach ($period as $date) {
            // I-exclude ang weekends (Saturday=6, Sunday=0) at Holidays
            if (!$date->isWeekend() && !in_array($date->toDateString(), $holidays)) {
                $workingDays[] = $date->toDateString();
            }
        }

        $totalWorkingDaysCount = count($workingDays);

        $employees = Employee::where('employment_type', 'Contractual')
            ->with(['attendances' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('attendance_date', [$startDate->toDateString(), $endDate->toDateString()]);
            }])->get();

        $totalGrossSalary = $employees->sum('salary');
        $totalDeductions = 0;

        foreach($employees as $emp) {
            $dailyRate = $emp->salary / 22; // Assumption: 22 working days average
            $empDeduction = 0;

            // Kunin ang mga dates na may record ang employee
            $presentDates = $emp->attendances->pluck('attendance_date')->toArray();

            // 3. I-check bawat working day kung pumasok siya
            foreach ($workingDays as $day) {
                if (!in_array($day, $presentDates)) {
                    // WALANG RECORD = ABSENT
                    $empDeduction += $dailyRate; 
                } else {
                    // MAY RECORD = Check naman natin kung LATE o UNDERTIME
                    $attendance = $emp->attendances->where('attendance_date', $day)->first();
                    
                    // Sample: Rate per minute (Daily Rate / 8 hours / 60 mins)
                    $ratePerMinute = ($dailyRate / 8) / 60;
                    
                    if ($attendance->late_minutes > 0) {
                        $empDeduction += $attendance->late_minutes * $ratePerMinute;
                    }
                    if ($attendance->undertime_minutes > 0) {
                        $empDeduction += $attendance->undertime_minutes * $ratePerMinute;
                    }
                }
            }

            $emp->computed_deduction = $empDeduction;
            $emp->net_salary = $emp->salary - $empDeduction;
            $totalDeductions += $empDeduction;
        }

        return view('admin.outputs.index', compact('employees', 'totalGrossSalary', 'totalDeductions', 'month'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function salaryDeductionOverview(Request $request)
    {
        //
        $month = $request->input('month', now()->format('Y-m'));
        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate = Carbon::parse($month)->endOfMonth();

        
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
