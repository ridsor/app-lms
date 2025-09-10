$(document).ready(function () {
    $("#start-exam-btn").on("click", function (e) {
        e.preventDefault();
        const id = $(this).data("id");
        const href = $(this).data("href");
        const originalHtml = $(this).html();
        const btnSubmit = $(this);

        Swal.fire({
            title: "Mulai Ujian",
            text: "Apakah Anda yakin ingin memulai ujian?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, mulai ujian!",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                btnSubmit
                    .prop("disabled", true)
                    .html(
                        '<i class="fa-solid fa-arrows-rotate fa-spin"></i> Mulai...'
                    );

                $.ajax({
                    url: `/ujian/${id}/mulai`,
                    method: "POST",
                    processData: false,
                    contentType: false,
                    success: function (res) {
                        if (res.success) {
                            const toast = new bootstrap.Toast(
                                $("#toast-success")
                            );
                            $("#toast-success #toast-text").text(res.message);
                            toast.show();
                            window.location.href = href;
                        }
                    },
                    error: function (xhr) {
                        const toast = new bootstrap.Toast($("#toast-error"));
                        $("#toast-error #toast-text").text(
                            xhr.responseJSON?.message
                        );
                        toast.show();
                    },
                    complete: function () {
                        btnSubmit.prop("disabled", false).html(originalHtml);
                    },
                });
            }
        });
    });
});
