<script src="https://cdn.tailwindcss.com"></script>

@php
    // ITO ANG DUMP DATA MO
    $employees = [
        (object)[
            'name' => 'Ahmad Al-Rashid',
            'position' => 'Senior Developer',
            'division' => 'Information Technology',
            'photo' => 'https://i.pravatar.cc/300?u=ahmad',
            'status' => 'In',
            'time_logged' => '07:45 AM'
        ],
        (object)[
            'name' => 'Sitti Fatima',
            'position' => 'Administrative Officer',
            'division' => 'Human Resources',
            'photo' => 'https://i.pravatar.cc/300?u=fatima',
            'status' => 'In',
            'time_logged' => '08:02 AM'
        ],
        (object)[
            'name' => 'Mohammad Hassan',
            'position' => 'Project Manager',
            'division' => 'Planning & Dev',
            'photo' => 'https://i.pravatar.cc/300?u=mohammad',
            'status' => 'Out',
            'time_logged' => '--:--'
        ],
        (object)[
            'name' => 'Zubair Kareem',
            'position' => 'Security Analyst',
            'division' => 'Cybersecurity',
            'photo' => 'https://i.pravatar.cc/300?u=zubair',
            'status' => 'In',
            'time_logged' => '08:15 AM'
        ],
    ];
@endphp

<div class="h-screen w-full bg-gray-50 flex flex-col font-sans overflow-hidden" style="background-image: url('https://www.transparenttextures.com/patterns/islamic-exercise.png');">
    
    <div class="w-full bg-emerald-900 shadow-lg border-b-4 border-yellow-500 z-10">
        <div class="h-1.5 flex">
            <div class="flex-1 bg-green-700"></div>
            <div class="flex-1 bg-white"></div>
            <div class="flex-1 bg-red-600"></div>
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
            <div class="text-right">
                <div class="text-xl font-mono font-bold leading-none" id="clock">00:00:00 AM</div>
                <div class="text-[10px] text-emerald-200 uppercase tracking-widest">{{ now()->format('F d, Y') }}</div>
            </div>
        </div>
    </div>

    <div class="flex-1 flex overflow-hidden">
        
        <div class="w-1/3 min-w-[350px] bg-white border-r border-emerald-100 p-6 flex flex-col shadow-inner">
            <div class="mb-6">
                <h2 class="text-emerald-900 font-black text-xl uppercase border-l-4 border-yellow-500 pl-3">Transaction Panel</h2>
                <p class="text-gray-500 text-xs mt-1 italic">Enter ID and select log type below.</p>
            </div>

            <form action="#" method="POST" class="space-y-6">
                @csrf
                <div class="space-y-2">
                    <label class="text-emerald-900 font-bold uppercase text-xs">Employee ID Number</label>
                    <div class="flex shadow-sm">
                        <span class="inline-flex items-center px-3 rounded-l-xl border border-r-0 border-gray-300 bg-emerald-50 text-emerald-800 font-bold text-sm">BPDA-</span>
                        <input type="number" name="employee_id" autofocus
                            class="w-full px-4 py-4 rounded-r-xl border-2 border-emerald-100 focus:border-emerald-600 focus:outline-none text-2xl font-black text-emerald-900 placeholder-gray-200">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-emerald-900 font-bold uppercase text-xs text-center block">Select Log Type</label>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach(['AM IN', 'AM OUT', 'PM IN', 'PM OUT', 'OT IN', 'OT OUT'] as $mode)
                        <label class="cursor-pointer group relative">
                            <input type="radio" name="attendance_mode" value="{{ $mode }}" class="peer hidden" {{ $loop->first ? 'checked' : '' }}>
                            <div class="py-4 text-center rounded-xl border-2 border-gray-100 bg-gray-50 text-gray-500 font-bold text-xs peer-checked:border-emerald-600 peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:shadow-md transition-all">
                                {{ $mode }}
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="w-full bg-emerald-800 hover:bg-emerald-700 text-yellow-400 font-black py-5 rounded-2xl shadow-xl transform active:scale-95 transition-all uppercase tracking-[0.2em] flex flex-col items-center justify-center leading-none mt-4">
                    <span class="text-lg">Submit Log</span>
                    <span class="text-[9px] mt-1 text-emerald-200 opacity-70 italic tracking-normal italic">Tap here to record attendance</span>
                </button>
            </form>

            <div class="mt-auto p-4 bg-emerald-50 rounded-xl border border-emerald-100 italic text-emerald-700 text-xs text-center">
                System Ready: Please enter your ID.
            </div>
        </div>

        <div class="flex-1 p-6 overflow-y-auto bg-gray-100/50">
            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($employees as $employee)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
                    <div class="relative aspect-[4/3] bg-emerald-950 overflow-hidden">
                        <img src="{{ $employee->photo }}" class="w-full h-full object-cover grayscale opacity-80 group-hover:grayscale-0 transition duration-500">
                        <div class="absolute bottom-2 right-2">
                             <span class="{{ $employee->status == 'In' ? 'bg-green-600' : 'bg-red-600' }} text-white text-[8px] px-2 py-0.5 rounded-full font-bold uppercase shadow-sm border border-white">
                                {{ $employee->status }}
                            </span>
                        </div>
                    </div>

                    <div class="p-2 flex-1 flex flex-col justify-between">
                        <div class="text-center">
                            <h3 class="text-[11px] font-black text-emerald-900 truncate leading-tight uppercase">{{ $employee->name }}</h3>
                            <p class="text-[9px] text-gray-400 truncate">{{ $employee->position }}</p>
                        </div>
                        <div class="mt-2 pt-1 border-t border-gray-50 flex justify-between items-center px-1">
                            <span class="text-[8px] font-bold text-gray-400 uppercase tracking-tighter">Logged:</span>
                            <span class="text-[10px] font-black text-emerald-800 font-mono">{{ $employee->time_logged }}</span>
                        </div>
                    </div>
                    <div class="h-1 bg-yellow-400 w-full"></div>
                </div>
                @endforeach
            </div>
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
</script>