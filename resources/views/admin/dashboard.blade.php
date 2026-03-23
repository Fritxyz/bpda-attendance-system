@extends('layouts.admin.top-and-side-bar')

@section('header', 'System Overview')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">

    <nav class="flex mb-8 text-sm" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li class="flex items-center gap-2">
                <i class="bi bi-speedometer2 text-xs"></i>
                <span class="text-gray-500 hover:text-emerald-600 transition flex items-center gap-1">Dashboard</span>
            </li>
        </ol>
    </nav>

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Dashboard</h2>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Real-time attendance analytics as of <span class="text-emerald-600">{{ now()->format('F d, Y') }}</span></p>
        </div>
        <div class="flex gap-2">
            <button class="bg-white border border-slate-200 p-2.5 rounded-xl text-slate-600 hover:bg-slate-50 transition shadow-sm">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
            <button class="bg-emerald-600 text-white px-6 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-emerald-100 hover:bg-slate-900 transition-all active:scale-95">
                <i class="bi bi-file-earmark-pdf me-2"></i> Export Report
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Total Workforce</p>
                <h3 class="text-3xl font-black text-slate-900 tracking-tight">145</h3>
                <div class="mt-2 flex items-center gap-1 text-[10px] font-bold text-emerald-600">
                    <i class="bi bi-graph-up-arrow"></i> <span>+2 New this month</span>
                </div>
            </div>
            <i class="bi bi-people-fill absolute -right-4 -bottom-4 text-7xl text-slate-50 group-hover:text-emerald-50 transition-colors"></i>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Present Today</p>
                <h3 class="text-3xl font-black text-emerald-600 tracking-tight">122</h3>
                <div class="mt-2 flex items-center gap-1 text-[10px] font-bold text-slate-400">
                    <span>84% Attendance Rate</span>
                </div>
            </div>
            <i class="bi bi-person-check-fill absolute -right-4 -bottom-4 text-7xl text-slate-50 group-hover:text-emerald-50 transition-colors"></i>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Late Arrivals</p>
                <h3 class="text-3xl font-black text-amber-500 tracking-tight">08</h3>
                <div class="mt-2 flex items-center gap-1 text-[10px] font-bold text-red-500">
                    <i class="bi bi-exclamation-triangle"></i> <span>Check logs</span>
                </div>
            </div>
            <i class="bi bi-clock-history absolute -right-4 -bottom-4 text-7xl text-slate-50 group-hover:text-amber-50 transition-colors"></i>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">On Leave</p>
                <h3 class="text-3xl font-black text-blue-600 tracking-tight">05</h3>
                <div class="mt-2 flex items-center gap-1 text-[10px] font-bold text-slate-400">
                    <span>Approved Requests</span>
                </div>
            </div>
            <i class="bi bi-calendar2-x-fill absolute -right-4 -bottom-4 text-7xl text-slate-50 group-hover:text-blue-50 transition-colors"></i>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <div class="lg:col-span-2 bg-white rounded-[2.5rem] p-8 border border-slate-200 shadow-xl shadow-slate-100/50">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h4 class="text-sm font-black text-slate-900 uppercase tracking-wider">Attendance Trend</h4>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Employee presence last 7 days</p>
                </div>
                <select class="bg-slate-50 border-none text-[10px] font-black uppercase tracking-widest rounded-xl px-4 py-2 outline-none cursor-pointer">
                    <option>Weekly View</option>
                    <option>Monthly View</option>
                </select>
            </div>
            <div class="h-[300px] w-full">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>

        <div class="bg-slate-900 rounded-[2.5rem] p-8 border border-slate-800 shadow-xl flex flex-col items-center justify-center text-center">
            <h4 class="text-sm font-black text-white uppercase tracking-widest mb-6">Punctuality Ratio</h4>
            <div class="relative w-full aspect-square max-w-[200px]">
                <canvas id="donutChart"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <span class="text-3xl font-black text-white tracking-tighter">92%</span>
                    <span class="text-[8px] font-bold text-emerald-400 uppercase tracking-[0.2em]">Efficiency</span>
                </div>
            </div>
            <div class="mt-8 grid grid-cols-2 gap-4 w-full">
                <div class="bg-white/5 rounded-2xl p-3">
                    <p class="text-[9px] font-black text-slate-500 uppercase mb-1">On-Time</p>
                    <p class="text-lg font-black text-emerald-400">114</p>
                </div>
                <div class="bg-white/5 rounded-2xl p-3">
                    <p class="text-[9px] font-black text-slate-500 uppercase mb-1">Late</p>
                    <p class="text-lg font-black text-amber-500">08</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <div>
                <h4 class="text-sm font-black text-slate-900 uppercase tracking-wider">Recent Activity</h4>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Latest time-in/out logs</p>
            </div>
            <a href="#" class="text-[10px] font-black text-emerald-600 uppercase tracking-widest hover:text-slate-900 transition">View Full Logs</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-white">
                        <th class="px-8 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Employee</th>
                        <th class="px-8 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Log Details</th>
                        <th class="px-8 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                        <th class="px-8 py-4 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php $logs = [['name' => 'Juan Dela Cruz', 'id' => 'BPDA-001', 'time' => '07:45 AM', 'type' => 'AM IN', 'status' => 'On-Time'], ['name' => 'Maria Santos', 'id' => 'BPDA-024', 'time' => '08:12 AM', 'type' => 'AM IN', 'status' => 'Late']]; @endphp
                    @foreach($logs as $log)
                    <tr class="group hover:bg-slate-50 transition-colors">
                        <td class="px-8 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-black text-xs uppercase shadow-md group-hover:scale-110 transition-transform">
                                    {{ substr($log['name'], 0, 1) }}{{ substr(explode(' ', $log['name'])[1], 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-black text-slate-900 uppercase tracking-tight">{{ $log['name'] }}</p>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $log['id'] }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-4">
                            <div class="flex flex-col">
                                <span class="text-xs font-black text-slate-700">{{ $log['time'] }}</span>
                                <span class="text-[9px] font-bold text-slate-400 uppercase">{{ $log['type'] }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-4">
                            @if($log['status'] == 'On-Time')
                            <span class="bg-emerald-50 text-emerald-600 px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border border-emerald-100">On-Time</span>
                            @else
                            <span class="bg-amber-50 text-amber-600 px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border border-amber-100">Late Arrival</span>
                            @endif
                        </td>
                        <td class="px-8 py-4 text-right">
                            <button class="h-8 w-8 rounded-lg text-slate-400 hover:bg-emerald-600 hover:text-white transition-all">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Line Chart
        const trendCtx = document.getElementById('attendanceChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'],
                datasets: [{
                    label: 'Attendance',
                    data: [120, 132, 125, 140, 122, 40, 20],
                    borderColor: '#059669', // Emerald 600
                    backgroundColor: 'rgba(5, 150, 105, 0.05)',
                    borderWidth: 4,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#059669',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { 
                        display: false,
                        beginAtZero: true 
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 9, weight: '900' },
                            color: '#94a3b8'
                        }
                    }
                }
            }
        });

        // Donut Chart
        const donutCtx = document.getElementById('donutChart').getContext('2d');
        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: ['On-time', 'Late'],
                datasets: [{
                    data: [92, 8],
                    backgroundColor: ['#10b981', '#f59e0b'],
                    borderWidth: 0,
                    borderRadius: 10
                }]
            },
            options: {
                cutout: '85%',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });
    });
</script>
@endsection