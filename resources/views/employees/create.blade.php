@extends('layouts.app')

@section('header', 'Employee Management')

@section('content')
<div class="max-w-4.5xl mx-auto px-4 py-3">
    
    <nav class="flex mb-6 text-sm text-gray-500" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition">Dashboard</a>
            </li>
            <li class="flex items-center">
                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                <a href="{{ route('employees.index') }}" class="ml-1 hover:text-blue-600 transition">Employees</a>
            </li>
            <li class="flex items-center" aria-current="page">
                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                <span class="ml-1 font-medium text-gray-800">New Registration</span>
            </li>
        </ol>
    </nav>

    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
        <div class="px-8 py-5 bg-gray-50 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-800">Add New Employee</h2>
            <p class="text-xs text-gray-500 mt-1 uppercase tracking-wider">Employment Registration Form</p>
        </div>

        <form method="POST" action="{{ route('employees.store') }}" class="p-8">
            @csrf

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
                    {{-- Employee ID with BPDA- Prefix --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Employee ID</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-100 text-gray-500 font-bold text-sm">
                                BPDA-
                            </span>
                            <input type="number" name="employee_id" value="{{ old('employee_id') }}" placeholder="1234567890" required
                                   class="flex-1 px-2 py-2 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                        </div>
                    </div>

                    
                </div>

                {{-- Full Name Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">First Name</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" required
                               class="w-full px-2 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1 text-gray-400">Middle Name</label>
                        <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                               class="w-full px-2 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" required
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
                                class="w-full px-2 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 outline-none transition appearance-none cursor-pointer">
                            <option value="" disabled {{ old('bureau') ? '' : 'selected' }}>Select Bureau</option>
                            <option value="PPB" {{ old('bureau') == 'PPB' ? 'selected' : '' }}>Planning and Policies Bureau     (PPB)</option>
                            <option value="RDSPB" {{ old('bureau') == 'RDSPB' ? 'selected' : '' }}>Research Development and Special Projects Bureau (RDPSB)</option>
                            <option value="FASS" {{ old('bureau') == 'FASS' ? 'selected' : '' }}>Finance and Administrative Support Services (FASS)</option>
                            <option value="Other" {{ old('bureau') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Division</label>
                        <select id="division-select" name="division" required disabled
                                class="w-full px-2 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 outline-none transition cursor-pointer disabled:bg-gray-100 disabled:text-gray-400">
                            <option value="" disabled selected>Select Bureau first</option>
                        </select>
                    </div>

                     <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Current Position</label>
                        <select id="position-select" name="position" required disabled
                                class="w-full px-2 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 outline-none transition cursor-pointer disabled:bg-gray-100 disabled:text-gray-400">
                            <option value="" disabled selected>Select Division first</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Employment Type</label>
                        <select name="employment_type" required id="employment-type-select"
                                class="w-full px-2 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 outline-none transition">
                            <option value="Permanent">Permanent</option>
                            <option value="Contractual">Contractual</option>
                            <option value="Job Order">Job Order</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Monthly Salary</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-400 font-bold">₱</span>
                            <input type="number" step="0.01" name="salary" value="{{ old('salary') }}" id="salary-id"
                                   class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">System Role</label>
                        <select name="role" required
                                class="w-full px-2 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 outline-none transition">
                            <option value="Employee">Employee (Standard)</option>
                            <option value="Admin">Admin (Full Access)</option>
                        </select>
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
    const divisionsByBureau = {
        'PPB': [
            'Macro-Economic Planning Division (MEPD)',
            'Economic Policy Division (EPD)',
            'Social Planning Division (SPD)',
            'Local Planning and Coordinating Division (LPCD)',
            'Infrastructure Planning Division (IPD)',
            'Peace, Public Order, Safety and Security Division (PPOSSD)',
            'Monitoring and Evaluation Division (MED)',
        ],
        'RDSPB': [
            'Information and Knowledge Management Division (IKMD)',
            'Research and Development Division (RDD)',
            'ODA/Nationally Funded Programs and Projects Coordination Division (ODA/NFPPCD)',
            'Economic Intelligence Division (EID)'
        ],
        'FASS': [
            'Finance Division',
            'Administrative Division',
        ],
        'Other': [
            'Other'
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

    const bureauSelect = document.getElementById('bureau-select');
    const divisionSelect = document.getElementById('division-select');
    const positionSelect = document.getElementById('position-select');
    const employmentTypeSelect = document.getElementById('employment-type-select');

    const getAcronym = text => (text.match(/\(([^)]+)\)/) || [, ''])[1].trim();

    bureauSelect.addEventListener('change', function() {
        const selectedBureau = this.value;
        const options = divisionsByBureau[selectedBureau] || [];

        // 1. Linisin ang kasalukuyang options
        divisionSelect.innerHTML = '<option value="" disabled selected>Select Division</option>';
        
        // 2. Enable ang Division select
        divisionSelect.disabled = false;
        divisionSelect.classList.remove('bg-gray-100');
        divisionSelect.classList.add('bg-white');

        // 3. Idagdag ang mga bagong options
        options.forEach(division => {
            const acronym = getAcronym(division);           // '' kung wala
            const el = document.createElement('option');
            
            el.value = acronym || division;                 // acronym kung meron, full name kung wala
            el.textContent = division;                      // palaging full name ang nakikita
            
            // Optional: idagdag ang acronym sa display para makita (hal. "Division Name (ACR)")
            // el.textContent = acronym ? `${division} (${acronym})` : division;
            
            console.log(`Acronym for "${division}": ${acronym}`);
            divisionSelect.appendChild(el);
        });
    });

    divisionSelect.addEventListener('change', function() {
        const selectDivision = this.value;
        const options = positionsByDivision[selectDivision] || [];

        // 1. Linisin ang kasalukuyang options
        positionSelect.innerHTML = '<option value="" disabled selected>Select Current Position</option>';
        
        // 2. Enable ang Division select
        positionSelect.disabled = false;
        positionSelect.classList.remove('bg-gray-100');
        positionSelect.classList.add('bg-white');

        // 3. Idagdag ang mga bagong options
        options.forEach(position => {
            const el = document.createElement('option');
            
            el.value = position;                // acronym kung meron, full name kung wala
            el.textContent = position;                      // palaging full name ang nakikita
            
            // Optional: idagdag ang acronym sa display para makita (hal. "Division Name (ACR)")
            // el.textContent = acronym ? `${division} (${acronym})` : division;
            
            console.log(position);
            positionSelect.appendChild(el);
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const employmentTypeSelect = document.getElementById('employment-type-select');
        const salaryInput = document.getElementById('salary-id');

        // 1. Gawa tayo ng function para reusable
        function toggleSalary() {
            if (employmentTypeSelect.value === "Permanent") {
                salaryInput.disabled = true;
                salaryInput.value = ''; // Linisin ang value
                salaryInput.classList.add('bg-gray-100', 'cursor-not-allowed');
                salaryInput.removeAttribute('required');
            } else {
                salaryInput.disabled = false;
                salaryInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
                salaryInput.setAttribute('required', 'required');
            }
        }

        // 2. Patakbuhin agad pag-load ng page (Initial Check)
        toggleSalary();

        // 3. Patakbuhin tuwing binabago ang dropdown
        employmentTypeSelect.addEventListener('change', toggleSalary);
    });



    employmentTypeSelect.addEventListener('change', function() {
        const employmentTypeSelect = document.getElementById('employment-type-select');
        // Siguraduhing 'salary-id' ang gamit dahil ito ang nasa HTML mo
        const salaryInput = document.getElementById('salary-id');

        function toggleSalary() {
            if (employmentTypeSelect.value === "Permanent") {
                salaryInput.disabled = true;
                salaryInput.value = ''; // Nililinis ang value pag disabled
                salaryInput.classList.add('bg-gray-100', 'cursor-not-allowed');
                salaryInput.removeAttribute('required');
            } else {
                salaryInput.disabled = false;
                salaryInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
                salaryInput.setAttribute('required', 'required');
            }
        }

        // Patakbuhin kapag nagbago ang selection
        employmentTypeSelect.addEventListener('change', toggleSalary);

        // Patakbuhin sa simula (initial load)
        toggleSalary();
});

// todo: ayusin ang salary according sa employment type
// tapos mag-example ng value 

</script>
@endsection