@extends('layouts.admin.top-and-side-bar')

@section('header', 'Employee Management')

@section('content')
<div class="max-w-4.5xl mx-auto px-4 py-3">
    
    <nav class="flex mb-6 text-sm text-gray-500" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1">
            <li class="inline-flex items-center">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition">Dashboard</a>
            </li>
            <li class="flex items-center">
                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                <a href="{{ route('employees.index') }}" class="ml-1 hover:text-blue-600 transition">Employees</a>
            </li>
            <li class="flex items-center" aria-current="page">
                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                <span class="ml-1 font-medium text-gray-800">Edit Employee</span>
            </li>
        </ol>
    </nav>

    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
        <div class="px-8 py-5 bg-gray-50 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-800">Edit Employee</h2>
            <p class="text-xs text-gray-500 mt-1 uppercase tracking-wider">Updating record for: {{ $employee->first_name }} {{ $employee->last_name }}</p>
        </div>

        <form method="POST" action="{{ route('employees.update', $employee->employee_id) }}" class="p-8" enctype="multipart/form-data">
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
                                placeholder="e.g. 123456789012345" required maxlength="15" pattern="\d{15}"
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
                            oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                            class="w-full px-2 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Middle Name</label>
                        <input type="text" name="middle_name" value="{{ old('middle_name', $employee->middle_name ) }}" required
                            oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                            class="w-full px-2 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Last Name</label>
                        
                        <input type="text" name="last_name" value="{{ old('last_name', $employee->last_name ) }}" required
                            oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                            class="w-full px-2 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Suffix</label>
                        <input type="text" name="suffix" value="{{ old('suffix', $employee->suffix) }}"
                            placeholder="Jr, Sr, III"
                            oninput="this.value = this.value.replace(/[^a-zA-Z\s\.]/g, '')"
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
                        <select id="position-select" name="position" required
                                class="w-full px-2 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 outline-none transition cursor-pointer disabled:bg-gray-100 disabled:text-gray-400">
                            <option value="" disabled selected>Select Division first</option>

                            {{-- @if(old('division', $employee->division === "MEPD"))
                                <option value="Chief Economic Developement Specialist" {{ old('position', $employee->position) === 'Chief Economic Developement Specialist' ? 'selected' : '' }}>Chief Economic Developement Specialist</option>
                                <option value="Socio Economic Developement Specialist" {{ old('position', $employee->position) === 'Socio Economic Developement Specialist' ? 'selected' : '' }}>Socio Economic Developement Specialist</option>
                                <option value="Economic Developement Specialist II" {{ old('position', $employee->position) === 'Economic Developement Specialist II' ? 'selected' : '' }}>Economic Developement Specialist II</option>
                                <option value="Economic Developement Analyst" {{ old('position', $employee->position) === 'Economic Developement Analyst' ? 'selected' : '' }}>Economic Developement Analyst</option>
                            @endif

                            @if(old('division', $employee->division === "EPD"))
                                <option value="Chief Economic Developement Specialist" {{ old('position', $employee->position) === 'Chief Economic Developement Specialist' ? 'selected' : '' }}>Chief Economic Developement Specialist</option>
                                <option value="Senior Economic Developement Specialist" {{ old('position', $employee->position) === 'Senior Economic Developement Specialist' ? 'selected' : '' }}>Senior Economic Developement Specialist</option>
                                <option value="Economic Developement Specialist III" {{ old('position', $employee->position) === 'Economic Developement Specialist III' ? 'selected' : '' }}>Economic Developement Specialist III</option>
                                <option value="Economic Developement Analyst" {{ old('position', $employee->position) === 'Economic Developement Analyst' ? 'selected' : '' }}>Economic Developement Analyst</option>
                            @endif

                            @if(old('division', $employee->division === "SPD"))
                                <option value="Planning Officer V" {{ old('position', $employee->position) === 'Planning Officer V' ? 'selected' : '' }}>Planning Officer V</option>
                                <option value="Planning Officer III" {{ old('position', $employee->position) === 'Planning Officer III' ? 'selected' : '' }}>Planning Officer III</option>
                                <option value="Planning Officer II" {{ old('position', $employee->position) === 'Planning Officer II' ? 'selected' : '' }}>Planning Officer II</option>
                                <option value="Planning Officer I" {{ old('position', $employee->position) === 'Planning Officer I' ? 'selected' : '' }}>Planning Officer I</option>
                            @endif

                            @if(old('division', $employee->division === "LPCD"))
                                <option value="Planning Officer V" {{ old('position', $employee->position) === 'Planning Officer V' ? 'selected' : '' }}>Planning Officer V</option>
                                <option value="Planning Officer IV" {{ old('position', $employee->position) === 'Planning Officer IV' ? 'selected' : '' }}>Planning Officer IV</option>
                                <option value="Development Management Officer II" {{ old('position', $employee->position) === 'Development Management Officer II' ? 'selected' : '' }}>Developement Management Officer II</option>
                                <option value="Development Management Officer I" {{ old('position', $employee->position) === 'Development Management Officer I' ? 'selected' : '' }}>Development Management Officer I</option>
                            @endif

                            @if(old('division', $employee->division === "IPD"))
                                <option value="Planning Officer V" {{ old('position', $employee->position) === 'Planning Officer V' ? 'selected' : '' }}>Planning Officer V</option>
                                <option value="Engineer III" {{ old('position', $employee->position) === 'Engineer III' ? 'selected' : '' }}>Engineer III</option>
                                <option value="Engineer II" {{ old('position', $employee->position) === 'Engineer II' ? 'selected' : '' }}>Engineer II</option>
                                <option value="Planning Officer I" {{ old('position', $employee->position) === 'Planning Officer I' ? 'selected' : '' }}>Planning Officer I</option>
                            @endif

                            @if(old('division', $employee->division === "PPOSSD"))
                                <option value="Planning Officer V" {{ old('position', $employee->position) === 'Planning Officer V' ? 'selected' : '' }}>Planning Officer V</option>
                                <option value="Development Management Officer III" {{ old('position', $employee->position) === 'Development Management Officer III' ? 'selected' : '' }}>Development Management Officer III</option>
                                <option value="Development Management Officer II" {{ old('position', $employee->position) === 'Development Management Officer II' ? 'selected' : '' }}>Development Management Officer II</option>
                                <option value="Planning Officer I" {{ old('position', $employee->position) === 'Planning Officer I' ? 'selected' : '' }}>Planning Officer I</option>
                            @endif

                            @if(old('division', $employee->division === "MED"))
                                <option value="Planning Officer V" {{ old('position', $employee->position) === 'Planning Officer V' ? 'selected' : '' }}>Planning Officer V</option>
                                <option value="Engineer III" {{ old('position', $employee->position) === 'Engineer III' ? 'selected' : '' }}>Engineer III</option>
                                <option value="Engineer II" {{ old('position', $employee->position) === 'Engineer II' ? 'selected' : '' }}>Engineer II</option>
                                <option value="Project Evaluation Officer I" {{ old('position', $employee->position) === 'Project Evaluation Officer I' ? 'selected' : '' }}>Project Evaluation Officer I</option>
                            @endif

                            @if(old('division', $employee->division === "IKMD"))
                                <option value="Information Technology Officer III" {{ old('position', $employee->position) === 'Information Technology Officer III' ? 'selected' : '' }}>Information Technology Officer III</option>
                                <option value="Supervision Administrative Officer" {{ old('position', $employee->position) === 'Supervision Administrative Officer' ? 'selected' : '' }}>Supervision Administrative Officer</option>
                                <option value="Information Technology Officer I" {{ old('position', $employee->position) === 'Information Technology Officer I' ? 'selected' : '' }}>Information Technology Officer I</option>
                                <option value="Administrative Officer I" {{ old('position', $employee->position) === 'Administrative Officer I' ? 'selected' : '' }}>Administrative Officer I</option>
                                <option value="Computer Programmer" {{ old('position', $employee->position) === 'Computer Programmer' ? 'selected' : '' }}>Computer Programmer</option>
                            @endif

                            @if(old('division', $employee->division === "RDD"))
                                <option value="Development Management Officer V" {{ old('position', $employee->position) === 'Development Management Officer V' ? 'selected' : '' }}>Development Management Officer V</option>
                                <option value="Development Management Officer III" {{ old('position', $employee->position) === 'Development Management Officer V' ? 'selected' : '' }}>Development Management Officer V</option>
                                <option value="Statistician II" {{ old('position', $employee->position) === 'Statistician II' ? 'selected' : '' }}>Statistician II</option>
                                <option value="Development Management Officer I" {{ old('position', $employee->position) === 'Development Management Officer I' ? 'selected' : '' }}>Development Management Officer I</option>
                            @endif

                            @if(old('division', $employee->division === "ODA/NFPPCD"))
                                <option value="Project Development Officer V" {{ old('position', $employee->position) === 'Project Development Officer V' ? 'selected' : '' }}>Project Development Officer V</option>
                                <option value="Project Development Officer III" {{ old('position', $employee->position) === 'Project Development Officer III' ? 'selected' : '' }}>Project Development Officer III</option>
                                <option value="Project Development Officer II" {{ old('position', $employee->position) === 'Project Development Officer II' ? 'selected' : '' }}>Project Development Officer II</option>
                                <option value="Project Development Officer I" {{ old('position', $employee->position) === 'Project Development Officer I' ? 'selected' : '' }}>Project Development Officer I</option>
                            @endif

                            @if(old('division', $employee->division === "EID"))
                                <option value="Chief Economic Development Specialist" {{ old('position', $employee->position) === 'Chief Economic Development Specialist' ? 'selected' : '' }}>Chief Economic Development Specialist</option>
                                <option value="Senior Economic Development Specialist" {{ old('position', $employee->position) === 'Senior Economic Development Specialist' ? 'selected' : '' }}>Senior Economic Development Specialist</option>
                                <option value="Economic Development Specialist II" {{ old('position', $employee->position) === 'Economic Development Specialist II' ? 'selected' : '' }}>Economic Development Specialist II</option>
                                <option value="Economic Development Analysts" {{ old('position', $employee->position) === 'Economic Development Analyst' ? 'selected' : '' }}>Economic Development Analyst</option>
                            @endif

                            @if(old('division', $employee->division === "Finance Division"))
                                <option value="Chief Accountant" {{ old('position', $employee->position) === 'Chief Accountant' ? 'selected' : '' }}>Chief Accountant</option>
                                <option value="Accountant III" {{ old('position', $employee->position) === 'Accountant III' ? 'selected' : '' }}>Accountant III</option>
                                <option value="Budget Officer III" {{ old('position', $employee->position) === 'Budget Officer III' ? 'selected' : '' }}>Budget Officer III</option>
                                <option value="Cashier III" {{ old('position', $employee->position) === 'Cashier III' ? 'selected' : '' }}>Cashier III</option>
                                <option value="Senior Bookkeeper" {{ old('position', $employee->position) === 'Senior Bookkeeper' ? 'selected' : '' }}>Senior Bookkeeper</option>
                                <option value="Disbursing Officer II" {{ old('position', $employee->position) === 'Disbursing Officer II' ? 'selected' : '' }}>Disbursing Officer</option>
                            @endif

                            @if(old('division', $employee->division === "Administrative Division"))
                                <option value="Chief Administratve Officer" {{ old('position', $employee->position) === 'Chief Administratve Officer' ? 'selected' : '' }}>Chief Administratve Officer</option>
                                <option value="HRMO II" {{ old('position', $employee->position) === 'HRMO II' ? 'selected' : '' }}>HRMO II</option>
                                <option value="Supply Officer II" {{ old('position', $employee->position) === 'Supply Officer II' ? 'selected' : '' }}>Supply Officer II</option>
                                <option value="Records Officer II" {{ old('position', $employee->position) === 'Records Officer II' ? 'selected' : '' }}>Records Officer II</option>
                                <option value="Clerk III" {{ old('position', $employee->position) === 'Clerk III' ? 'selected' : '' }}>Clerk III</option>
                            @endif

                            @if(old('division', $employee->division === "Other"))
                                <option value="Bangsamoro Director General" {{ old('position', $employee->position) === 'Bangsamoro Director General' ? 'selected' : '' }}>Bangsamoro Director General</option>
                                <option value="Attorney IV" {{ old('position', $employee->position) === 'Attorney IV' ? 'selected' : '' }}>Attorney IV</option>
                                <option value="Internal Auditor II" {{ old('position', $employee->position) === 'Internal Auditor II' ? 'selected' : '' }}>Internal Auditor II</option>
                                <option value="Administrative Aide IV" {{ old('position', $employee->position) === 'Administrative Aide IV' ? 'selected' : '' }}>Administrative Aide IV</option>
                                <option value="Deputy Director General" {{ old('position', $employee->position) === 'Deputy Director General' ? 'selected' : '' }}>Deputy Director General</option>
                                <option value="Executive Assistant I" {{ old('position', $employee->position) === 'Executive Assistant I' ? 'selected' : '' }}>Executive Assistant I</option>
                            @endif --}}
                            {{-- todo: fass and other --}}
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Employment Type</label>
                        <select name="employment_type" required id="employment-type-select"
                                class="w-full px-2 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 outline-none transition">
                            <option value="Permanent" {{ $employee->employment_type === 'Permanent' ? 'selected' : '' }}>Permanent</option>
                            <option value="Contractual" {{ $employee->employment_type === 'Contractual' ? 'selected' : '' }}>Contractual</option>
                            <option value="Job Order" {{ $employee->employment_type === 'Job Order' ? 'selected' : '' }}>Job Order</option>
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
                    {{-- Password with Regenerate Button --}}
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
                    <input type="checkbox" name="is_active" id="is_active" value="1" checked
                           class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer transition">
                    <label for="is_active" class="ml-2 block text-sm font-bold text-gray-600 cursor-pointer">Active Status</label>
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

    // --- Dropdown Logic (Bureau → Division → Position) ---
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
            { value: 'Other', label: 'Other' }
        ]
    };

    const positionsByDivision = {
        // Planning and Policies Bureau (PPB)
        'MEPD': [
            'Chief Economic Development Specialist',
            'Socio Economic Development Specialist',
            'Economic Development Specialist II',
            'Economic Development Analyst',
        ],
        'EPD': [
            'Chief Economic Development Specialist',
            'Senior Economic Development Specialist',
            'Economic Development Specialist III',
            'Economic Development Analyst',
        ],
        'SPD': [
            'Planning Officer V',
            'Planning Officer III',
            'Planning Officer II',
            'Planning Officer I',
        ],
        'IPD': [
            'Planning Officer V',
            'Engineer III',
            'Engineer II',
            'Planning Officer I',
        ],
        'PPOSSD': [
            'Planning Officer V',
            'Development Management Officer III',
            'Development Management Officer II',
            'Planning Officer I',
        ],
        'MED': [
            'Planning Officer V',
            'Engineer III',
            'Engineer II',
            'Project Evaluation Officer I',
        ],
        'LPCD': [
            'Planning Officer V',
            'Planning Officer IV',
            'Development Management Officer II',
            'Developement Management Officer I',
        ],
        // Reseach Development and Special Projects Bureau (RDSPB)
        'RDD': [
            'Development Management Officer V',
            'Development Management Officer III',
            'Statistician II',
            'Development Management Officer I',
        ],
        'IKMD': [
            'Information Technology Officer III',
            'Supervision Administrative Officer',
            'Information Technology Officer I',
            'Administrative Officer I',
            'Computer Programmer',
        ],
        'ODA/NFPPCD': [
            'Project Development Officer V',
            'Project Developement Officer III',
            'Project Development Officer II',
            'Project Development Officer I',
        ],
        'EID': [
            'Chief Economic Development Specialist',
            'Senior Economic Development Specialist',
            'Economic Development Specialist II',
            'Economic Development Analyst',
        ],
        'Finance Division': [
            'Chief Accountant',
            'Accountant III',
            'Budget Officer III',
            'Cashier III',
            'Senior Bookkeeper',
            'Disbursing Officer II',
        ],
        'Administrative Division': [
            'Chief Administrative Officer',
            'HRMO II',
            'Supply Officer II',
            'Records Officer II',
            'Clerk III',
        ],
        'Other': [
            'Bangsamoro Director General',
            'Attorney IV',
            'Internal Auditor II',
            'Administrative Aide IV',
            'Deputy Director General',
            'Executive Assistant I',
        ],
    };

    const original = {
        bureau:   "{{ old('bureau',   $employee->bureau   ?? '') }}",
        division: "{{ old('division', $employee->division ?? '') }}",
        position: "{{ old('position', $employee->position ?? '') }}",
    };

    function populateSelect(selectElement, items, selectedValue = '', isObjectArray = false) {
        selectElement.innerHTML = '<option value="" disabled>Select...</option>';

        items.forEach(item => {
            const option = document.createElement('option');
            
            if (isObjectArray) {
                // For divisions (array of objects)
                option.value = item.value;
                option.textContent = item.label;
            } else {
                // For positions (array of strings)
                option.value = item;
                option.textContent = item;
            }

            if (item.value === selectedValue || item === selectedValue) {
                option.selected = true;
            }
            selectElement.appendChild(option);
        });

        // Fallback if saved value not in current list
        if (selectedValue && !selectElement.querySelector(`option[value="${selectedValue}"]`)) {
            const fallback = document.createElement('option');
            fallback.value = selectedValue;
            fallback.disabled = true;
            fallback.textContent = `${selectedValue} (previous – not available now)`;
            fallback.style.color = '#dc2626';
            fallback.selected = true;
            selectElement.appendChild(fallback);
        }
    }

    const bureauSelect = document.getElementById('bureau-select');
    const divisionSelect = document.getElementById('division-select');
    const positionSelect = document.getElementById('position-select');

    const getAcronym = text => (text.match(/\(([^)]+)\)/) || [, ''])[1].trim();

    // When Bureau changes → reset & populate Division + Position
    bureauSelect.addEventListener('change', () => {
        const bureau = bureauSelect.value;
        const divisionItems = divisionsByBureau[bureau] || [];

        // 1. Repopulate Division
        populateSelect(divisionSelect, divisionItems, original.division, true);

        // 2. Reset & disable Position until division is chosen
        positionSelect.innerHTML = '<option value="" disabled selected>Select Division first</option>';
        positionSelect.disabled = true;

        // If there's already a division selected (after repopulation), trigger position update
        if (divisionSelect.value) {
            divisionSelect.dispatchEvent(new Event('change'));
        }
    });

    divisionSelect.addEventListener('change', () => {
        const divisionCode = divisionSelect.value;
        const positionItems = positionsByDivision[divisionCode] || [];

        populateSelect(positionSelect, positionItems, original.position, false);
        
        positionSelect.disabled = positionItems.length === 0;
    });

    // Initialize form with saved values on page load
    document.addEventListener('DOMContentLoaded', () => {
        if (original.bureau) {
            bureauSelect.value = original.bureau;
            bureauSelect.dispatchEvent(new Event('change'));  // This triggers everything
        }
    });

    // --- Salary Toggle (one clean version only) ---
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

            toggleSalaryField(); // initial check
            employmentTypeSelect.addEventListener('change', toggleSalaryField);
        }
    });

    // Sa dulo ng <script> block mo, palitan 'to
    document.getElementById('employee-create-form')?.addEventListener('submit', function(e) {
        const employeeIdInput = document.querySelector('input[name="employee_id"]');
        const suffixInput     = document.querySelector('input[name="suffix"]');

        // 1. Employee ID check
        if (employeeIdInput.value.length !== 15 || !/^\d{15}$/.test(employeeIdInput.value)) {
            alert("Employee ID dapat eksaktong 15 digits (numero lamang).");
            employeeIdInput.focus();
            e.preventDefault();
            return;
        }

        // 2. Suffix check
        if (suffixInput.value.trim() !== "") {
            const validSuffix = /^(Jr|Sr|I|II|III|IV|V|VI|VII|VIII|IX|X|Jr\.|Sr\.)$/i;
            if (!validSuffix.test(suffixInput.value.trim())) {
                alert("Hindi valid ang suffix. Gamitin: Jr, Sr, o Roman numerals (I, II, III, etc.)");
                suffixInput.focus();
                e.preventDefault();
                return;
            }
        }

        // Kung okay → payagan mag-submit
    });
</script>
@endsection