@extends('layouts.admin.top-and-side-bar')

@section('header', 'Weekly Attendance Report')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">

    <nav class="flex mb-8 text-sm" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li class="flex items-center gap-2">
                <i class="bi bi-clock-history text-xs"></i>
                <span class="text-gray-500 hover:text-emerald-600 transition flex items-center gap-1">Timekeeping</span>
                <i class="bi bi-chevron-right text-[10px] text-gray-400"></i>
            </li>
            <li>
                <button class="font-bold text-emerald-900 uppercase tracking-wider text-[11px]">
                    Weekly Attendance
                </button>
            </li>
        </ol>
    </nav>

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

    @if (session('info'))
        <div id="info-alert" class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 text-blue-700 rounded-r-lg flex justify-between items-center shadow-sm">
            <div class="flex items-center">
                {{-- Info Icon --}}
                <svg class="w-5 h-5 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-sm font-bold">{{ session('info') }}</p>
            </div>
            <button onclick="document.getElementById('info-alert').remove()" class="text-blue-500 hover:text-blue-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    {{-- Header Section --}}
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-8">
        <div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">Weekly Attendance Logs</h2>
            <p class="text-sm text-slate-500 mt-1">Review and manage Weekly time records for all BPDA personnel.</p>
        </div>
        
        <div class="flex flex-wrap items-center justify-end gap-2 bg-slate-50/50 p-1.5 rounded-2xl border border-slate-100">
            <div class="relative">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
                <input type="text" id="dtrSearchInput" name="search"
                    placeholder="Search name or ID..." 
                    class="pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-xs focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none w-full sm:w-60 shadow-sm transition-all placeholder:text-slate-400 font-medium">
            </div>
            
            <div class="flex items-center bg-white border border-slate-200 rounded-xl shadow-sm px-2 focus-within:ring-4 focus-within:ring-emerald-500/10 focus-within:border-emerald-500 transition-all">
                <span class="text-[9px] font-black text-slate-400 uppercase px-2 border-r border-slate-100 mr-2">Week Of</span>
                <input type="week" id="dtrDateFilter"
                    value="{{ request('date', now()->toDateString()) }}" 
                    class="py-2 pr-1 bg-transparent text-xs font-bold outline-none text-slate-600 cursor-pointer uppercase">
            </div>

            <div class="relative" x-data="{ filterOpen: false }">
                <button @click="filterOpen = !filterOpen" 
                        :class="filterOpen ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-slate-600 border-slate-200'"
                        class="p-2.5 border rounded-xl hover:shadow-md transition-all flex items-center gap-2 shadow-sm">
                    <i class="bi bi-funnel"></i>
                    <span class="text-xs font-bold px-1">Filters</span>
                </button>

                <div x-show="filterOpen" 
                    @click.away="filterOpen = false"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    class="absolute right-0 mt-3 w-72 bg-white rounded-2xl shadow-2xl border border-slate-100 z-50 p-4">
                    
                    <form action="{{ route('dtr.view') }}" method="GET" class="space-y-4">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Bureau</label>
                            <select name="bureau" id="bureau-select" class="w-full bg-slate-50 border-none rounded-lg text-xs font-bold p-2.5 focus:ring-2 focus:ring-emerald-500">
                                <option value="">All Bureaus</option>
                                <option value="PPB" {{ request('bureau') == 'PPB' ? 'selected' : '' }}>PPB</option>
                                <option value="RDSPB" {{ request('bureau') == 'RDSPB' ? 'selected' : '' }}>RDSPB</option>
                                <option value="FASS" {{ request('bureau') == 'FASS' ? 'selected' : '' }}>FASS</option>
                                <option value="Other" {{ request('bureau') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Division</label>
                            <select name="division" id="division-select" class="w-full bg-slate-50 border-none rounded-lg text-xs font-bold p-2.5 focus:ring-2 focus:ring-emerald-500">
                                <option value="" disabled selected>Select Division</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Employment Type</label>
                            <select name="type" id="type-select" class="w-full bg-slate-50 border-none rounded-lg text-xs font-bold p-2.5 focus:ring-2 focus:ring-emerald-500">
                                <option value="">All Types</option>
                                <option value="Permanent" {{ request('type') == 'Permanent' ? 'selected' : '' }}>Permanent</option>
                                <option value="Contractual" {{ request('type') == 'Contractual' ? 'selected' : '' }}>Contractual</option>
                                <option value="Job Order" {{ request('type') == 'Job Order' ? 'selected' : '' }}>Job Order</option>
                            </select>
                        </div>

                        <div class="pt-2 border-t border-slate-50 flex gap-2">
                            <a href="{{ route('wtr.view') }}" class="flex-1 text-center py-2 text-[10px] font-black uppercase text-slate-400 hover:text-slate-600 transition">Clear</a>
                            <button type="submit" class="flex-1 bg-emerald-600 text-white py-2 rounded-lg text-[10px] font-black uppercase tracking-widest shadow-lg shadow-emerald-100">Apply</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <button onclick="printWeeklyAttendance()" class="bg-slate-900 text-white px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-md hover:bg-emerald-600 transition-all flex items-center gap-2">
                <i class="bi bi-printer text-xs"></i> 
                <span>Print Weekly Attendance</span>
            </button>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200 overflow-hidden">
        <div id="dtrTableContainer" class="overflow-x-auto">
            @include('partials.admin.timekeeping._wtr_table')
        </div>
    </div>
    
    <div class="mt-8 text-center">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Bangsamoro Planning and Development Authority</p>
    </div>
</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tableContainer = document.getElementById('dtrTableContainer');
        
        const filters = {
            date: document.getElementById('dtrDateFilter'),
            search: document.getElementById('dtrSearchInput'),
            bureau: document.getElementById('bureau-select'),
            division: document.getElementById('division-select'),
            type: document.getElementById('type-select'),
        };

        const divisionsByBureau = {
            'PPB': ['MEPD', 'EPD', 'SPD', 'LPCD', 'IPD', 'PPOSSD', 'MED'],
            'RDSPB': ['IKMD', 'RDD', 'ODA/NFPPCD', 'EID'],
            'FASS': ['Finance Division', 'Administrative Division'],
            'Other': ['Other']
        };

        function updateTable() {
            // Visual indicator na naglo-load
            tableContainer.style.opacity = '0.5';

            const params = new URLSearchParams({
                date: filters.date.value,
                search: filters.search.value,
                bureau: filters.bureau.value,
                division: filters.division.value,
                type: filters.type.value,
                ajax: 1
            });

            const url = `${window.location.pathname}?${params.toString()}`;
            
            // Update URL sa browser bar nang hindi nagre-refresh
            window.history.pushState({}, '', url.replace('&ajax=1', ''));

            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                tableContainer.innerHTML = html;
                tableContainer.style.opacity = '1';
            })
            .catch(error => {
                console.error('Error:', error);
                tableContainer.style.opacity = '1';
            });
        }

        // --- EVENT LISTENERS ---

        // Bureau Change: Update divisions AND refresh table
        filters.bureau.addEventListener('change', function() {
            const selectedBureau = this.value;
            const options = divisionsByBureau[selectedBureau] || [];
            
            filters.division.innerHTML = '<option value="">All Divisions</option>';
            options.forEach(div => {
                const el = document.createElement('option');
                el.value = div;
                el.textContent = div;
                filters.division.appendChild(el);
            });

            updateTable();
        });

        // Automatic trigger para sa ibang dropdowns
        filters.date.addEventListener('change', updateTable);
        filters.division.addEventListener('change', updateTable);
        filters.type.addEventListener('change', updateTable);

        // Search with Debounce (para hindi agad mag-request sa bawat pindot ng letra)
        let timeout = null;
        filters.search.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(updateTable, 500);
        });
    });

    // Idagdag ito sa loob ng script tag mo
    function printWeeklyAttendance() {
        const date = document.getElementById('dtrDateFilter').value;
        const bureau = document.getElementById('bureau-select').value;
        const division = document.getElementById('division-select').value;
        const type = document.getElementById('type-select').value;
        const search = document.getElementById('dtrSearchInput').value;
        
        // Ipasa ang lahat ng kasalukuyang filters sa print URL
        const params = new URLSearchParams({
            date, bureau, division, type, search
        });
        
        const url = `{{ route('wtr.print') }}?${params.toString()}`;
        window.open(url, '_blank');
    }
</script>
