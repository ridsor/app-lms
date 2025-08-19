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
        Monday: "Senin",
        Tuesday: "Selasa",
        Wednesday: "Rabu",
        Thursday: "Kamis",
        Friday: "Jumat",
        Saturday: "Sabtu",
        Sunday: "Minggu",
    };
    return days[day] || day;
}

function handleCopyText(text) {
    navigator.clipboard.writeText(text);
    const toast = new bootstrap.Toast($("#toast-success"));
    $("#toast-success #toast-text").text("Link berhasil disalin!");
    toast.show();
}

const flatpickrLocationID = {
    firstDayOfWeek: 1, // Mulai dari Senin
    weekdays: {
        shorthand: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
        longhand: [
            "Minggu",
            "Senin",
            "Selasa",
            "Rabu",
            "Kamis",
            "Jumat",
            "Sabtu",
        ],
    },
    months: {
        shorthand: [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "Mei",
            "Jun",
            "Jul",
            "Agu",
            "Sep",
            "Okt",
            "Nov",
            "Des",
        ],
        longhand: [
            "Januari",
            "Februari",
            "Maret",
            "April",
            "Mei",
            "Juni",
            "Juli",
            "Agustus",
            "September",
            "Oktober",
            "November",
            "Desember",
        ],
    },
};
