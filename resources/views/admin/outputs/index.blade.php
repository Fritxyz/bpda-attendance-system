@extends('layouts.admin.top-and-side-bar')

@section('header', 'Salary Deduction')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    {{-- Breadcrumb --}}
    <nav class="flex mb-8 text-sm" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li class="flex items-center gap-2">
                <i class="bi bi-cash-stack text-xs text-slate-400"></i>
                <span class="text-gray-500 hover:text-emerald-600 transition flex items-center gap-1 uppercase tracking-wider text-[10px] font-bold">Payroll</span>
                <i class="bi bi-chevron-right text-[10px] text-gray-400"></i>
            </li>
            <li>
                <span class="font-bold text-emerald-900 uppercase tracking-wider text-[11px]">Salary Deduction</span>
            </li>
        </ol>
    </nav>

    {{-- Header Section --}}
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-8">
        <div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">Payroll Overview</h2>
            <p class="text-sm text-slate-500 mt-1">Monitor salary disbursements and automatic deductions for this period.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            {{-- Search --}}
            <div class="relative group">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-emerald-500 transition-colors"></i>
                <input type="text" placeholder="Search employee..." 
                       class="pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none w-full sm:w-64 shadow-sm transition-all">
            </div>

            {{-- Date Filter --}}
            <div class="relative">
                <input type="month" value="{{ $month ?? now()->format('Y-m') }}" 
                       class="pl-4 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:ring-4 focus:ring-emerald-500/10 outline-none shadow-sm transition-all">
            </div>

            {{-- Filter Button (Alpine.js like your reference) --}}
            <div class="relative" x-data="{ filterOpen: false }">
                <button @click="filterOpen = !filterOpen" 
                        :class="filterOpen ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-slate-600 border-slate-200'"
                        class="p-2.5 border rounded-xl hover:shadow-md transition-all flex items-center gap-2 shadow-sm">
                    <i class="bi bi-funnel"></i>
                    <span class="text-xs font-bold px-1">Options</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Statistics Cards (The "Overview" vibe) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-6 rounded-3xl shadow-xl shadow-slate-200/40 border border-slate-100 relative">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Gross Disbursement</p>
            <h3 class="text-2xl font-black text-slate-900">₱{{ number_format($totalGrossSalary ?? 0, 2) }}</h3>
            <div class="mt-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-tight">Projected for this month</span>
            </div>
        </div>

        <div class="bg-emerald-600 p-6 rounded-3xl shadow-xl shadow-emerald-200/50 relative overflow-hidden">
            <i class="bi bi-patch-check absolute -right-4 -bottom-4 text-8xl text-white/10"></i>
            <p class="text-[10px] font-black text-emerald-100 uppercase tracking-widest mb-1">Net Take Home</p>
            <h3 class="text-2xl font-black text-white">₱{{ number_format(($totalGrossSalary ?? 0) - ($totalDeductions ?? 0), 2) }}</h3>
            <div class="mt-3 flex items-center gap-2">
                <span class="text-[10px] font-bold text-emerald-100 uppercase tracking-tight italic">After all statutory deductions</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-xl shadow-slate-200/40 border border-slate-100 relative">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Deductions</p>
            <h3 class="text-2xl font-black text-rose-600">₱{{ number_format($totalDeductions ?? 0, 2) }}</h3>
            <div class="mt-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-tight">Attendance & Tax Billed</span>
            </div>
        </div>
    </div>

    {{-- Main Table Section --}}
    <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Personnel & Position</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Bureau / Division</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Employment</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Gross Pay</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center text-rose-500">Deductions</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Net Amount</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($employees as $emp)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            {{-- Personnel & Position --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="relative">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white font-black text-xs shadow-lg shadow-emerald-200/50">
                                            {{ substr($emp->first_name, 0, 1) }}{{ substr($emp->last_name, 0, 1) }}
                                        </div>
                                        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-white rounded-full flex items-center justify-center shadow-sm border border-slate-100">
                                            <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-sm font-black text-slate-800 tracking-tight group-hover:text-emerald-700 transition-colors">
                                            {{ $emp->full_name }}
                                        </div>
                                        <div class="text-[10px] font-bold text-emerald-600/80 uppercase tracking-tight">
                                            {{ $emp->position }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Bureau & Division --}}
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex flex-col gap-1">
                                    <span class="text-[10px] font-black text-slate-700 uppercase tracking-tight bg-slate-100 px-2 py-0.5 rounded-md">
                                        {{ $emp->bureau }}
                                    </span>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">
                                        {{ $emp->division }}
                                    </span>
                                </div>
                            </td>

                            {{-- Employment Type --}}
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[9px] font-black uppercase tracking-widest border border-emerald-100">
                                    {{ $emp->employment_type }}
                                </span>
                            </td>

                            {{-- Gross Pay --}}
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-bold text-slate-700 italic">
                                    ₱{{ number_format($emp->salary, 2) }}
                                </span>
                            </td>

                            {{-- Deductions --}}
                            <td class="px-6 py-4 text-center">
                                <div class="inline-block px-3 py-1 bg-rose-50 rounded-lg border border-rose-100">
                                    <div class="text-xs font-black text-rose-600 tracking-tight">
                                        ₱{{ number_format($emp->computed_deduction, 2) }}
                                    </div>
                                </div>
                            </td>

                            {{-- Net Amount --}}
                            <td class="px-6 py-4 text-center">
                                <div class="text-sm font-black text-slate-900 tracking-tighter bg-emerald-50/50 py-2 rounded-xl border border-dashed border-emerald-200">
                                    ₱{{ number_format($emp->salary - $emp->computed_deduction, 2) }}
                                </div>
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-end">
                                    <a href="#" class="flex items-center gap-3 px-8 py-2.5 rounded-xl bg-blue-50 text-blue-700 border border-blue-100 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all duration-200 group whitespace-nowrap min-w-max">
                                        <i class="bi bi-eye text-xs"></i>
                                        <span class="text-[10px] font-black uppercase tracking-wider">View Details</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-24 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                    <i class="bi bi-inbox text-2xl text-slate-200"></i>
                                </div>
                                <span class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">No payroll records identified</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Footer --}}
    <div class="mt-8 text-center">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Bangsamoro Planning and Development Authority</p>
    </div>
</div>
@endsection