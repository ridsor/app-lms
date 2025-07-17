$(document).ready(function () {
    $("#change_attendance").on("click", function (e) {
        $(this).hide();
        $("#cancel_change_attendance").show();
        $("#save_attendance").show();

        $(".status-column").show();
        $(".status-input").show();
        $(".update-column").hide();
        $(".update-value").hide();
    });

    $("#save_attendance").on("click", function (e) {
        const button = $(this);
        const originalHtml = button.html();
        button
            .prop("disabled", true)
            .html('<i class="fa-solid fa-arrows-rotate fa-spin"></i>');

        const meeting_id = $(this).data("meeting-id");
        const attendances = [];
        $("#attendance-table .status-input").each(function () {
            if ($(this).data("user-id")) {
                const status =
                    $(this).find(".status-value:checked").val() || null;
                attendances.push({
                    user_id: $(this).data("user-id"),
                    status: status,
                });
            }
        });

        const data = {
            attendances,
        };

        $.ajax({
            url: `/kehadiran/pertemuan/${meeting_id}`,
            method: "PATCH",
            data: JSON.stringify(data),
            processData: false,
            contentType: false,
            contentType: "application/json",
            success: function (res) {
                if (res.success) {
                    const toast = new bootstrap.Toast($("#toast-success"));
                    $("#toast-success #toast-text").text(res.message);
                    toast.show();
                    location.reload();
                }
            },
            error: function (xhr) {
                const toast = new bootstrap.Toast($("#toast-error"));
                $("#toast-error #toast-text").text(
                    xhr.responseJSON?.message || "Gagal menyimpan kehadiran"
                );
                toast.show();
                button.prop("disabled", false).html(originalHtml);
            },
        });
    });

    $("#cancel_change_attendance").on("click", function (e) {
        $(this).hide();
        $("#change_attendance").show();
        $("#save_attendance").hide();

        $(".status-column").hide();
        $(".status-input").hide();
        $(".update-column").show();
        $(".update-value").show();
    });

    statuses.forEach(function (value) {
        $(`.status-all-${value}`).on("click", function () {
            $(`.status-input`).each(function () {
                $(this)
                    .find(`.status-value[value="${value}"]`)
                    .prop("checked", true);
            });
        });
    });

    function checkStatusAll() {
        let statuses = [];
        $("#attendance-table .status-value:checked").each(function () {
            statuses.push($(this).val());
        });
        let statusIsSame =
            statuses.length > 0 &&
            statuses.every(function (s) {
                return s === statuses[0];
            });
        if (statusIsSame) {
            $(`[name='status-all'][value='${statuses[0]}']`).prop(
                "checked",
                true
            );
        } else {
            $(`[name='status-all']:checked`).prop("checked", false);
        }
    }
    checkStatusAll();
    $("#attendance-table .status-value").on("change", function (e) {
        checkStatusAll();
    });
});
