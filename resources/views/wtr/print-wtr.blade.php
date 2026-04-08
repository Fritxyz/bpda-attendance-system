<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Weekly Attendance Report</title>
    <style>
        /* PDF Optimization */
        @page { size: a4 landscape; margin: 0.8cm; }
        
        body { 
            font-family: 'Helvetica', sans-serif; 
            font-size: 9px; 
            color: #1e293b; /* Slate 800 */
            margin: 0; 
            background-color: #fff;
        }

        /* --- Header Section --- */
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; border: none; }
        .logo-cell { width: 85px; vertical-align: middle; }
        .text-cell { text-align: center; vertical-align: middle; }
        
        .gov-sub-name { font-size: 10px; color: #475569; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px; }
        .agency-name { font-size: 15px; font-weight: bold; color: #0f172a; text-transform: uppercase; line-height: 1.2; }
        .agency-address { font-size: 9px; color: #64748b; margin-top: 2px; }
        .report-title { 
            display: inline-block;
            font-size: 13px; 
            font-weight: 800; 
            color: #059669; /* Emerald 600 */
            margin-top: 10px; 
            padding-bottom: 2px;
            border-bottom: 2px solid #059669;
            text-transform: uppercase;
        }
        .period-box { margin-top: 8px; font-size: 10px; font-weight: bold; color: #334155; }

        /* --- Table Styling --- */
        table.main-table { 
            width: 100%; 
            border-collapse: collapse; 
            table-layout: fixed; 
            border: 1.5px solid #334155; 
        }

        /* Column Headers */
        th { 
            background-color: #f8fafc; 
            border: 1px solid #334155; 
            padding: 10px 5px; 
            text-align: center; 
            text-transform: uppercase; 
            font-size: 8px; 
            font-weight: bold;
            color: #0f172a;
        }
        .day-label { display: block; font-size: 7px; color: #64748b; font-weight: normal; margin-bottom: 2px; }

        /* Employee Info Column */
        .emp-cell { 
            padding: 10px; 
            border: 1px solid #334155; 
            text-align: left; 
            vertical-align: middle; 
            background-color: #fff;
        }
        .emp-name { font-size: 10px; font-weight: bold; color: #000; text-transform: uppercase; margin-bottom: 3px; }
        .emp-id { font-size: 8px; color: #059669; font-weight: bold; margin-bottom: 2px; }
        .emp-sub { font-size: 7.5px; color: #64748b; line-height: 1.2; }

        /* Attendance Data Cells */
        .att-cell { 
            border: 1px solid #334155; 
            padding: 6px 3px; 
            text-align: center; 
            vertical-align: middle; 
        }

        /* Time Display Boxes */
        .session-box { 
            margin-bottom: 4px;
            padding: 3px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .session-box:last-of-type { border-bottom: none; margin-bottom: 0; }
        
        .session-label { font-size: 6.5px; font-weight: bold; color: #94a3b8; margin-right: 4px; }
        .time-text { font-weight: bold; color: #1e293b; font-size: 8.5px; }
        .miss-text { color: #e11d48; font-weight: 800; } /* Rose 600 */

        /* OT & Holiday */
        .ot-text { font-size: 7.5px; font-weight: bold; color: #b45309; margin-top: 3px; }
        .holiday-box { 
            background-color: #fff1f2; 
            border: 1px solid #ffe4e6; 
            padding: 6px 2px; 
            border-radius: 3px;
        }
        .holiday-tag { font-weight: 800; font-size: 7px; color: #be123c; text-transform: uppercase; display: block; }
        .holiday-name { font-size: 7px; font-weight: bold; color: #9f1239; }

        /* Total Column */
        .total-cell { 
            background-color: #f0fdfa; /* Emerald 50 */
            border: 1px solid #334155; 
            text-align: center;
        }
        .total-text { font-size: 11px; font-weight: 800; color: #065f46; }
        .total-label { font-size: 6px; color: #059669; font-weight: bold; text-transform: uppercase; margin-top: 2px; }

        .no-logs { color: #cbd5e1; font-size: 7px; font-weight: bold; letter-spacing: 0.5px; }

        /* --- Footer Signatory --- */
        .footer-note { margin-top: 15px; font-size: 7px; color: #94a3b8; font-style: italic; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td class="logo-cell">
                <img src="data:image/png;base64,{{ $barmmLogo }}" style="width: 75px; height: 75px; object-fit: contain;">
            </td>
            
            <td class="text-cell">
                <div class="gov-sub-name">Republic of the Philippines</div>
                <div class="agency-name">Bangsamoro Planning and Development Authority</div>
                <div class="agency-address">Bangsamoro Government Center, Cotabato City</div>
                <div class="report-title">Weekly Attendance Report</div>
                <div class="period-box">
                    Period: {{ $startOfWeek->format('F d') }} - {{ $startOfWeek->copy()->addDays(4)->format('d, Y') }}
                </div>
            </td>
            
            <td class="logo-cell" style="text-align: right;">
                <img src="data:image/png;base64,{{ $bpdaLogo }}" style="width: 75px; height: 75px; object-fit: contain;">
            </td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 18%;">Employee Information</th>
                @for ($i = 0; $i < 5; $i++)
                    @php $day = $startOfWeek->copy()->addDays($i); @endphp
                    <th style="width: 14.4%;">
                        <span class="day-label">{{ $day->format('l') }}</span>
                        {{ $day->format('M d, Y') }}
                    </th>
                @endfor
                <th style="width: 10%;">Weekly Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $employee)
                @php $weeklyMinutes = 0; @endphp
                <tr>
                    <td class="emp-cell">
                        <div class="emp-name">{{ $employee->full_name }}</div>
                        <div class="emp-id">ID: {{ $employee->employee_id }}</div>
                        <div class="emp-sub"><strong>{{ $employee->position }}</strong></div>
                        <div class="emp-sub">{{ $employee->division }}</div>
                    </td>

                    @for ($i = 0; $i < 5; $i++)
                        @php 
                            $dateObj = $startOfWeek->copy()->addDays($i);
                            $dateStr = $dateObj->toDateString();
                            $holiday = $holidays->filter(function($h) use ($dateStr) {
                                return \Carbon\Carbon::parse($h->date)->format('Y-m-d') === $dateStr;
                            })->first();    
                            $attendance = $employee->attendances->first(fn($a) => \Carbon\Carbon::parse($a->attendance_date)->toDateString() === $dateStr);
                            
                            // Tawagin ang updated logic
                            $dailyMins = app(\App\Http\Controllers\Admin\WTRController::class)->calculateDailyMinutes($attendance, $dateStr, $holidays);
                            $weeklyMinutes += $dailyMins;
                        @endphp

                        <td class="att-cell">
                            @if($holiday)
                                <div class="holiday-box">
                                    <span class="holiday-tag">Holiday</span>
                                    <span class="holiday-name">{{ $holiday->name }}</span>
                            @elseif($attendance)
                                <div class="session-box">
                                    <span class="session-label">AM</span>
                                    <span class="time-text {{ !$attendance->am_in ? 'miss-text' : '' }}">{{ $attendance->am_in ? date('h:i', strtotime($attendance->am_in)) : 'MISSING' }}</span>
                                    <span style="color:#cbd5e1">|</span>
                                    <span class="time-text {{ !$attendance->am_out ? 'miss-text' : '' }}">{{ $attendance->am_out ? date('h:i', strtotime($attendance->am_out)) : 'MISSING' }}</span>
                                </div>

                                <div class="session-box">
                                    <span class="session-label">PM</span>
                                    <span class="time-text {{ !$attendance->pm_in ? 'miss-text' : '' }}">{{ $attendance->pm_in ? date('h:i', strtotime($attendance->pm_in)) : 'MISSING' }}</span>
                                    <span style="color:#cbd5e1">|</span>
                                    <span class="time-text {{ !$attendance->pm_out ? 'miss-text' : '' }}">{{ $attendance->pm_out ? date('h:i', strtotime($attendance->pm_out)) : 'MISSING' }}</span>
                                </div>

                                @if($attendance->ot_in || $attendance->ot_out)
                                    <div class="ot-text">
                                        OT: {{ $attendance->ot_in ? date('h:i', strtotime($attendance->ot_in)) : '--' }} - {{ $attendance->ot_out ? date('h:i', strtotime($attendance->ot_out)) : '--' }}
                                    </div>
                                @endif
                            @else
                                <div class="no-logs">--- NO LOGS ---</div>
                            @endif
                        </td>
                    @endfor

                    <td class="total-cell">
                        @php 
                            $h = floor($weeklyMinutes / 60);
                            $m = $weeklyMinutes % 60;
                        @endphp
                        <div class="total-text">{{ $h }}h {{ $m > 0 ? $m.'m' : '' }}</div>
                        <div class="total-label">Total Rendered</div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer-note">
        * Generated by BPDA Timekeeping System on {{ now()->format('F d, Y h:i A') }}
    </div>

</body>
</html>