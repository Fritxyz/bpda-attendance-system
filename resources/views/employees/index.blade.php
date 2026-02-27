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

    {{-- Success Message Alert --}}
    @if (session('success'))
        <div id="success-alert" class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-lg flex justify-between items-center shadow-sm">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-sm font-bold">{{ session('success') }}</p>
            </div>
            <button onclick="document.getElementById('success-alert').remove()" class="text-green-500 hover:text-green-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    @endif

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
                <input type="text" placeholder="Search by name or ID..."  id="search-input" name="search"
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
                                <select name="bureau" id="bureau-select" class="w-full bg-slate-50 border-none rounded-lg text-xs font-bold p-2.5 focus:ring-2 focus:ring-emerald-500">
                                    <option value="">All Bureaus</option>
                                    <option value="PPB" {{ request('bureau') == 'PPB' ? 'selected' : '' }}>PPB</option>
                                    <option value="RDSPB" {{ request('bureau') == 'RDSPB' ? 'selected' : '' }}>RDSPB</option>
                                    <option value="FASS" {{ request('bureau') == 'FASS' ? 'selected' : '' }}>FASS</option>
                                    <option value="Other" {{ request('bureau') == 'Other' ? 'selected' : '' }}>Other</option>
                                    {{-- Idagdag dito ang ibang Bureaus ng BPDA --}}
                                </select>
                            </div>

                            {{-- Division Filter --}}
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Division</label>
                                <select name="division" id="division-select" class="w-full bg-slate-50 border-none rounded-lg text-xs font-bold p-2.5 focus:ring-2 focus:ring-emerald-500">
                                    <option value="" disabled selected>Select Division</option>
                                </select>
                            </div>

                            {{-- Employment Type Filter --}}
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Employment Type</label>
                                <select name="type" class="w-full bg-slate-50 border-none rounded-lg text-xs font-bold p-2.5 focus:ring-2 focus:ring-emerald-500">
                                    <option value="">All Types</option>
                                    <option value="Permanent" {{ request('type') == 'Permanent' ? 'selected' : '' }}>Permanent</option>
                                    <option value="Contractual" {{ request('type') == 'Contractual' ? 'selected' : '' }}>Contractual</option>
                                    <option value="Job Order" {{ request('type') == 'Job Order' ? 'selected' : '' }}>Job Order</option>
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
    <div id="employee-table-container" class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200 overflow-hidden">
        
        @include('employees.partials.table')

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

<script src="{{ asset('js/admin/employee-filter.js') }}"></script>
@endsection