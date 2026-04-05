<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BPDA Attendance System - Home</title>
    @vite('resources/css/app.css')
    <link rel="icon" href="{{ asset('images/bpda-logo.jpg') }}">
</head>
<body>
    <div class="h-screen w-full bg-gray-50 flex flex-col font-sans overflow-hidden" style="background-image: url('https://www.transparenttextures.com/patterns/islamic-exercise.png');">
        
        <div class="w-full bg-emerald-900 shadow-lg border-b-4 border-yellow-500 z-10">
            <div class="h-1.5 flex">
                <div class="flex-1 bg-green-700"></div>
            </div>
            <div class="px-6 py-3 flex items-center justify-between text-white">
                <div class="flex items-center gap-3">
                    <div class="flex gap-1">
                        <img src="{{ asset('images/barmm-logo.png') }}" class="w-10 h-10 bg-white rounded-full p-0.5">
                        <img src="{{ asset('images/bpda-logo.jpg') }}" class="w-10 h-10 bg-white rounded-full p-0.5">
                    </div>
                    <div>
                        <h1 class="text-sm md:text-lg font-black tracking-tighter uppercase italic leading-none">BPDA Attendance System</h1>
                        <p class="text-yellow-400 text-[10px] font-semibold uppercase">Bangsamoro Planning & Development Authority</p>
                    </div>
                </div>

                <div class="flex items-center gap-6">
                    <div class="text-right border-r border-emerald-700/50 pr-6 hidden md:block">
                        <div class="text-xl font-mono font-bold leading-none text-white" id="clock">00:00:00 AM</div>
                        <div class="text-[10px] text-emerald-200 uppercase tracking-widest">{{ now()->format('F d, Y') }}</div>
                    </div>

                    <a href="{{ route('auth.login') }}" 
                    class="flex items-center gap-2 bg-yellow-500 hover:bg-yellow-400 text-emerald-950 px-5 py-2.5 rounded-xl font-black text-xs uppercase transition-all shadow-lg active:scale-95 group">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 group-hover:rotate-12 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        <span>Portal Login</span>
                    </a>
                </div>
            </div>           
        </div>

        @if(session('error'))
            <div class="mx-6 mt-4 bg-red-700 border-l-8 border-yellow-400 text-white rounded-xl shadow-lg p-4 flex items-center gap-3 animate-pulse alert-message" id="error-message">
                
                <svg class="w-6 h-6 text-yellow-300 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2L1 21h22L12 2zm0 6l1 7h-2l1-7zm0 9h2v2h-2v-2z"/>
                </svg>

                <div>
                    <p class="font-black uppercase text-sm tracking-wide">Transaction Failed</p>
                    <p class="text-xs text-red-100">{{ session('error') }}</p>
                </div>

            </div>
        @endif

        @if(session('success'))
            <div class="mx-6 mt-4 bg-emerald-800 border-l-8 border-yellow-400 text-white rounded-xl shadow-lg p-4 flex items-center gap-3 animate-fade-in alert-message">
                
                <svg class="w-6 h-6 text-yellow-300 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M9 16.2l-3.5-3.5L4 14.2 9 19l12-12-1.5-1.5z"/>
                </svg>

                <div>
                    <p class="font-black uppercase text-sm tracking-wide">Attendance Recorded</p>
                    <p class="text-xs text-emerald-100">{{ session('success') }}</p>
                </div>

            </div>
        @endif

        <div class="flex-1 flex overflow-hidden">
            
            <div class="w-1/3 min-w-[350px] bg-white border-r border-emerald-100 p-6 flex flex-col shadow-inner">
                <div class="mb-6">
                    <h2 class="text-emerald-900 font-black text-xl uppercase border-l-4 border-yellow-500 pl-3">Transaction Panel</h2>
                    <p class="text-gray-500 text-xs mt-1 italic">Enter ID and select log type below.</p>
                </div>

                <form action="{{ route('attendance.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-emerald-900 font-bold uppercase text-xs">Employee ID Number</label>
                        <div class="flex shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-xl border border-r-0 border-gray-300 bg-emerald-50 text-emerald-800 font-bold text-sm">BPDA-</span>
                            <input type="text" name="employee_id" autofocus maxlength="8" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 8);" onkeydown="if(['e', 'E', '.', '-'].includes(event.key)) event.preventDefault();"
                                class="w-full px-4 py-4 rounded-r-xl border-2 border-emerald-100 focus:border-emerald-600 focus:outline-none text-2xl font-black text-emerald-900 placeholder-gray-200">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-emerald-900 font-bold uppercase text-xs text-center block">Select Log Type</label>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach(['AM IN', 'AM OUT', 'PM IN', 'PM OUT', 'OT IN', 'OT OUT'] as $mode)
                            <label class="cursor-pointer group relative">
                                <input type="radio" 
                                    name="attendance_mode" 
                                    value="{{ $mode }}" 
                                    class="peer hidden" 
                                    {{ (old('attendance_mode', 'AM IN') == $mode) ? 'checked' : '' }}>
                                
                                <div class="py-4 text-center rounded-xl border-2 border-gray-100 bg-gray-50 text-gray-500 font-bold text-xs peer-checked:border-emerald-600 peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:shadow-md transition-all">
                                    {{ $mode }}
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" id="submitBtn" class="w-full bg-emerald-800 hover:bg-emerald-700 text-yellow-400 font-black py-5 rounded-2xl shadow-xl transform active:scale-95 transition-all uppercase tracking-[0.2em] flex flex-col items-center justify-center leading-none mt-4">
                        <span class="text-lg">Submit Log</span>
                        <span class="text-[9px] mt-1 text-emerald-200 opacity-70 tracking-normal italic">Tap here to record attendance</span>
                    </button>
                </form>

                <div class="mt-auto p-4 bg-emerald-50 rounded-xl border border-emerald-100 italic text-emerald-700 text-xs text-center">
                    System Ready: Please enter your ID.
                </div>
            </div>

            <div class="flex-1 p-6 overflow-y-auto bg-gray-100/50 space-y-4 snap-y snap-mandatory">
                @foreach($employees as $employee)
                    @php
                        $todayLog = $employee->attendances->first();
                        $isOut = $todayLog && $todayLog->pm_out ? true : false;
                        $statusText = $todayLog ? ($isOut ? 'Out' : 'In') : 'No Log';
                        
                        $lastTime = '--:--';
                        if($todayLog) {
                            $lastTime = $todayLog->ot_out ?? $todayLog->ot_in ?? 
                                        $todayLog->pm_out ?? $todayLog->pm_in ?? 
                                        $todayLog->am_out ?? $todayLog->am_in ?? '--:--';
                        }
                    @endphp

                    <div class="bg-white rounded-2xl shadow-md border-l-8 border-emerald-800 overflow-hidden flex h-[30%] min-h-[180px] snap-start hover:shadow-xl transition-all duration-300">          
                        <div class="w-1/3 bg-emerald-950 relative">
                            <img src="{{ $employee->profile_picture ? asset('storage/' . $employee->profile_picture) : asset('images/bpda-logo.jpg') }}" 
                                class="w-full h-full object-cover grayscale opacity-90 group-hover:grayscale-0">
                            
                            <div class="absolute inset-0 bg-gradient-to-t from-emerald-900/80 to-transparent"></div>
                            <div class="absolute bottom-3 left-3">
                                <span class="{{ $statusText == 'In' ? 'bg-green-500' : ($statusText == 'Out' ? 'bg-red-500' : 'bg-gray-500') }} text-white text-[10px] px-3 py-1 rounded-full font-black uppercase border border-white/50 shadow-lg">
                                    {{ $statusText }}
                                </span>
                            </div>
                        </div>

                        <div class="flex-1 p-6 flex flex-col justify-between relative bg-white">
                            <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-50 rounded-bl-full -z-0 opacity-50"></div>

                            <div class="relative z-10">
                                <h3 class="text-2xl font-black text-emerald-900 uppercase tracking-tight leading-tight">
                                    {{ $employee->first_name }} {{ $employee->last_name }}
                                </h3>
                                <p class="text-emerald-700 font-bold text-sm tracking-wide">{{ $employee->position }}</p>
                                <p class="text-gray-400 text-xs italic mt-1">{{ $employee->division }}</p>
                            </div>

                            <div class="relative z-10 flex justify-between items-end border-t border-gray-100 pt-4">
                                <div>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Recent Activity Today</p>
                                    <p class="text-lg font-black text-emerald-900 font-mono">
                                        {{ $lastTime != '--:--' ? \Carbon\Carbon::parse($lastTime)->format('h:i A') : '--:--' }}
                                    </p>
                                </div>  
                            </div>
                            <div class="absolute bottom-0 left-0 h-1 bg-yellow-400 w-full"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        function updateClock() {
            const now = new Date();
            const options = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
            document.getElementById('clock').textContent = now.toLocaleTimeString('en-US', options);
        }
        setInterval(updateClock, 1000); 
        updateClock();

        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert-message');
            alerts.forEach(alert => {
                alert.style.transition = "opacity 0.5s";
                alert.style.opacity = "0";
                setTimeout(() => alert.remove(), 500);
            });
        }, 3000);   
    </script>
</body>
</html>