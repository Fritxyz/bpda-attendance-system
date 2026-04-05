<x-guest-layout>
    <div class="mb-4">
        <a href="{{ route('attendance.index') }}" class="inline-flex items-center text-[11px] font-bold uppercase tracking-widest text-slate-400 hover:text-emerald-600 transition-colors group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 transform group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Attendance Kiosk
        </a>
    </div>
    
    <div class="mb-6 text-center">
        <div class="flex py-1 gap-2 justify-center items-center align-middle text-center">
            <img src="{{ asset('images/bpda-logo.jpg') }}" alt="BPDA Logo" class="mix-blend-multiply h-24 w-auto">
            <img src="{{ asset('images/barmm-logo.png') }}" alt="BARMM Logo" class="h-24 w-auto scale-95">
        </div>
        
        <h1 class="mt-4 text-xl font-extrabold text-slate-900 uppercase tracking-tighter">
            ATTENDANCE <span class="text-indigo-900">SYSTEM</span>
        </h1>
        <div class="flex items-center justify-center gap-2 mt-1">
            <span class="h-[1px] w-8 bg-emerald-600"></span>
            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">
                BPDA - BARMM
            </p>
            
            <span class="h-[1px] w-8 bg-emerald-600"></span>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow-2xl rounded-xl border border-slate-200">
        <div class="flex h-1.5">
            <div class="flex-1 bg-yellow-500"></div>     <!-- mas bright → dapat makita agad kung gumagana -->
            <div class="flex-1 bg-emerald-600"></div>
        </div>

        <div class="p-8">
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('auth.store') }}">
                @csrf

                <div>
                    <label class="block font-bold text-[11px] uppercase tracking-wider text-slate-600 mb-1">
                        {{ __('ID NUMBER') }}
                    </label>
                    <div class="relative">
                        <x-text-input id="employee_id" 
                            class="block w-full border-slate-300 focus:border-indigo-600 focus:ring-indigo-600 rounded-lg pl-3 bg-slate-50 text-sm" 
                            type="text" name="employee_id" :value="old('employee_id')" required autofocus 
                            placeholder="BPDA-########" />
                    </div>
                    
                </div>

                <div class="mt-5" x-data="{ show: false }">
                    <label class="block font-bold text-[11px] uppercase tracking-wider text-slate-600 mb-1">
                        {{ __('PASSWORD') }}
                    </label>
    
                    <div class="relative flex items-center">
                        <x-text-input id="password" 
                            class="block w-full border-slate-300 focus:border-indigo-600 focus:ring-indigo-600 rounded-lg pl-3 pr-10 bg-slate-50 text-sm"
                            ::type="show ? 'text' : 'password'" 
                            name="password" 
                            required 
                            autocomplete="current-password"
                            placeholder="••••••••" />

                        <button type="button" 
                            @click="show = !show" 
                            class="absolute right-0 pr-3 flex items-center text-slate-400 hover:text-indigo-600 transition-colors focus:outline-none">
                            
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>

                            <svg x-show="show" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
    
                    <x-input-error :messages="$errors->get('employee_id')" class="mt-2 text-xs" />
                </div>

                <div class="mt-8">
                    <x-primary-button class="w-full justify-center py-3.5 bg-indigo-900 hover:bg-slate-900 rounded-lg shadow-lg active:transform active:scale-[0.98] transition-all">
                        <span class="text-xs font-bold uppercase tracking-[2px]">
                            {{ __('Sign In') }}
                        </span>
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <div class="text-center mt-6">
        <p class="text-[11px] text-slate-400 font-semibold">
            &copy; {{ date('Y') }} Bangsamoro Planning Developement Authority - BARMM. v1.0.0
        </p>
    </div>
</x-guest-layout>