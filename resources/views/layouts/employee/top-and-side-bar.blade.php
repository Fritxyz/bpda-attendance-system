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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body { font-family: 'Inter', sans-serif; overflow: hidden; }
        /* Para hindi mag-overlap ang content sa sidebar */
        .main-wrapper { height: 100vh; display: flex; }
        .sidebar-container { min-width: 260px; max-width: 260px; }
        .content-container { flex-grow: 1; overflow-y: auto; background-color: #f8fafc; }

        /* Custom Scrollbar para sa Sidebar */
    #sidebar .flex-1::-webkit-scrollbar {
        width: 5px; /* Gawing manipis */
    }

    #sidebar .flex-1::-webkit-scrollbar-track {
        background: transparent; 
    }

    #sidebar .flex-1::-webkit-scrollbar-thumb {
        background: rgba(251, 191, 36, 0.3); /* Kulay Yellow (Yellow-400) na medyo transparent */
        border-radius: 10px;
    }

    #sidebar .flex-1:hover::-webkit-scrollbar-thumb {
        background: rgba(251, 191, 36, 0.6); /* Mas matingkad kapag naka-hover */
    }

    /* Para sa Firefox */
    #sidebar .flex-1 {
        scrollbar-width: thin;
        scrollbar-color: rgba(251, 191, 36, 0.3) transparent;
    }

    /* Iwasan ang layout shift */
    [x-cloak] { display: none !important; }
    </style>
</head>
<body>

    <div class="main-wrapper flex h-screen overflow-hidden" x-data="{ sidebarOpen: false, workforceOpen: true, timekeepingOpen: true, leaveManagementOpen: true, holidayManagement: true }">
        
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

            <div class="flex-1 overflow-y-auto py-4 px-3 custom-scrollbar">    
                <div class="mb-4">
                    <a href="#" 
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition text-sm font-bold group {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-800 border-l-4 border-yellow-400 text-white' : 'hover:bg-emerald-800/50 text-emerald-100' }}">
                        <i class="bi bi-speedometer2 # ? 'text-yellow-400' : 'text-yellow-500 group-hover:scale-110 transition' }}"></i> 
                        Dashboard
                    </a>
                </div>

                <div class="mb-4">
                    <a href="#" 
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition text-sm font-bold group {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-800 border-l-4 border-yellow-400 text-white' : 'hover:bg-emerald-800/50 text-emerald-100' }}">
                        <i class="bi bi-speedometer2 # ? 'text-yellow-400' : 'text-yellow-500 group-hover:scale-110 transition' }}"></i> 
                        Dashboard
                    </a>
                </div>
            </div>

            <div class="p-4 bg-emerald-950 border-t border-emerald-800/50 shadow-inner">
                <div class="flex items-center gap-3 px-2 py-2 bg-emerald-900/40 rounded-2xl border border-emerald-800">
                    <div class="w-10 h-10 min-w-[40px] rounded-xl bg-yellow-500 flex items-center justify-center font-black text-emerald-950 shadow-lg">
                       SE
                    </div>
                    <div class="flex-1 overflow-hidden">
                        <p class="text-[11px] font-bold truncate">
                            Sample Employee
                        </p>
                        <p class="text-[9px] text-emerald-400 uppercase font-black tracking-tighter italic">Administrator</p>
                    </div>
                    <form action="{{ route('auth.logout') }}" method="POST" id="logout-form">
                        @csrf
                        <button type="button" onclick="confirmLogout()" class="text-emerald-400 hover:text-red-400 transition-colors p-1">
                            <i class="bi bi-power text-xl"></i>
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
                        <span class="text-[10px] font-bold bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full uppercase italic" id="live-clock">
                            {{ now()->setTimezone('Asia/Manila')->format('l, F d, Y | h:i:ss A') }}
                        </span>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        function confirmLogout() {
            Swal.fire({
                title: 'Logout?',
                text: "Do you want to logout?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',          // red-600
                cancelButtonColor: '#10b981',           // emerald-600
                confirmButtonText: 'Yes, logout',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }
    </script>
    <script src="{{ asset('js/admin/sidebar-clock.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- DAGDAG MO ITO --}}
    @stack('scripts')
</body>
</html>