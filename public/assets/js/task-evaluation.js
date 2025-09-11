$("#form_evaluation").on("submit", function (e) {
    e.preventDefault();

    const task_id = $(this).data("id");
    const task_submission_id = $(this).data("tasksubmission-id");
    const btnSubmit = $(this).find("button[type='submit']");
    const originalHtml = btnSubmit.html();
    btnSubmit
        .prop("disabled", true)
        .html('<i class="fa-solid fa-arrows-rotate fa-spin"></i>');

    const formData = new FormData(this);
    $.ajax({
        url: `/jadwal/pertemuan/tugas/penilaian/${task_submission_id}`,
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            if (res.success) {
                const toast = new bootstrap.Toast($("#toast-success"));
                $("#toast-success #toast-text").text(res.message);
                toast.show();
            }
        },
        error: function (xhr) {
            const toast = new bootstrap.Toast($("#toast-error"));
            $("#toast-error #toast-text").text(xhr.responseJSON?.message);
            toast.show();
        },
        complete: function () {
            btnSubmit.prop("disabled", false).html(originalHtml);
        },
    });
});
