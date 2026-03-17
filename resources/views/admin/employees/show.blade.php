@extends('layouts.admin.top-and-side-bar')

@section('header', 'Employee Management')

@section('content')
<style>
    /* Tatanggalin nito ang sidebar at buttons kapag nag-print */
    @media print {
        .no-print, button, select, nav, .sidebar {
            display: none !important;
        }
        .bg-white {
            border: none !important;
            shadow: none !important;
        }
        body {
            background: white;
        }
    }
</style>

<div class="max-w-7xl mx-auto px-4 py-6">
    <nav class="flex mb-8 text-sm" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li class="flex items-center gap-2">
                <i class="bi bi-person-vcard text-xs"></i>
                <span class="text-gray-500 hover:text-emerald-600 transition flex items-center gap-1">Employees</span>
                <i class="bi bi-chevron-right text-[10px] text-gray-400"></i>
            </li>
            <li>
                <a href="{{ route('employees.index') }}" class="font-bold text-emerald-900 uppercase tracking-wider text-[11px]">
                    View All Employees
                </a>
            </li>
            <li class="flex items-center gap-2">
                <i class="bi bi-chevron-right text-[10px] text-gray-400"></i>
                <span class="font-bold text-emerald-900 uppercase tracking-wider text-[11px]">{{ $employee->first_name }} {{ $employee->last_name }} {{ $employee->suffix }}</span>
            </li>
        </ol>
    </nav>

    <div class="max-w-6xl mx-auto py-8 px-4" x-data="{ activeTab: 'info' }">
        
        <div class="flex flex-col md:flex-row md:items-center gap-6 mb-10 no-print">
            <div class="w-24 h-24 rounded-3xl overflow-hidden border-4 border-white shadow-xl shadow-slate-200 shrink-0">
                    <img src="{{ $employee->profile_picture ? asset('storage/' . $employee->profile_picture) : asset('images/bpda-logo.jpg') }}"  alt="Profile" class="w-full h-full object-cover">   
            </div>

            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-4">
                    <h1 class="text-3xl font-black text-slate-800 uppercase tracking-tighter">{{ $employee->first_name }} {{ $employee->last_name }}</h1>
                    
                    @if($employee->is_active)
                        <span class="bg-emerald-100 text-emerald-700 text-[10px] font-black px-3 py-1 rounded-full uppercase">
                            Active
                        </span>
                    @else
                        <span class="bg-red-100 text-red-700 text-[10px] font-black px-3 py-1 rounded-full uppercase">
                            Inactive
                        </span>
                    @endif
                </div>
                
                <p class="text-slate-500 font-medium mt-1">{{ $employee->employee_id }}</p>
                
                <div class="flex gap-2 mt-2">
                    <button @click="activeTab = 'info'" :class="activeTab === 'info' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all">Overview</button>
                    <button @click="activeTab = 'attendance'" :class="activeTab === 'attendance' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all">Attendance History</button>
                    <button></button>
                    <a href={{ route('employees.edit', $employee->employee_id) }} class="bg-white border border-slate-200 text-slate-600 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-2 hover:bg-slate-50 transition-all shadow-sm ml-auto md:ml-0">
                        <i class="bi bi-pencil-square"></i> Edit
                    </a>
                </div>
            </div>
        </div>

        <div x-show="activeTab === 'info'" x-transition class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2 space-y-6">

                <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
                    <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-8">Work Information</h3>
                    
                    <div class="grid grid-cols-2 gap-y-8 gap-x-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Bureau</label>
                            <p class="font-bold text-slate-700">{{ $employee->bureau }}</p>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Division</label>
                            <p class="font-bold text-slate-700">{{ $employee->division }}</p>
                        </div>
                         <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Current Position</label>
                            <p class="font-bold text-slate-700">{{ $employee->position }}</p>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Employment Type</label>
                            <p class="font-bold text-slate-700">{{ $employee->employment_type }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if ($employee->salary)
                <div class="space-y-6">
                    <div class="bg-emerald-600 p-8 rounded-[2rem] text-white shadow-lg shadow-emerald-100">
                        <h3 class="text-[10px] font-black uppercase tracking-widest opacity-70 mb-4">Salary Scale</h3>
                        <p class="text-3xl font-black">₱{{ number_format($employee->salary, 2) }}</p>
                    </div>
                </div>
            @endif
            
        </div>

        <div x-show="activeTab === 'attendance'" x-transition>
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-8 border-b border-slate-50 flex flex-col md:flex-row justify-between items-center gap-4 no-print">
                    <div>
                        <h3 class="text-xl font-black text-slate-800 uppercase tracking-tighter">Daily Time Record</h3>
                        <p class="text-sm text-slate-500 font-medium">Monthly Summary of Attendance</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <form id="filter-form" class="flex items-center gap-3 no-print">
                            <input type="month" id="month-input" name="month" value="{{ $selectedMonth }}" 
                                class="bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-600 px-4 py-2 focus:ring-2 focus:ring-emerald-500">
                            <button type="button" onclick="window.print()" class="bg-emerald-600 text-white px-6 py-2.5 rounded-xl text-sm font-black flex items-center gap-2 hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-100">
                                <i class="bi bi-printer-fill"></i> PRINT DTR
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="overflow-x-auto" id="attendance-container">
                    @include('partials.admin.employees._monthly_attendance_table')
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('month-input').addEventListener('change', function() {
        const month = this.value;
        const container = document.getElementById('attendance-container');
        
        // Optional: Maglagay ng loading effect
        container.style.opacity = '0.5';

        fetch(`{{ route('employees.show', $employee->employee_id) }}?month=${month}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            container.innerHTML = html;
            container.style.opacity = '1';
        })
        .catch(error => {
            console.error('Error:', error);
            container.style.opacity = '1';
        });
    });
</script>
@endsection

