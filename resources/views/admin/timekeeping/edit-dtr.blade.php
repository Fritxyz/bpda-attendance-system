@extends('layouts.admin.top-and-side-bar')

@section('header', 'Edit Daily Time Record')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">

    {{-- Breadcrumb --}}
    <nav class="flex mb-8 text-sm" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li>
                <a href="{{ route('dtr.view') }}" class="text-gray-500 hover:text-emerald-600 transition flex items-center gap-1">
                    <i class="bi bi-house-door text-xs"></i>
                    Daily Time Record
                </a>
            </li>
            <li class="flex items-center gap-2">
                <i class="bi bi-chevron-right text-[10px] text-gray-400"></i>
                <span class="font-bold text-emerald-900 uppercase tracking-wider text-[11px]">Edit Daily Time Record</span>
            </li>
        </ol>
    </nav>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
            <p class="font-bold">There are some issues with your input:</p>
            <ul class="list-disc list-inside text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-3xl p-6 border border-slate-200 mb-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            {{-- Avatar Placeholder --}}
            <div class="h-14 w-14 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-black text-xl shadow-lg uppercase">
                ID
            </div>
            <div>
                <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight">{{ $attendance->employee->first_name }} {{ $attendance->employee->last_name }}</h2>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Employee ID: <span class="text-emerald-600">{{ $attendance->employee->employee_id }}</span></p>
            </div>
        </div>
        
        <div class="bg-slate-50 px-6 py-3 rounded-2xl border border-slate-100 text-center">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">DTR Log Date</p>
            <span class="text-sm font-black text-slate-700 uppercase">{{ $attendance->attendance_date }}</span>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200 overflow-hidden">
        <form id="editDtrForm" action="{{ route('dtr.update', ['employee' => $attendance->employee->employee_id, 'date' => $attendance->attendance_date]) }}" method="POST" class="p-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- AM SESSION --}}
                <div class="space-y-4 p-6 bg-slate-50/50 rounded-3xl border border-slate-100">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="bi bi-sun text-amber-500"></i>
                        <h4 class="text-[11px] font-black uppercase text-slate-900 tracking-wider">Morning Session</h4>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1.5 ml-1">Time In (AM)</label>
                        <input type="time" name="am_in" value="{{ $attendance->am_in ? date('H:i', strtotime($attendance->am_in)) : '' }}"
                            class="w-full bg-white border border-slate-200 px-4 py-2.5 rounded-xl text-sm font-bold focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1.5 ml-1">Time Out (AM)</label>
                        <input type="time" name="am_out" value="{{ $attendance->am_out ? date('H:i', strtotime($attendance->am_out)) : '' }}"
                            class="w-full bg-white border border-slate-200 px-4 py-2.5 rounded-xl text-sm font-bold focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all outline-none">
                    </div>
                </div>

                {{-- PM SESSION --}}
                <div class="space-y-4 p-6 bg-slate-50/50 rounded-3xl border border-slate-100">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="bi bi-cloud-sun text-blue-500"></i>
                        <h4 class="text-[11px] font-black uppercase text-slate-900 tracking-wider">Afternoon Session</h4>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1.5 ml-1">Time In (PM)</label>
                        <input type="time" name="pm_in" value="{{ $attendance->pm_in ? date('H:i', strtotime($attendance->pm_in)) : '' }}"
                            class="w-full bg-white border border-slate-200 px-4 py-2.5 rounded-xl text-sm font-bold focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1.5 ml-1">Time Out (PM)</label>
                        <input type="time" name="pm_out" value="{{ $attendance->pm_out ? date('H:i', strtotime($attendance->pm_out)) : '' }}" 
                            class="w-full bg-white border border-slate-200 px-4 py-2.5 rounded-xl text-sm font-bold focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all outline-none">
                    </div>
                </div>
                
                {{-- OVERTIME --}}
                <div class="col-span-1 md:col-span-2 space-y-4 p-6 bg-emerald-50/30 rounded-3xl border border-emerald-100">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="bi bi-moon-stars text-emerald-600"></i>
                        <h4 class="text-[11px] font-black uppercase text-emerald-600 tracking-wider">Overtime (OT)</h4>
                    </div>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-emerald-700/50 uppercase mb-1.5 ml-1">OT In</label>
                            <input type="time" name="ot_in" value="{{ $attendance->ot_in ? date('H:i', strtotime($attendance->ot_in)) : '' }}"
                                class="w-full bg-white border border-emerald-100 px-4 py-2.5 rounded-xl text-sm font-bold focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-emerald-700/50 uppercase mb-1.5 ml-1">OT Out</label>
                            <input type="time" name="ot_out" value="{{ $attendance->ot_out ? date('H:i', strtotime($attendance->ot_out)) : '' }}"
                                class="w-full bg-white border border-emerald-100 px-4 py-2.5 rounded-xl text-sm font-bold focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all outline-none">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 p-6 bg-slate-50 rounded-3xl border border-dashed border-slate-300">
                <div class="flex items-center gap-2 mb-3">
                    <i class="bi bi-chat-right-text text-slate-500"></i>
                    <h4 class="text-[11px] font-black uppercase text-slate-900 tracking-wider">Reason for Modification</h4>
                </div>
                <textarea name="remarks" rows="2" 
                    placeholder="Enter reason for manual adjustment (e.g., Forgot to log out, Official Business, etc.)"
                    class="w-full bg-white border border-slate-200 px-4 py-3 rounded-2xl text-xs font-medium focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all outline-none resize-none placeholder:text-slate-400"
                    required></textarea>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end gap-3">
                <a href="{{ route('dtr.view') }}" class="px-6 py-3 text-[11px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition-colors">Discard Changes</a>
                <button type="submit" class="bg-emerald-600 text-white px-10 py-3 rounded-2xl font-black text-[11px] uppercase tracking-widest shadow-xl shadow-emerald-100 hover:bg-slate-900 hover:shadow-slate-200 transition-all active:scale-95">
                    Apply Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dtrForm = document.getElementById('editDtrForm');
        
        if (dtrForm) {
            dtrForm.addEventListener('submit', function(e) {
                e.preventDefault(); // Stop form from auto-submitting
                
                Swal.fire({
                    title: 'Confirm Changes?',
                    text: "You are about to modify this employee's attendance record.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#059669',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, update it!',
                }).then((result) => {
                    if (result.isConfirmed) {
                        dtrForm.submit(); // Submit after confirmation
                    }
                });
            });
        }
    });
</script>
@endpush
