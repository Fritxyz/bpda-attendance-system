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
                        <th class="px-6 py-5 text-[11px] font-black text-slate-500 uppercase tracking-widest text-center">Status</th>
                        <th class="px-8 py-5 text-[11px] font-black text-slate-500 uppercase tracking-widest text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($travelOrders as $to)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center text-slate-500 group-hover:from-emerald-500 group-hover:to-teal-600 group-hover:text-white transition-all shadow-sm">
                                    <i class="bi bi-file-earmark-text-fill"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-emerald-600 leading-none mb-1">{{ $to->to_number }}</p>
                                    <p class="text-sm font-black text-slate-900 uppercase tracking-tight">{{ $to->employee->last_name }}, {{ $to->employee->first_name }}</p>
                                    <p class="text-[11px] text-slate-500 font-medium">{{ $to->employee->position }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-slate-800 uppercase flex items-center gap-1">
                                    <i class="bi bi-geo-alt-fill text-rose-500"></i> {{ $to->destination }}
                                </span>
                                <span class="text-[11px] text-slate-500 mt-1 line-clamp-1 italic">"{{ $to->purpose }}"</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <div class="inline-flex flex-col bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-100 group-hover:bg-white transition-colors">
                                <span class="text-xs font-black text-slate-700 leading-tight">{{ $to->date_from->format('M d, Y') }}</span>
                                <div class="flex items-center justify-center gap-1 my-0.5">
                                    <div class="h-[1px] w-2 bg-slate-300"></div>
                                    <span class="text-[9px] font-black text-slate-400 uppercase">to</span>
                                    <div class="h-[1px] w-2 bg-slate-300"></div>
                                </div>
                                <span class="text-xs font-black text-slate-700 leading-tight">{{ $to->date_to->format('M d, Y') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            @php
                                $statusClasses = [
                                    'approved' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                    'pending'  => 'bg-amber-100 text-amber-700 border-amber-200',
                                    'cancelled' => 'bg-rose-100 text-rose-700 border-rose-200'
                                ];
                            @endphp
                            <span class="inline-flex px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $statusClasses[$to->status] ?? $statusClasses['pending'] }}">
                                {{ $to->status }}
                            </span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center justify-end gap-2">
                                <button title="Edit" class="p-2 rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-emerald-600 hover:border-emerald-200 hover:shadow-sm transition-all">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button title="Delete" class="p-2 rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-rose-600 hover:border-rose-200 hover:shadow-sm transition-all">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-24 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-200 mb-4 border-2 border-dashed border-slate-100">
                                    <i class="bi bi-airplane text-3xl"></i>
                                </div>
                                <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">No Travel Records</h3>
                                <p class="text-xs text-slate-500 mt-1">No official business orders have been filed yet.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
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