<div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-black uppercase tracking-[0.1em] text-slate-400">
                <th class="px-6 py-4 text-slate-600">Personnel Information</th>
                <th class="px-4 py-4 text-center">Bureau/Division</th>
                <th class="px-4 py-4 text-center">Employment</th>
                <th class="px-4 py-4 text-center">Date</th>
                <th class="px-4 py-4 text-center">Actual In</th>
                <th class="px-4 py-4 text-center text-red-600 bg-red-50/30">Late Duration</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($lateRecords as $record)
            <tr class="hover:bg-slate-50/80 transition-colors group">
                {{-- Name & Position --}}
                <td class="px-6 py-5">
                    <div class="flex items-center gap-4">
                        <div class="h-11 w-11 flex-shrink-0 rounded-2xl bg-gradient-to-br from-red-50 to-red-100 text-red-700 flex items-center justify-center font-black text-xs shadow-sm border border-red-200/50 uppercase">
                            {{ substr($record->employee->first_name, 0, 1) }}{{ substr($record->employee->last_name, 0, 1) }}
                        </div>
                        <div class="flex flex-col min-w-0">
                            <h3 class="text-sm font-black text-slate-800 tracking-tight uppercase mb-1">
                                {{ $record->employee->first_name }} {{ $record->employee->last_name }}
                            </h3>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">
                                {{ $record->employee->position ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                </td>

                {{-- Bureau/Division --}}
                <td class="px-4 py-4 text-center">
                    <div class="flex flex-col items-center gap-1">
                        <span class="text-[10px] font-black uppercase tracking-wider text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100/50">
                            {{ $record->employee->division ?? 'N/A' }}
                        </span>
                        <span class="text-[9px] text-slate-400 font-bold uppercase">{{ $record->employee->bureau ?? 'N/A' }}</span>
                    </div>
                </td>

                {{-- Type --}}
                <td class="px-4 py-4 text-center">
                    <span class="text-[10px] font-bold text-slate-600 uppercase bg-slate-100 px-2 py-1 rounded-md">
                        {{ $record->employee->employment_type }}
                    </span>
                </td>

                {{-- Date --}}
                <td class="px-4 py-4 text-center font-mono text-xs text-slate-600">
                    {{ \Carbon\Carbon::parse($record->attendance_date)->format('M d, Y') }}
                </td>

                {{-- Time In --}}
                <td class="px-4 py-4 text-center font-mono text-sm text-red-600 font-black">
                    {{ \Carbon\Carbon::parse($record->am_in)->format('h:i A') }}
                </td>

                {{-- Late Duration Badge --}}
                <td class="px-4 py-4 text-center bg-red-50/10">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-100 text-red-700 text-[11px] font-black uppercase rounded-lg border border-red-200 shadow-sm">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        {{ $record->computed_late }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-20 text-center">
                    <div class="flex flex-col items-center">
                        <div class="h-20 w-20 bg-emerald-50 rounded-full flex items-center justify-center mb-4">
                            <i class="bi bi-check2-circle text-4xl text-emerald-500"></i>
                        </div>
                        <h4 class="text-slate-800 font-black uppercase tracking-widest text-sm">All Clear!</h4>
                        <p class="text-slate-400 text-xs mt-1 font-medium">No personnel recorded as late for this period.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>