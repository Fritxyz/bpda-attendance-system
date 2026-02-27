const divisionsByBureau = {
    'PPB': ['MEPD', 'EPD', 'SPD', 'LPCD', 'IPD', 'PPOSSD', 'MED'],
    'RDSPB': ['IKMD', 'RDD', 'ODA/NFPPCD', 'EID'],
    'FASS': ['Finance Division', 'Administrative Division'],
    'Other': ['Other']
};

const positionsByDivision = {
    'MEPD': ['Chief Economic Development Specialist', 'Socio Economic Development Specialist', 'Economic Development Specialist II', 'Economic Development Analyst'],
    'EPD': ['Chief Economic Development Specialist', 'Senior Economic Development Specialist', 'Economic Development Specialist III', 'Economic Development Analyst'],
    'SPD': ['Planning Officer V', 'Planning Officer III', 'Planning Officer II', 'Planning Officer I'],
    'IPD': ['Planning Officer V', 'Engineer III', 'Engineer II', 'Planning Officer I'],
    'PPOSSD': ['Planning Officer V', 'Development Management Officer III', 'Development Management Officer II', 'Planning Officer I'],
    'MED': ['Planning Officer V', 'Engineer III', 'Engineer II', 'Project Evaluation Officer I'],
    'LPCD': ['Planning Officer V', 'Planning Officer IV', 'Development Management Officer II', 'Developement Management Officer I'],
    'RDD': ['Development Management Officer V', 'Development Management Officer III', 'Statistician II', 'Development Management Officer I'],
    'IKMD': ['Information Technology Officer III', 'Supervision Administrative Officer', 'Information Technology Officer I', 'Administrative Officer I', 'Computer Programmer'],
    'ODA/NFPPCD': ['Project Development Officer V', 'Project Developement Officer III', 'Project Development Officer II', 'Project Development Officer I'],
    'EID': ['Chief Economic Development Specialist', 'Senior Economic Development Specialist', 'Economic Development Specialist II', 'Economic Development Analyst'],
    'Finance Division': ['Chief Accountant', 'Accountant III', 'Budget Officer III', 'Cashier III', 'Senior Bookkeeper', 'Disbursing Officer II'],
    'Administrative Division': ['Chief Administrative Officer', 'HRMO II', 'Supply Officer II', 'Records Officer II', 'Clerk III'],
    'Other': ['Bangsamoro Director General', 'Attorney IV', 'Internal Auditor II', 'Administrative Aide IV', 'Deputy Director General', 'Executive Assistant I'],
};

const bureauSelect = document.getElementById('bureau-select');
const divisionSelect = document.getElementById('division-select');
const positionSelect = document.getElementById('position-select'); // Siguraduhin na may ID ito sa Blade

// --- HELPER FUNCTIONS ---

function updateDivisions(selectedBureau, selectedValue = '') {
    const options = divisionsByBureau[selectedBureau] || [];
    divisionSelect.innerHTML = '<option value="">All Divisions</option>';
    
    if (selectedBureau) {
        divisionSelect.disabled = false;
        divisionSelect.classList.remove('bg-gray-100');
        
        options.forEach(div => {
            const el = document.createElement('option');
            el.value = div;
            el.textContent = div;
            if (div === selectedValue) el.selected = true;
            divisionSelect.appendChild(el);
        });
    } else {
        divisionSelect.disabled = true;
        divisionSelect.classList.add('bg-gray-100');
    }
}

function updatePositions(selectedDivision, selectedValue = '') {
    if (!positionSelect) return; // Iwas error kung wala sa page ang position select
    const options = positionsByDivision[selectedDivision] || [];
    positionSelect.innerHTML = '<option value="">Select Position</option>';
    
    if (selectedDivision) {
        positionSelect.disabled = false;
        positionSelect.classList.remove('bg-gray-100');
        
        options.forEach(pos => {
            const el = document.createElement('option');
            el.value = pos;
            el.textContent = pos;
            if (pos === selectedValue) el.selected = true;
            positionSelect.appendChild(el);
        });
    } else {
        positionSelect.disabled = true;
        positionSelect.classList.add('bg-gray-100');
    }
}

// --- EVENT LISTENERS ---

