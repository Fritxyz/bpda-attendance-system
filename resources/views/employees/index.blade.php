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
            {{-- // --}}
            {{-- Filter Section --}}
                <div class="relative" x-data="{ filterOpen: false }">
                    <button @click="filterOpen = !filterOpen" 
                            :class="filterOpen ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-slate-600 border-slate-200'"
                            class="p-2.5 border rounded-xl hover:shadow-md transition-all flex items-center gap-2 shadow-sm">
                        <i class="bi bi-funnel"></i>
                        <span class="text-xs font-bold px-1">Filters</span>
                    </button>

                    {{-- Filter Dropdown Menu --}}
                    <div x-show="filterOpen" 
                        @click.away="filterOpen = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        class="absolute right-0 mt-3 w-72 bg-white rounded-2xl shadow-2xl border border-slate-100 z-50 p-4">
                        
                        <form action="{{ route('employees.index') }}" method="GET" class="space-y-4">
                            {{-- Bureau Filter --}}
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Bureau</label>
                                <select name="bureau" class="w-full bg-slate-50 border-none rounded-lg text-xs font-bold p-2.5 focus:ring-2 focus:ring-emerald-500">
                                    <option value="">All Bureaus</option>
                                    <option value="PPB" {{ request('bureau') == 'PPB' ? 'selected' : '' }}>Finance</option>
                                    <option value="Admin" {{ request('bureau') == 'Admin' ? 'selected' : '' }}>Admin</option>
                                    {{-- Idagdag dito ang ibang Bureaus ng BPDA --}}
                                </select>
                            </div>

                            {{-- Employment Type Filter --}}
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Employment Type</label>
                                <select name="type" class="w-full bg-slate-50 border-none rounded-lg text-xs font-bold p-2.5 focus:ring-2 focus:ring-emerald-500">
                                    <option value="">All Types</option>
                                    <option value="Permanent" {{ request('type') == 'Permanent' ? 'selected' : '' }}>Permanent</option>
                                    <option value="Contractual" {{ request('type') == 'Contractual' ? 'selected' : '' }}>Contractual</option>
                                    <option value="Coterminous" {{ request('type') == 'Coterminous' ? 'selected' : '' }}>Coterminous</option>
                                </select>
                            </div>

                            {{-- Status Filter --}}
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Status</label>
                                <div class="flex gap-2">
                                    <label class="flex-1">
                                        <input type="radio" name="status" value="1" class="hidden peer" {{ request('status') == '1' ? 'checked' : '' }}>
                                        <div class="text-center p-2 rounded-lg border border-slate-100 text-[10px] font-bold uppercase peer-checked:bg-emerald-50 peer-checked:border-emerald-500 peer-checked:text-emerald-700 cursor-pointer hover:bg-slate-50">Active</div>
                                    </label>
                                    <label class="flex-1">
                                        <input type="radio" name="status" value="0" class="hidden peer" {{ request('status') == '0' ? 'checked' : '' }}>
                                        <div class="text-center p-2 rounded-lg border border-slate-100 text-[10px] font-bold uppercase peer-checked:bg-rose-50 peer-checked:border-rose-500 peer-checked:text-rose-700 cursor-pointer hover:bg-slate-50">Inactive</div>
                                    </label>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-slate-50 flex gap-2">
                                <a href="{{ route('employees.index') }}" class="flex-1 text-center py-2 text-[10px] font-black uppercase text-slate-400 hover:text-slate-600 transition">Clear</a>
                                <button type="submit" class="flex-1 bg-emerald-600 text-white py-2 rounded-lg text-[10px] font-black uppercase tracking-widest shadow-lg shadow-emerald-100">Apply</button>
                            </div>
                        </form>
                    </div>
                </div>
           {{-- // --}}
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
                                    <p class="text-[10px] font-black text-emerald-600 leading-none mb-1">{{ $employee->employee_id }}</p>
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
                            {{-- Flex container to center the button --}}
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('employees.create', $employee->id) }}" 
                                class="group/btn relative flex items-center justify-center w-10 h-10 bg-white border border-slate-200 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white hover:border-blue-600 hover:shadow-lg hover:shadow-blue-200 transition-all duration-300 active:scale-90"
                                title="Edit Profile">
                                    {{-- Icon stays centered --}}
                                    <i class="bi bi-pencil-square text-lg transition-transform duration-300 group-hover/btn:scale-110"></i>
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