$(document).ready(function () {
    // =================== Timer Countdown ===================
    if (typeof duration !== "undefined" && duration > 0) {
        const display = document.getElementById("countdown");

        if (display) {
            const endTime = Date.now() + duration * 1000;

            const timer = setInterval(() => {
                const now = Date.now();
                const diff = Math.floor((endTime - now) / 1000); // sisa detik

                if (diff <= 0) {
                    clearInterval(timer);
                    display.textContent = "00:00";
                    location.reload(); // Reload to update status
                    return;
                }

                const hours = Math.floor(diff / 3600);
                const minutes = Math.floor((diff % 3600) / 60);
                const seconds = diff % 60;

                display.textContent = `${String(hours).padStart(2, "0")}:${String(minutes).padStart(2, "0")}:${String(
                    seconds
                ).padStart(2, "0")}`;
            }, 1000);
        }
    }

    const preview = $("#ukk-submission-preview");
    
    // Normalisasi data awal
    let formattedContents = [];
    if (ukkSubmissionContents.links) {
        ukkSubmissionContents.links.forEach(link => {
            formattedContents.push({ url: link, id: null });
        });
    }
    if (ukkSubmissionContents.files) {
        ukkSubmissionContents.files.forEach(file => {
            formattedContents.push({ name: file.name, size: file.size, path: file.path, id: file.path }); // Menggunakan path sebagai ID sementara untuk file yang sudah ada
        });
    }
    
    let submissionContents = formattedContents;

    showContentPreview(submissionContents, preview, checkSubmitStatus);

    $("#ukk-submission-content-link").on("submit", function (e) {
        e.preventDefault();
        const linkInput = $("#ukk-submission-content-link [name='link']");
        const value = linkInput.val().trim();

        if (!isValidUrlContent({ url: value })) {
            linkInput.addClass("is-invalid");
            return false;
        }

        submissionContents.push({
            url: value,
            id: null
        });

        showContentPreview(submissionContents, preview, checkSubmitStatus);
        $("#linkModal").modal("hide");
        $("#ukk-submission-content-link")[0].reset();
    });

    $("#ukk-submission-content-file").on("change", function (e) {
        submissionContents = submissionContents.concat(
            Array.from(e.target.files)
        );
        showContentPreview(submissionContents, preview, checkSubmitStatus);
    });

    preview.on("click", ".btn-remove-file", function () {
        var idx = $(this).data("idx");
        const item = submissionContents[idx];
        
        if (item && item.path) {
            deleteContent.push(item.path);
        }

        submissionContents = submissionContents.filter((_, i) => i != idx);
        showContentPreview(submissionContents, preview, checkSubmitStatus);
    });

    function checkSubmitStatus() {
        $("#submit-practice").attr("disabled", false);
    }

    $("#submit-practice").on("click", function () {
        const btnSubmit = $(this);
        const originalHtml = btnSubmit.html();
        btnSubmit.prop("disabled", true).html('<i class="fa-solid fa-arrows-rotate fa-spin"></i>');

        const formData = new FormData();
        formData.append('description', $('#practice-description').val());
        
        let fileIndex = 0;
        let linkIndex = 0;

        submissionContents.forEach((item) => {
            if (isValidUrlContent(item)) {
                formData.append(`links[${linkIndex}]`, item.url);
                linkIndex++;
            } else if (item instanceof File) {
                formData.append(`files[${fileIndex}]`, item);
                fileIndex++;
            } else if (item.path) {
                // File yang sudah ada dan tidak dihapus
                formData.append(`existing_files[${fileIndex}][name]`, item.name);
                formData.append(`existing_files[${fileIndex}][path]`, item.path);
                formData.append(`existing_files[${fileIndex}][size]`, item.size);
                fileIndex++;
            }
        });

        deleteContent.forEach((path, index) => {
            formData.append(`delete_files[${index}]`, path);
        });

        $.ajax({
            url: `/ukk/${ukkId}/praktik/submit`,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                if (res.success || res.data) {
                    const toast = new bootstrap.Toast($("#toast-success"));
                    $("#toast-success #toast-text").text(res.message);
                    toast.show();
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                }
            },
            error: function (xhr) {
                const message = xhr.responseJSON ? xhr.responseJSON.message : "Terjadi kesalahan";
                const toast = new bootstrap.Toast($("#toast-error"));
                $("#toast-error #toast-text").text(message);
                toast.show();
                btnSubmit.prop("disabled", false).html(originalHtml);
            },
        });
    });
});
