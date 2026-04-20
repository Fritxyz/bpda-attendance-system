@forelse($travelOrders as $to)
    <tr class="hover:bg-slate-50/50 transition-colors group">
        <td class="px-8 py-5">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center text-slate-500 group-hover:from-emerald-500 group-hover:to-teal-600 group-hover:text-white transition-all shadow-sm">
                    <i class="bi bi-file-earmark-text-fill"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-emerald-600 leading-none mb-1">{{ $to->to_number }}</p>
                    <p class="text-sm font-black text-slate-900 uppercase tracking-tight">{{ $to->employee->last_name ?? 'N/A' }}, {{ $to->employee->first_name ?? 'N/A' }}</p>
                    <p class="text-[11px] text-slate-500 font-medium">{{ $to->employee->position ?? 'N/A' }}</p>
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
        
        <td class="px-8 py-5">
            <div class="flex items-center justify-end align-middle gap-2">
                <a href="{{ route('travels.field.edit', $to->id) }}" 
                    class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-blue-50 text-emerald-700 border border-emerald-100 hover:bg-emerald-600 hover:text-white hover:border-emerald-600 transition-all duration-200 group">
                    <i class="bi bi-pencil-square text-xs"></i>
                    <span class="text-[10px] font-black uppercase tracking-wider">Edit</span>
                </a>

                {{-- Delete Button --}}
                <form action="{{ route('travels.field.delete', $to->id) }}" method="POST" class="delete-travel-order-form mt-3">
                    @csrf
                    @method('delete')

                    <input type="hidden" name="remarks" class="remarks-input"> 

                    <button type="button" 
                        class="delete-btn flex items-center gap-2 px-3 py-1.5 rounded-lg bg-red-50 text-red-700 border border-red-100 hover:bg-red-600 hover:text-white hover:border-red-600 transition-all duration-200 group"
                            data-travel-order-name="{{ $to->to_number }}">
                            <i class="bi bi-trash text-xs"></i>
                            <span class="text-[10px] font-black uppercase tracking-wider">Delete</span>
                    </button>
                </form>
                
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

<script>
        document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.delete-btn');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const form = this.closest('.delete-travel-order-form');
                const remarksInput = form.querySelector('.remarks-input');
                const travelOrderName = this.getAttribute('data-travel-order-name') || 'this record';

                Swal.fire({
                    title: '<span class="text-slate-800 tracking-tight">Confirm Deletion</span>',
                    html: `
                        <div class="text-sm text-slate-500 mb-4 text-center">
                            Are you sure you want to delete <b class="text-rose-600">${travelOrderName}</b>?<br>
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