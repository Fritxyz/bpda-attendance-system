
<div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead class="bg-slate-50/80">
            <tr>
                <th rowspan="2" class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest border-r border-slate-100">Day / Date</th>
                <th colspan="2" class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase text-center border-b border-slate-100">Morning</th>
                <th colspan="2" class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase text-center border-b border-slate-100 border-x">Afternoon</th>
                <th colspan="2" class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase text-center border-b border-slate-100">Overtime</th>
                <th rowspan="2" class="px-4 py-5 text-[10px] font-black text-slate-400 uppercase text-center bg-slate-100/30 border-x border-slate-100">Total Hours</th>
                <th colspan="2" class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase text-center border-b border-slate-100">LATE & OT/UT</th>
                <th rowspan="2" class="px-4 py-5 text-[10px] font-black text-slate-400 uppercase text-center">Status</th>
            </tr>
            <tr class="bg-slate-50/30">
                <th class="px-2 py-2 text-[9px] font-bold text-slate-400 uppercase text-center">In</th>
                <th class="px-2 py-2 text-[9px] font-bold text-slate-400 uppercase text-center">Out</th>
                <th class="px-2 py-2 text-[9px] font-bold text-slate-400 uppercase text-center border-l border-slate-100">In</th>
                <th class="px-2 py-2 text-[9px] font-bold text-slate-400 uppercase text-center border-r border-slate-100">Out</th>
                <th class="px-2 py-2 text-[9px] font-bold text-slate-400 uppercase text-center">In</th>
                <th class="px-2 py-2 text-[9px] font-bold text-slate-400 uppercase text-center">Out</th>
                <th class="px-2 py-2 text-[9px] font-bold text-amber-600 uppercase text-center border-l border-slate-100">Late</th>
                <th class="px-2 py-2 text-[9px] font-bold text-red-600 uppercase text-center">UT / OT</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50 text-[13px]">
            @foreach($attendance as $dayNumber => $data)
                @php 
                    $record = $data['record'];
                    $isWeekend = in_array($data['day_name'], ['Sat', 'Sun']);
                    $isHoliday = $data['is_holiday'];
                    $isLeave = $data['is_leave'];
                @endphp
                
                <tr class="{{ $isWeekend ? 'bg-slate-50/50' : '' }} {{ $isHoliday ? 'bg-amber-50/40' : '' }} {{ $isLeave ? 'bg-blue-50/20' : '' }} hover:bg-slate-100/30 transition-all">
                    {{-- Date Column --}}
                    <td class="px-6 py-4 font-bold border-r border-slate-100 {{ $isHoliday ? 'text-amber-700' : 'text-slate-700' }}">
                        {{ sprintf('%02d', $dayNumber) }} 
                        <span class="{{ $isWeekend ? 'text-blue-400' : ($isHoliday ? 'text-amber-400' : 'text-slate-400') }} font-medium ml-1">
                            {{ $data['day_name'] }}
                        </span>
                    </td>

                    @if($isHoliday)
                        {{-- HOLIDAY ROW --}}
                        <td colspan="6" class="px-4 py-4 text-center border-r border-slate-100 italic">
                            <span class="text-[10px] font-black text-amber-600 uppercase tracking-[0.4em]">Regular Holiday (No Work)</span>
                        </td>
                    @elseif($isLeave)
                        {{-- LEAVE ROW --}}
                        <td colspan="6" class="px-4 py-4 text-center border-r border-slate-100">
                            <span class="text-[10px] font-black text-blue-500 uppercase tracking-widest italic">Approved Vacation Leave</span>
                        </td>
                    @elseif(!$record && !$isWeekend)
                        {{-- ABSENT ROW --}}
                        <td colspan="6" class="px-4 py-4 text-center border-r border-slate-100 font-black text-red-200 tracking-widest uppercase text-[10px]">
                            No Record / Absent
                        </td>
                    @else
                        {{-- REGULAR DATA / WEEKEND --}}
                        <td class="px-2 py-4 text-center font-bold text-slate-600">{{ $record && $record->am_in ? date('H:i', strtotime($record->am_in)) : '--:--' }}</td>
                        <td class="px-2 py-4 text-center text-slate-500">{{ $record && $record->am_out ? date('H:i', strtotime($record->am_out)) : '--:--' }}</td>
                        <td class="px-2 py-4 text-center border-l border-slate-100 font-bold text-slate-600">{{ $record && $record->pm_in ? date('H:i', strtotime($record->pm_in)) : '--:--' }}</td>
                        <td class="px-2 py-4 text-center border-r border-slate-100 font-bold text-slate-600">{{ $record && $record->pm_out ? date('H:i', strtotime($record->pm_out)) : '--:--' }}</td>
                        <td class="px-2 py-4 text-center text-emerald-600 font-bold italic">{{ $record && $record->ot_in ? date('H:i', strtotime($record->ot_in)) : '--:--' }}</td>
                        <td class="px-2 py-4 text-center text-emerald-600 font-bold italic">{{ $record && $record->ot_out ? date('H:i', strtotime($record->ot_out)) : '--:--' }}</td>
                    @endif

                    {{-- Total Hours --}}
                    <td class="px-4 py-4 text-center font-black text-slate-700 bg-slate-50/30 border-x border-slate-100 uppercase tracking-tighter">
                        @if($isLeave) 8h 00m @elseif($record) {{ $record->computed_total_hours }} @else 0h 0m @endif
                    </td>

                    {{-- Late (Static —) --}}
                    <td class="px-2 py-4 text-center text-slate-300 border-l border-slate-100 italic">—</td>

                    {{-- UT / OT --}}
                    <td class="px-2 py-4 text-center font-black">
                        @if($record)
                            <span class="{{ str_contains($record->diff_ut_ot, '+') ? 'text-emerald-600' : (str_contains($record->diff_ut_ot, '-') ? 'text-red-500' : 'text-slate-300') }}">
                                {{ $record->diff_ut_ot }}
                            </span>
                        @elseif(!$isWeekend && !$isHoliday && !$isLeave)
                            <span class="text-red-600">8h 00m</span>
                        @else
                            <span class="text-slate-300">—</span>
                        @endif
                    </td>

                    {{-- Status Badge --}}
                    <td class="px-4 py-4 text-center">
                        @if($isHoliday)
                            <i class="bi bi-star-fill text-amber-400"></i>
                        @elseif($isLeave)
                            <span class="bg-blue-100 text-blue-700 text-[9px] font-black px-3 py-1 rounded-full uppercase">On Leave</span>
                        @elseif($record)
                            <span class="bg-{{ $record->status_color }}-100 text-{{ $record->status_color }}-700 text-[9px] font-black px-2 py-1 rounded uppercase">
                                {{ $record->attendance_status }}
                            </span>
                        @elseif($isWeekend)
                            <span class="text-slate-300 text-[9px] font-black uppercase tracking-widest">Weekend</span>
                        @else
                            <span class="bg-red-600 text-white text-[9px] font-black px-3 py-1 rounded uppercase shadow-sm">Absent</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        {{-- <tbody class="divide-y divide-slate-50 text-[13px]">
            
            <tr class="hover:bg-slate-50/50 transition-all">
                <td class="px-6 py-4 font-bold text-slate-700 border-r border-slate-100">01 <span class="text-slate-400 font-medium ml-1">Mon</span></td>
                <td class="px-2 py-4 text-center text-slate-600 font-bold">07:55</td> 
                <td class="px-2 py-4 text-center text-slate-500">12:05</td>
                <td class="px-2 py-4 text-center text-slate-600 font-bold border-l border-slate-100">12:58</td>
                <td class="px-2 py-4 text-center text-slate-600 font-bold border-r border-slate-100">05:05</td>
                <td class="px-2 py-4 text-center text-slate-300 italic">-- : --</td>
                <td class="px-2 py-4 text-center text-slate-300 italic">-- : --</td>
                <td class="px-4 py-4 text-center font-black text-slate-700 bg-slate-50/30 border-x border-slate-100 uppercase tracking-tighter">8h 12m</td>
                <td class="px-2 py-4 text-center text-slate-300 border-l border-slate-100">—</td>
                <td class="px-2 py-4 text-center text-slate-300">—</td>
                <td class="px-4 py-4 text-center">
                    <span class="text-emerald-600 font-black text-[9px] uppercase tracking-widest">Regular</span>
                </td>
            </tr>

            <tr class="hover:bg-amber-50/10 transition-all">
                <td class="px-6 py-4 font-bold text-slate-700 border-r border-slate-100">02 <span class="text-slate-400 font-medium ml-1">Tue</span></td>
                <td class="px-2 py-4 text-center text-red-500 font-black">08:20</td> <td class="px-2 py-4 text-center text-slate-500">12:00</td>
                <td class="px-2 py-4 text-center text-slate-600 font-bold border-l border-slate-100">01:00</td>
                <td class="px-2 py-4 text-center text-slate-600 font-bold border-r border-slate-100">05:00</td>
                <td class="px-2 py-4 text-center text-slate-300 italic">-- : --</td>
                <td class="px-2 py-4 text-center text-slate-300 italic">-- : --</td>
                <td class="px-4 py-4 text-center font-black text-slate-700 bg-slate-50/30 border-x border-slate-100 uppercase tracking-tighter">7h 40m</td>
                <td class="px-2 py-4 text-center font-bold text-amber-600 border-l border-slate-100">20m</td>
                <td class="px-2 py-4 text-center text-slate-300">—</td>
                <td class="px-4 py-4 text-center">
                    <span class="bg-amber-100 text-amber-700 text-[9px] font-black px-2 py-1 rounded uppercase">Late</span>
                </td>
            </tr>

            <tr class="hover:bg-red-50/10 transition-all">
                <td class="px-6 py-4 font-bold text-slate-700 border-r border-slate-100">03 <span class="text-slate-400 font-medium ml-1">Wed</span></td>
                <td class="px-2 py-4 text-center text-slate-600 font-bold">07:45</td>
                <td class="px-2 py-4 text-center text-slate-500">12:00</td>
                <td class="px-2 py-4 text-center text-slate-600 font-bold border-l border-slate-100">01:00</td>
                <td class="px-2 py-4 text-center text-red-500 font-black border-r border-slate-100">03:30</td> <td class="px-2 py-4 text-center text-slate-300 italic">-- : --</td>
                <td class="px-2 py-4 text-center text-slate-300 italic">-- : --</td>
                <td class="px-4 py-4 text-center font-black text-slate-700 bg-slate-50/30 border-x border-slate-100 uppercase tracking-tighter">6h 30m</td>
                <td class="px-2 py-4 text-center text-slate-300 border-l border-slate-100">—</td>
                <td class="px-2 py-4 text-center font-bold text-red-600">1h 30m</td>
                <td class="px-4 py-4 text-center">
                    <span class="bg-red-100 text-red-700 text-[9px] font-black px-2 py-1 rounded uppercase">Undertime</span>
                </td>
            </tr>

            <tr class="hover:bg-emerald-50/30 transition-all">
                <td class="px-6 py-4 font-bold text-slate-700 border-r border-slate-100">04 <span class="text-slate-400 font-medium ml-1">Thu</span></td>
                <td class="px-2 py-4 text-center text-slate-600 font-bold">07:50</td>
                <td class="px-2 py-4 text-center text-slate-500">12:00</td>
                <td class="px-2 py-4 text-center text-slate-600 font-bold border-l border-slate-100">01:00</td>
                <td class="px-2 py-4 text-center text-slate-600 font-bold border-r border-slate-100">05:00</td>
                <td class="px-2 py-4 text-center text-emerald-600 font-bold tracking-tighter">05:30</td> 
                <td class="px-2 py-4 text-center text-emerald-600 font-bold tracking-tighter">08:00</td> 
                <td class="px-4 py-4 text-center font-black text-emerald-700 bg-emerald-50/50 border-x border-slate-100 uppercase tracking-tighter">10h 30m</td>
                <td class="px-2 py-4 text-center text-slate-300 border-l border-slate-100">—</td>
                <td class="px-2 py-4 text-center font-bold text-emerald-600">+2h 30m</td>
                <td class="px-4 py-4 text-center">
                    <span class="bg-emerald-100 text-emerald-700 text-[9px] font-black px-2 py-1 rounded uppercase">OT Rendered</span>
                </td>
            </tr>

            <tr class="bg-amber-50/40">
                <td class="px-6 py-5 font-bold text-amber-700 border-r border-slate-100">05 <span class="text-amber-400 font-medium ml-1">Fri</span></td>
                <td colspan="6" class="px-4 py-5 text-center border-r border-slate-100 italic">
                    <span class="text-[10px] font-black text-amber-600 uppercase tracking-[0.4em]">Regular Holiday (No Work)</span>
                </td>
                <td class="px-4 py-5 text-center font-black text-slate-400 bg-slate-100/30 border-x border-slate-100">—</td>
                <td colspan="2" class="px-2 py-4 text-center border-l border-slate-100 italic text-slate-300 uppercase text-[9px]">Exempted</td>
                <td class="px-4 py-4 text-center">
                    <i class="bi bi-star-fill text-amber-400"></i>
                </td>
            </tr>

            <tr class="bg-blue-50/20">
                <td class="px-6 py-5 font-bold text-slate-700 border-r border-slate-100">08 <span class="text-slate-400 font-medium ml-1">Mon</span></td>
                <td colspan="6" class="px-4 py-5 text-center border-r border-slate-100">
                    <span class="text-[10px] font-black text-blue-500 uppercase tracking-widest italic">Approved Vacation Leave</span>
                </td>
                <td class="px-4 py-5 text-center font-black text-slate-700 bg-slate-50/30 border-x border-slate-100 uppercase">8h 00m</td>
                <td colspan="2" class="px-2 py-4 text-center border-l border-slate-100">—</td>
                <td class="px-4 py-4 text-center">
                    <span class="bg-blue-100 text-blue-700 text-[9px] font-black px-3 py-1 rounded-full uppercase">On Leave</span>
                </td>
            </tr>

            <tr class="bg-red-50/30">
                <td class="px-6 py-5 font-bold text-red-700 border-r border-slate-100">09 <span class="text-red-400 font-medium ml-1">Tue</span></td>
                <td colspan="6" class="px-4 py-5 text-center border-r border-slate-100 font-black text-red-200 tracking-widest uppercase text-[10px]">No Record / Absent</td>
                <td class="px-4 py-5 text-center font-black text-red-600 bg-red-100/20 border-x border-slate-100 uppercase">0h 00m</td>
                <td class="px-2 py-4 text-center text-slate-300 border-l border-slate-100">—</td>
                <td class="px-2 py-4 text-center font-bold text-red-600 uppercase">8h 00m</td>
                <td class="px-4 py-4 text-center">
                    <span class="bg-red-600 text-white text-[9px] font-black px-3 py-1 rounded uppercase shadow-sm shadow-red-200 tracking-tighter">Absent</span>
                </td>
            </tr>
        </tbody> --}}

        {{-- <tfoot class="bg-slate-800 text-white font-bold no-print">
            <tr>
                <td colspan="7" class="px-8 py-4 text-right text-[10px] uppercase tracking-widest border-r border-slate-700">Monthly Accumulation:</td>
                <td class="px-4 py-4 text-center bg-slate-700 border-x border-slate-600">160h 22m</td>
                <td class="px-2 py-4 text-center text-amber-400">20m</td>
                <td class="px-2 py-4 text-center text-emerald-400">2h 30m</td>
                <td class="px-4 py-4 text-center text-[9px] uppercase tracking-tighter opacity-50 italic text-slate-300">End of Record</td>
            </tr>
        </tfoot> --}}
    </table>
</div>