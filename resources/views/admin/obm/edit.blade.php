@extends('layouts.admin.top-and-side-bar')

@section('header', 'Edit Travel Order')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-8">

    {{-- Breadcrumb --}}
    <nav class="flex mb-8 text-sm" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li class="flex items-center gap-2">
                <i class="bi bi-airplane text-xs text-emerald-600"></i>
                <span class="text-gray-500 transition flex items-center gap-1">Travel Management</span>
                <i class="bi bi-chevron-right text-[10px] text-gray-400"></i>
            </li>
            <li>
                <a href="#" class="font-bold text-emerald-900 uppercase tracking-wider text-[11px] hover:underline">
                    Travel Orders / OB
                </a>
            </li>
            <li>
                <i class="bi bi-chevron-right text-[10px] text-gray-400"></i>
                <span class="font-bold text-emerald-900 uppercase tracking-wider text-[11px] opacity-50">
                    Edit Travel Order
                </span>
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

    {{-- Error Alerts --}}
    @if ($errors->any())
        <div class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-700 rounded-r-lg shadow-sm">
            <p class="text-sm font-bold uppercase tracking-tight">Data Validation Failed</p>
            <ul class="mt-1 list-disc list-inside text-xs font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Page Title --}}
    <div class="mb-10">
        <h1 class="text-3xl font-black text-slate-900 tracking-tight italic uppercase">Travel Authorization</h1>
        <p class="text-slate-500 text-sm mt-1 flex items-center gap-2 font-medium">
            <i class="bi bi-plus-circle-fill text-emerald-600"></i>
            Edit an Official Business or Travel Order for employees.
        </p>
    </div>

    <form action="{{ route('travels.field.update', $travelOrder->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-2xl shadow-slate-200/60 overflow-hidden transition-all hover:border-emerald-200">
            {{-- Accent Top Bar --}}
            <div class="h-3 bg-gradient-to-r from-emerald-800 to-teal-600 w-full"></div>

            <div class="p-8 md:p-12 space-y-10">
                
                {{-- Employee & TO Number --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Assigned Employee</label>
                        <div class="relative custom-tom-select">
                            <select id="employee-select" name="employee_id" required autocomplete="off"
                                    placeholder="Type name to search..."
                                    class="w-full">
                                <option value=""></option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->employee_id }}" 
                                        {{ (old('employee_id', $travelOrder->employee_id) == $employee->employee_id) ? 'selected' : '' }}>
                                        {{ strtoupper($employee->last_name) }}, {{ $employee->first_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Travel Order (TO) No.</label>
                        <div class="relative">
                            <i class="bi bi-hash absolute left-5 top-1/2 -translate-y-1/2 text-emerald-600 font-black"></i>
                            <input type="text" name="to_number" placeholder="e.g., 2026-04-001" required value="{{ old('to_number', $travelOrder->to_number) }}"
                                   class="w-full pl-10 pr-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-800 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all placeholder:font-normal">
                        </div>
                    </div>
                </div>

                {{-- Destination & Purpose --}}
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Destination Location</label>
                        <div class="relative">
                            <i class="bi bi-geo-alt-fill absolute left-5 top-1/2 -translate-y-1/2 text-rose-500"></i>
                            <input type="text" name="destination" placeholder="e.g., Zamboanga City, Philippines" required value="{{ old('destination', $travelOrder->destination) }}"
                                   class="w-full pl-12 pr-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-800 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Purpose of Travel</label>
                        <textarea name="purpose" rows="3" placeholder="Explain the objective of this official business..." required
                                  class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-800 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all placeholder:font-normal">{{ old('purpose', $travelOrder->purpose) }}</textarea>
                    </div>
                </div>

                {{-- Inclusive Dates --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Departure Date</label>
                        <div class="relative group">
                            <i class="bi bi-calendar-range absolute left-5 top-1/2 -translate-y-1/2 text-emerald-600 transition-colors"></i>
                            <input type="date" name="date_from" required value="{{ old('date_from', \Carbon\Carbon::parse($travelOrder->date_from)->format('Y-m-d')) }}"
                                   class="w-full pl-12 pr-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-800 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Return Date</label>
                        <div class="relative group">
                            <i class="bi bi-calendar-check absolute left-5 top-1/2 -translate-y-1/2 text-emerald-600 transition-colors"></i>
                            <input type="date" name="date_to" required value="{{ old('date_to', \Carbon\Carbon::parse($travelOrder->date_to)->format('Y-m-d')) }}"
                                   class="w-full pl-12 pr-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-800 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Actions --}}
            <div class="px-8 py-8 bg-slate-50/80 border-t border-slate-100 flex flex-col md:flex-row justify-between items-center gap-6">
                <a href="#" class="text-xs font-black uppercase tracking-[0.2em] text-slate-400 hover:text-rose-600 transition-all flex items-center gap-2">
                    <i class="bi bi-x-circle text-sm"></i>
                    Cancel Operation
                </a>
                
                <button type="submit" class="w-full md:w-auto px-10 py-4 bg-emerald-800 hover:bg-emerald-900 text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl shadow-2xl shadow-emerald-900/30 transition-all active:scale-95 flex items-center justify-center gap-3">
                    <i class="bi bi-cloud-check-fill text-lg"></i>
                    Authorize Travel Order
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        new TomSelect("#employee-select", {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            },
            controlInput: '<input>',
            render: {
                option: function(data, escape) {
                    return `<div class="px-4 py-3 hover:bg-emerald-50 transition-colors">
                                <div class="font-bold text-slate-800 uppercase text-xs">${escape(data.text)}</div>
                            </div>`;
                },
                item: function(data, escape) {
                    return `<div class="font-bold text-slate-800 uppercase text-sm">${escape(data.text)}</div>`;
                }
            }
        });
    });
</script>

<style>
    /* 1. Force fixed height sa lahat ng primary inputs */
    .form-input-shared, 
    .ts-wrapper .ts-control {
        height: 53px !important;
        min-height: 53px !important;
        box-sizing: border-box !important;
    }

    /* 2. Tom Select Specific Fixes */
    .ts-wrapper .ts-control {
        border-radius: 1rem !important; /* rounded-2xl */
        padding: 0 1.25rem !important; 
        background-color: #f8fafc !important; /* slate-50 */
        border: 1px solid #e2e8f0 !important; /* slate-200 */
        font-weight: 700 !important;
        font-size: 0.875rem !important;
        display: flex !important;
        align-items: center !important;
    }

    /* Itong part na ito ang magsisiguro na hindi lulubog yung text */
    .ts-wrapper.single .ts-control input {
        margin-top: 0 !important;
        display: flex !important;
        align-items: center !important;
        height: 100% !important;
    }

    /* 3. Focus State Alignment */
    .ts-wrapper.focus .ts-control {
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1) !important;
        border-color: #10b981 !important;
        background-color: #ffffff !important;
    }

    /* 4. Dropdown Styling */
    .ts-dropdown {
        border-radius: 1.25rem !important;
        margin-top: 8px !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1) !important;
        border: 1px solid #f1f5f9 !important;
        padding: 0.5rem !important;
    }

    /* Linisin ang UI (Tanggal arrow) */
    .ts-wrapper.single .ts-control::after {
        display: none !important;
    }
</style>

<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
@endsection