<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BPDA Attendance System</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; overflow: hidden; }
        /* Para hindi mag-overlap ang content sa sidebar */
        .main-wrapper { height: 100vh; display: flex; }
        .sidebar-container { min-width: 260px; max-width: 260px; }
        .content-container { flex-grow: 1; overflow-y: auto; background-color: #f8fafc; }
    </style>
</head>
<body>

    <div class="main-wrapper flex h-screen overflow-hidden" x-data="{ sidebarOpen: false, workforceOpen: true, timekeepingOpen: false }">
        
        <div x-show="sidebarOpen" 
            @click="sidebarOpen = false" 
            class="fixed inset-0 z-40 bg-emerald-950/60 backdrop-blur-sm lg:hidden transition-opacity">
        </div>

       <nav id="sidebar" 
         class="sidebar-container fixed inset-y-0 left-0 z-50 w-64 bg-emerald-950 text-white flex flex-col border-r-4 border-yellow-500 shadow-2xl transform transition-transform duration-300 lg:relative lg:translate-x-0"
         :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            
            <div class="p-6 border-b border-emerald-800/50 flex flex-col items-center">
                <div class="w-20 h-20 bg-white rounded-2xl p-1 shadow-lg mb-3">
                    <img src="{{ asset('images/bpda-logo.jpg') }}" alt="BPDA Logo" class="w-full h-full object-contain rounded-xl">
                </div>
                <h3 class="text-sm font-black tracking-widest uppercase text-center leading-tight">
                    BPDA <span class="text-yellow-400 font-sans">Attendance</span>
                </h3>
                <p class="text-[10px] text-emerald-300 font-medium mt-1 uppercase tracking-tighter">Bangsamoro Government</p>
            </div>

            <div class="flex-1 overflow-y-auto py-4 px-3">
                
                <div class="mb-4 px-2">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-emerald-800 transition text-sm font-bold {{ request()->routeIs('dashboard') ? 'bg-emerald-800 border-r-4 border-yellow-400' : '' }}">
                        <i class="bi bi-speedometer2 text-yellow-500"></i> Dashboard
                    </a>
                </div>

                <div class="mb-2">
                    <p class="px-4 text-[10px] font-black text-emerald-500 uppercase tracking-widest mb-2">Workforce</p>
                    
                    <button @click="workforceOpen = !workforceOpen" class="w-full flex items-center justify-between px-4 py-3 bg-emerald-900/30 rounded-xl text-sm font-bold hover:bg-emerald-800 transition">
                        <span class="flex items-center gap-3">
                            <i class="bi bi-people-fill text-yellow-500"></i> Employees
                        </span>
                        <i class="bi bi-chevron-down text-[10px] transition-transform" :class="workforceOpen ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="workforceOpen" x-collapse class="mt-2 space-y-1">
                        <a href="{{ route('employees.index') }}" class="block pl-12 py-2 text-xs text-emerald-200 hover:text-white transition {{ request()->routeIs('employees.index') ? 'text-yellow-400 font-bold' : '' }}">
                            View All Employees
                        </a>
                        <a href="{{ route('employees.create') }}" class="block pl-12 py-2 text-xs text-emerald-200 hover:text-white transition {{ request()->routeIs('employees.create') ? 'text-yellow-400 font-bold' : '' }}">
                            + Add New Employee
                        </a>
                        {{-- <a href="#" class="block pl-12 py-2 text-xs text-emerald-600 cursor-not-allowed italic">
                            Department Records
                        </a> --}}
                    </div>
                </div>

                <div class="mb-2 pt-4">
                    <p class="px-4 text-[10px] font-black text-emerald-500 uppercase tracking-widest mb-2">Reports</p>
                    
                    <button @click="timekeepingOpen = !timekeepingOpen" class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold hover:bg-emerald-800 transition">
                        <span class="flex items-center gap-3">
                            <i class="bi bi-calendar-check text-yellow-500"></i> Timekeeping
                        </span>
                        <i class="bi bi-chevron-down text-[10px] transition-transform" :class="timekeepingOpen ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="timekeepingOpen" x-collapse class="mt-2 space-y-1">
                        <a href="#" class="block pl-12 py-2 text-xs text-emerald-200 hover:text-white transition">Daily Time Record</a>
                        <a href="#" class="block pl-12 py-2 text-xs text-emerald-200 hover:text-white transition">Overtime Logs</a>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-emerald-950/80 border-t border-emerald-900 shadow-inner">
                <div class="flex items-center gap-3 px-2 py-2">
                    <div class="w-10 h-10 rounded-xl bg-yellow-500 flex items-center justify-center font-black text-emerald-950 shadow-lg">
                        A
                    </div>
                    <div class="flex-1 overflow-hidden">
                        <p class="text-xs font-bold truncate">Admin User</p>
                        <p class="text-[9px] text-emerald-400 uppercase font-black tracking-tighter italic">Super Admin</p>
                    </div>
                    <form action="#" method="POST">
                        <button type="submit" class="text-emerald-400 hover:text-red-400 transition">
                            <i class="bi bi-box-arrow-right text-lg"></i>
                        </button>
                    </form>
                </div>
            </div>
        </nav>

<div class="content-container flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50">
        
        <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
            <div class="px-4 lg:px-8 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 text-emerald-900 bg-emerald-50 rounded-lg">
                        <i class="bi bi-list text-2xl"></i>
                    </button>
                    <h1 class="text-base lg:text-lg font-black text-emerald-900 tracking-tight truncate">
                        @yield('header', 'System Overview')
                    </h1>
                </div>
                
                <div class="hidden sm:flex items-center gap-4">
                    <span class="text-[10px] font-bold bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full uppercase italic">
                        {{ now()->format('l, F d, Y') }}
                    </span>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 lg:p-8">
            @yield('content')
        </main>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>