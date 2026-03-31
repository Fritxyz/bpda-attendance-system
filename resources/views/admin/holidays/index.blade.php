@extends('layouts.admin.top-and-side-bar')

@section('header', 'Holidays Management')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8">
    <nav class="flex mb-8 text-sm" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li class="flex items-center gap-2">
                <i class="bi bi-calendar-event text-xs"></i>
                <span class="text-gray-500 hover:text-emerald-600 transition flex items-center gap-1">Holidays</span>
                <i class="bi bi-chevron-right text-[10px] text-gray-400"></i>
            </li>
            <li>
                <button class="font-bold text-emerald-900 uppercase tracking-wider text-[11px]">
                    View All Holidays
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

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Holiday Management</h1>
            <p class="text-slate-500 text-sm mt-1 flex items-center gap-2">
                <i class="bi bi-info-circle"></i>
                Official public holidays for the Fiscal Year 2026 (should be dynamic).
            </p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('holiday.create') }}" class="flex items-center gap-2 px-5 py-2.5 bg-emerald-800 hover:bg-emerald-900 text-white text-xs font-bold uppercase tracking-widest rounded-lg shadow-md transition-all active:scale-95">
                <i class="bi bi-plus-lg text-sm"></i>
                Create Entry
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex flex-wrap justify-between items-center gap-4">
            <div class="relative w-full md:w-80">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" placeholder="Search holiday..." 
                       class="w-full pl-9 pr-4 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
            </div>
            <div class="flex gap-2">
                <select class="text-xs font-bold border-slate-200 rounded-lg py-2 pl-3 pr-8 focus:ring-emerald-500/20 outline-none">
                    <option>2026</option>
                    <option>2025</option>
                </select>
                <button class="p-2 border border-slate-200 rounded-lg hover:bg-white text-slate-500">
                    <i class="bi bi-filter"></i>
                </button>
            </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[10px] font-black uppercase tracking-[0.15em] text-slate-400 bg-slate-50/50">
                            <th class="px-8 py-4 border-b border-slate-100 w-[25%]">Full Date</th>
                            <th class="px-6 py-4 border-b border-slate-100 w-[30%]">Holiday Designation</th>
                            <th class="px-6 py-4 border-b border-slate-100 w-[20%] text-center">Classification</th>
                            <th class="px-8 py-4 border-b border-slate-100 w-[25%] text-right">Authority & Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($holidays as $holiday)
                            @php
                                // Check kung ang holiday date ay tapos na (isPast) o ngayon (isToday)
                                $isPast = $holiday->date->isPast() && !$holiday->date->isToday();
                            @endphp

                            {{-- Row styling: Magiging grayscale at medyo faded kapag tapos na --}}
                            <tr class="transition-all group {{ $isPast ? 'bg-slate-50/40 opacity-70 grayscale-[0.6]' : 'hover:bg-slate-50/80' }}">
                                
                                {{-- Column: Full Date --}}
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-5">
                                        <div class="relative">
                                            <div class="flex flex-col items-center justify-center w-14 h-14 bg-white border {{ $isPast ? 'border-slate-200' : 'border-emerald-500/20 shadow-sm' }} rounded-2xl group-hover:scale-105 transition-transform shrink-0">
                                                <span class="text-[10px] font-black {{ $isPast ? 'text-slate-400' : 'text-emerald-600' }} uppercase tracking-tighter">
                                                    {{ $holiday->date->format('M') }}
                                                </span>
                                                <span class="text-xl font-black {{ $isPast ? 'text-slate-600' : 'text-emerald-950' }} leading-none">
                                                    {{ $holiday->date->format('d') }}
                                                </span>
                                            </div>
                                            
                                            {{-- Small Check Indicator kung tapos na --}}
                                            @if($isPast)
                                                <div class="absolute -top-1 -right-1 w-5 h-5 bg-slate-400 border-2 border-white rounded-full flex items-center justify-center shadow-sm">
                                                    <i class="bi bi-check-lg text-white text-[10px]"></i>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="min-w-0">
                                            <p class="text-sm font-bold {{ $isPast ? 'text-slate-500 line-through' : 'text-slate-800' }} leading-none">
                                                {{ $holiday->date->format('l') }}
                                            </p>
                                            <p class="text-[10px] font-medium text-slate-400 mt-1.5 uppercase tracking-wide italic">
                                                {{ $isPast ? 'Historical' : 'Upcoming' }} • {{ $holiday->date->format('Y') }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Column: Holiday Designation --}}
                                <td class="px-6 py-6">
                                    <div class="space-y-1">
                                        <p class="text-sm font-extrabold {{ $isPast ? 'text-slate-500' : 'text-slate-900' }} tracking-tight leading-tight uppercase">
                                            {{ $holiday->name }}
                                        </p>
                                        <div class="flex items-center gap-2">
                                            <i class="bi bi-geo-alt text-[10px] {{ $isPast ? 'text-slate-400' : 'text-emerald-600' }}"></i>
                                            <span class="text-[10px] text-slate-500 font-semibold uppercase tracking-tight italic">
                                                {{ $holiday->type == 'Local' ? 'BARMM Local' : 'National' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Column: Classification (Badge) --}}
                                <td class="px-6 py-6 text-center">
                                    @php
                                        $typeColors = [
                                            'Regular' => 'bg-emerald-50 border-emerald-100 text-emerald-700',
                                            'Special Working' => 'bg-amber-50 border-amber-100 text-amber-700',
                                            'Special Non-Working' => 'bg-amber-50 border-amber-100 text-amber-700',
                                            'Local'   => 'bg-blue-50 border-blue-100 text-blue-700'
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border {{ $isPast ? 'bg-slate-100 border-slate-200 text-slate-500' : ($typeColors[$holiday->type] ?? '') }} text-[10px] font-black uppercase tracking-widest">
                                        {{ $holiday->type }}
                                    </span>
                                </td>

                                {{-- Column: Authority & Actions --}}
                                <td class="px-8 py-6">
                                    <div class="flex items-center justify-end gap-4">
                                        <div class="text-right border-r border-slate-200 pr-4 hidden sm:block">
                                            <p class="text-[11px] font-black text-slate-700 leading-none uppercase italic">{{ $holiday->reference ?? 'N/A' }}</p>
                                            <p class="text-[9px] text-slate-400 mt-1 font-bold uppercase tracking-tighter">Legal Reference</p>
                                        </div>

                                        <div class="flex items-center gap-1">
                                            <a href="{{ route('holiday.edit', $holiday->id) }}" class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all">
                                                <i class="bi bi-pencil-square text-lg"></i>
                                            </a>
                                            
                                            {{-- Update mo yung form mo sa loob ng loop --}}
                                            <form action="{{ route('holiday.destroy', $holiday->id) }}" method="POST" class="delete-holiday-form">
                                                @csrf
                                                @method('DELETE')
                                                {{-- Dito natin ilalagay yung remarks galing sa SweetAlert --}}
                                                <input type="hidden" name="remarks" class="remarks-input"> 
                                                
                                                <button type="button" 
                                                        class="delete-btn ..." 
                                                        data-holiday-name="{{ $holiday->name }}">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            {{-- Empty State --}}
                            <tr>
                                <td colspan="4" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="bi bi-calendar-x text-5xl text-slate-200 mb-4"></i>
                                        <p class="text-slate-400 font-black uppercase tracking-[0.2em] text-xs">No records found for the current filter</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
    </div>

    <div class="mt-8 text-center">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Bangsamoro Planning and Development Authority</p>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.delete-btn');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const form = this.closest('.delete-holiday-form');
                const remarksInput = form.querySelector('.remarks-input');
                const holidayName = this.getAttribute('data-holiday-name') || 'this record';

                Swal.fire({
                    title: '<span class="text-slate-800 tracking-tight">Confirm Deletion</span>',
                    html: `
                        <div class="text-sm text-slate-500 mb-4 text-center">
                            Are you sure you want to delete <b class="text-rose-600">${holidayName}</b>?<br>
                            <span class="text-[11px] italic text-slate-400">This action will be permanently recorded in the audit trail.</span>
                        </div>
                    `,
                    icon: 'warning',
                    input: 'textarea', // Textarea para sa mas mahabang explanation kung kailangan
                    inputLabel: 'Reason for Deletion',
                    inputPlaceholder: 'Please provide a justification (e.g., Erroneous entry, event cancelled...)',
                    inputAttributes: {
                        'aria-label': 'Type your reason here',
                        'class': 'text-xs p-3 rounded-lg border-slate-200'
                    },
                    showCancelButton: true,
                    confirmButtonColor: '#be123c', // Rose-700
                    cancelButtonColor: '#64748b', // Slate-500
                    confirmButtonText: 'Confirm Delete',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true, // Standard UX: Cancel on the left
                    
                    // Validation: Mandatory ang remarks para sa compliance
                    inputValidator: (value) => {
                        if (!value || value.trim().length < 5) {
                            return 'Please provide a valid reason (minimum 5 characters).';
                        }
                    },
                    
                    didOpen: () => {
                        // Fine-tuning the appearance of the textarea
                        const textarea = Swal.getInput();
                        textarea.style.fontSize = '12px';
                        textarea.style.height = '80px';
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        remarksInput.value = result.value.trim();

                        Swal.fire({
                            title: 'Processing...',
                            html: 'Finalizing deletion and updating audit logs.',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); }
                        });

                        form.submit();
                    }
                });
            });
        });
    });
</script>
</div>
@endsection