bureauSelect.addEventListener('change', function() {
    updateDivisions(this.value);
    updatePositions(''); // Correct
    fetchEmployees();
});

divisionSelect.addEventListener('change', function() {
    updatePositions(this.value);

});

// --- INITIAL LOAD (Fixes the Bug) ---
document.addEventListener('DOMContentLoaded', function () {
    // 1. Kunin ang values mula sa URL (parameters)
    const urlParams = new URLSearchParams(window.location.search);
    const currentBureau = bureauSelect.value;
    const currentDivision = urlParams.get('division');
    const currentPosition = urlParams.get('position');

    // 2. I-trigger ang chain reaction base sa kung ano ang naka-load na value
    if (currentBureau) {
        updateDivisions(currentBureau, currentDivision);
    }
    
    if (currentDivision) {
        updatePositions(currentDivision, currentPosition);
    }

    // 3. Salary Toggle Logic
    const employmentTypeSelect = document.getElementById('employment-type-select');
    const salaryInput = document.getElementById('salary-id');

    if (employmentTypeSelect && salaryInput) {
        function toggleSalary() {
            if (employmentTypeSelect.value === "Permanent") {
                salaryInput.disabled = true;
                salaryInput.value = '';
                salaryInput.classList.add('bg-gray-100', 'cursor-not-allowed');
            } else {
                salaryInput.disabled = false;
                salaryInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
            }
        }
        toggleSalary();
        employmentTypeSelect.addEventListener('change', toggleSalary);
    }

    
});

document.addEventListener('click', function (e) {
    // Hanapin kung ang cliniclick ay link sa loob ng pagination-container
    if (e.target.closest('#pagination-container a')) {
        e.preventDefault();
        
        // Kunin ang URL (halimbawa: employees?page=2)
        const url = e.target.closest('a').href;
        
        // Tawagin ang fetch function gamit ang page URL
        fetchEmployees(url);
    }
});

// --- AJAX SEARCH LOGIC ---

const searchInput = document.getElementById('search-input');
const tableContainer = document.getElementById('employee-table-container');

let debounceTimer;

if (searchInput) {
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            fetchEmployees();
        }, 500); // Maghihintay ng 0.5 seconds pagkatapos tumigil mag-type
    });
}

// I-update ang fetchEmployees function para tumanggap ng URL
function fetchEmployees(pageUrl = null) {
    const searchInputEl = document.getElementById('search-input');

    // 1. Kunin ang base path (halimbawa: /admin/employee/all)
    let baseUrl = pageUrl ? pageUrl.split('?')[0] : window.location.pathname;
    
    // 1. Kunin ang base params
    const params = new URLSearchParams({
        'search-input': searchInputEl.value || '',
        bureau: document.getElementById('bureau-select').value || '',
        division: document.getElementById('division-select').value || '',
        type: document.querySelector('select[name="type"]').value || '',
        status: document.querySelector('input[name="status"]:checked')?.value || '',
        ajax: 1
    });

    let fetchUrl;

    // 3. Kung galing sa pagination link, kunin ang 'page' number nito
    if (pageUrl) {
        const urlObj = new URL(pageUrl);
        const page = urlObj.searchParams.get('page');
        if (page) params.set('page', page);
    }

    // 4. BUUIN ANG TAMANG URL (Dito tayo nagka-error)
    // Pinagsasama nito ang baseUrl + ? + lahat ng params
    fetchUrl = `${baseUrl}?${params.toString()}`;
    tableContainer.classList.add('opacity-50', 'pointer-events-none');

    fetch(fetchUrl, {
        headers: { 
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        
    })
    .then(response => response.text())
    .then(html => {
        tableContainer.innerHTML = html;
        tableContainer.classList.remove('opacity-50', 'pointer-events-none');
        
        // I-update ang URL sa browser para kung i-refresh, nasa tamang page pa rin
        window.history.pushState({}, '', fetchUrl.replace('&ajax=1', '').replace('?ajax=1', ''));
    })
    .catch(error => console.error('Error:', error));
}

// I-update rin ang Apply button ng filter para AJAX na rin
document.querySelector('form').addEventListener('submit', function(e) {
    e.preventDefault();
    fetchEmployees();
});