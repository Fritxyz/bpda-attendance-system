// Idagdag ito sa dulo ng iyong script
document.querySelector('form').addEventListener('submit', function(e) {
    const suffixInput = document.getElementsByName('suffix')[0];
    const employeeId = document.getElementsByName('employee_id')[0];
    
    // 1. Employee ID Length Check
    if (employeeId.value.length !== 15) {
        alert("Employee ID must be exactly 15 digits.");
        e.preventDefault();
        return;
    }

    // 2. Suffix Validation (Regex for Jr, Sr, and Roman Numerals)
    const validSuffixRegex = /^(Jr|Sr|I|II|III|IV|V|VI|VII|VIII|IX|X|Jr\.|Sr\.)$/i;
    
    if (suffixInput.value.trim() !== "" && !validSuffixRegex.test(suffixInput.value.trim())) {
        alert("Invalid Suffix. Please use Jr, Sr, or Roman Numerals (I, II, III, etc.)");
        suffixInput.focus();
        e.preventDefault();
        return;
    }
});