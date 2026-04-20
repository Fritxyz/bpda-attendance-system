@extends('layouts.admin.top-and-side-bar')

@section('header', 'Official Travel Management')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6" x-data="{ openCreateModal: false }">
    {{-- Breadcrumb --}}
    <nav class="flex mb-8 text-sm" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li class="flex items-center gap-2">
                <i class="bi bi-calendar2-check-fill text-xs text-emerald-600"></i>
                <span class="text-gray-500 flex items-center gap-1">On Travel</span>
                <i class="bi bi-chevron-right text-[10px] text-gray-400"></i>
            </li>
            <li>
                <span class="font-bold text-emerald-900 uppercase tracking-wider text-[11px]">Travel Orders / OB</span>
            </li>
        </ol>
    </nav>

    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div class="space-y-1">
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">Official Business Log</h2>
            <p class="text-sm text-slate-500 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Track and manage employee travels and field works.
            </p>
        </div>
        
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            {{-- Enhanced Search Bar --}}
            <form action="#" method="GET" class="relative group">
                <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-emerald-500 transition-colors"></i>
                <input type="text" 
                       name="search" 
                       id="search-input"
                       value="{{ request('search') }}"
                       placeholder="Search TO or Name..." 
                       class="pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-2xl text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none w-full md:w-72 shadow-sm shadow-slate-200/50 transition-all placeholder:text-slate-400">
            </form>

            {{-- Action Button --}}
            <a href="{{ route('travels.field.create') }}"
                    class="bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white px-6 py-3 rounded-2xl text-[11px] font-black uppercase tracking-[0.1em] shadow-xl shadow-emerald-200 transition-all flex items-center justify-center gap-2 whitespace-nowrap">
                <i class="bi bi-plus-lg text-sm"></i>
                Create Travel Order
            </a>
        </div>
    </div>

    {{-- Main Table --}}
    <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-8 py-5 text-[11px] font-black text-slate-500 uppercase tracking-widest">Employee & Document</th>
                        <th class="px-6 py-5 text-[11px] font-black text-slate-500 uppercase tracking-widest">Purpose & Destination</th>
                        <th class="px-6 py-5 text-[11px] font-black text-slate-500 uppercase tracking-widest text-center">Travel Period</th>
                        <th class="px-8 py-5 text-[11px] font-black text-slate-500 uppercase tracking-widest text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100" id="travelOrdersTableBody">
                    @include('partials.admin.obm._travel_orders_table');
                </tbody>
            </table>
        </div>
        
        @if($travelOrders->hasPages())
        <div class="px-8 py-4 bg-slate-50/50 border-t border-slate-100">
            {{ $travelOrders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-input');
        const tableBody = document.getElementById('travelOrdersTableBody');
        let timeout = null;

        searchInput.addEventListener('keyup', function() {
            clearTimeout(timeout);
            
            // Maghintay ng 300ms bago tumawag sa server (Debounce)
            timeout = setTimeout(function() {
                const query = searchInput.value;

                fetch(`{{ route('travels.field.index') }}?search=${query}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    // Palitan ang laman ng table body ng bagong HTML
                    tableBody.innerHTML = html;
                })
                .catch(error => console.warn('Something went wrong.', error));
            }, 300);
        });
    });
</script>