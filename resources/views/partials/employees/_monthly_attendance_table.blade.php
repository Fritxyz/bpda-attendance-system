
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
                    $isHoliday = $data['is_holiday'] ?? false;
                    $isLeave = $data['is_leave'] ?? false;
                @endphp
                
                <tr class="hover:bg-slate-100/30 transition-all {{ $data['is_future'] || $data['is_before_hire'] ? 'opacity-40 bg-slate-50/30' : '' }} {{ $isWeekend ? 'bg-slate-50/50' : '' }} {{ $isHoliday ? 'bg-amber-50/40' : '' }}">
                    
                    {{-- 1. Date Column --}}
                    <td class="px-6 py-4 font-bold border-r border-slate-100 {{ $isHoliday ? 'text-amber-700' : 'text-slate-700' }}">
                        {{ sprintf('%02d', $dayNumber) }} 
                        <span class="text-[10px] {{ $isWeekend ? 'text-blue-400' : ($isHoliday ? 'text-amber-400' : 'text-slate-400') }} font-medium ml-1">
                            {{ $data['day_name'] }}
                        </span>
                    </td>

                    @if($data['is_before_hire'])
                        {{-- CASE 1: Bago pa siya ma-hire --}}
                        <td colspan="6" class="px-4 py-4 text-center border-r border-slate-100">
                            <span class="text-[9px] font-bold text-slate-300 uppercase tracking-[0.2em]">--- Not Yet Officially Employed ---</span>
                        </td>
                        <td class="px-4 py-5 text-center bg-slate-100/10 border-x border-slate-100 text-slate-200 italic">--</td>
                        <td colspan="2" class="px-4 py-4 text-center text-slate-200 italic border-r border-slate-100">--</td>
                        <td class="px-4 py-4 text-center">
                            <span class="text-slate-300 text-[9px] font-black uppercase opacity-50">N/A</span>
                        </td>

                    @elseif($data['is_future'])
                        {{-- CASE 2: Future date (Wala pang record) --}}
                        <td colspan="6" class="px-4 py-4 text-center border-r border-slate-100">
                            <span class="text-[9px] font-bold text-slate-200 uppercase tracking-widest italic">No Data Available</span>
                        </td>
                        <td class="px-4 py-5 text-center bg-slate-100/10 border-x border-slate-100 text-slate-200 italic">--</td>
                        <td colspan="2" class="px-4 py-4 text-center text-slate-200 italic border-r border-slate-100">--</td>
                        <td class="px-4 py-4 text-center">
                            <span class="text-slate-200 text-[9px] font-black uppercase italic">Upcoming</span>
                        </td>

                    @else
                        {{-- CASE 3: Active Days (Regular Attendance Logic) --}}
                        @if($isHoliday)
                            <td colspan="6" class="px-4 py-4 text-center border-r border-slate-100 italic">
                                <span class="text-[10px] font-black text-amber-600 uppercase tracking-[0.4em]">Regular Holiday</span>
                            </td>
                        @elseif($isLeave)
                            <td colspan="6" class="px-4 py-4 text-center border-r border-slate-100 italic">
                                <span class="text-[10px] font-black text-blue-500 uppercase tracking-widest">Approved Leave</span>
                            </td>
                        @elseif(!$record && !$isWeekend)
                            <td colspan="6" class="px-4 py-4 text-center border-r border-slate-100 font-black text-red-200 tracking-widest uppercase text-[10px]">
                                No Record / Absent
                            </td>
                        @else
                            {{-- Morning --}}
                            <td class="px-2 py-4 text-center font-bold text-slate-600">{{ $record && $record->am_in ? date('h:i A', strtotime($record->am_in)) : '--:--' }}</td>
                            <td class="px-2 py-4 text-center text-slate-500">{{ $record && $record->am_out ? date('h:i A', strtotime($record->am_out)) : '--:--' }}</td>
                            {{-- Afternoon --}}
                            <td class="px-2 py-4 text-center border-l border-slate-100 font-bold text-slate-600">{{ $record && $record->pm_in ? date('h:i A', strtotime($record->pm_in)) : '--:--' }}</td>
                            <td class="px-2 py-4 text-center border-r border-slate-100 font-bold text-slate-600">{{ $record && $record->pm_out ? date('h:i A', strtotime($record->pm_out)) : '--:--' }}</td>
                            {{-- Overtime --}}
                            <td class="px-2 py-4 text-center text-emerald-600 font-bold italic">{{ $record && $record->ot_in ? date('h:i A', strtotime($record->ot_in)) : '--:--' }}</td>
                            <td class="px-2 py-4 text-center text-emerald-600 font-bold italic">{{ $record && $record->ot_out ? date('h:i A', strtotime($record->ot_out)) : '--:--' }}</td>
                        @endif

                        {{-- 2. Total Hours --}}
                        <td class="px-4 py-5 text-center font-black text-slate-700 bg-slate-100/30 border-x border-slate-100 uppercase tracking-tighter">
                            @if($isLeave) 8h 00m @elseif($record) {{ $record->computed_total_hours }} @else 0h 0m @endif
                        </td>

                        {{-- 3. Late --}}
                        <td class="px-2 py-4 text-center text-slate-300 border-l border-slate-100 italic">—</td>

                        {{-- 4. UT / OT --}}
                        <td class="px-2 py-4 text-center font-black border-r border-slate-100">
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

                        {{-- 5. Status Badge --}}
                        <td class="px-4 py-4 text-center">
                            @if($isHoliday)
                                <i class="bi bi-star-fill text-amber-400"></i>
                            @elseif($isLeave)
                                <span class="bg-blue-100 text-blue-700 text-[9px] font-black px-3 py-1 rounded-full uppercase">Leave</span>
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
                    @endif
                </tr>
            @endforeach
        </tbody>

    </table>
</div>