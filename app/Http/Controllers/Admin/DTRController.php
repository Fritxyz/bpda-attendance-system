<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAttendanceRequest;
use App\Models\Attendance;
use App\Models\AuditTrail;
use App\Models\Employee;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


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

            $totalMinutes = $regularMinutes;  
            $hours   = floor($totalMinutes / 60);
            $minutes = $totalMinutes % 60;
            $record->computed_total_hours = "{$hours}h {$minutes}m";

            $requiredMinutes = 8 * 60;
            $diffMinutes = $totalMinutes - $requiredMinutes;

            if ($diffMinutes > 0) {
                $diffH = floor($diffMinutes / 60);
                $diffM = $diffMinutes % 60;
                $record->diff_hours = "+{$diffH}h {$diffM}m";
                $record->attendance_status = 'OVERTIME';
                $record->status_color = 'blue';
            } elseif ($diffMinutes < 0 && $totalMinutes > 0) {
                $absDiff = abs($diffMinutes);
                $diffH = floor($absDiff / 60);
                $diffM = $absDiff % 60;
                $record->diff_hours = "-{$diffH}h {$diffM}m";
                $record->attendance_status = 'UNDERTIME';
                $record->status_color = 'rose';
            } else {
                $record->diff_hours = '0h 0m';
                $record->attendance_status = 'REGULAR';
                $record->status_color = 'emerald';
            }

            return $record;
        });

        if ($request->ajax()) {
            return view('partials.admin.timekeeping._dtr_table', compact('today_records'))->render();
        }

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

        $dateOnly = Carbon::parse($attendance->attendance_date)->toDateString();

        return view('admin.timekeeping.edit-dtr', compact('attendance', 'dateOnly'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttendanceRequest $request, $employee, $date)
    {
        $attendance = Attendance::where('employee_id', $employee)
                            ->where('attendance_date', $date)
                            ->firstOrFail();

        $formatTime = fn($time) => $time ? Carbon::parse($time)->format('Y-m-d h:i A') : 'EMPTY';

        $oldData = [
            'am_in'  => $formatTime($attendance->am_in),
            'am_out' => $formatTime($attendance->am_out),
            'pm_in'  => $formatTime($attendance->pm_in),
            'pm_out' => $formatTime($attendance->pm_out),
            'ot_in' => $formatTime($attendance->ot_in),
            'ot_out' => $formatTime($attendance->ot_out),
        ];

        $attendance->update($request->validated());

        $newData = [
            'am_in'  => $formatTime($attendance->am_in),
            'am_out' => $formatTime($attendance->am_out),
            'pm_in'  => $formatTime($attendance->pm_in),
            'pm_out' => $formatTime($attendance->pm_out),
            'ot_in'  => $formatTime($attendance->ot_in),
            'ot_out' => $formatTime($attendance->ot_out),
        ];

        // 4. I-record sa Audit Trail
        $data = AuditTrail::create([
            'user_id'        => Auth::getUser()->employee_id, // Ang Admin na nag-edit
            'event'          => 'Updated',
            'auditable_type' => get_class($attendance),
            'auditable_id'   => $attendance->id,
            'old_values'     => $oldData,
            'new_values'     => $newData,
            'remarks'        => $request->remarks ?? "Manual DTR adjustment for {$date}", // Maganda kung may 'reason' field sa form mo
            'ip_address'     => request()->ip(),
        ]);

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

    public function generateDTR($employee_id, $month, $year) 
    {
        $employee = Employee::where('employee_id', $employee_id)->firstOrFail();
        
        $start = Carbon::create($year, $month, 1);
        $end = $start->copy()->endOfMonth();
        $period = CarbonPeriod::create($start, $end);

        $attendances = Attendance::where('employee_id', $employee_id)
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
}
