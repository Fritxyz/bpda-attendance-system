<div class="overflow-x-auto text-slate-700">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-slate-50/50 border-b border-slate-100">
                <th class="w=1/3 px-6 py-5 text-[11px] font-black text-slate-500 uppercase tracking-widest">Employee Information</th>
                <th class="w=1/4 px-6 py-5 text-[11px] font-black text-slate-500 uppercase tracking-widest text-center">Bureau / Division</th>
                <th class="w=1/5 px-6 py-5 text-[11px] font-black text-slate-500 uppercase tracking-widest text-center">Type & Salary</th>
                <th class="w=1/6 px-6 py-5 text-[11px] font-black text-slate-500 uppercase tracking-widest text-center">Status</th>
                <th class="w=1/6 px-6 py-5 text-[11px] font-black text-slate-500 uppercase tracking-widest text-right">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($employees as $employee)
            <tr class="hover:bg-slate-50/80 transition-colors group">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-4">
                        {{-- Initial Avatar --}}
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-black text-sm shadow-md shadow-emerald-100">
                            {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-emerald-600 leading-none mb-1">{{ $employee->employee_id }}</p>
                            <p class="text-sm font-black text-slate-900 uppercase tracking-tight">{{ $employee->last_name }}, {{ $employee->first_name }}</p>
                            <p class="text-[11px] text-slate-500 font-medium italic mt-0.5">{{ $employee->position }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-center">
                    <div class="inline-flex flex-col">
                        <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-[10px] font-black rounded border border-blue-100 uppercase tracking-tighter self-center">
                            {{ $employee->bureau }}
                        </span>
                        <span class="text-[11px] text-slate-500 font-bold mt-1.5">{{ $employee->division }}</span>
                    </div>
                </td>
                <td class="px-6 py-4 text-center">
                    <div class="flex flex-col items-center">
                        <span class="text-xs font-black text-slate-700 uppercase tracking-tighter">{{ $employee->employment_type }}</span>
                        @if($employee->salary)
                            <span class="text-[11px] font-mono text-emerald-600 font-black bg-emerald-50 px-2 py-0.5 rounded mt-1">₱{{ number_format($employee->salary, 2) }}</span>
                        @else
                            <span class="text-[10px] text-slate-300 italic mt-1 font-medium">Standard Scale</span>
                        @endif
                    </div>
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $employee->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $employee->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500' }}"></span>
                        {{ $employee->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="px-6 py-4 text-right">
                    {{-- Action Buttons Alignment --}}
                    <div class="flex items-center justify-center gap-2">
                        {{-- Edit Button --}}
                        <a href="{{ route('employees.create', $employee->id) }}" 
                        class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-emerald-600 hover:text-black hover:border-emerald-600 hover:shadow-lg hover:shadow-emerald-100 transition-all duration-200 group/btn"
                        title="Edit Record">
                            <i class="bi bi-pencil-square text-sm"></i>
                        </a>

                        {{-- View/Details Button --}}
                        <a href="{{ route('employees.index', $employee->id) }}" 
                        class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-blue-600 hover:text-black hover:border-blue-600 hover:shadow-lg hover:shadow-blue-100 transition-all duration-200 group/btn"
                        title="View Details">
                            <i class="bi bi-eye text-sm"></i>
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-24 text-center">
                    <div class="max-w-xs mx-auto">
                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-dashed border-slate-200">
                            <i class="bi bi-people text-3xl text-slate-300"></i>
                        </div>
                        <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">No Personnel Found</h3>
                        <p class="text-xs text-slate-500 mt-2">Start by registering your first employee in the system.</p>
                        <a href="{{ route('employees.create') }}" class="inline-block mt-5 text-emerald-600 font-black text-[10px] uppercase tracking-widest border-b-2 border-emerald-600 pb-0.5 hover:text-emerald-700 hover:border-emerald-700 transition">
                            + Add New Record
                        </a>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($employees->hasPages())
    <div id="pagination-container" class="px-6 py-4 bg-slate-50/50 border-t border-slate-100">
        {{ $employees->links() }}
    </div>
@endif