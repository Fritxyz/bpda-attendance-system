<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Attendance;
use App\Models\AuditTrail;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    /**
     * Display a (filtered/unfiltered) listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Employee::query();
        
        if ($request->filled('search-input')) {
            $search = $request->input('search-input');
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('bureau')) {
            $query->where('bureau', $request->bureau);
        }

        if ($request->filled('division')) {
            $query->where('division', $request->division);
        }

        if ($request->filled('type')) {
            $query->where('employment_type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('is_Active', $request->status);
        }

        $employees = $query->latest()->paginate(10)->appends($request->all( ));

        if ($request->ajax() || $request->has('ajax')) {
            return view('partials.admin.employees.table', compact('employees'))->render();
        }

        return view('admin.employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
        $year = date('Y'); 
        $count = Employee::whereYear('created_at', $year)->count() + 1;
        $employee_id = $autoID = $year . str_pad($count, 4, '0', STR_PAD_LEFT);

        return view('admin.employees.create', compact('employee_id'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        $validated = $request->validated();

        return DB::transaction(function () use ($request, $validated) {
            $validated['is_active'] = $request->has('is_active');
            $validated['employee_id'] = 'BPDA-' . $request->input('employee_id');

            if ($request->hasFile('profile_picture')) {
                $extension = $request->file('profile_picture')->getClientOriginalExtension();
                $fileName = 'BPDA-' . $request->input('employee_id') . '.' . $extension;
                
                $imagePath = $request->file('profile_picture')->storeAs('profiles', $fileName, 'public');
                $validated['profile_picture'] = $imagePath; 
            }

            $employee = Employee::create($validated);

            $user = User::create([
                'employee_id' => $employee->employee_id,
                'password'    => $request->input('password'),
                'role'        => $request->input('role', 'Employee'),
            ]);

            $fieldsToAudit = [
                'first_name', 'last_name', 'middle_name', 'role', 'suffix', 
                'bureau', 'division', 'position', 'salary', 'employment_type', 
                'is_active', 'profile_picture'
            ];

            $auditSnapshot = array_merge(
                $employee->only($fieldsToAudit),
                ['role' => $user->role]
            );

            AuditTrail::create([
                'user_id'        => Auth::getUser()->employee_id, 
                'event'          => 'Created',
                'auditable_type' => get_class($employee),
                'auditable_id'   => $user->id,
                'old_values'     => json_encode([]), 
                'new_values'     => $auditSnapshot,
                'ip_address'     => $request->ip(),
                'remarks'        => "Registered new personnel: " . $employee->first_name . " " . $employee->last_name,
            ]);

            return redirect()->route('employees.index')
                ->with('success', "Employee record and associated account successfully established. ID: {$user->employee_id}");
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $employee_id)
    {
        $employee = Employee::where('employee_id', $employee_id)->firstOrFail();
        $selectedMonth = $request->get('month', now()->timezone('Asia/Manila')->format('Y-m'));
        $parsedMonth = Carbon::parse($selectedMonth);
        $now = now()->timezone('Asia/Manila');
        $hireDate = Carbon::parse($employee->created_at)->startOfDay();
        $parsedMonth = Carbon::parse($selectedMonth);
        $startDate = $parsedMonth->copy()->startOfMonth();
        $endDate = $parsedMonth->copy()->endOfMonth();

        $holidays = \App\Models\Holiday::whereBetween('date', [$startDate, $endDate])
            ->get()
            ->keyBy(function($holiday) {
                return \Carbon\Carbon::parse($holiday->date)->format('Y-m-d');
            })
            ->map(function($holiday) {
                return $holiday->name; 
            })
            ->toArray();

        $employee_type = $employee->employment_type;

        if($employee_type === "Contractual") {
            $attendanceRecords = Attendance::where('employee_id', $employee_id)
                ->whereYear('attendance_date', $parsedMonth->year)
                ->whereMonth('attendance_date', $parsedMonth->month)
                ->get()
                ->map(function ($record) use($employee) {
                    $tz = 'Asia/Manila';
                    $dateStr = Carbon::parse($record->attendance_date)->format('Y-m-d');

                    // Helper function para sigurado tayong "Today" ang date ng bawat log
                    $parseTime = function($time) use ($dateStr, $tz) {
                        if (!$time) return null;
                        $onlyTime = date('H:i:s', strtotime($time));
                        return Carbon::parse($dateStr . ' ' . $onlyTime, $tz);
                    };

                    $amIn  = $parseTime($record->am_in);
                    $amOut = $parseTime($record->am_out);
                    $pmIn  = $parseTime($record->pm_in);
                    $pmOut = $parseTime($record->pm_out);
                    $otIn  = $parseTime($record->ot_in);
                    $otOut = $parseTime($record->ot_out);

                    $rawDailyRate = $employee->salary / 22; 
                    $dailyRate = floor($rawDailyRate * 100) / 100;
                    $rawHourlyRate = $dailyRate / 8;
                    $hourlyRate = floor($rawHourlyRate * 100) / 100;
                    $rawMinuteRate = $hourlyRate / 60;
                    $minuteRate = floor($rawMinuteRate * 100) / 100;

                    // --- LATE COMPUTATION ---
                    $lateMinutes = 0;
                    $morningSched = Carbon::parse($dateStr . ' 08:15:00', $tz);
                    $afternoonSched = Carbon::parse($dateStr . ' 13:15:00', $tz);

                    if ($amIn && $amIn->gt($morningSched)) {
                        $lateMinutes += $morningSched->diffInMinutes($amIn);
                    }
                    if (!$amIn) {
                        if($pmIn && $pmIn->gt($afternoonSched)) {
                            $lateMinutes += $afternoonSched->diffInMinutes($pmIn);
                        } 
                    }

                    $record->salary_deduction_by_late = floor(($minuteRate * $lateMinutes) * 100) / 100;

                    $record->computed_late = ($lateMinutes > 0) ? 
                        (floor($lateMinutes / 60) > 0 ? floor($lateMinutes / 60)."h ".($lateMinutes % 60)."m" : ($lateMinutes % 60)."m") 
                        : null;

                    // --- TOTAL HOURS COMPUTATION ---
                    $totalMinutes = 0;

                    if(($amIn && !$amOut) && (!$pmIn && $pmOut)) {
                        $totalMinutes = $amIn->diffInMinutes($pmOut);  
                        $totalMinutes -= 60; 
                    } else if(($amIn && $amOut) && (!$pmIn && $pmOut)) {
                        $totalMinutes = $amIn->diffInMinutes($pmOut);  
                        $totalMinutes -= 60; 
                    } else if(($amIn && !$amOut) && ($pmIn && $pmOut)) {
                        $totalMinutes = $amIn->diffInMinutes($pmOut);  
                        $totalMinutes -= 60; 
                    } else {
                        // Morning Shift
                        if ($amIn && $amOut && $amOut->gt($amIn)) {
                            $totalMinutes += $amIn->diffInMinutes($amOut);
                        }
                        // Afternoon Shift
                        if ($pmIn && $pmOut && $pmOut->gt($pmIn)) {
                            $totalMinutes += $pmIn->diffInMinutes($pmOut);
                        }
                        // Overtime Shift
                        if ($otIn && $otOut && $otOut->gt($otIn)) {
                            $totalMinutes += $otIn->diffInMinutes($otOut);
                        }
                    }

                    $h = floor($totalMinutes / 60);
                    $m = $totalMinutes % 60;
                    $record->computed_total_hours = "{$h}h {$m}m";

                    $record->salary_today = $totalMinutes * $minuteRate;

                    // --- UT / OT LOGIC ---
                    $requiredMinutes = 480; // 8 hours
                    $diffWithoutLate = $totalMinutes - $requiredMinutes;
                    $diff = $diffWithoutLate + $lateMinutes;


                    $record->salary_today = floor(($totalMinutes * $minuteRate) * 100) / 100;

                    $record->salary_deduction_undertime = 0;

                    if ($diff > 0) {
                        // OVERTIME
                        $record->diff_ut_ot = "+" . floor($diff / 60) . "h " . ($diff % 60) . "m";
                        $record->attendance_status = 'OVERTIME';
                        $record->status_color = 'emerald';
                        $record->salary_deduction_undertime = 0; // Walang bawas
                    } elseif ($diff < 0 && $totalMinutes > 0) {
                        // UNDERTIME
                        $absDiff = abs($diff  - $lateMinutes);
                        $record->diff_ut_ot = "-" . floor($absDiff / 60) . "h " . ($absDiff % 60) . "m";
                        $record->attendance_status = 'UNDERTIME';
                        $record->status_color = 'rose';
                        
                        // Computation ng bawas base sa nawalang minuto
                        $absDiff = abs($diff - $lateMinutes); 
                        $record->salary_deduction_undertime = floor(($absDiff * $minuteRate) * 100) / 100;
                    } elseif ($totalMinutes >= 480) {
                        // REGULAR (Eksaktong 8 oras o higit pa pero di counted as OT)
                        $record->diff_ut_ot = '0h 0m';
                        $record->attendance_status = 'REGULAR';
                        $record->status_color = 'emerald';
                        $record->salary_deduction_undertime = 0; // Walang bawas
                    } else {
                        // ABSENT (0 ang total minutes)
                        $record->diff_ut_ot = '-8h 0m';
                        $record->attendance_status = 'ABSENT';
                        $record->status_color = 'rose';
                        $record->salary_deduction_undertime = $dailyRate;
                        $record->salary_deduction_by_late = 0; // No late if absent
                    }

                    $record->total_day_deduction = $record->salary_deduction_undertime + $record->salary_deduction_by_late;
                    return $record;
                })
                ->keyBy(fn($item) => Carbon::parse($item->attendance_date)->format('Y-m-d'));

            $daysInMonth = $parsedMonth->daysInMonth;
            $attendance = [];

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $currentIterationDate = Carbon::create($parsedMonth->year, $parsedMonth->month, $day)->startOfDay();
                $dateStr = $currentIterationDate->format('Y-m-d');

                // Logic Flags
                $isFuture = $currentIterationDate->gt($now->startOfDay());
                $isBeforeHire = $currentIterationDate->lt($hireDate);
                $isWeekend = in_array($currentIterationDate->format('D'), ['Sat', 'Sun']);
                $isHoliday = isset($holidays[$dateStr]);

                // Bagong Logic para sa Absent Deduction
                $isAbsent = (!$attendanceRecords->has($dateStr) && !$isWeekend && !$isHoliday && !$isFuture && !$isBeforeHire);

                if ($isAbsent) {
                    // Dito lang tayo mag-a-apply ng deduction kung employed na siya at absent
                    $absentDeduction = floor(($employee->salary / 22) * 100) / 100;
                } else {
                    $absentDeduction = 0;
                }
                
                $attendance[$day] = [
                    'day_name'      => $currentIterationDate->format('D'),
                    'date_str'      => $dateStr,
                    'is_future'     => $isFuture,      
                    'is_before_hire' => $isBeforeHire, 
                    'is_holiday'     => isset($holidays[$dateStr]), 
                    'holiday_name'   => $holidays[$dateStr] ?? null, 
                    'is_leave'      => false,          
                    'record'        => $attendanceRecords->get($dateStr) ?? null
                ];
            }

            if ($request->ajax()) {
                return view('partials.admin.employees._monthly_attendance_table', compact('attendance', 'employee', 'attendanceRecords'))->render();
            }

            return view('admin.employees.show', compact('employee', 'attendance', 'selectedMonth', 'attendanceRecords'));
        } else if($employee_type === "Permanent") {
            $selectedMonth = $request->query('month', Carbon::now()->format('Y-m'));
            $parsedMonth = Carbon::parse($selectedMonth);
            // 1. Kunin ang current balance mula sa database.
            // Ito ang magiging 'Base' natin.
            $startingBalance = $employee->leave_credits;

            $totalMonthlyDeduction = 0;

            $attendanceRecords = Attendance::where('employee_id', $employee_id)
                ->whereYear('attendance_date', $parsedMonth->year)
                ->whereMonth('attendance_date', $parsedMonth->month)
                ->get()
                ->map(function ($record) use (&$totalMonthlyDeduction) {
                    $tz = 'Asia/Manila';
                    $dateStr = Carbon::parse($record->attendance_date)->format('Y-m-d');

                    $parseTime = function($time) use ($dateStr, $tz) {
                        if (!$time) return null;
                        return Carbon::parse($dateStr . ' ' . date('H:i:s', strtotime($time)), $tz);
                    };

                    $amIn = $parseTime($record->am_in); $amOut = $parseTime($record->am_out);
                    $pmIn = $parseTime($record->pm_in); $pmOut = $parseTime($record->pm_out);

                    // CSC Late/UT Computation
                    $lateMinutes = 0;
                    $morningSched = Carbon::parse($dateStr . ' 08:15:00', $tz);
                    $afternoonSched = Carbon::parse($dateStr . ' 13:15:00', $tz);

                    if ($amIn && $amIn->gt($morningSched)) $lateMinutes += $morningSched->diffInMinutes($amIn);
                    if (!$amIn && $pmIn && $pmIn->gt($afternoonSched)) $lateMinutes += $afternoonSched->diffInMinutes($pmIn);

                    $totalMinutes = 0;
                    if ($amIn && $amOut && $amOut->gt($amIn)) $totalMinutes += $amIn->diffInMinutes($amOut);
                    if ($pmIn && $pmOut && $pmOut->gt($pmIn)) $totalMinutes += $pmIn->diffInMinutes($pmOut);

                    $requiredMinutes = 480;
                    $undertimeMinutes = ($totalMinutes > 0 && $totalMinutes < $requiredMinutes) 
                        ? max(0, ($requiredMinutes - $totalMinutes) - $lateMinutes) 
                        : 0;

                    $totalLostMinutes = $lateMinutes + $undertimeMinutes;
                    
                    if ($totalMinutes == 0) {
                        $record->credit_deduction = 1.000; // Absent
                    } else {
                        $record->credit_deduction = round($totalLostMinutes * 0.00208333, 3);
                    }

                    $totalMonthlyDeduction += $record->credit_deduction;
                    
                    $record->computed_late = ($lateMinutes > 0) ? "{$lateMinutes}m" : null;
                    $record->computed_undertime = ($undertimeMinutes > 0) ? "{$undertimeMinutes}m" : null;
                    $record->computed_total_hours = floor($totalMinutes / 60) . "h " . ($totalMinutes % 60) . "m";

                    return $record;
                })->keyBy(fn($item) => Carbon::parse($item->attendance_date)->format('Y-m-d'));

            $daysInMonth = $parsedMonth->daysInMonth;
            $attendance = [];

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $currentIterationDate = Carbon::create($parsedMonth->year, $parsedMonth->month, $day)->startOfDay();
                $dateStr = $currentIterationDate->format('Y-m-d');

                $isFuture = $currentIterationDate->gt($now->startOfDay());
                $isBeforeHire = $currentIterationDate->lt($hireDate);
                $isWeekend = in_array($currentIterationDate->format('D'), ['Sat', 'Sun']);
                $isHoliday = isset($holidays[$dateStr]);

                // Absent Logic: Employed na siya pero walang record
                if (!$attendanceRecords->has($dateStr) && !$isWeekend && !$isHoliday && !$isFuture && !$isBeforeHire) {
                    $totalMonthlyDeduction += 1.000;
                }

                $attendance[$day] = [
                    'day_name' => $currentIterationDate->format('D'),
                    'date_str' => $dateStr,
                    'is_future' => $isFuture,
                    'is_before_hire' => $isBeforeHire,
                    'is_holiday' => $isHoliday,
                    'holiday_name' => $holidays[$dateStr] ?? null,
                    'record' => $attendanceRecords->get($dateStr) ?? null
                ];
            }

            // Calculation para sa UI:
            // Ang 'Current Balance' sa DB ay ang final amount. 
            // Para makuha ang 'Starting Balance' ng buwan, i-reverse natin ang deduction.
            $endingBalance = $startingBalance - $totalMonthlyDeduction;

            $currentStoredCredits = $endingBalance;

            $viewData = compact('attendance', 'employee', 'currentStoredCredits', 'totalMonthlyDeduction', 'startingBalance', 'endingBalance', 'selectedMonth');

            return $request->ajax() 
                ? view('partials.admin.employees._monthly_attendance_table', $viewData)->render()
                : view('admin.employees.show', $viewData);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        $user = User::where("employee_id", '=', $employee->employee_id)->first();
        $employee->employee_id = substr($employee->employee_id, 5);

        return view('admin.employees.edit', [
            'employee_id'=> $employee->employee_id,
            'employee' => $employee,
            'user_role' => $user->role,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, $employee) 
    {
        $formattedId = str_starts_with($employee, 'BPDA-') ? $employee : 'BPDA-' . $employee;
        $employeeRecord = Employee::where('employee_id', $formattedId)->firstOrFail();
        $user = User::where('employee_id', $employeeRecord->employee_id)->first();

        $validated = $request->validated();
        $validated['employee_id'] = 'BPDA-' . $request->employee_id;
        $validated['is_active'] = $request->has('is_active');

        if ($request->employment_type === 'Permanent') {
            $validated['salary'] = null;
        }

        if ($request->hasFile('profile_picture') && $request->file('profile_picture')->isValid()) {
            if ($employeeRecord->profile_picture && !str_contains($employeeRecord->profile_picture, 'bpda-logo.jpg')) {
                Storage::disk('public')->delete($employeeRecord->profile_picture);
            }
            $path = $request->file('profile_picture')->store('employees/profiles', 'public');
            $validated['profile_picture'] = $path;
        }

        $employeeRecord->fill($validated);

        $userWillChange = false;
        if ($user) {
            $passwordChanged = $request->filled('password');
            $roleChanged = $user->role !== $validated['role'];
            $idChanged = $user->employee_id !== $validated['employee_id'];
            
            if ($passwordChanged || $roleChanged || $idChanged) {
                $userWillChange = true;
            }
        }

        if (!$employeeRecord->isDirty() && !$userWillChange) {
            return redirect()
                ->route('employees.index')
                ->with('info', "No changes were detected for {$employeeRecord->first_name} {$employeeRecord->last_name}.");
        }

        $fieldsToAudit = ['first_name', 'last_name', 'middle_name', 'role', 'suffix', 'bureau', 'division', 'position', 'salary', 'employment_type', 'is_active', 'profile_picture'];
        $oldEmployeeData = $employeeRecord->getOriginal();
        $oldUserData = $user ? $user->only(['role']) : [];
        $combinedOldData = array_merge($oldEmployeeData, $oldUserData);

        $newUserData = [];
        if ($user && $userWillChange) {
            $userData = [
                'employee_id' => $validated['employee_id'],
                'role'        => $validated['role'],
            ];
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            $user->update($userData);
            $newUserData = $user->only(['role']);
        }

        $employeeRecord->save();

        $newEmployeeData = $employeeRecord->only($fieldsToAudit);
        $combinedNewData = array_merge($newEmployeeData, $newUserData);

        $finalOldValues = [];
        $finalNewValues = [];

        foreach ($combinedNewData as $key => $newValue) {
            $oldValue = $combinedOldData[$key] ?? null;

            if ($oldValue != $newValue) {
                $finalOldValues[$key] = $oldValue;
                $finalNewValues[$key] = $newValue;
            }
        }

        AuditTrail::create([
            'user_id'        => Auth::getUser()->employee_id, 
            'event'          => 'Updated',
            'auditable_type' => get_class($employeeRecord),
            'auditable_id'   => $employeeRecord->id,
            'old_values'     => $finalOldValues,
            'new_values'     => $finalNewValues,
            'remarks'        => "Updated profile for personnel: {$employeeRecord->first_name} {$employeeRecord->last_name}",
            'ip_address'     => request()->ip(),
        ]);

        return redirect()
            ->route('employees.index')
            ->with('success', "Employee {$employeeRecord->first_name} successfully updated.");
    }
}
