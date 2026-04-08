<div class="overflow-x-auto custom-scrollbar">
    <table class="w-full text-left border-separate border-spacing-0">
        <thead class="bg-slate-50 sticky top-0 z-20">
            <tr>
                {{-- Sticky Column for Name & Details --}}
                <th class="sticky left-0 z-30 bg-slate-50 p-4 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-r border-slate-200 min-w-[320px]">
                    Employee Information
                </th>
                
                @for ($i = 0; $i < 5; $i++)
                    @php $currentDay = $startOfWeek->copy()->addDays($i); @endphp
                    <th class="p-4 text-center border-b border-slate-200 min-w-[180px]">
                        <span class="block text-[10px] font-black text-slate-400 uppercase tracking-tighter">{{ $currentDay->format('D') }}</span>
                        <span class="block text-sm font-bold text-slate-700">{{ $currentDay->format('M d') }}</span>
                    </th>
                @endfor

                <th class="p-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-200 min-w-[120px]">
                    Weekly Total
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach($employees as $employee)

                @php
                    $weeklyTotalMinutes = 0;
                    foreach($employee->attendances as $att) {
                        $dailyMinutes = 0;

                        // AM Session
                        if ($att->am_in && $att->am_out) {
                            $dailyMinutes += \Carbon\Carbon::parse($att->am_in)->diffInMinutes(\Carbon\Carbon::parse($att->am_out));
                        }

                        // PM Session
                        if ($att->pm_in && $att->pm_out) {
                            $dailyMinutes += \Carbon\Carbon::parse($att->pm_in)->diffInMinutes(\Carbon\Carbon::parse($att->pm_out));
                        }

                        // Straight Duty (AM In to PM Out without break logs)
                        if ($att->am_in && $att->pm_out && !$att->am_out && !$att->pm_in) {
                            $diff = \Carbon\Carbon::parse($att->am_in)->diffInMinutes(\Carbon\Carbon::parse($att->pm_out));
                            $dailyMinutes = max(0, $diff - 60); // Bawas 1hr lunch
                        }

                        // Overtime
                        if ($att->ot_in && $att->ot_out) {
                            $dailyMinutes += \Carbon\Carbon::parse($att->ot_in)->diffInMinutes(\Carbon\Carbon::parse($att->ot_out));
                        }

                        $weeklyTotalMinutes += $dailyMinutes;
                    }

                    // Convert minutes to Hours and Minutes format (e.g., 40h 30m)
                    $hours = floor($weeklyTotalMinutes / 60);
                    $remainingMinutes = $weeklyTotalMinutes % 60;
                @endphp

                <tr class="hover:bg-slate-50/50 transition-colors">
                    {{-- Updated Sticky Employee Cell with Full Details --}}
                    <td class="sticky left-0 z-10 bg-white p-4 border-r border-slate-100 shadow-[4px_0_10px_-4px_rgba(0,0,0,0.05)]">
                        <div class="flex items-start gap-3">
                            {{-- Avatar with Employment Type Indicator --}}
                            <div class="relative">
                                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-base shadow-sm border border-emerald-200">
                                    {{ substr($employee->first_name, 0, 1) }}
                                </div>
                            </div>

                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-black text-slate-900 leading-tight truncate uppercase">{{ $employee->full_name }}</p>
                                <p class="text-[10px] font-bold text-emerald-600 mt-0.5 tracking-tight">{{ $employee->employee_id }}</p>
                                
                                <div class="mt-2 space-y-0.5">
                                    <div class="flex items-center gap-1.5 text-[9px] text-slate-500 font-medium">
                                        <i class="bi bi-briefcase text-[10px]"></i>
                                        <span class="truncate">{{ $employee->position }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-[9px] text-slate-500 font-medium">
                                        <i class="bi bi-building text-[10px]"></i>
                                        <span class="truncate">{{ $employee->bureau }} — {{ $employee->division }}</span>
                                    </div>
                                    <div class="mt-1 inline-block px-1.5 py-0.5 rounded bg-slate-100 text-[8px] font-black text-slate-500 uppercase tracking-tighter">
                                        {{ $employee->employment_type }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    
                    @for ($i = 0; $i < 5; $i++)
                        @php 
                            $currentLoopDate = $startOfWeek->copy()->addDays($i)->toDateString();
                            
                            // Isang beses lang i-check ang holiday
                            $holiday = $holidays->filter(function($h) use ($currentLoopDate) {
                                return \Carbon\Carbon::parse($h->date)->format('Y-m-d') === $currentLoopDate;
                            })->first();

                            // Isang beses lang i-check ang attendance
                            $attendance = $employee->attendances->first(function($value) use ($currentLoopDate) {
                                return \Carbon\Carbon::parse($value->attendance_date)->toDateString() === $currentLoopDate;
                            });
                        @endphp
                        
                        <td class="p-3 text-center border-r border-slate-50 last:border-0 align-middle">
                            @if($attendance)
                                <div class="flex flex-col gap-1.5 max-w-[150px] mx-auto">
                                    {{-- AM SESSION --}}
                                    <div class="flex justify-between items-center bg-slate-50 rounded-md px-2 py-1 border border-slate-100">
                                        <span class="text-[8px] font-black text-slate-400 uppercase">AM</span>
                                        <div class="text-[10px] font-bold">
                                            <span class="{{ $attendance->am_in ? 'text-slate-700' : 'text-red-400' }}">{{ $attendance->am_in ? \Carbon\Carbon::parse($attendance->am_in)->format('h:i') : 'MISS' }}</span>
                                            <span class="text-slate-300 mx-0.5">-</span>
                                            <span class="{{ $attendance->am_out ? 'text-slate-700' : 'text-red-400' }}">{{ $attendance->am_out ? \Carbon\Carbon::parse($attendance->am_out)->format('h:i') : 'MISS' }}</span>
                                        </div>
                                    </div>

                                    {{-- PM SESSION --}}
                                    <div class="flex justify-between items-center bg-slate-50 rounded-md px-2 py-1 border border-slate-100">
                                        <span class="text-[8px] font-black text-slate-400 uppercase">PM</span>
                                        <div class="text-[10px] font-bold">
                                            <span class="{{ $attendance->pm_in ? 'text-slate-700' : 'text-red-400' }}">{{ $attendance->pm_in ? \Carbon\Carbon::parse($attendance->pm_in)->format('h:i') : 'MISS' }}</span>
                                            <span class="text-slate-300 mx-0.5">-</span>
                                            <span class="{{ $attendance->pm_out ? 'text-slate-700' : 'text-red-400' }}">{{ $attendance->pm_out ? \Carbon\Carbon::parse($attendance->pm_out)->format('h:i') : 'MISS' }}</span>
                                        </div>
                                    </div>

                                    {{-- OT SESSION --}}
                                    @if($attendance->ot_in || $attendance->ot_out)
                                        <div class="flex justify-between items-center bg-amber-50 rounded-md px-2 py-1 border border-amber-100">
                                            <span class="text-[8px] font-black text-amber-500 uppercase">OT</span>
                                            <div class="text-[10px] font-bold text-amber-700">
                                                <span>{{ $attendance->ot_in ? \Carbon\Carbon::parse($attendance->ot_in)->format('h:i') : '??' }}</span>
                                                <span class="text-amber-300 mx-0.5">-</span>
                                                <span>{{ $attendance->ot_out ? \Carbon\Carbon::parse($attendance->ot_out)->format('h:i') : '??' }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @elseif($holiday)
                                {{-- HOLIDAY DISPLAY --}}
                                <div class="py-2 flex flex-col items-center">
                                    <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-600 text-[9px] font-black uppercase tracking-widest shadow-sm border border-rose-200">
                                        Holiday
                                    </span>
                                    <span class="text-[8px] text-rose-400 font-bold mt-1 truncate max-w-[120px]" title="{{ $holiday->name }}">
                                        {{ $holiday->name }}
                                    </span>
                                </div>
                            @else
                                <div class="py-4 flex flex-col items-center opacity-20">
                                    <i class="bi bi-clock text-xs text-slate-400"></i>
                                    <span class="text-[8px] font-bold uppercase text-slate-400 mt-1">No Logs</span>
                                </div>
                            @endif
                        </td>
                    @endfor
                    
                    <td class="p-4 text-center align-middle">
                        <div class="inline-flex flex-col items-center bg-emerald-50 px-3 py-2 rounded-xl border border-emerald-100 shadow-sm">
                            <span class="text-sm font-black text-emerald-900 tracking-tight">
                                {{ $hours }}h {{ $remainingMinutes > 0 ? $remainingMinutes . 'm' : '' }}
                            </span>
                            <span class="text-[8px] text-emerald-600 font-bold uppercase mt-0.5 tracking-tighter">
                                Total Rendered
                            </span>
                        </div>
                        
                        {{-- Optional: Warning pag kulang sa 40 hours --}}
                        @if($hours < 40)
                            <div class="mt-1 flex items-center justify-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-orange-400 animate-pulse"></span>
                                <span class="text-[8px] font-bold text-orange-500 uppercase">Under 40h</span>
                            </div>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>