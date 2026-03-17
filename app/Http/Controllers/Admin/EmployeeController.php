<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
        // 1. Simulan ang query
        $query = Employee::query();
        
        // 2. Search Filter (Name o ID)
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

        // Eto ang trick: Kapag AJAX, table partial lang ang ibabalik
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
            
            // 1. I-prepare ang basic data
            $validated['is_active'] = $request->has('is_active');
            $validated['employee_id'] = 'BPDA-' . $request->input('employee_id');

            // 2. Handle Image Upload
            if ($request->hasFile('profile_picture')) {
                $extension = $request->file('profile_picture')->getClientOriginalExtension();
                // Mas maganda kung ang ID ang filename para unique
                $fileName = 'BPDA-' . $request->input('employee_id') . '.' . $extension;
                
                // I-store ang file
                $imagePath = $request->file('profile_picture')->storeAs('profiles', $fileName, 'public');
                
                // Eto ang kulang mo: I-update ang validated array para ang path ang ma-save, hindi yung file object
                $validated['profile_picture'] = $imagePath; 
            }

            // 3. I-save ang Employee Profile
            $employee = Employee::create($validated);

            // 4. I-create ang User Account
            $user = User::create([
                'employee_id' => $employee->employee_id,
                'password'    => $request->input('password'),
                'role'        => $request->input('role', 'Employee'),
            ]);

            return redirect()->route('employees.index')
                ->with('success', "Employee and Account created! Employee ID: {$user->employee_id}");
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $employee_id)
    {
        $employee = Employee::where('employee_id', $employee_id)->firstOrFail();
        $selectedMonth = $request->get('month', now()->timezone('Asia/Manila')->format('Y-m'));
        $date = Carbon::parse($selectedMonth);

        $attendanceRecords = Attendance::where('employee_id', $employee_id)
            ->whereYear('attendance_date', $date->year)
            ->whereMonth('attendance_date', $date->month)
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

        $daysInMonth = $date->daysInMonth;
        $attendance = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDate = Carbon::create($date->year, $date->month, $day);
            $dateStr = $currentDate->format('Y-m-d');
            
            // --- STATIC DATA MOCKUP ---
            $isHoliday = ($day == 5); // Example: May 5 is Holiday
            $isOnLeave = ($day == 8); // Example: May 8 is Leave
            // --------------------------

            $attendance[$day] = [
                'day_name'   => $currentDate->format('D'),
                'date_str'   => $dateStr,
                'is_holiday' => $isHoliday,
                'is_leave'   => $isOnLeave,
                'record'     => $attendanceRecords->get($dateStr) ?? null
            ];
        }

        if ($request->ajax()) {
            return view('partials.admin.employees._monthly_attendance_table', compact('attendance'))->render();
        }

        return view('admin.employees.show', compact('employee', 'attendance', 'selectedMonth'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        // dd($employee->all());
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
        // todo: ayusin ang update logic haha and test rigidly hahaha
        // 1. Hanapin ang record (Dito natin kukunin ang Object)
        $formattedId = str_starts_with($employee, 'BPDA-') ? $employee : 'BPDA-' . $employee;
        $employeeRecord = Employee::where('employee_id', $formattedId)->firstOrFail();

        $validated = $request->validated();

        $validated['employee_id'] = 'BPDA-' . $request->employee_id;
        $validated['is_active'] = $request->has('is_active');

        if ($request->employment_type === 'Permanent') {
            $validated['salary'] = null;
        }

        // dd($validated);

        // 2. Handle Profile Picture (Same logic mo)
        if ($request->hasFile('profile_picture') && $request->file('profile_picture')->isValid()) {
            if ($employeeRecord->profile_picture && !str_contains($employeeRecord->profile_picture, 'bpda-logo.jpg')) {
                Storage::disk('public')->delete($employeeRecord->profile_picture);
            }
            $path = $request->file('profile_picture')->store('employees/profiles', 'public');
            $validated['profile_picture'] = $path;
        }

        // 3. Handle Password & User logic
        if ($request->filled('password') || $request->filled('role') || $request->filled('employee_id')) {
            $user = User::where('employee_id', $employeeRecord->getOriginal('employee_id'))->first();
            
            if ($user) {
                $userData = [];
                
                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($request->password);
                }
                
                // I-sync ang employee_id at role sa User account
                $userData['employee_id'] = $validated['employee_id'];
                $userData['role'] = $validated['role'];

                $user->update($userData);
            }
        }

        // 4. Eto ang importante: Gamitin ang Object ($employeeRecord), hindi yung parameter string
        $employeeRecord->update($validated);

        return redirect()
            ->route('employees.index')
            ->with('success', "Employee {$employeeRecord->first_name} successfully updated.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
