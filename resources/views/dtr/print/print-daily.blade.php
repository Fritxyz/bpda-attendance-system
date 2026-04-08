<!DOCTYPE html>
<html>
<head>
    <title>Daily Attendance Report ({{ $date }})</title>
    <link rel="icon" href="{{ asset('images/bpda-logo.jpg') }}">
    <style>
        /* Landscape setup para kasya ang maraming columns */
        @page { size: a4 landscape; margin: 1cm; }
        
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; color: #333; margin: 0; }
        
        /* Header Logo Section */
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .logo-cell { width: 100px; vertical-align: middle; }
        .text-cell { text-align: center; vertical-align: middle; }
        
        .agency-name { font-size: 16px; font-weight: bold; text-transform: uppercase; margin: 0; }
        .sub-name { font-size: 11px; margin-bottom: 5px; }
        .report-title { font-size: 13px; font-weight: bold; color: #444; text-decoration: underline; }
        
        /* Table Styling */
        table.main-table { width: 100%; border-collapse: collapse; margin-top: 10px; table-layout: fixed; }
        th { background-color: #f2f2f2; border: 1px solid #000; padding: 8px; text-align: center; text-transform: uppercase; font-size: 9px; }
        td { border: 1px solid #000; padding: 6px; text-align: center; word-wrap: break-word; }
        
        .emp-name { text-align: left; font-weight: bold; padding-left: 10px; }
        .office-cell { text-align: left; padding-left: 8px; font-size: 8.5px; }
        
        /* Footer/Signatory */
        .footer { margin-top: 40px; width: 100%; }
        .sig-container { float: right; width: 250px; text-align: center; }
        .sig-line { border-top: 1.5px solid #000; margin-top: 40px; padding-top: 5px; font-weight: bold; text-transform: uppercase; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td class="logo-cell" style="width: 100px; text-align: center; vertical-align: middle;">
                <img src="data:image/png;base64,{{ $barmmLogo }}" 
                    alt="BARMM Logo" 
                    style="width: 80px; height: 80px; object-fit: contain;">
            </td>
            
            <td class="text-cell" style="text-align: center; vertical-align: middle;">
                <div class="sub-name">Republic of the Philippines</div>
                <div class="agency-name" style="font-size: 14px; font-weight: bold; text-transform: uppercase;">Bangsamoro Planning and Development Authority</div>
                <div class="sub-name">Bangsamoro Government Center, Cotabato City</div>
                <div class="report-title" style="margin-top: 10px; font-weight: 800;">DAILY ATTENDANCE REPORT</div>
                <div style="margin-top: 5px; font-weight: bold;">As of {{ $date }}</div>
                @if($bureau) 
                    <div style="font-size: 10px; margin-top: 3px;">Bureau: {{ $bureau }} {{ $division ? "($division)" : "" }}</div> 
                @endif
            </td>
            
            <td class="logo-cell" style="width: 100px; text-align: center; vertical-align: middle;">
                <img src="data:image/png;base64,{{ $bpdaLogo }}" 
                    alt="BPDA Logo" 
                    style="width: 80px; height: 80px; object-fit: contain;">
            </td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th width="3%">#</th>
                <th width="18%">Employee Name</th>
                <th width="15%">Bureau / Division</th>
                <th width="10%">AM In</th>
                <th width="10%">AM Out</th>
                <th width="10%">PM In</th>
                <th width="10%">PM Out</th>
                <th width="10%">OT In</th>
                <th width="10%">OT Out</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $index => $record)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="emp-name">{{ $record->employee->last_name }}, {{ $record->employee->first_name }}</td>
                <td class="office-cell" style="text-align: center">{{ $record->employee->bureau }} - {{ $record->employee->division }}</td>
                
                <td>{{ $record->am_in ? date('h:i A', strtotime($record->am_in)) : '---' }}</td>
                <td>{{ $record->am_out ? date('h:i A', strtotime($record->am_out)) : '---' }}</td>
                <td>{{ $record->pm_in ? date('h:i A', strtotime($record->pm_in)) : '---' }}</td>
                <td>{{ $record->pm_out ? date('h:i A', strtotime($record->pm_out)) : '---' }}</td>
                <td>{{ $record->ot_in ? date('h:i A', strtotime($record->ot_in)) : '---' }}</td>
                <td>{{ $record->ot_out ? date('h:i A', strtotime($record->ot_out)) : '---' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div class="sig-container">
            <div class="sig-line">
                {{ Auth::user()->name }}
            </div>
            <div>Verified By / HR Officer</div>
        </div>
        <div style="clear: both;"></div>
    </div>

</body>
</html>