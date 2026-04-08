<!DOCTYPE html>
<html>
<head>
    <title>CSC Form 48 - {{ $employee->last_name }} DTR ({{ $start->format('F Y') }})</title>
    <style>
        @page { 
            size: A4; 
            margin: 7mm; 
        }
        body { 
            font-family: 'Arial', sans-serif; 
            font-size: 8pt; 
            color: #000; 
            line-height: 1.05;
            margin: 0;
            padding: 0;
        }
        
        .wrapper { 
            width: 100%; 
            display: table; 
            table-layout: fixed; 
            page-break-inside: avoid;
            page-break-after: avoid;
        }
        .dtr-side { 
            display: table-cell; 
            width: 47%; 
            padding: 0 7px; 
            vertical-align: top;
            page-break-inside: avoid;
        }

        .form-no { font-style: italic; font-size: 7.5pt; margin-bottom: 1px; }
        .title { text-align: center; font-size: 12.5pt; font-weight: bold; margin: 10px 0 0 0; }
        .dots { text-align: center; margin: -3px 0 8px 0; font-size: 9pt; }

        .name-section { 
            border-bottom: 1px solid black; 
            text-align: center; 
            margin-top: 30px; 
            min-height: 18px; 
            font-weight: bold; 
            text-transform: uppercase; 
            font-size: 10.5pt; 
        }
        .sub-label { text-align: center; font-size: 7.5pt; margin-top: 1px; }

        .info-group { 
            margin-top: 9px; 
            font-size: 8pt; 
            line-height: 1.3; 
        }
        .underline { 
            border-bottom: 1px solid black; 
            display: inline-block; 
            min-width: 95px; 
        }

        .dtr-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 8px; 
            table-layout: fixed; 
        }
        .dtr-table th, .dtr-table td { 
            border: 1px solid black; 
            text-align: center; 
            height: 19px; 
            font-size: 7.5pt; 
            padding: 0; 
            vertical-align: middle;
        }
        .dtr-table th { font-weight: bold; }
        .day-col { width: 32px; }

        .total-row td { 
            height: 24px; 
            font-weight: bold; 
        }

        .cert { 
            font-size: 7.8pt; 
            margin-top: 12px; 
            text-align: justify; 
            line-height: 1.15; 
        }

        .sig-box { 
            margin-top: 22px;           
            border-top: 1.5px solid black; 
            width: 100%; 
            margin-left: auto; 
            margin-right: auto; 
        }

        
        .verified { 
            margin-top: 22px;           
            font-size: 8pt; 
        }
        .verified-text {
            text-align: left;
            margin-bottom: 8px;
        }
        .in-charge-line { 
            border-bottom: 1.2px solid black; 
            width: 92%; 
            margin: 0 auto 4px auto; 
        }
        .in-charge-label {
            text-align: center; 
            font-size: 8pt; 
            margin-top: 5px;
        }
    </style>
</head>
<body>

<div class="wrapper">
    @for ($i = 0; $i < 2; $i++)
    <div class="dtr-side">
        <div class="form-no">Civil Service Form No. 48</div>
        <div class="title">DAILY TIME RECORD</div>
        <div class="dots">-----o0o-----</div>

        <div class="name-section">{{ $employee->first_name }} {{ $employee->last_name }}</div>
        <div class="sub-label">(Name)</div>

        <div class="info-group">
            For the month of <span class="underline" style="min-width: 160px;">{{ $start->format('F Y') }}</span><br>
            Official hours for arrival <span style="margin-left: 15px;">Regular days</span> 
            <span class="underline" style="min-width: 75px;">8:00 - 5:00</span><br>
            and departure <span style="margin-left: 32px;">Saturdays</span> 
            <span class="underline" style="min-width: 75px;"></span>
        </div>

        <table class="dtr-table">
            <thead>
                <tr>
                    <th rowspan="2" class="day-col">Day</th>
                    <th colspan="2">A.M.</th>
                    <th colspan="2">P.M.</th>
                    <th colspan="2">Undertime</th>
                </tr>
                <tr>
                    <th>Arrival</th>
                    <th>Depar-<br>ture</th>
                    <th>Arrival</th>
                    <th>Depar-<br>ture</th>
                    <th style="font-size: 7pt;">Hours</th>
                    <th style="font-size: 7pt;">Min-<br>utes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($period as $date)
                    @php 
                        $day = $date->day; 
                        $log = $attendances->get($day);
                        $isHoliday = $holidays->has($day); // Check kung holiday ang araw na ito
                    @endphp
                    <tr>
                        <td>{{ $day }}</td>
                        
                        {{-- A.M. IN: Dito natin ilalagay ang 'H' kung holiday --}}
                        <td style="{{ $isHoliday ? 'font-weight: bold; color: red;' : '' }}">
                            @if($isHoliday)
                                H
                            @else
                                {{ ($log && $log->am_in) ? \Carbon\Carbon::parse($log->am_in)->format('h:i') : '' }}
                            @endif
                        </td>

                        <td>{{ ($log && $log->am_out) ? \Carbon\Carbon::parse($log->am_out)->format('h:i') : '' }}</td>
                        <td>{{ ($log && $log->pm_in) ? \Carbon\Carbon::parse($log->pm_in)->format('h:i') : '' }}</td>
                        <td>{{ ($log && $log->pm_out) ? \Carbon\Carbon::parse($log->pm_out)->format('h:i') : '' }}</td>
                        <td></td>
                        <td></td>
                    </tr>
                @endforeach
                
                <tr class="total-row">
                    <td colspan="5" style="text-align: right; padding-right: 8px;">Total</td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <div class="cert">
            I certify on my honor that the above is a true and correct report of the hours of work performed, record of which was made daily at the time of arrival and departure from office.
        </div>

        <div class="sig-box"></div>
        <div class="sub-label" style="font-size: 8.5pt; margin-top: 2px;">Signature of Employee</div>

        <div class="verified">
            <div class="verified-text">VERIFIED as to the prescribed office hours:</div>
            <div class="sig-box"></div>
            <div class="in-charge-label">In Charge</div>
        </div>
    </div>
    @endfor
</div>

</body>
</html>