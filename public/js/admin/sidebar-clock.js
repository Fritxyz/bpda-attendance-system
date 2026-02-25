
function updateClock() {
    const now = new Date();
    const options = { 
        timeZone: 'Asia/Manila',
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit', 
        minute: '2-digit', 
        second: '2-digit',
        hour12: true 
    };
    
    const formatter = new Intl.DateTimeFormat('en-US', options);
    // Ginagawa nating uppercase at pinapalitan ang "at" ng "|" para bumagay sa design mo
    let parts = formatter.format(now).replace(', ', ' | ').replace(' at ', ' | ');
    document.getElementById('live-clock').textContent = parts;
}

// I-update bawat segundo
setInterval(updateClock, 1000);
