@extends('layouts.admin.top-and-side-bar')

@section('header', 'Daily Time Record')

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
                    Daily Attendance
                </button>
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
            <div class="relative">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
                <input type="text" id="dtrSearchInput" name="search"
                    placeholder="Search name or ID..." 
                    class="pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-xs focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none w-full sm:w-60 shadow-sm transition-all placeholder:text-slate-400 font-medium">
            </div>
            
            <div class="flex items-center bg-white border border-slate-200 rounded-xl shadow-sm px-2 focus-within:ring-4 focus-within:ring-emerald-500/10 focus-within:border-emerald-500 transition-all">
                <span class="text-[9px] font-black text-slate-400 uppercase px-2 border-r border-slate-100 mr-2">Date</span>
                <input type="date" id="dtrDateFilter"
                    value="{{ request('date', now()->toDateString()) }}" 
                    class="py-2 pr-1 bg-transparent text-xs font-bold outline-none text-slate-600 cursor-pointer uppercase">
            </div>
            
            <button class="bg-slate-900 text-white px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-md hover:bg-emerald-600 transition-all flex items-center gap-2">
                <i class="bi bi-printer text-xs"></i> 
                <span>Print Daily Attendance</span>
            </button>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200 overflow-hidden">
        <div id="dtrTableContainer" class="overflow-x-auto">
             @include('partials.admin.timekeeping._dtr_table')
        </div>
    </div>
    
    <div class="mt-8 text-center">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Bangsamoro Planning and Development Authority</p>
    </div>
</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.getElementById('dtrDateFilter');
        const searchInput = document.getElementById('dtrSearchInput');
        // I-target natin ang container para hindi mawala ang event listeners
        const tableContainer = document.getElementById('dtrTableContainer');

        function updateTable() {
            tableContainer.classList.add('opacity-40');

            const params = new URLSearchParams({
                date: dateInput.value,
                search: searchInput.value
            });

            const url = `${window.location.pathname}?${params.toString()}`;
            window.history.pushState({}, '', url);

            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                // I-replace ang buong table content
                tableContainer.innerHTML = html;
                tableContainer.classList.remove('opacity-40');
            })
            .catch(error => {
                console.error('Error:', error);
                tableContainer.classList.remove('opacity-40');
            });
        }

        dateInput.addEventListener('change', updateTable);

        let timeout = null;
        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(updateTable, 500);
        });
    });
</script>
