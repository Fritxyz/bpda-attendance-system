@extends('layouts.admin.top-and-side-bar')

@section('header', 'Audit Trails')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    {{-- Breadcrumb --}}
    <nav class="flex mb-8 text-sm" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li class="flex items-center gap-2">
                <i class="bi bi-shield-check text-xs"></i>
                <span class="text-gray-500 hover:text-emerald-600 transition flex items-center gap-1">Security</span>
                <i class="bi bi-chevron-right text-[10px] text-gray-400"></i>
            </li>
            <li>
                <button class="font-bold text-emerald-900 uppercase tracking-wider text-[11px]">System Logs</button>
            </li>
        </ol>
    </nav>

    {{-- Header Section --}}
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-8">
        <div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">Audit Trails</h2>
            <p class="text-sm text-slate-500 mt-1">Track all administrative actions and system changes.</p>
        </div>
        
        <form action="{{ route('admin.audittrail') }}" method="GET" class="flex flex-wrap items-center justify-end gap-2 bg-slate-50/50 p-1.5 rounded-2xl border border-slate-100">
            <div class="flex items-center bg-white border border-slate-200 rounded-xl shadow-sm px-2">
                <span class="text-[9px] font-black text-slate-400 uppercase px-2 border-r border-slate-100 mr-2">Module</span>
                <select name="type" onchange="this.form.submit()" class="py-2 bg-transparent text-xs font-bold outline-none text-slate-600 cursor-pointer uppercase border-none focus:ring-0">
                    <option value="">All Modules</option>
                    <option value="Attendance" {{ request('type') == 'Attendance' ? 'selected' : '' }}>Attendance</option>
                    <option value="Holiday" {{ request('type') == 'Holiday' ? 'selected' : '' }}>Holidays</option>
                    <option value="Employee" {{ request('type') == 'Employee' ? 'selected' : '' }}>Employee</option>
                </select>
            </div>
            
            <a href="{{ route('admin.audittrail') }}" class="bg-white text-slate-600 border border-slate-200 px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-sm hover:bg-slate-50 transition-all">
                Reset
            </a>
        </form>
    </div>

    {{-- Table Section --}}
    <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Timestamp</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Performer</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Event</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Details</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Changes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        {{-- Timestamp --}}
                        <td class="px-6 py-4">
                            <div class="text-xs font-bold text-slate-700">
                                {{ $log->created_at->timezone('Asia/Manila')->format('M d, Y') }}
                            </div>
                            <div class="text-[10px] text-emerald-600 font-black uppercase tracking-tighter">
                                {{ $log->created_at->timezone('Asia/Manila')->format('h:i A') }}
                            </div>
                        </td>

                        {{-- Performer --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded-full bg-slate-900 text-white flex items-center justify-center font-black text-[10px]">
                                    {{ strtoupper(substr($log->user->first_name ?? 'S', 0, 1)) }}{{ strtoupper(substr($log->user->last_name ?? 'S', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="text-xs font-black text-slate-800 uppercase tracking-tight">
                                        {{ $log->user->first_name ?? 'System' }} {{ $log->user->last_name ?? '' }}
                                    </div>
                                    <div class="text-[9px] text-slate-400 font-medium">{{ $log->ip_address }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- Event --}}
                        <td class="px-6 py-4">
                            @php
                                $colors = [
                                    'created' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    'updated' => 'bg-amber-50 text-amber-700 border-amber-100',
                                    'deleted' => 'bg-rose-50 text-rose-700 border-rose-100',
                                ];
                                $eventKey = strtolower($log->event);
                                $colorClass = $colors[$eventKey] ?? 'bg-slate-50 text-slate-600 border-slate-100';
                            @endphp
                            <span class="px-2 py-0.5 rounded-md border {{ $colorClass }} text-[9px] font-black uppercase">
                                {{ $log->event }}
                            </span>
                        </td>

                        {{-- Details Column --}}
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-2">
                                {{-- Module Badge --}}
                                <div class="flex items-center gap-1.5">
                                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Type:</span>
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">
                                        {{ class_basename($log->auditable_type) }}
                                    </span>
                                </div>

                                {{-- Target Data Display --}}
                                <div class="flex flex-col bg-slate-50/80 p-2 rounded-lg border border-slate-100">
                                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Target Data:</span>
                                    <div class="text-[11px] font-bold text-slate-800">
                                        
                                        @if($log->auditable_type == 'App\Models\Attendance')
                                            @if($log->auditable)
                                                <div class="flex flex-col">
                                                    <span class="uppercase">DTR OF: {{ $log->auditable->employee->first_name ?? '' }} {{ $log->auditable->employee->last_name ?? 'Unknown' }}</span>
                                                    <span class="text-[10px] text-slate-500 font-medium">
                                                        <i class="bi bi-calendar3 mr-1"></i>Date: {{ \Carbon\Carbon::parse($log->auditable->attendance_date)->format('M d, Y') }}
                                                    </span>
                                                </div>
                                            @else
                                                <span class="text-slate-400 italic">Attendance Record Deleted (#{{ $log->auditable_id }})</span>
                                            @endif

                                        @elseif($log->auditable_type == 'App\Models\Employee')
                                            @if($log->auditable)
                                                <div class="flex flex-col">
                                                    <span class="uppercase">PERSONNEL: {{ $log->auditable->first_name }} {{ $log->auditable->last_name }}</span>
                                                    <span class="text-[10px] text-slate-500 font-medium">ID: {{ $log->auditable->employee_id }}</span>
                                                </div>
                                            @else
                                                <span class="text-slate-400 italic">Employee Record Deleted (#{{ $log->auditable_id }})</span>
                                            @endif

                                        @elseif($log->auditable_type == 'App\Models\Holiday')
                                            @if($log->auditable)
                                                <div class="flex flex-col">
                                                    <span class="uppercase">HOLIDAY: {{ $log->auditable->name }}</span>
                                                    <span class="text-[10px] text-slate-500 font-medium">
                                                        <i class="bi bi-calendar-event mr-1"></i>Date: {{ \Carbon\Carbon::parse($log->auditable->date)->format('M d, Y') }}
                                                    </span>
                                                </div>
                                            @else
                                                {{-- Kapag deleted na, hugutin sa JSON values ang huling name --}}
                                                @php
                                                    $fallbackValues = is_array($log->old_values) ? $log->old_values : json_decode($log->old_values, true);
                                                    $fallbackName = $fallbackValues['name'] ?? 'Unknown Holiday';
                                                @endphp
                                                <div class="flex flex-col">
                                                    <span class="text-slate-500 font-bold uppercase">HOLIDAY: {{ $fallbackName }}</span>
                                                    <span class="text-rose-500 text-[9px] font-black uppercase tracking-tighter italic">Record Deleted</span>
                                                    <span class="text-[9px] text-slate-400">Ref ID: #{{ $log->auditable_id }}</span>
                                                </div>
                                            @endif

                                        @else
                                            <span class="text-slate-400 italic font-medium">ID: #{{ $log->auditable_id }}</span>
                                        @endif

                                    </div>
                                </div>

                                {{-- Remarks --}}
                                <div class="flex flex-col">
                                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Remarks:</span>
                                    <p class="text-[10px] text-slate-600 italic leading-tight bg-white border-l-2 border-emerald-500 pl-2 py-1">
                                        {{ $log->remarks ?: 'No remarks provided.' }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        {{-- Changes Column --}}
                        <td class="px-6 py-4">
                            @if(strtolower($log->event) == 'updated')
                                <div class="grid grid-cols-1 gap-1.5 max-w-[250px]">
                                    @php 
                                        $newVals = is_array($log->new_values) ? $log->new_values : json_decode($log->new_values, true);
                                        $oldVals = is_array($log->old_values) ? $log->old_values : json_decode($log->old_values, true);
                                        
                                        $displayOrder = [
                                            'am_in' => 'AM In', 'am_out' => 'AM Out',
                                            'pm_in' => 'PM In', 'pm_out' => 'PM Out',
                                            'ot_in' => 'OT In', 'ot_out' => 'OT Out',
                                            'first_name' => 'First Name', 'middle_name' => 'Middle Name', 'last_name' => 'Last Name', 'suffix' => "Suffix",
                                            'bureau' => 'Bureau', 'division' => 'Division', 'position' => 'Current Position', 
                                            'salary' => 'Salary', 'employment_type' => 'Employment Type', 'is_active' => 'Status',
                                            'profile_picture' => 'Profile Picture', 'role' => 'Role',
                                            'name' => 'Holiday Name',
                                            'date' => 'Holiday Date',
                                            'type' => 'Category',
                                            'reference' => 'Reference',
                                            'remarks' => 'Remarks'
                                        ];

                                        // I-check muna natin kung may nagbago sa fields na nasa displayOrder (excluding remarks for holidays)
                                        $hasFieldChanges = false;
                                        foreach($displayOrder as $key => $label) {
                                            if($log->auditable_type == 'App\Models\Holiday' && $key == 'remarks') continue;
                                            if(isset($newVals[$key])) {
                                                $hasFieldChanges = true;
                                                break;
                                            }
                                        }
                                    @endphp

                                    {{-- 1. I-render ang main field changes --}}
                                    @foreach($displayOrder as $key => $label)
                                        @if($log->auditable_type == 'App\Models\Holiday' && $key == 'remarks') @continue @endif

                                        @if(isset($newVals[$key]))
                                            <div class="flex flex-col border-l-2 border-slate-200 pl-2 py-0.5">
                                                <span class="text-[9px] font-black text-slate-400 uppercase">{{ $label }}</span>
                                                <div class="flex items-center gap-1.5 text-[10px]">
                                                    @php
                                                        $oldRaw = $oldVals[$key] ?? 'EMPTY';
                                                        $newRaw = $newVals[$key] ?? 'EMPTY';
                                                        if ($key === 'is_active') {
                                                            $oldDisplay = ($oldRaw == 1) ? 'ACTIVE' : (($oldRaw == 0 && $oldRaw !== 'EMPTY') ? 'INACTIVE' : 'EMPTY');
                                                            $newDisplay = ($newRaw == 1) ? 'ACTIVE' : 'INACTIVE';
                                                        } 
                                                        // ETO YUNG PARA SA DATE LANG (FORMATTING)
                                                        elseif ($key === 'holiday_date' || $key === 'date') {
                                                            $oldDisplay = ($oldRaw !== 'EMPTY') ? \Carbon\Carbon::parse($oldRaw)->format('M d, Y') : 'EMPTY';
                                                            $newDisplay = \Carbon\Carbon::parse($newRaw)->format('M d, Y');
                                                        }
                                                        else {
                                                            $oldDisplay = $oldRaw;
                                                            $newDisplay = $newRaw;
                                                        }
                                                    @endphp
                                                    <span class="text-rose-500 line-through font-medium">{{ $oldDisplay }}</span>
                                                    <i class="bi bi-arrow-right text-slate-300"></i>
                                                    <span class="text-emerald-600 font-black">{{ $newDisplay }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach

                                    {{-- 2. Fallback: Kung Holiday ito at Remarks lang ang binago --}}
                                    @if(!$hasFieldChanges && $log->auditable_type == 'App\Models\Holiday' && isset($newVals['remarks']))
                                        <div class="text-[10px] font-bold text-amber-600 flex items-center gap-1">
                                            <i class="bi bi-chat-left-text-fill text-[9px]"></i> Only Remarks were updated
                                        </div>
                                    @elseif(!$hasFieldChanges)
                                        <span class="text-[10px] text-slate-300 italic">No value changes tracked</span>
                                    @endif
                                </div>
                            @elseif(strtolower($log->event) == 'created')
                                <div class="text-[10px] font-bold text-emerald-600 flex items-center gap-1">
                                    <i class="bi bi-plus-circle-fill"></i> New data entry established
                                </div>
                            @else
                                <span class="text-[10px] text-slate-300 italic">No value changes tracked</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-10 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <i class="bi bi-database-exclamation text-5xl text-slate-100 mb-4"></i>
                                <h4 class="text-slate-400 text-xs font-black uppercase tracking-widest">No logs found</h4>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($logs->hasPages())
        <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

    <div class="mt-8 text-center">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Audit Management System • BPDA</p>
    </div>
</div>
@endsection