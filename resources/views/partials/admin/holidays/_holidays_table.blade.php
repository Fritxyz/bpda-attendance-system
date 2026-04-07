@forelse($holidays as $holiday)
    @php
        $isPast = $holiday->date->isPast() && !$holiday->date->isToday();
    @endphp

    <tr class="transition-all group {{ $isPast ? 'bg-slate-50/40 opacity-70 grayscale-[0.6]' : 'hover:bg-slate-50/80' }}">
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
                        {{ $isPast ? 'Completed' : 'Upcoming' }} • {{ $holiday->date->format('Y') }}
                    </p>
                </div>
            </div>
        </td>

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

        <td class="px-8 py-6">
            <div class="flex items-center justify-end gap-4">
                <div class="text-right border-r border-slate-200 pr-4 hidden sm:block">
                    <p class="text-[11px] font-black text-slate-700 leading-none uppercase italic">{{ $holiday->reference ?? 'N/A' }}</p>
                    <p class="text-[9px] text-slate-400 mt-1 font-bold uppercase tracking-tighter">Legal Reference</p>
                </div>

                <div class="flex items-center gap-2">
                    {{-- EDIT BUTTON --}}
                    <a href="{{ route('holiday.edit', $holiday->id) }}" 
                    class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-green-50 text-green-700 border border-green-100 hover:bg-green-600 hover:text-white hover:border-green-600 transition-all duration-200 group">
                        <i class="bi bi-pencil-square text-base"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest hidden lg:block">Edit</span>
                    </a>
                    
                    {{-- DELETE BUTTON --}}
                    <form action="{{ route('holiday.destroy', $holiday->id) }}" method="POST" class="delete-holiday-form">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="remarks" class="remarks-input"> 
                        
                        <button type="button" 
                                class="delete-btn flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-50 text-red-700 border border-red-100 hover:bg-red-600 hover:text-white hover:border-red-600 transition-all duration-200 group" 
                                data-holiday-name="{{ $holiday->name }}">
                            <i class="bi bi-trash3 text-base"></i>
                            <span class="text-[10px] font-black uppercase tracking-widest hidden lg:block">Delete</span>
                        </button>
                    </form>
                </div>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="4" class="px-8 py-20 text-center">
            <div class="flex flex-col items-center">
                <i class="bi bi-calendar-x text-5xl text-slate-200 mb-4"></i>
                <p class="text-slate-400 font-black uppercase tracking-[0.2em] text-xs">No records found for the current filter</p>
            </div>
        </td>
    </tr>
@endforelse

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
                    input: 'textarea', 
                    inputLabel: 'Reason for Deletion',
                    inputPlaceholder: 'Please provide a justification (e.g., Erroneous entry, event cancelled...)',
                    inputAttributes: {
                        'aria-label': 'Type your reason here',
                        'class': 'text-xs p-3 rounded-lg border-slate-200'
                    },
                    showCancelButton: true,
                    confirmButtonColor: '#be123c', 
                    cancelButtonColor: '#64748b', 
                    confirmButtonText: 'Confirm Delete',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true, 
                    
                    inputValidator: (value) => {
                        if (!value || value.trim().length < 5) {
                            return 'Please provide a valid reason (minimum 5 characters).';
                        }
                    },
                    
                    didOpen: () => {
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
