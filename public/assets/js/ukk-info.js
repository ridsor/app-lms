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
                    display.textContent = "00:00:00";
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
