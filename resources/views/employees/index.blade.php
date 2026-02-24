@extends('layouts.app')

@section('header', 'Employee Management')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    
    {{-- Breadcrumb --}}
    <nav class="flex mb-8 text-sm" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li>
                <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-emerald-600 transition flex items-center gap-1">
                    <i class="bi bi-house-door text-xs"></i>
                    Dashboard
                </a>
            </li>
            <li class="flex items-center gap-2">
                <i class="bi bi-chevron-right text-[10px] text-gray-400"></i>
                <span class="font-bold text-emerald-900 uppercase tracking-wider text-[11px]">Personnel Directory</span>
            </li>
        </ol>
    </nav>

    {{-- Header Section --}}
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-8">
        <div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">Workforce Directory</h2>
            <p class="text-sm text-slate-500 mt-1">Manage and monitor all registered personnel of BPDA.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            {{-- Search --}}
            <div class="relative group">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-emerald-500 transition-colors"></i>
                <input type="text" placeholder="Search by name or ID..." 
                       class="pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none w-full sm:w-72 shadow-sm transition-all">
            </div>

            {{-- Filter Button (Optional but looks good) --}}
            <button class="p-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 transition shadow-sm">
                <i class="bi bi-funnel"></i>
            </button>

            {{-- Add Button --}}
            <a href="{{ route('employees.create') }}" 
               class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-emerald-200 transition active:scale-95">
                <i class="bi bi-person-plus-fill"></i>
                Add Employee
            </a>
        </div>
    </div>

    {{-- Stats Overview (Quick glance) --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Personnel</p>
            <p class="text-2xl font-black text-slate-800">{{ $employees->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm border-l-4 border-l-emerald-500">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Active Status</p>
            <p class="text-2xl font-black text-emerald-600">{{ $employees->where('is_active', true)->count() }}</p>
        </div>
    </div>

    {{-- Main Table Card --}}
    <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto text-slate-700">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-6 py-5 text-[11px] font-black text-slate-500 uppercase tracking-widest">Employee Information</th>
                        <th class="px-6 py-5 text-[11px] font-black text-slate-500 uppercase tracking-widest text-center">Bureau / Division</th>
                        <th class="px-6 py-5 text-[11px] font-black text-slate-500 uppercase tracking-widest text-center">Type & Salary</th>
                        <th class="px-6 py-5 text-[11px] font-black text-slate-500 uppercase tracking-widest text-center">Status</th>
                        <th class="px-6 py-5 text-[11px] font-black text-slate-500 uppercase tracking-widest text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($employees as $employee)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                {{-- Initial Avatar --}}
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-black text-sm shadow-md shadow-emerald-100">
                                    {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-emerald-600 leading-none mb-1">BPDA-{{ $employee->employee_id }}</p>
                                    <p class="text-sm font-black text-slate-900 uppercase tracking-tight">{{ $employee->last_name }}, {{ $employee->first_name }}</p>
                                    <p class="text-[11px] text-slate-500 font-medium italic mt-0.5">{{ $employee->position }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="inline-flex flex-col">
                                <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-[10px] font-black rounded border border-blue-100 uppercase tracking-tighter self-center">
                                    {{ $employee->bureau }}
                                </span>
                                <span class="text-[11px] text-slate-500 font-bold mt-1.5">{{ $employee->division }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-xs font-black text-slate-700 uppercase tracking-tighter">{{ $employee->employment_type }}</span>
                                @if($employee->salary)
                                    <span class="text-[11px] font-mono text-emerald-600 font-black bg-emerald-50 px-2 py-0.5 rounded mt-1">₱{{ number_format($employee->salary, 2) }}</span>
                                @else
                                    <span class="text-[10px] text-slate-300 italic mt-1 font-medium">Standard Scale</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $employee->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $employee->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500' }}"></span>
                                {{ $employee->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2 group-hover:translate-x-0">
                                <a href="{{ route('employees.create', $employee->id) }}" 
                                   class="p-2.5 bg-white border border-slate-200 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white hover:border-blue-600 transition shadow-sm"
                                   title="Edit Profile">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-24 text-center">
                            <div class="max-w-xs mx-auto">
                                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-dashed border-slate-200">
                                    <i class="bi bi-people text-3xl text-slate-300"></i>
                                </div>
                                <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">No Personnel Found</h3>
                                <p class="text-xs text-slate-500 mt-2">Start by registering your first employee in the system.</p>
                                <a href="{{ route('employees.create') }}" class="inline-block mt-5 text-emerald-600 font-black text-[10px] uppercase tracking-widest border-b-2 border-emerald-600 pb-0.5 hover:text-emerald-700 hover:border-emerald-700 transition">
                                    + Add New Record
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        {{-- @if($employees->hasPages())
        <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100">
            {{ $employees->links() }}
        </div>
        @endif --}}
    </div>

    {{-- Footer Branding --}}
    <div class="mt-8 text-center">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Bangsamoro Planning and Development Authority</p>
    </div>
</div>
@endsection