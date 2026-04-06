@extends('layouts.admin.top-and-side-bar')

@section('header', 'Create New Holiday')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-8">

    <nav class="flex mb-8 text-sm" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li class="flex items-center gap-2">
                <i class="bi bi-calendar-event text-xs"></i>
                <span class="text-gray-500 hover:text-emerald-600 transition flex items-center gap-1">Holidays</span>
                <i class="bi bi-chevron-right text-[10px] text-gray-400"></i>
            </li>
            <li>
                <a href={{ route('holiday.index') }} class="font-bold text-emerald-900 uppercase tracking-wider text-[11px]">
                    View All Holidays
                </a>
            </li>
            <li>
                <i class="bi bi-chevron-right text-[10px] text-gray-400"></i>
                <button class="font-bold text-emerald-900 uppercase tracking-wider text-[11px]">
                    Create New Holiday
                </button>
            </li>
        </ol>
    </nav>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-lg">
            <p class="text-sm font-bold">Please correct the following errors:</p>
            <ul class="mt-1 list-disc list-inside text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-10">
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Holiday Designation</h1>
        <p class="text-slate-500 text-sm mt-1 flex items-center gap-2">
            <i class="bi bi-file-earmark-plus text-emerald-600"></i>
            Register a new official holiday and its legal basis.
        </p>
    </div>

    <form action="{{ route('holiday.store') }}" method="POST" class="space-y-6">
        @csrf
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xl shadow-slate-200/40 overflow-hidden">
            <div class="h-2 bg-emerald-800 w-full"></div>

            <div class="p-8 md:p-10 space-y-8">
                
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.15em]">Official Holiday Name</label>
                    <input type="text" name="name" placeholder="e.g., Maundy Thursday" required maxlength="100"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all placeholder:text-slate-300 placeholder:font-normal">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2 text-left">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.15em]">Specific Date</label>
                        <div class="relative">
                            <i class="bi bi-calendar-event absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="date" name="date" required
                                   class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.15em]">Classification</label>
                        <select name="type" required
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all">
                            <option value="" disabled selected>Select Type</option>
                            <option value="Regular">Regular Holiday</option>
                            <option value="Special Working">Special Non-Working Day</option>
                            <option value="Special Non-Working">Special Non-Working Day</option>
                            <option value="Local">Local Holiday (BARMM)</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.15em]">Legal Basis / Authority Reference</label>
                    <div class="relative">
                        <i class="bi bi-bank absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="reference" placeholder="e.g., Proclamation No. 902, s. 2025" maxlength="150"
                               class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all placeholder:text-slate-300 placeholder:font-normal">
                    </div>
                    <p class="text-[10px] text-slate-400 italic">Mention the official issuance that justifies this holiday.</p>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.15em]">Internal Remarks (Optional)</label>
                    <textarea name="remarks" rows="3" placeholder="Additional details or implementation notes..." maxlength="500"
                              class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all placeholder:text-slate-300 placeholder:font-normal"></textarea>
                </div>
            </div>

            <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4">
                <a href="{{ route('holiday.index') }}" class="text-xs font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition">
                    Discard Changes
                </a>
                <div class="flex gap-3 w-full md:w-auto">
                    <button type="submit" class="w-full md:w-auto px-8 py-3 bg-emerald-800 hover:bg-emerald-900 text-white text-xs font-black uppercase tracking-widest rounded-xl shadow-lg shadow-emerald-900/20 transition-all active:scale-95">
                        Finalize Entry
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection