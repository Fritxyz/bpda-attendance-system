@if ($employee->employment_type === "Contractual") 
    <div class="overflow-x-auto selection:bg-emerald-100 shadow-lg rounded-lg border border-slate-200">
        <table class="w-full text-left border-collapse bg-white table-fixed min-w-[1000px]">
            <thead class="bg-slate-100 sticky top-0 z-10">
                <tr>
                    <th rowspan="2" class="w-[120px] px-4 py-4 text-[10px] font-black text-slate-700 uppercase tracking-widest border-r border-slate-200">Day / Date</th>
                    <th colspan="2" class="px-2 py-2 text-[10px] font-black text-slate-700 uppercase text-center border-b border-slate-200">Morning</th>
                    <th colspan="2" class="px-2 py-2 text-[10px] font-black text-slate-700 uppercase text-center border-b border-slate-200 border-x">Afternoon</th>
                    <th colspan="2" class="px-2 py-2 text-[10px] font-black text-slate-700 uppercase text-center border-b border-slate-200">Overtime</th>
                    <th rowspan="2" class="w-[100px] px-2 py-4 text-[10px] font-black text-slate-700 uppercase text-center bg-slate-200/50 border-x border-slate-200">Total Hours</th>
                    <th colspan="3" class="px-2 py-2 text-[10px] font-black text-slate-700 uppercase text-center border-b border-slate-200">LATE & OT/UT</th>
                    <th rowspan="2" class="w-[110px] px-2 py-4 text-[10px] font-black text-slate-700 uppercase text-center">Status</th>
                </tr>
                <tr class="bg-slate-50">
                    <th class="px-2 py-2 text-[9px] font-bold text-slate-600 uppercase text-center">In</th>
                    <th class="px-2 py-2 text-[9px] font-bold text-slate-600 uppercase text-center">Out</th>
                    <th class="px-2 py-2 text-[9px] font-bold text-slate-600 uppercase text-center border-l border-slate-200">In</th>
                    <th class="px-2 py-2 text-[9px] font-bold text-slate-600 uppercase text-center border-r border-slate-200">Out</th>
                    <th class="px-2 py-2 text-[9px] font-bold text-slate-600 uppercase text-center">In</th>
                    <th class="px-2 py-2 text-[9px] font-bold text-slate-600 uppercase text-center">Out</th>
                    <th class="px-2 py-2 text-[9px] font-bold text-amber-700 uppercase text-center border-l border-slate-200">Late</th>
                    <th class="px-2 py-2 text-[9px] font-bold text-rose-700 uppercase text-center">UT / OT</th>
                    <th class="px-2 py-2 text-[9px] font-bold text-rose-700 uppercase text-center border-r">Deduction</th>
                </tr>
            </thead>
            
            <tbody class="divide-y divide-slate-200 text-[12px]">
                @php 
                    $grandTotalMinutes = 0;
                    $grandTotalLateMinutes = 0;
                    $grandTotalUTOTMinutes = 0;
                    
                    // Eto ang papalit sa $grandTotalDeduction para hiwalay sila
                    $totalLateDeduction = 0;      
                    $totalUndertimeDeduction = 0; 
                @endphp

                @foreach($attendance as $dayNumber => $data)
                    @php 
                        $record = $data['record'];
                        $isWeekend = in_array($data['day_name'], ['Sat', 'Sun']);
                        $isHoliday = $data['is_holiday'] ?? false;
                        $isLeave = $data['is_leave'] ?? false;

                        if ($record) {
                            // Total Hours Sum
                            preg_match('/(\d+)h\s+(\d+)m/', $record->computed_total_hours, $matches);
                            if(count($matches) == 3) { $grandTotalMinutes += ($matches[1] * 60) + $matches[2]; }

                            $totalLateDeduction += $record->salary_deduction_by_late ?? 0;
                            $totalUndertimeDeduction += $record->salary_deduction_undertime ?? 0;

                            // Late Sum
                            if($record->computed_late) {
                                preg_match_all('/(\d+)/', $record->computed_late, $lateMatches);
                                $vals = $lateMatches[0];
                                if(count($vals) == 2) { $grandTotalLateMinutes += ($vals[0] * 60) + $vals[1]; }
                                else if(count($vals) == 1) { $grandTotalLateMinutes += $vals[0]; }
                            }

                            // UT/OT Sum logic
                            if($record->diff_ut_ot != '0h 0m' && $record->diff_ut_ot != '—') {
                                $isNeg = str_contains($record->diff_ut_ot, '-');
                                preg_match_all('/(\d+)/', $record->diff_ut_ot, $diffMatches);
                                $dvals = $diffMatches[0];
                                $m = (count($dvals) == 2) ? ($dvals[0] * 60) + $dvals[1] : $dvals[0];
                                $grandTotalUTOTMinutes += $isNeg ? -$m : $m;
                            }

                        } elseif (!$isWeekend && !$isHoliday && !$isLeave && !$data['is_future'] && !$data['is_before_hire'] && !$data['is_travel']) {
                            $absentVal = floor(($employee->salary / 22) * 100) / 100;
                            $totalUndertimeDeduction += $absentVal; // Absent is considered undertime/deduction
                            $grandTotalUTOTMinutes -= 480;
                        }
                    @endphp
                    
                    <tr class="hover:bg-slate-50 transition-colors {{ $isWeekend ? 'bg-blue-50/20' : '' }}">
                        <td class="px-4 py-3 font-bold border-r border-slate-200">
                            {{ sprintf('%02d', $dayNumber) }} <span class="text-[10px] text-slate-500 ml-1">{{ $data['day_name'] }}</span>
                        </td>

                        @if($data['is_before_hire'] || $data['is_future'])
                            <td colspan="6" class="px-2 py-3 text-center border-r border-slate-200 text-slate-300 italic text-[10px]">
                                {{ $data['is_before_hire'] ? 'PRE-EMPLOYMENT' : 'UPCOMING' }}
                            </td>
                            <td class="bg-slate-50 border-r border-slate-200"></td>
                            <td colspan="3" class="border-r border-slate-200"></td>
                            <td></td>
                        @else
                            {{-- Logics for Holiday/Leave/Weekend/Active --}}
                            @if($isHoliday)
                                <td colspan="6" class="px-2 py-3 text-center border-r border-slate-200 bg-amber-50 text-amber-700 font-bold text-[10px] tracking-widest uppercase">Holiday: {{ $data['holiday_name'] }}</td>
                            @elseif($isLeave)
                                <td colspan="6" class="px-2 py-3 text-center border-r border-slate-200 bg-blue-50 text-blue-700 font-bold text-[10px] tracking-widest uppercase">Approved Leave</td>
                            @elseif($data['is_travel'] ?? false) 
                                <td colspan="6" class="px-2 py-3 text-center border-r border-slate-200 bg-emerald-50 text-emerald-700 font-bold text-[10px] tracking-widest uppercase italic">Official Travel</td>
                            @elseif($isWeekend)
                                <td colspan="6" class="px-2 py-3 text-center border-r border-slate-200 text-blue-700 font-black tracking-[0.4em] uppercase text-[10px]">Weekend</td>
                            @elseif(!$record)
                                <td colspan="6" class="px-2 py-3 text-center border-r border-slate-200 text-rose-700 font-bold text-[10px] uppercase">Absent</td>
                            @else
                                <td class="px-1 py-3 text-center">{{ $record->am_in ? date('h:i A', strtotime($record->am_in)) : '--' }}</td>
                                <td class="px-1 py-3 text-center">{{ $record->am_out ? date('h:i A', strtotime($record->am_out)) : '--' }}</td>
                                <td class="px-1 py-3 text-center border-l border-slate-100">{{ $record->pm_in ? date('h:i A', strtotime($record->pm_in)) : '--' }}</td>
                                <td class="px-1 py-3 text-center">{{ $record->pm_out ? date('h:i A', strtotime($record->pm_out)) : '--' }}</td>
                                <td class="px-1 py-3 text-center border-l border-slate-100 text-emerald-600">{{ $record->ot_in ? date('h:i A', strtotime($record->ot_in)) : '--' }}</td>
                                <td class="px-1 py-3 text-center text-emerald-600">{{ $record->ot_out ? date('h:i A', strtotime($record->ot_out)) : '--' }}</td>
                            @endif

                            <td class="px-2 py-3 text-center font-bold bg-slate-50 border-x border-slate-200">
                                @if($isLeave) 8h 0m @elseif($record) {{ $record->computed_total_hours }} @else -- @endif
                            </td>

                            <td class="px-1 py-3 text-center border-r border-slate-100 text-amber-700 font-medium">
                                {{ $record && $record->computed_late ? $record->computed_late : '—' }}
                            </td>

                            <td class="px-1 py-3 text-center border-r border-slate-100 text-rose-700 font-medium">
                                @if($record)
                                    {{ $record->diff_ut_ot != '—' ? $record->diff_ut_ot : '—' }}
                                @elseif(!$record && !$isWeekend && !$isHoliday && !$isLeave && !$data['is_future'] && !$data['is_travel'])
                                    8h 0m
                                @else — @endif
                            </td>

                            <td class="px-1 py-3 text-center border-r border-slate-200 text-rose-700 leading-tight">
                                @if($record)
                                    <div class="text-[9px] text-slate-400">L: ₱{{ number_format($record->salary_deduction_by_late, 2) }}</div>
                                    <div class="text-[9px] text-slate-400">U: ₱{{ number_format($record->salary_deduction_undertime, 2) }}</div>
                                    <div class="font-bold border-t border-slate-100 mt-1">
                                        ₱{{ number_format($record->salary_deduction_by_late + $record->salary_deduction_undertime, 2) }}
                                    </div>
                                @elseif($data['is_travel'] ?? false)
                                    <span class="font-bold text-emerald-600">₱0.00</span>
                                @elseif(!$record && !$isWeekend && !$isHoliday && !$isLeave && !$data['is_future'])
                                    <span class="font-bold text-rose-700">₱{{ number_format(floor(($employee->salary / 22) * 100) / 100, 2) }}</span>
                                @else — @endif
                            </td>

                            <td class="px-2 py-3 text-center">
                                @php
                                    $status = $isHoliday ? 'Holiday' : ($isLeave ? 'Leave' : ($isWeekend ? 'Weekend' : ($record ? $record->attendance_status : 'Absent')));
                                    $color = $isHoliday ? 'amber-500' : ($isLeave ? 'blue-600' : ($isWeekend ? 'slate-400' : ($record ? ($record->status_color == 'emerald' ? 'emerald-600' : ($record->status_color == 'amber' ? 'amber-500' : 'rose-600')) : 'rose-700')));
                                @endphp
                                <span class="text-[9px] font-black uppercase px-2 py-1 rounded {{ in_array($status, ['Leave', 'Absent', 'REGULAR', 'OVERTIME', 'UNDERTIME']) ? 'bg-'.$color.' text-white' : 'text-'.$color }}">{{ $status }}</span>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>

            <tfoot class="bg-slate-900 text-white font-bold uppercase tracking-widest text-[10px]">
                <tr class="border-b border-slate-700 bg-slate-800/50">
                    <td colspan="7" class="px-4 py-3 text-right border-r border-slate-700 text-slate-400">
                        Overall Totals
                    </td>
                    <td class="px-1 py-3 text-center border-r border-slate-700 text-emerald-400">
                        {{ floor($grandTotalMinutes / 60) }}h {{ $grandTotalMinutes % 60 }}m
                    </td>
                    <td class="px-1 py-3 text-center border-r border-slate-700 text-amber-500">
                        {{ floor($grandTotalLateMinutes / 60) }}h {{ $grandTotalLateMinutes % 60}}m 
                    </td>
                    <td class="px-1 py-3 text-center border-r border-slate-700 text-rose-400">
                        @php
                            $utHours = floor(abs($grandTotalUTOTMinutes) / 60);
                            $utMins = abs($grandTotalUTOTMinutes) % 60;
                            $prefix = $grandTotalUTOTMinutes < 0 ? '-' : '';
                        @endphp
                        {{ $prefix }}{{ $utHours }}h {{ $utMins }}m
                    </td>
                    <td colspan="1" class="px-1 py-3 text-center border-r border-slate-700 bg-rose-950/30 text-rose-300 text-[12px]">
                        ₱{{ number_format($totalLateDeduction + $totalUndertimeDeduction, 2) }}
                    </td>
                    <td colspan="" class="px-1 py-3"></td>
                </tr>
                <tr class="bg-slate-950">
                    <td colspan="7" class="px-4 py-4 text-left border-r border-slate-800 text-slate-500 font-medium normal-case tracking-normal">
                        BASIC SALARY: <span class="text-slate-200">₱{{ number_format($employee->salary, 2) }}</span> | 
                        LATE: <span class="text-rose-400">(₱{{ number_format($totalLateDeduction, 2) }})</span> | 
                        UT/ABSENT: <span class="text-rose-400">(₱{{ number_format($totalUndertimeDeduction, 2) }})</span>
                    </td>
                    <td colspan="5" class="px-4 py-4 text-center bg-emerald-950/40 border-l border-slate-800">
                        <span class="text-slate-400 mr-2 text-[9px]">NET SALARY FOR THIS MONTH:</span>
                        <span class="text-[16px] text-emerald-400 font-black">
                            @php
                                // Kunin natin ang bilang ng mga araw na may record (pumasok)
                                $hasAttendance = $attendanceRecords->count() > 0;
                                
                                // Logic: Kung walang attendance record sa buwan na ito, 0 agad.
                                // Kung meron, i-compute ang sahod minus deductions.
                                if (!$hasAttendance) {
                                    $netSalary = 0;
                                } else {
                                    $netSalary = $employee->salary - ($totalLateDeduction + $totalUndertimeDeduction);
                                }

                                // Siguraduhin na hindi mag-negative (safety net)
                                $displaySalary = max(0, $netSalary);
                            @endphp
                            ₱{{ number_format($displaySalary, 2) }}
                        </span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div> 
@elseif ($employee->employment_type === "Permanent") 
    <div class="grid grid-cols-5 gap-3 mb-4 p-4">

        <div class="bg-white rounded-xl border border-slate-200 border-t-2 border-t-slate-400 p-4">
            <div class="text-[10px] text-slate-400 uppercase tracking-widest mb-1">Starting Balance</div>
            <div class="text-xl font-black text-slate-700">{{ number_format($leaveSummary['starting_balance'], 3) }}</div>
            <div class="text-[10px] text-slate-400 mt-1">days</div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 border-t-2 border-t-emerald-500 p-4">
            <div class="text-[10px] text-emerald-600 uppercase tracking-widest mb-1">+ Monthly Accrual</div>
            <div class="text-xl font-black text-emerald-700">+{{ number_format($leaveSummary['accrual'], 3) }}</div>
            <div class="text-[10px] text-slate-400 mt-1">earned this month</div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 border-t-2 border-t-rose-500 p-4">
            <div class="text-[10px] text-rose-500 uppercase tracking-widest mb-1">− Deductions</div>
            <div class="text-xl font-black text-rose-700">
                −{{ number_format($leaveSummary['late_deduction'] + $leaveSummary['ut_deduction'] + $leaveSummary['absent_deduction'], 3) }}
            </div>
            <div class="text-[10px] text-slate-400 mt-1 leading-relaxed">
                L: {{ number_format($leaveSummary['late_deduction'], 3) }} &nbsp;|&nbsp;
                UT: {{ number_format($leaveSummary['ut_deduction'], 3) }} &nbsp;|&nbsp;
                A: {{ number_format($leaveSummary['absent_deduction'], 3) }}
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 border-t-2 border-t-slate-400 p-4">
            <div class="text-[10px] text-slate-400 uppercase tracking-widest mb-1">Net Change</div>
            <div class="text-xl font-black {{ $leaveSummary['net_change'] >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                {{ $leaveSummary['net_change'] >= 0 ? '+' : '' }}{{ number_format($leaveSummary['net_change'], 3) }}
            </div>
            <div class="text-[10px] text-slate-400 mt-1">this month</div>
        </div>

        <div class="bg-slate-50 rounded-xl border border-slate-200 border-t-2 {{ $leaveSummary['ending_balance'] < 0 ? 'border-t-rose-500' : 'border-t-emerald-500' }} p-4">
            <div class="text-[10px] text-slate-400 uppercase tracking-widest mb-1">Ending Balance</div>
            <div class="text-xl font-black {{ $leaveSummary['ending_balance'] < 0 ? 'text-rose-700' : 'text-emerald-700' }}">
                {{ number_format($leaveSummary['ending_balance'], 3) }}
            </div>
            <div class="text-[10px] text-slate-400 mt-1">days remaining</div>
        </div>

    </div>

    <div class="overflow-x-auto selection:bg-emerald-100 shadow-lg rounded-lg border border-slate-200">
        <table class="w-full text-left border-collapse bg-white table-fixed min-w-[1000px]">
            <thead class="bg-slate-100 sticky top-0 z-10">
                <tr>
                    <th rowspan="2" class="w-[120px] px-4 py-4 text-[10px] font-black text-slate-700 uppercase tracking-widest border-r border-slate-200">Day / Date</th>
                    <th colspan="2" class="px-2 py-2 text-[10px] font-black text-slate-700 uppercase text-center border-b border-slate-200">Morning</th>
                    <th colspan="2" class="px-2 py-2 text-[10px] font-black text-slate-700 uppercase text-center border-b border-slate-200 border-x">Afternoon</th>
                    <th colspan="2" class="px-2 py-2 text-[10px] font-black text-slate-700 uppercase text-center border-b border-slate-200">Overtime</th>
                    <th rowspan="2" class="w-[100px] px-2 py-4 text-[10px] font-black text-slate-700 uppercase text-center bg-slate-200/50 border-x border-slate-200">Total Hours</th>
                    <th colspan="3" class="px-2 py-2 text-[10px] font-black text-slate-700 uppercase text-center border-b border-slate-200">CSC LEAVE CREDIT DEDUCTIONS</th>
                    <th rowspan="2" class="w-[110px] px-2 py-4 text-[10px] font-black text-slate-700 uppercase text-center">Status</th>
                </tr>
                <tr class="bg-slate-50">
                    <th class="px-2 py-2 text-[9px] font-bold text-slate-600 uppercase text-center">In</th>
                    <th class="px-2 py-2 text-[9px] font-bold text-slate-600 uppercase text-center">Out</th>
                    <th class="px-2 py-2 text-[9px] font-bold text-slate-600 uppercase text-center border-l border-slate-200">In</th>
                    <th class="px-2 py-2 text-[9px] font-bold text-slate-600 uppercase text-center border-r border-slate-200">Out</th>
                    <th class="px-2 py-2 text-[9px] font-bold text-slate-600 uppercase text-center">In</th>
                    <th class="px-2 py-2 text-[9px] font-bold text-slate-600 uppercase text-center">Out</th>
                    <th class="px-2 py-2 text-[9px] font-bold text-amber-700 uppercase text-center border-l border-slate-200">Late (Min)</th>
                    <th class="px-2 py-2 text-[9px] font-bold text-rose-700 uppercase text-center">UT (Min)</th>
                    <th class="px-2 py-2 text-[9px] font-bold text-rose-700 uppercase text-center border-r">Deduction (Days)</th>
                </tr>
            </thead>
            
            <tbody class="divide-y divide-slate-200 text-[12px]">
                @php 
                    $grandTotalMinutes = 0;
                    $grandTotalLateMinutes = 0;
                    $grandTotalUTMinutes = 0;
                    $grandTotalCreditDeduction = 0;
                @endphp

                @foreach($attendance as $dayNumber => $data)
                    @php 
                        $record = $data['record'];
                        $isWeekend = in_array($data['day_name'], ['Sat', 'Sun']);
                        $isHoliday = $data['is_holiday'] ?? false;
                        $isLeave = $data['is_leave'] ?? false;
                        $isFuture = $data['is_future'] ?? false;

                        if ($record) {
                            // Summing Total Hours
                            preg_match('/(\d+)h\s+(\d+)m/', $record->computed_total_hours, $matches);
                            if(count($matches) == 3) { $grandTotalMinutes += ($matches[1] * 60) + $matches[2]; }

                            // Credit Deduction Sum
                            $grandTotalCreditDeduction += $record->credit_deduction ?? 0;

                            // Late Sum (Parsing "15m" to 15)
                            if($record->computed_late) {
                                preg_match('/(\d+)/', $record->computed_late, $lMatches);
                                $grandTotalLateMinutes += $lMatches[0] ?? 0;
                            }

                            // Undertime Sum
                            if($record->computed_undertime) {
                                preg_match('/(\d+)/', $record->computed_undertime, $uMatches);
                                $grandTotalUTMinutes += $uMatches[0] ?? 0;
                            }

                        } elseif (!$isWeekend && !$isHoliday && !$isLeave && !$isFuture && !$data['is_before_hire']) {
                            // Absent Logic
                            $grandTotalCreditDeduction += 1.000;
                        }
                    @endphp
                    
                    <tr class="hover:bg-slate-50 transition-colors {{ $isWeekend ? 'bg-blue-50/20' : '' }}">
                        <td class="px-4 py-3 font-bold border-r border-slate-200">
                            {{ sprintf('%02d', $dayNumber) }} <span class="text-[10px] text-slate-500 ml-1">{{ $data['day_name'] }}</span>
                        </td>

                        @if($data['is_before_hire'] || $isFuture)
                            <td colspan="6" class="px-2 py-3 text-center border-r border-slate-200 text-slate-300 italic text-[10px]">
                                {{ $data['is_before_hire'] ? 'PRE-EMPLOYMENT' : 'UPCOMING' }}
                            </td>
                            <td class="bg-slate-50 border-r border-slate-200"></td>
                            <td colspan="3" class="border-r border-slate-200"></td>
                            <td></td>
                        @else
                            @if($isHoliday)
                                <td colspan="6" class="px-2 py-3 text-center border-r border-slate-200 bg-amber-50 text-amber-700 font-bold text-[10px] tracking-widest uppercase">Holiday: {{ $data['holiday_name'] }}</td>
                            @elseif($isLeave)
                                <td colspan="6" class="px-2 py-3 text-center border-r border-slate-200 bg-blue-50 text-blue-700 font-bold text-[10px] tracking-widest uppercase">Approved Leave</td>
                            @elseif($isWeekend)
                                <td colspan="6" class="px-2 py-3 text-center border-r border-slate-200 text-blue-200 font-black tracking-[0.4em] uppercase text-[10px]">Weekend</td>
                            @elseif(!$record)
                                <td colspan="6" class="px-2 py-3 text-center border-r border-slate-200 text-rose-400 font-bold text-[10px] uppercase">Absent</td>
                            @else
                                <td class="px-1 py-3 text-center">{{ $record->am_in ? date('h:i A', strtotime($record->am_in)) : '--' }}</td>
                                <td class="px-1 py-3 text-center">{{ $record->am_out ? date('h:i A', strtotime($record->am_out)) : '--' }}</td>
                                <td class="px-1 py-3 text-center border-l border-slate-100">{{ $record->pm_in ? date('h:i A', strtotime($record->pm_in)) : '--' }}</td>
                                <td class="px-1 py-3 text-center">{{ $record->pm_out ? date('h:i A', strtotime($record->pm_out)) : '--' }}</td>
                                <td class="px-1 py-3 text-center border-l border-slate-100 text-emerald-600">{{ $record->ot_in ? date('h:i A', strtotime($record->ot_in)) : '--' }}</td>
                                <td class="px-1 py-3 text-center text-emerald-600">{{ $record->ot_out ? date('h:i A', strtotime($record->ot_out)) : '--' }}</td>
                            @endif

                            <td class="px-2 py-3 text-center font-bold bg-slate-50 border-x border-slate-200">
                                @if($isLeave) 8h 0m @elseif($record) {{ $record->computed_total_hours }} @else -- @endif
                            </td>

                            {{-- Late (Minutes) --}}
                            <td class="px-1 py-3 text-center border-r border-slate-100 text-amber-700 font-medium">
                                {{ $record && $record->computed_late ? $record->computed_late : '—' }}
                            </td>

                            {{-- Undertime (Minutes) --}}
                            <td class="px-1 py-3 text-center border-r border-slate-100 text-rose-700 font-medium">
                                {{ $record && $record->computed_undertime ? $record->computed_undertime : '—' }}
                            </td>

                            {{-- Deduction (3 Decimal Places) --}}
                            <td class="px-1 py-3 text-center border-r border-slate-200 text-rose-700 leading-tight bg-rose-50/30">
                                @if($record)
                                    <div class="font-bold text-[13px]">{{ number_format($record->credit_deduction, 3) }}</div>
                                    <div class="text-[8px] text-slate-400 uppercase">Days</div>
                                @elseif(!$record && !$isWeekend && !$isHoliday && !$isLeave && !$isFuture)
                                    <span class="font-black text-rose-700">1.000</span>
                                    <div class="text-[8px] text-slate-400 uppercase">Full Day</div>
                                @else — @endif
                            </td>

                            <td class="px-2 py-3 text-center">
                                @php
                                    $status = $isHoliday ? 'Holiday' : ($isLeave ? 'Leave' : ($isWeekend ? 'Weekend' : ($record ? $record->attendance_status : 'Absent')));
                                    $color = $isHoliday ? 'amber-500' : ($isLeave ? 'blue-600' : ($isWeekend ? 'slate-400' : ($record ? ($record->status_color == 'emerald' ? 'emerald-600' : ($record->status_color == 'amber' ? 'amber-500' : 'rose-600')) : 'rose-700')));
                                @endphp
                                <span class="text-[9px] font-black uppercase px-2 py-1 rounded {{ in_array($status, ['Leave', 'Absent', 'REGULAR', 'OVERTIME', 'UNDERTIME', 'LATE/UT']) ? 'bg-'.$color.' text-white' : 'text-'.$color }}">{{ $status }}</span>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>

            <tfoot class="bg-slate-900 text-white font-bold uppercase tracking-widest text-[10px]">
                <tr class="border-b border-slate-700 bg-slate-800/50">
                    <td colspan="7" class="px-4 py-3 text-right border-r border-slate-700 text-slate-400">Monthly Totals</td>
                    <td class="px-1 py-3 text-center border-r border-slate-700 text-emerald-400">
                        {{ floor($grandTotalMinutes / 60) }}h {{ $grandTotalMinutes % 60 }}m
                    </td>
                    <td class="px-1 py-3 text-center border-r border-slate-700 text-amber-500">{{ $grandTotalLateMinutes }}m</td>
                    <td class="px-1 py-3 text-center border-r border-slate-700 text-rose-400">{{ $grandTotalUTMinutes }}m</td>
                    <td class="px-1 py-3 text-center border-r border-slate-700 bg-rose-950/30 text-rose-300 text-[12px]">
                        {{ number_format($totalMonthlyDeduction, 3) }}
                    </td>
                    <td class="px-1 py-3"></td>
                </tr>
            </tfoot>
        </table>
    </div>
@endif