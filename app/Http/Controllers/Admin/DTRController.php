<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAttendanceRequest;
use App\Models\Attendance;
use App\Models\AuditTrail;
use App\Models\Employee;
use App\Models\TravelOrder;
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

        $bureau = $request->input('bureau');
        $division = $request->input('division');
        $type = $request->input('type');
        $status = $request->input('status');

        $query = Attendance::with('employee')
            ->whereDate('attendance_date', $targetDate);

        if ($search) {
            $query->whereHas('employee', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        $query->whereHas('employee', function($q) use ($bureau, $division, $type, $status) {
            if ($bureau) {
                $q->where('bureau', $bureau);
            }
            if ($division) {
                $q->where('division', $division);
            }
            if ($type) {
                $q->where('employment_type', $type); 
            }
            if (isset($status)) { 
                $q->where('is_active', $status);
            }
        });

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

        // 1. Kunin lang ang mga fields na nasa validation (iwas sa extra inputs)
        $validated = $request->validated();
        $attendance->fill($validated);
        
        $changes = $attendance->getDirty(); 
        
        if (empty($changes)) {
            return redirect()->back()->with('info', "No changes were made.");
        }

        $oldData = [];
        $newData = [];
        
        // Tukuyin natin kung alin lang ang time fields
        $timeFields = ['am_in', 'am_out', 'pm_in', 'pm_out', 'ot_in', 'ot_out'];

        foreach ($changes as $field => $newValue) {
            $originalValue = $attendance->getOriginal($field);

            if (in_array($field, $timeFields)) {
                // Safe Parsing Logic
                $oldData[$field] = $this->safeFormatTime($originalValue);
                $newData[$field] = $this->safeFormatTime($newValue);
            } else {
                // Para sa 'remarks' o ibang non-time fields
                $oldData[$field] = $originalValue;
                $newData[$field] = $newValue;
            }
        }

        $attendance->save();

        // Audit Trail
        AuditTrail::create([
            'user_id'        => Auth::user()->employee_id,
            'event'          => 'Updated',
            'auditable_type' => get_class($attendance),
            'auditable_id'   => $attendance->id,
            'old_values'     => $oldData,
            'new_values'     => $newData,
            'remarks'        => $request->remarks ?? "Manual DTR adjustment",
            'ip_address'     => request()->ip(),
        ]);

        return redirect()->route('dtr.view')
                        ->with('success', "Attendance updated successfully.");
    }

    /**
     * Helper function para i-format ang time nang hindi nag-e-error
     */
    private function safeFormatTime($value)
    {
        if (empty($value) || $value === 'EMPTY') return 'EMPTY';
        
        try {
            return \Carbon\Carbon::parse($value)->format('h:i A');
        } catch (\Exception $e) {
            // Kung hindi talaga ma-parse (gaya ng "edit pm in"), ibalik ang original string
            return $value;
        }
    }

    public function generateDTR($employee_id, $month, $year) 
    {
        // dd($month, $year);
        $employee = Employee::where('employee_id', $employee_id)->firstOrFail();
        
        $start = Carbon::create($year, $month, 1);
        $end = $start->copy()->endOfMonth();
        $period = CarbonPeriod::create($start, $end);

        // Kunin ang holidays at i-format ang key bilang 'day'
        $holidays = \App\Models\Holiday::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get()
            ->keyBy(function($holiday) {
                return Carbon::parse($holiday->date)->day;
            });

        $travels = \App\Models\TravelOrder::where('employee_id', $employee_id)
            ->where(function($query) use ($start, $end) {
                $query->whereBetween('date_from', [$start->toDateString(), $end->toDateString()])
                    ->orWhereBetween('date_to', [$start->toDateString(), $end->toDateString()]);
            })
            ->get();


        $attendances = Attendance::where('employee_id', $employee_id)
            ->whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year)
            ->get()
            ->keyBy(function($item) {
                return Carbon::parse($item->attendance_date)->day;
            });

        // Isama ang $holidays sa compact
        $pdf = Pdf::loadView('dtr.print.blade', compact('employee', 'period', 'attendances', 'start', 'holidays', 'travels'))
                ->setPaper('a4', 'portrait');
                
        return $pdf->stream("DTR_{$employee->last_name}.pdf");
    }

    public function printDailyAttendance(Request $request)
    {
        $barmmLogo = base64_encode(file_get_contents(public_path('images/barmm-logo.png')));
        $bpdaLogo = base64_encode(file_get_contents(public_path('images/bpda-logo.jpg')));
        $targetDate = $request->input('date', now()->toDateString());
        $bureau = $request->input('bureau');
        $division = $request->input('division');

        $query = Attendance::with('employee')
            ->whereDate('attendance_date', $targetDate);

        // Apply same filters as index
        $query->whereHas('employee', function($q) use ($bureau, $division) {
            if ($bureau) $q->where('bureau', $bureau);
            if ($division) $q->where('division', $division);
        });

        $records = $query->get()->sortBy('employee.last_name');

        $pdf = Pdf::loadView('dtr.print.print-daily', [
            'records' => $records,
            'date' => Carbon::parse($targetDate)->format('F d, Y'),
            'bureau' => $bureau,
            'division' => $division,
            'barmmLogo' => $barmmLogo,
            'bpdaLogo' => $bpdaLogo,
        ])->setPaper('a4', 'landscape'); 

        return $pdf->stream("Daily_Attendance_{$targetDate}.pdf");
    }
}
