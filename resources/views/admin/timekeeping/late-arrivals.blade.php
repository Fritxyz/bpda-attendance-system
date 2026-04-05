@extends('layouts.admin.top-and-side-bar')

@section('header', 'Late Arrivals')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">

    {{-- Breadcrumb --}}
    <nav class="flex mb-8 text-sm" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li class="flex items-center gap-2">
                <i class="bi bi-clock-history text-xs text-slate-400"></i>
                <span class="text-slate-500 transition flex items-center gap-1">Timekeeping</span>
                <i class="bi bi-chevron-right text-[10px] text-gray-400"></i>
            </li>
            <li>
                <span class="font-black text-emerald-900 uppercase tracking-wider text-[11px]">
                    Late Arrivals
                </span>
            </li>
        </ol>
    </nav>

    {{-- Header Section --}}
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-8">
        <div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight uppercase">Tardiness Logs</h2>
            <p class="text-sm text-slate-500 mt-1">Personnel who arrived after the <span class="font-bold text-red-500">08:15 AM</span> cutoff.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-2 bg-slate-50/50 p-1.5 rounded-2xl border border-slate-100">
            <div class="relative">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
                <input type="text" placeholder="Search personnel..." id="dtrSearchInput"
                    class="pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-xs focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none w-full sm:w-60 shadow-sm transition-all placeholder:text-slate-400 font-medium">
            </div>
            <div class="flex items-center bg-white border border-slate-200 rounded-xl shadow-sm px-2">
                <span class="text-[9px] font-black text-slate-400 uppercase px-2 border-r border-slate-100 mr-2">Filter Date</span>
                <input type="date" id="dtrDateFilter" value="{{ now()->toDateString() }}" class="py-2 pr-1 bg-transparent text-xs font-bold outline-none text-slate-600 uppercase cursor-pointer">
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200 overflow-hidden">
        <div id="dtrTableContainer">
            @include('partials.admin.timekeeping._late_arrivals_table')
        </div>
    </div>

    {{-- Footer Branding --}}
    <div class="mt-8 text-center">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Bangsamoro Planning and Development Authority</p>
    </div>
</div>

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
@endsection