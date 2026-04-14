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
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Real-time attendance analytics as of <span class="text-emerald-600">{{ now()->timezone('Asia/Manila')->format('F d, Y') }}</span></p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm relative overflow-hidden group">
                <div class="relative z-10">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Total Workforce</p>
                    <h3 class="text-3xl font-black text-slate-900 tracking-tight">{{ $totalEmployeeCount }}</h3>
                    <div class="mt-2 flex items-center gap-1 text-[10px] font-bold text-emerald-600">
                        <i class="bi bi-graph-up-arrow"></i> <span>{{ $newEmployeesThisMonth }} New this month</span>
                    </div>
                </div>
                <i class="bi bi-people-fill absolute -right-4 -bottom-4 text-7xl text-slate-50 group-hover:text-emerald-50 transition-colors"></i>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm relative overflow-hidden group">
                <div class="relative z-10">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Present Today</p>
                    <h3 class="text-3xl font-black text-emerald-600 tracking-tight">{{ $presentToday }}</h3>

                </div>
                <i class="bi bi-person-check-fill absolute -right-4 -bottom-4 text-7xl text-slate-50 group-hover:text-emerald-50 transition-colors"></i>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm relative overflow-hidden group">
                <div class="relative z-10">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Late Arrivals</p>
                    <h3 class="text-3xl font-black text-amber-500 tracking-tight">{{ $lateToday }}</h3>
                    <div class="mt-2 flex items-center gap-1 text-[10px] font-bold text-red-500">
                        <i class="bi bi-exclamation-triangle"></i> <span>Check logs</span>
                    </div>
                </div>
                <i class="bi bi-clock-history absolute -right-4 -bottom-4 text-7xl text-slate-50 group-hover:text-amber-50 transition-colors"></i>
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
                        <span class="text-3xl font-black text-white tracking-tighter">{{ $punctualityRate }}%</span>
                        <span class="text-[8px] font-bold text-emerald-400 uppercase tracking-[0.2em]">Efficiency</span>
                    </div>
                </div>
                <div class="mt-8 grid grid-cols-2 gap-4 w-full">
                    <div class="bg-white/5 rounded-2xl p-3">
                        <p class="text-[9px] font-black text-slate-500 uppercase mb-1">On-Time</p>
                        <p class="text-lg font-black text-emerald-400">{{ $onTimeToday }}</p>
                    </div>
                    <div class="bg-white/5 rounded-2xl p-3">
                        <p class="text-[9px] font-black text-slate-500 uppercase mb-1">Late</p>
                        <p class="text-lg font-black text-amber-500">{{ $lateToday }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Line Chart - Injected with Dynamic Data
            const trendCtx = document.getElementById('attendanceChart').getContext('2d');
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: 'Attendance',
                        data: {!! json_encode($attendanceTrend) !!},
                        borderColor: '#059669',
                        backgroundColor: 'rgba(5, 150, 105, 0.05)',
                        borderWidth: 4,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: '#059669'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { display: true, beginAtZero: true, grid: { color: '#f1f5f9' } },
                        x: { grid: { display: false } }
                    }
                }
            });

            // Donut Chart - Dynamic
            const donutCtx = document.getElementById('donutChart').getContext('2d');
            new Chart(donutCtx, {
                type: 'doughnut',
                data: {
                    labels: ['On-time', 'Late'],
                    datasets: [{
                        data: [{{ $presentToday - $lateToday }}, {{ $lateToday }}],
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