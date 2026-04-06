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

    @if (session('info'))
        <div id="info-alert" class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 text-blue-700 rounded-r-lg flex justify-between items-center shadow-sm">
            <div class="flex items-center">
                {{-- Info Icon --}}
                <svg class="w-5 h-5 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-sm font-bold">{{ session('info') }}</p>
            </div>
            <button onclick="document.getElementById('info-alert').remove()" class="text-blue-500 hover:text-blue-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Holiday Management</h1>
            <p class="text-slate-500 text-sm mt-1 flex items-center gap-2">
                <i class="bi bi-info-circle"></i>
                Official public holidays.
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
                <input type="text" placeholder="Search holiday..." id="holidaySearch"
                       class="w-full pl-9 pr-4 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
            </div>
            <div class="flex gap-2">
                <select id="yearFilter" name="year" class="text-xs font-bold border-slate-200 rounded-lg py-2 pl-3 pr-8 focus:ring-emerald-500/20 outline-none">
                    <option value="">All Years</option> {{-- Option para makita lahat --}}
                    
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
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
                    <tbody class="divide-y divide-slate-50" id="holidayTableBody">
                        @include('partials.admin.holidays._holidays_table');
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

    const searchInput = document.getElementById('holidaySearch');
    const yearFilter = document.getElementById('yearFilter');
    const tableBody = document.getElementById('holidayTableBody');

    function fetchHolidays() {
        const search = searchInput.value;
        const year = yearFilter.value;
        
        // Pag-construct ng URL
        const url = new URL("{{ route('holiday.index') }}");
        url.searchParams.append('search', search);
        url.searchParams.append('year', year);

        // Fetch request
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            tableBody.innerHTML = html;
            // Note: Kailangan mong i-re-initialize ang SweetAlert event listeners dito
            // dahil bago na ang mga buttons sa loob ng table.
            reinitializeDeleteButtons(); 
        })
        .catch(error => console.warn('Error fetching holidays:', error));
    }

    // Debounce function para hindi stress ang server sa bawat pindot ng keyboard
    function debounce(func, timeout = 300){
        let timer;
        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => { func.apply(this, args); }, timeout);
        };
    }
    
    const processSearch = debounce(() => fetchHolidays());

    searchInput.addEventListener('input', processSearch);
    yearFilter.addEventListener('change', fetchHolidays);
</script>
</div>
@endsection