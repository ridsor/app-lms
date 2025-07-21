$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});

function getQueryParams() {
    let params = {};
    let query = new URLSearchParams(window.location.search);
    query.forEach((value, key) => {
        params[key] = value;
    });
    return params;
}

function getDayName(day) {
    const days = {
        'Monday': 'Senin',
        'Tuesday': 'Selasa',
        'Wednesday': 'Rabu',
        'Thursday': 'Kamis',
        'Friday': 'Jumat',
        'Saturday': 'Sabtu',
        'Sunday': 'Minggu'
    };
    return days[day] || day;
}
