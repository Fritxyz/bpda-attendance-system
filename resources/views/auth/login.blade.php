<x-guest-layout>
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

                <div class="mt-5">
                    <label class="block font-bold text-[11px] uppercase tracking-wider text-slate-600 mb-1">
                        {{ __('PASSWORD') }}
                    </label>
                    <x-text-input id="password" 
                        class="block w-full border-slate-300 focus:border-indigo-600 focus:ring-indigo-600 rounded-lg pl-3 bg-slate-50 text-sm"
                        type="password" name="password" required autocomplete="current-password"
                        placeholder="••••••••" />
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