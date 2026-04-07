<div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-black uppercase tracking-[0.1em] text-slate-400">
                <th rowspan="2" class="px-6 py-4 text-slate-600 border-r border-slate-100 text-center">Personnel Info</th>
                <th colspan="2" class="px-2 py-3 text-center border-r border-slate-100 bg-slate-50/50">Morning Session</th>
                <th colspan="2" class="px-2 py-3 text-center border-r border-slate-100 bg-slate-50/50">Afternoon Session</th>
                <th colspan="2" class="px-2 py-3 text-center border-r border-slate-100 bg-emerald-50/30 text-emerald-600">Overtime</th>
                <th rowspan="2" class="px-4 py-4 text-center border-r border-slate-100">Total Hours</th>
                <th rowspan="2" class="px-4 py-4 text-center border-r border-slate-100">Status</th>
                <th rowspan="2" class="px-6 py-4 text-center">Actions</th>
            </tr>
            <tr class="bg-white border-b border-slate-200 text-[9px] font-black uppercase tracking-widest text-slate-500">
                <th class="px-4 py-2 text-center border-r border-slate-100">In</th>
                <th class="px-4 py-2 text-center border-r border-slate-100">Out</th>
                <th class="px-4 py-2 text-center border-r border-slate-100">In</th>
                <th class="px-4 py-2 text-center border-r border-slate-100">Out</th>
                <th class="px-4 py-2 text-center border-r border-slate-100 bg-emerald-50/20">In</th>
                <th class="px-4 py-2 text-center border-r border-slate-100 bg-emerald-50/20">Out</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($today_records as $record)
            <tr class="hover:bg-slate-50/80 transition-colors group">
                <td class="px-6 py-5 border-r border-slate-100/50">
                    <div class="flex items-center gap-4">
                        <div class="h-11 w-11 flex-shrink-0 rounded-2xl bg-gradient-to-br from-emerald-50 to-emerald-100 text-emerald-700 flex items-center justify-center font-black text-xs shadow-sm border border-emerald-200/50 uppercase tracking-tighter">
                            {{ substr($record->employee->first_name, 0, 1) }}{{ substr($record->employee->last_name, 0, 1) }}
                        </div>

                        <div class="flex flex-col min-w-0">
                            <h3 class="text-sm font-black text-slate-800 tracking-tight uppercase leading-none mb-1.5 truncate">
                                {{ $record->employee->first_name }} {{ $record->employee->last_name }}
                            </h3>
                            
                            <div class="flex items-center gap-1.5 mb-1.5">
                                @if($record->employee->bureau)
                                    <span class="text-[9px] font-black uppercase tracking-wider text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200/50">
                                        {{ $record->employee->bureau }}
                                    </span>
                                @endif

                                <span class="text-[9px] font-black uppercase tracking-wider text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100/50">
                                    {{ $record->employee->division ?? 'N/A' }}
                                </span>
                            </div>

                            {{-- Position Row (Underneath) --}}
                            <div class="flex items-center gap-1">
                                <i class="bi bi-person-badge text-[10px] text-slate-300"></i> {{-- Optional icon para mas maganda --}}
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter truncate">
                                    {{ $record->employee->position ?? 'N/A' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </td>
                
                <td class="px-4 py-4 text-center font-mono text-xs text-slate-600">
                    {{ $record->am_in ? date('h:i A', strtotime($record->am_in)) : '--:--' }}
                </td>
                <td class="px-4 py-4 text-center font-mono text-xs text-slate-600 border-r border-slate-100/50">
                    {{ $record->am_out ? date('h:i A', strtotime($record->am_out)) : '--:--' }}
                </td>
                
                <td class="px-4 py-4 text-center font-mono text-xs text-slate-600">
                    {{ $record->pm_in ? date('h:i A', strtotime($record->pm_in)) : '--:--' }}
                </td>
                <td class="px-4 py-4 text-center font-mono text-xs text-slate-600 border-r border-slate-100/50">
                    {{ $record->pm_out ? date('h:i A', strtotime($record->pm_out)) : '--:--' }}
                </td>

                <td class="px-4 py-4 text-center font-mono text-xs text-emerald-600 font-bold bg-emerald-50/10">
                    {{ $record->ot_in ? date('h:i A', strtotime($record->ot_in)) : '--:--' }}
                </td>
                <td class="px-4 py-4 text-center font-mono text-xs text-emerald-600 font-bold bg-emerald-50/10 border-r border-slate-100/50">
                    {{ $record->ot_out ? date('h:i A', strtotime($record->ot_out)) : '--:--' }}
                </td>

                <td class="px-4 py-4 text-center font-black text-slate-700 border-r border-slate-100/50">
                    {{ $record->computed_total_hours }}
                </td>

                <td class="px-4 py-4 text-center border-r border-slate-100/50">
                    <span class="inline-block px-3 py-1 bg-{{ $record->status_color }}-50 text-{{ $record->status_color }}-600 text-[10px] font-black uppercase rounded-lg border border-{{ $record->status_color }}-100 shadow-sm">
                        {{ $record->attendance_status }}: {{ $record->diff_hours }}
                    </span>
                </td>

                <td class="px-6 py-4">
                    <div class="flex justify-end items-center gap-1">
                        <a href="{{ route('dtr.edit', ['employee' => $record->employee->employee_id, 'date' => $record->attendance_date]) }}" 
                        class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-green-50 text-green-700 border border-green-100 hover:bg-green-600 hover:text-white hover:border-green-600 transition-all duration-200 group" 
                            >
                            <i class="bi bi-pencil-square text-base"></i>
                            <span class="text-[10px] font-black uppercase tracking-widest hidden lg:block">Edit</span>
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="px-6 py-12 text-center">
                    <i class="bi bi-calendar2-x text-4xl text-slate-200 mb-3 block"></i>
                    <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">No attendance records found for today.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>