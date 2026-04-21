$(document).ready(function () {
    $("#start-ukk-btn").on("click", function (e) {
        e.preventDefault();
        const id = $(this).data("id");
        const originalHtml = $(this).html();
        const btnSubmit = $(this);

        Swal.fire({
            title: "Mulai UKK",
            text: "Apakah Anda yakin ingin memulai UKK? Waktu akan mulai berjalan.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, mulai!",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                btnSubmit
                    .prop("disabled", true)
                    .html(
                        '<i class="fa-solid fa-arrows-rotate fa-spin"></i> Mulai...'
                    );

                $.ajax({
                    url: `/ukk/${id}/teori/mulai`,
                    method: "POST",
                    success: function (res) {
                        if (res.success) {
                            window.location.href = `/ukk/${id}/teori/pengerjaan`;
                        }
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                        });
                    },
                    complete: function () {
                        btnSubmit.prop("disabled", false).html(originalHtml);
                    },
                });
            }
        });
    });
});
