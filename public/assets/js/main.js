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

function showToast(type, message) {
    const toastEl = $(`#toast-${type}`);
    toastEl.find("#toast-text").text(message);
    new bootstrap.Toast(toastEl).show();
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

function getFileType(filename) {
    const extension = filename.toLowerCase().split(".").pop();

    const imageExtensions = ["jpg", "jpeg", "png", "gif", "bmp", "svg", "webp"];
    const videoExtensions = ["mp4", "avi", "mov", "wmv", "flv", "mkv", "webm"];
    const audioExtensions = ["mp3", "wav", "ogg", "aac", "flac", "m4a"];
    const documentExtensions = [
        "pdf",
        "doc",
        "docx",
        "txt",
        "rtf",
        "xlsx",
        "xls",
        "ppt",
        "pptx",
    ];
    const archiveExtensions = ["zip", "rar", "7z", "tar", "gz"];

    if (imageExtensions.includes(extension)) {
        return "image";
    } else if (videoExtensions.includes(extension)) {
        return "video";
    } else if (audioExtensions.includes(extension)) {
        return "audio";
    } else if (documentExtensions.includes(extension)) {
        return "document";
    } else if (archiveExtensions.includes(extension)) {
        return "archive";
    } else {
        return "other";
    }
}

function getFileExtension(filename) {
    if (!filename) return "";
    return filename.split(".").pop().toLowerCase();
}
