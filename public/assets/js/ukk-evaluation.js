$(document).ready(function () {
    $(".answer-score").on("change", function () {
        const input = $(this);
        const score = input.val();
        const answerId = input.data("answer-id");
        const ukkId = input.data("ukk-id");
        const maxScore = parseInt(input.attr("maxlength"));

        if (score === "") return;

        if (parseInt(score) > maxScore) {
            const toast = new bootstrap.Toast($("#toast-error"));
            $("#toast-error #toast-text").text(`Skor maksimal adalah ${maxScore}`);
            toast.show();
            input.addClass("is-invalid");
            return;
        }

        input.removeClass("is-invalid");

        $.ajax({
            url: `/ukk/${ukkId}/score/${answerId}`,
            method: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                score: score,
            },
            success: function (response) {
                // if (response.success) {
                //     const toast = new bootstrap.Toast($("#toast-success"));
                //     $("#toast-success #toast-text").text(response.message);
                //     toast.show();
                // }
            },
            error: function (xhr) {
                const toast = new bootstrap.Toast($("#toast-error"));
                $("#toast-error #toast-text").text(
                    xhr.responseJSON?.message || "Gagal mengubah skor"
                );
                toast.show();
            },
        });
    });

    if (typeof GLightbox !== "undefined") {
        GLightbox({
            selector: ".glightbox",
        });
    }
});
