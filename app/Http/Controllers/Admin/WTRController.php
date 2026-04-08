<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Holiday;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WTRController extends Controller
{
    //
    public function index(Request $request) 
    {
        $weekInput = $request->input('date'); 

        if ($weekInput && str_contains($weekInput, '-W')) {
            $parts = explode('-W', $weekInput);
            $startOfWeek = Carbon::now()->setISODate((int)$parts[0], (int)$parts[1], 1)->startOfDay();
        } else {
            $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        }

        $endOfWeek = $startOfWeek->copy()->addDays(4)->endOfDay();

        $holidays = Holiday::whereBetween('date', [
            $startOfWeek->toDateString(), 
            $endOfWeek->toDateString()
        ])->get();

        $query = Employee::query();

        // --- DYNAMIC FILTERS ---
        
        // Search Filter
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                ->orWhere('middle_name', 'like', '%' . $request->search . '%')
                ->orWhere('last_name', 'like', '%' . $request->search . '%')
                ->orWhere('employee_id', 'like', '%' . $request->search . '%');
            });
        }

        // Bureau Filter
        if ($request->filled('bureau')) {
            $query->where('bureau', $request->bureau);
        }

        // Division Filter
        if ($request->filled('division')) {
            $query->where('division', $request->division);
        }

        // Employment Type Filter
        if ($request->filled('type')) {
            $query->where('employment_type', $request->type);
        }

        $employees = $query->with(['attendances' => function($q) use ($startOfWeek, $endOfWeek) {
            $q->whereBetween('attendance_date', [
                $startOfWeek->toDateString(), 
                $endOfWeek->toDateString()
            ]);
        }])->paginate(20)->withQueryString();

        if ($request->ajax()) {
            return view('partials.admin.timekeeping._wtr_table', compact('employees', 'startOfWeek', 'holidays'))->render();
        }

        return view('admin.timekeeping.view-wtr', compact('employees', 'startOfWeek', 'holidays'));
    }

// Sa loob ng WTRController.php

    public function calculateDailyMinutes($att, $dateStr, $holidays) 
    {
        // Check muna kung ang petsang ito ay holiday
        $isHoliday = $holidays->contains('date', $dateStr);
        
        if ($isHoliday) {
            return 480; // I-credit ang 8 hours (480 mins) para sa holiday
        }

        if (!$att) return 0;

        $totalMinutes = 0;

        // Case 1: Standard AM Session
        if ($att->am_in && $att->am_out) {
            $totalMinutes += Carbon::parse($att->am_in)->diffInMinutes(Carbon::parse($att->am_out));
        }

        // Case 2: Standard PM Session
        if ($att->pm_in && $att->pm_out) {
            $totalMinutes += Carbon::parse($att->pm_in)->diffInMinutes(Carbon::parse($att->pm_out));
        }

        // Case 3: Straight Duty (Deduct 1hr lunch)
        if ($att->am_in && $att->pm_out && !$att->am_out && !$att->pm_in) {
            $diff = Carbon::parse($att->am_in)->diffInMinutes(Carbon::parse($att->pm_out));
            $totalMinutes = max(0, $diff - 60); 
        }

        // Case 4: Overtime (Dagdag sa total)
        if ($att->ot_in && $att->ot_out) {
            $totalMinutes += Carbon::parse($att->ot_in)->diffInMinutes(Carbon::parse($att->ot_out));
        }

        return $totalMinutes;
    }

    public function print(Request $request) 
    {
        $barmmLogo = base64_encode(file_get_contents(public_path('images/barmm-logo.png')));
        $bpdaLogo = base64_encode(file_get_contents(public_path('images/bpda-logo.jpg')));
        // ... (Retention of your existing date logic)
        $weekInput = $request->input('date'); 
        if ($weekInput && str_contains($weekInput, '-W')) {
            $parts = explode('-W', $weekInput);
            $startOfWeek = Carbon::now()->setISODate((int)$parts[0], (int)$parts[1], 1)->startOfDay();
        } else {
            $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        }
        $endOfWeek = $startOfWeek->copy()->addDays(4)->endOfDay();

        $query = Employee::query();

        // Filters
        if ($request->filled('bureau')) $query->where('bureau', $request->bureau);
        if ($request->filled('division')) $query->where('division', $request->division);
        if ($request->filled('type')) $query->where('employment_type', $request->type);
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                ->orWhere('last_name', 'like', "%{$request->search}%");
            });
        }

        $employees = $query->with(['attendances' => function($q) use ($startOfWeek, $endOfWeek) {
            $q->whereBetween('attendance_date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()]);
        }])->get();

        $holidays = Holiday::whereBetween('date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])->get();

        // Load PDF view
        $pdf = Pdf::loadView('wtr.print-wtr', compact('employees', 'startOfWeek', 'holidays', 'bpdaLogo', 'barmmLogo'))
                ->setPaper('a4', 'landscape'); // Landscape para kasya ang 5 days

        return $pdf->stream('Weekly_Attendance_Report.pdf');
    }
}

