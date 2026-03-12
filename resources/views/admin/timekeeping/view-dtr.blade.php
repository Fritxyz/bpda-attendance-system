@extends('layouts.admin.top-and-side-bar')

@section('header', 'Daily Time Record')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    
    {{-- Breadcrumb --}}
    <nav class="flex mb-8 text-sm" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-emerald-600 transition flex items-center gap-1">
                    <i class="bi bi-house-door text-xs"></i>
                    Dashboard
                </a>
            </li>
            <li class="flex items-center gap-2">
                <i class="bi bi-chevron-right text-[10px] text-gray-400"></i>
                <span class="font-bold text-emerald-900 uppercase tracking-wider text-[11px]">Daily Time Record Monitoring</span>
            </li>
        </ol>
    </nav>

    {{-- Header Section --}}
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-8">
        <div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">Attendance Logs</h2>
            <p class="text-sm text-slate-500 mt-1">Review and manage daily time records for all BPDA personnel.</p>
        </div>
        
        <div class="flex flex-wrap items-center justify-end gap-2 bg-slate-50/50 p-1.5 rounded-2xl border border-slate-100">
            {{-- Search Group --}}
            <div class="relative">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
                <input type="text" id="dtrSearchInput"
                    placeholder="Search name or ID..." 
                    class="pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-xs focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none w-full sm:w-60 shadow-sm transition-all placeholder:text-slate-400 font-medium">
            </div>
            
            {{-- Date Filter with Label --}}
            <div class="flex items-center bg-white border border-slate-200 rounded-xl shadow-sm px-2 focus-within:ring-4 focus-within:ring-emerald-500/10 focus-within:border-emerald-500 transition-all">
                <span class="text-[9px] font-black text-slate-400 uppercase px-2 border-r border-slate-100 mr-2">Date</span>
                <input type="date" id="dtrDateFilter"
                    value="{{ request('date', now()->toDateString()) }}" 
                    class="py-2 pr-1 bg-transparent text-xs font-bold outline-none text-slate-600 cursor-pointer uppercase">
            </div>
            
            {{-- Action Button --}}
            <button class="bg-slate-900 text-white px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-md hover:bg-emerald-600 transition-all flex items-center gap-2">
                <i class="bi bi-printer text-xs"></i> 
                <span>Print Daily Attendance</span>
            </button>
        </div>
    </div>

    {{-- Main DTR Table Card --}}
    <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-black uppercase tracking-[0.1em] text-slate-400">
                        <th rowspan="2" class="px-6 py-4 text-slate-600 border-r border-slate-100 text-center">Personnel Info</th>
                        <th colspan="2" class="px-2 py-3 text-center border-r border-slate-100 bg-slate-50/50">Morning Session</th>
                        <th colspan="2" class="px-2 py-3 text-center border-r border-slate-100 bg-slate-50/50">Afternoon Session</th>
                        <th colspan="2" class="px-2 py-3 text-center border-r border-slate-100 bg-emerald-50/30 text-emerald-600">Overtime</th>
                        <th rowspan="2" class="px-4 py-4 text-center border-r border-slate-100">Total Hours</th>
                        <th rowspan="2" class="px-4 py-4 text-center border-r border-slate-100">Status</th>
                        <th rowspan="2" class="px-6 py-4 text-right">Actions</th>
                    </tr>
                    <tr class="bg-white border-b border-slate-200 text-[9px] font-black uppercase tracking-widest text-slate-500">
                        <th class="px-4 py-2 text-center border-r border-slate-100">In</th>
                        <th class="px-4 py-2 text-center border-r border-slate-100">Out</th>
                        <th class="px-4 py-2 text-center border-r border-slate-100">In</th>
                        <th class="px-4 py-2 text-center border-r border-slate-100">Out</th>
                        <th class="px-4 py-2 text-center border-r border-slate-100 bg-emerald-50/20">In</th>
                        <th class="px-4 py-2 text-center border-r border-slate-100 bg-emerald-50/20">Out</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($today_records as $record)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-5 border-r border-slate-100/50">
                            <div class="flex items-center gap-4">
                                {{-- Modern Initials Avatar --}}
                                <div class="h-11 w-11 flex-shrink-0 rounded-2xl bg-gradient-to-br from-emerald-50 to-emerald-100 text-emerald-700 flex items-center justify-center font-black text-xs shadow-sm border border-emerald-200/50 uppercase tracking-tighter">
                                    {{ substr($record->employee->first_name, 0, 1) }}{{ substr($record->employee->last_name, 0, 1) }}
                                </div>

                                {{-- Employee Details --}}
                                <div class="flex flex-col min-w-0">
                                    <h3 class="text-sm font-black text-slate-800 tracking-tight uppercase leading-none mb-1.5 truncate">
                                        {{ $record->employee->first_name }} {{ $record->employee->last_name }}
                                    </h3>
                                    
                                    <div class="flex items-center gap-2">
                                        {{-- Division Badge-style --}}
                                        <span class="text-[9px] font-black uppercase tracking-wider text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100/50">
                                            {{ $record->employee->division ?? 'N/A' }}
                                        </span>
                                        
                                        {{-- Dot Separator --}}
                                        <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                                        
                                        {{-- Position --}}
                                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter truncate">
                                            {{ $record->employee->position ?? 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        
                        {{-- AM Session --}}
                        <td class="px-4 py-4 text-center font-mono text-xs text-slate-600">
                            {{ $record->am_in ? date('h:i A', strtotime($record->am_in)) : '--:--' }}
                        </td>
                        <td class="px-4 py-4 text-center font-mono text-xs text-slate-600 border-r border-slate-100/50">
                            {{ $record->am_out ? date('h:i A', strtotime($record->am_out)) : '--:--' }}
                        </td>
                        
                        {{-- PM Session --}}
                        <td class="px-4 py-4 text-center font-mono text-xs text-slate-600">
                            {{ $record->pm_in ? date('h:i A', strtotime($record->pm_in)) : '--:--' }}
                        </td>
                        <td class="px-4 py-4 text-center font-mono text-xs text-slate-600 border-r border-slate-100/50">
                            {{ $record->pm_out ? date('h:i A', strtotime($record->pm_out)) : '--:--' }}
                        </td>

                        {{-- OT Session --}}
                        <td class="px-4 py-4 text-center font-mono text-xs text-emerald-600 font-bold bg-emerald-50/10">
                            {{ $record->ot_in ? date('h:i A', strtotime($record->ot_in)) : '--:--' }}
                        </td>
                        <td class="px-4 py-4 text-center font-mono text-xs text-emerald-600 font-bold bg-emerald-50/10 border-r border-slate-100/50">
                            {{ $record->ot_out ? date('h:i A', strtotime($record->ot_out)) : '--:--' }}
                        </td>

                        {{-- Computed Hours --}}
                        <td class="px-4 py-4 text-center font-black text-slate-700 border-r border-slate-100/50">
                            {{ $record->computed_total_hours }}
                        </td>

                        {{-- Status Badge (Dynamic from Controller) --}}
                        <td class="px-4 py-4 text-center border-r border-slate-100/50">
                            <span class="inline-block px-3 py-1 bg-{{ $record->status_color }}-50 text-{{ $record->status_color }}-600 text-[10px] font-black uppercase rounded-lg border border-{{ $record->status_color }}-100 shadow-sm">
                                {{ $record->attendance_status }}: {{ $record->diff_hours }}
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex justify-end items-center gap-1">
                                {{-- Edit Button (Redirect Version) --}}
                                <a href="{{ route('dtr.edit', ['employee' => $record->employee->employee_id, 'date' => $record->attendance_date]) }}" 
                                class="p-3 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all duration-200 group/btn" 
                                    title="Edit Record">
                                    <i class="bi bi-pencil-square text-sm"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-6 py-12 text-center">
                            <i class="bi bi-calendar2-x text-4xl text-slate-200 mb-3 block"></i>
                            <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">No attendance records found for today.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Footer Branding --}}
    <div class="mt-8 text-center">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Bangsamoro Planning and Development Authority</p>
    </div>
</div>
@endsection

{{-- JavaScript sa pinakababa ng file --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.getElementById('dtrDateFilter');
        const searchInput = document.getElementById('dtrSearchInput');

        function updateFilters() {
            const date = dateInput.value;
            const search = searchInput.value;
            const url = new URL(window.location.href);
            
            if (date) url.searchParams.set('date', date);
            if (search) url.searchParams.set('search', search);
            else url.searchParams.delete('search');

            window.location.href = url.toString();
        }

        // Trigger on date change
        dateInput.addEventListener('change', updateFilters);

        // Trigger on search enter key
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                updateFilters();
            }
        });
    });
</script>

