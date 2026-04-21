@extends('layouts.admin.top-and-side-bar')

@section('header', 'Employee Management')

@section('content')
<div class="max-w-4.5xl mx-auto px-4 py-3">
    
    <nav class="flex mb-8 text-sm" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li class="flex items-center gap-2">
                <i class="bi bi-person-vcard text-xs"></i>
                <span class="text-gray-500 hover:text-emerald-600 transition flex items-center gap-1">Employees</span>
                <i class="bi bi-chevron-right text-[10px] text-gray-400"></i>
            </li>
            <li>
                <a href="{{ route('employees.index') }}" class="font-bold text-emerald-900 uppercase tracking-wider text-[11px]">
                    View All Employees
                </a>
                <i class="bi bi-chevron-right text-[10px] text-gray-400"></i>
            </li>
            <li>
                <button  class="font-bold text-emerald-900 uppercase tracking-wider text-[11px]">
                    Edit Employee
                </button>
            </li>
            <li class="flex items-center gap-2">
                <i class="bi bi-chevron-right text-[10px] text-gray-400"></i>
                <span class="font-bold text-emerald-900 uppercase tracking-wider text-[11px]">{{ $employee->first_name }} {{ $employee->last_name }} {{ $employee->suffix }}</span>
            </li>
        </ol>
    </nav>

    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
        <div class="px-8 py-5 bg-gray-50 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-800">Edit Employee</h2>
            <p class="text-xs text-gray-500 mt-1 uppercase tracking-wider">Updating record for: {{ $employee->first_name }} {{ $employee->last_name }}</p>
        </div>

        <form id="editEmployeeForm" method="POST" action="{{ route('employees.update', $employee->employee_id) }}" class="p-8" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Error Summary --}}
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-lg">
                    <p class="text-sm font-bold">Please correct the following errors:</p>
                    <ul class="mt-1 list-disc list-inside text-xs">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="space-y-6">
                <h3 class="text-xs font-black text-blue-600 uppercase tracking-widest border-b pb-2">Identification</h3>
                
                <div class="grid grid-cols-1 ">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Employee ID</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-100 text-gray-500 font-bold text-sm">
                                BPDA-
                            </span>
                            <input type="text" name="employee_id" value="{{ old('employee_id', $employee->employee_id) }}" id="main-employee-id"
                                placeholder="e.g. 123456789012345" required readonly
                                title="Employee ID must be exactly 15 digits."
                                oninput="syncEmployeeId(this.value)"
                                class="flex-1 px-2 py-2 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                        </div>
                    </div>
                </div>

                {{-- Full Name Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">First Name</label>
                        <input type="text" name="first_name" value="{{ old('first_name', $employee->first_name ) }}" required
                           oninput="this.value = this.value.replace(/[^a-zA-Z\s\-]/g, '')" maxlength="100"
                            class="w-full px-2 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Middle Name</label>
                        <input type="text" name="middle_name" value="{{ old('middle_name', $employee->middle_name ) }}" required
                            oninput="this.value = this.value.replace(/[^a-zA-Z\s\-]/g, '')" maxlength="100"
                            class="w-full px-2 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name', $employee->last_name ) }}" required
                            oninput="this.value = this.value.replace(/[^a-zA-Z\s\-]/g, '')" maxlength="100"
                            class="w-full px-2 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Suffix</label>
                        <input type="text" name="suffix" value="{{ old('suffix', $employee->suffix) }}"
                            placeholder="Jr, Sr, III"
                            oninput="this.value = this.value.replace(/[^a-zA-Z\s\.]/g, '')" maxlength="10"
                            class="w-full px-2 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                    </div>
                </div>
            </div>

            <div class="mt-10 space-y-6">
                <h3 class="text-xs font-black text-blue-600 uppercase tracking-widest border-b pb-2">Work Assignment</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Bureau</label>
                        <select name="bureau" required id="bureau-select"
                                class="w-full px-2 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 outline-none transition  cursor-pointer">
                            <option value="" disabled {{ old('bureau') ? '' : 'selected' }}>Select Bureau</option>
                            <option value="PPB" {{ old('bureau', $employee->bureau ) == 'PPB' ? 'selected' : '' }}>Planning and Policies Bureau (PPB)</option>
                            <option value="RDSPB" {{ old('bureau', $employee->bureau ) == 'RDSPB' ? 'selected' : '' }}>Research Development and Special Projects Bureau (RDSPB)</option>
                            <option value="FASS" {{ old('bureau', $employee->bureau ) == 'FASS' ? 'selected' : '' }}>Finance and Administrative Support Services (FASS)</option>
                            <option value="Other" {{ old('bureau', $employee->bureau ) == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Division</label>
                        <select id="division-select" name="division" required
                                class="w-full px-2 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 outline-none transition cursor-pointer disabled:bg-gray-100 disabled:text-gray-400">
                            <option value="" selected disabled>Select Bureau first</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Current Position</label>
                        <input type="text" name="position" id="position-input" 
                            placeholder="Select division first"
                            value="{{ old('position', $employee->position) }}"
                            class="w-full px-2 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 outline-none transition disabled:bg-gray-100 disabled:cursor-not-allowed disabled:text-gray-400"
                            required>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Employment Type</label>
                        <select name="employment_type" required id="employment-type-select"
                                class="w-full px-2 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 outline-none transition">
                            <option value="Permanent" {{ $employee->employment_type === 'Permanent' ? 'selected' : '' }}>Permanent</option>
                            <option value="Contractual" {{ $employee->employment_type === 'Contractual' ? 'selected' : '' }}>Contractual</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Monthly Salary</label>
                        <div class="relative flex items-center">
                            <span class="absolute left-4 z-10 text-gray-500 font-bold pointer-events-none">
                                ₱
                            </span>
                            
                            <input type="number" step="0.01" name="salary" value="{{ old('salary', $employee->salary ) }}" id="salary-id"
                                placeholder="0.00"
                                class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                        </div>
                    </div>

                    {{-- Ilagay ito after ng Monthly Salary div --}}
                    <div id="leave-credits-field">
                        <label class="block text-sm font-bold text-gray-700 mb-1">
                            Leave Credits
                            <span class="text-[10px] text-gray-400 font-normal normal-case">(upon hire)</span>
                        </label>
                        <input 
                            type="number" 
                            name="leave_credits" 
                            id="leave-credits-id"
                            value="{{ old('leave_credits', $employee->leave_credits ?? 0) }}"
                            step="0.001" 
                            min="0"
                            placeholder="0.000"
                            class="w-full pl-4 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">System Role</label>
                        <select name="role" required
                                class="w-full px-2 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 outline-none transition">
                            <option value="Employee" {{ $user_role === "Employee" ? 'selected' : '' }}>Employee (Standard)</option>
                            <option value="Admin" {{ $user_role === "Admin" ? 'selected' : '' }}>Admin (Full Access)</option>
                        </select>
                    </div>            
                </div>
            </div>

            <div class="mt-10 space-y-6">
                <h3 class="text-xs font-black text-blue-600 uppercase tracking-widest border-b pb-2">Account Creation</h3>

                <div class="grid  gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Password</label>
                        <div class="relative flex items-center">
                            <input type="text" name="password" id="password-input" 
                                class="w-full pl-5 pr-12 py-2.5 border border-gray-300 rounded-lg bg-gray-50 font-mono text-sm focus:ring-2 focus:ring-blue-500 outline-none cursor-not-allowed transition" readonly>
                            
                            {{-- Regenerate Button (Icon Only) --}}
                            <button type="button" onclick="regeneratePassword()" 
                                class="absolute right-2 p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-full transition"
                                title="Generate New Password">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Profile Picture Upload --}}
                <div class="flex flex-col items-center md:flex-row md:space-x-8 space-y-4 md:space-y-0 bg-blue-50/50 p-6 rounded-xl border border-blue-100">
                    <div class="relative">
                        <div class="w-32 h-32 rounded-full border-4 border-white shadow-lg overflow-hidden bg-gray-200">
                            <img id="profile-preview" src="{{ $employee->profile_picture ? asset('storage/' . $employee->profile_picture) : asset('images/bpda-logo.jpg') }}" 
                                class="w-full h-full object-cover" alt="Profile Preview">
                        </div>
                        <label for="profile_picture" class="absolute bottom-0 right-0 bg-blue-600 p-2 rounded-full text-white cursor-pointer hover:bg-blue-700 shadow-md transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </label>
                        <input type="file" name="profile_picture" id="profile_picture" class="hidden" accept=".jpeg,.jpg,.png" onchange="previewImage(this)">
                    </div>
                    <div class="flex-1">
                        <h4 class="text-sm font-bold text-gray-700">Employee Photo</h4>
                        <p class="text-xs text-gray-500 mt-1">Upload a professional headshot. JPEG, PNG, or WebP (Max: 2MB).</p>
                        <button type="button" onclick="document.getElementById('profile_picture').click()" 
                                class="mt-3 text-xs font-bold text-blue-600 hover:text-blue-800 transition">
                            Browse Files
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-10 pt-6 border-t border-gray-100 flex items-center justify-between">
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ $employee->is_active == true ? 'checked' : ''}}
                           class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer transition">
                    <label for="is_active" class="ml-2 block text-sm font-bold text-gray-600 cursor-pointer">Active Employee</label>
                </div>

                <div class="flex space-x-3">
                    <a href="{{ route('employees.index') }}" 
                       class="px-5 py-2 text-sm font-bold text-gray-400 hover:text-gray-600 transition">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg shadow-lg shadow-blue-200 transition active:scale-95">
                        Register Employee
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function previewImage(input) {
        const preview = document.getElementById('profile-preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();  
            reader.onload = function(e) {
                preview.src = e.target.result;
            }        
            reader.readAsDataURL(input.files[0]);
        }
    }

    function regeneratePassword() {
        const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%";
        let password = "";
        for (let i = 0; i < 10; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        
        const input = document.getElementById('password-input');
        if(input) {
            input.value = password;
            // Visual feedback na nag-change ang value
            input.classList.add('ring-2', 'ring-blue-400');
            setTimeout(() => input.classList.remove('ring-2', 'ring-blue-400'), 500);
        }
    }

    const divisionsByBureau = {
        'PPB': [
            { value: 'MEPD',  label: 'Macro-Economic Planning Division (MEPD)' },
            { value: 'EPD',   label: 'Economic Planning Division (EPD)' },
            { value: 'SPD',   label: 'Social Planning Division (SPD)' },
            { value: 'LPCD',  label: 'Local Planning and Coordinating Division (LPCD)' },
            { value: 'IPD',   label: 'Infrastructure Planning and Coordinating Division (IPD)' },
            { value: 'PPOSSD',label: 'Peace, Public Order, Safety, and Security Division (PPOSSD)' },
            { value: 'MED',   label: 'Monitoring and Evaluation Division (MED)' },
        ],
        'RDSPB': [
            { value: 'IKMD',       label: 'Information and Knowledge Management Division (IKMD)' },
            { value: 'RDD',        label: 'Research and Development Division (RDD)' },
            { value: 'ODA/NFPPCD', label: 'ODA/National Funded Programs and Projects Coordination Division' },
            { value: 'EID',        label: 'Economic Intelligence Division (EID)' },
        ],
        'FASS': [
            { value: 'Finance Division',       label: 'Finance Division' },
            { value: 'Administrative Division',label: 'Administrative Division' },
        ],
        'Other': [
            { value: 'Other', label: 'Other' },
            { value: 'Utility', label: 'Utility' }
        ]
    };

    const bureauSelect = document.getElementById('bureau-select');
    const divisionSelect = document.getElementById('division-select');
    const positionInput = document.getElementById('position-input');

    const currentDivision = "{{ old('division', $employee->division) }}";

    function populateDivisions(bureauValue, selectedDivision = '') {
        const divisions = divisionsByBureau[bureauValue] || [];
        divisionSelect.innerHTML = '<option value="" disabled selected>Select Division</option>';
        
        divisions.forEach(div => {
            const option = document.createElement('option');
            option.value = div.value;
            option.textContent = div.label;
            if (div.value === selectedDivision) option.selected = true;
            divisionSelect.appendChild(option);
        });

        divisionSelect.disabled = divisions.length === 0;
    }

    bureauSelect.addEventListener('change', function() {
        populateDivisions(this.value);
        
        positionInput.value = '';
        positionInput.disabled = true;
        positionInput.placeholder = "Select division first";
    });

    divisionSelect.addEventListener('change', function() {
        if (this.value) {
            positionInput.disabled = false;
            positionInput.placeholder = "Enter current position";
        } else {
            positionInput.disabled = true;
            positionInput.value = '';
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        if (bureauSelect.value) {
            populateDivisions(bureauSelect.value, currentDivision);
        }

        if (divisionSelect.value || currentDivision) {
            positionInput.disabled = false;
            positionInput.placeholder = "Enter current position";
        } else {
            positionInput.disabled = true;
        }

        const employmentTypeSelect = document.getElementById('employment-type-select');
        const salaryInput = document.getElementById('salary-id');
        const leaveCreditsInput    = document.getElementById('leave-credits-id');
        const leaveCreditsField    = document.getElementById('leave-credits-field');

        function toggleFields() {
            const isPermanent = employmentTypeSelect.value === 'Permanent';

            // Salary — disabled pag Permanent
            salaryInput.disabled = isPermanent;
            salaryInput.classList.toggle('bg-gray-100', isPermanent);
            salaryInput.classList.toggle('cursor-not-allowed', isPermanent);
            salaryInput.classList.toggle('opacity-60', isPermanent);

            if (isPermanent) {
                salaryInput.removeAttribute('required');
                salaryInput.value = '';
            } else {
                salaryInput.setAttribute('required', 'required');
            }

            // Leave Credits — disabled pag Contractual
            leaveCreditsInput.disabled = !isPermanent;
            leaveCreditsInput.classList.toggle('bg-gray-100', !isPermanent);
            leaveCreditsInput.classList.toggle('cursor-not-allowed', !isPermanent);
            leaveCreditsInput.classList.toggle('opacity-60', !isPermanent);

            // Pag Contractual — i-zero out ang value
            if (!isPermanent) {
                leaveCreditsInput.value = '0';
            }

            // Itago ang current balance indicator pag Contractual
            leaveCreditsField.style.opacity = isPermanent ? '1' : '0.5';
    }

    toggleFields(); // run on load
    employmentTypeSelect.addEventListener('change', toggleFields);
});

    document.addEventListener('DOMContentLoaded', () => {
        const employmentTypeSelect = document.getElementById('employment-type-select');
        const salaryInput = document.getElementById('salary-id');

        if (employmentTypeSelect && salaryInput) {
            function toggleSalaryField() {
                if (employmentTypeSelect.value === "Permanent") {
                    salaryInput.disabled = true;
                    salaryInput.value = '';
                    salaryInput.classList.add('bg-gray-100', 'cursor-not-allowed');
                    salaryInput.removeAttribute('required');
                } else {
                    salaryInput.disabled = false;
                    salaryInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
                    salaryInput.setAttribute('required', 'required');
                }
            }

            toggleSalaryField(); 
            employmentTypeSelect.addEventListener('change', toggleSalaryField);
        }
    });

    document.getElementById('employee-create-form')?.addEventListener('submit', function(e) {
        const employeeIdInput = document.querySelector('input[name="employee_id"]');
        const suffixInput     = document.querySelector('input[name="suffix"]');

        if (employeeIdInput.value.length !== 15 || !/^\d{15}$/.test(employeeIdInput.value)) {
            alert("Employee ID dapat eksaktong 15 digits (numero lamang).");
            employeeIdInput.focus();
            e.preventDefault();
            return;
        }

        if (suffixInput.value.trim() !== "") {
            const validSuffix = /^(Jr|Sr|I|II|III|IV|V|VI|VII|VIII|IX|X|Jr\.|Sr\.)$/i;
            if (!validSuffix.test(suffixInput.value.trim())) {
                alert("Hindi valid ang suffix. Gamitin: Jr, Sr, o Roman numerals (I, II, III, etc.)");
                suffixInput.focus();
                e.preventDefault();
                return;
            }
        }
    });
</script>
@endsection