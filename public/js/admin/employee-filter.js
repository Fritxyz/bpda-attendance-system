 const divisionsByBureau = {
    'PPB': [
        'MEPD',
        'EPD',
        'SPD',
        'LPCD',
        'IPD',
        'PPOSSD',
        'MED',
    ],
    'RDSPB': [
        'IKMD',
        'RDD',
        'ODA/NFPPCD',
        'EID    '
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