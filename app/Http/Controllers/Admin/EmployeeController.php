<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Attendance;
use App\Models\AuditTrail;
use App\Models\Employee;
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
        $parsedMonth = Carbon::parse($selectedMonth);
        $now = now()->timezone('Asia/Manila');

        $hireDate = Carbon::parse($employee->created_at)->startOfDay();

        $attendanceRecords = Attendance::where('employee_id', $employee_id)
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
            return view('partials.admin.employees._monthly_attendance_table', compact('attendance', 'employee'))->render();
        }

        return view('admin.employees.show', compact('employee', 'attendance', 'selectedMonth'));
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
